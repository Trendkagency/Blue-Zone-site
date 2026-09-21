<?php

namespace App\Http\Controllers\Admin\Mr;

use App\Http\Controllers\Controller;
use App\Models\Mr\ContactClassification;
use Illuminate\Http\Request;

class ClassificationController extends Controller
{
    public function index(Request $request)
    {
        $classifications = ContactClassification::withCount('contacts')
            ->orderBy('sort_order')
            ->get();

        if ($request->has('export')) {
            $exporter = app(\App\Services\Mr\Export\MrTableExcelExporter::class);
            $metadata = ['Total Classifications' => $classifications->count()];
            $totalDoctors = $classifications->sum('contacts_count');

            $kpiCards = [
                ['label' => 'Total Classes', 'val' => (string)$classifications->count(), 'bg' => 'F1F5F9', 'fg' => '0F172A', 'border' => 'CBD5E1'],
                ['label' => 'Registered Doctors', 'val' => (string)$totalDoctors, 'bg' => 'E0F2FE', 'fg' => '0369A1', 'border' => 'BAE6FD'],
            ];

            $columns = [
                [
                    'key' => fn($c) => $c->code,
                    'header' => 'Class Code',
                    'width' => 12,
                    'type' => 'badge',
                    'align' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'badgeColors' => fn($val) => match (strtoupper((string)$val)) {
                        'A+' => ['bg' => 'FEF3C7', 'fg' => '92400E'],
                        'A' => ['bg' => 'E0F2FE', 'fg' => '0369A1'],
                        'B' => ['bg' => 'F1F5F9', 'fg' => '334155'],
                        default => ['bg' => 'F8FAFC', 'fg' => '64748B'],
                    }
                ],
                ['key' => fn($c) => $c->label, 'header' => 'Classification Label', 'width' => 26, 'align' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT],
                ['key' => fn($c) => (int)$c->points, 'header' => 'Points Per Visit', 'width' => 16, 'type' => 'number', 'align' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                ['key' => fn($c) => (int)$c->required_visits, 'header' => 'Required Frequency', 'width' => 18, 'type' => 'number', 'align' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                ['key' => fn($c) => (int)$c->contacts_count, 'header' => 'Doctors Registered', 'width' => 18, 'type' => 'number', 'align' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                [
                    'key' => fn($c) => $c->is_active ? 'Active' : 'Inactive',
                    'header' => 'Status',
                    'width' => 12,
                    'type' => 'badge',
                    'align' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'badgeColors' => fn($val) => $val === 'Active' ? ['bg' => 'DCFCE7', 'fg' => '15803D'] : ['bg' => 'FEE2E2', 'fg' => 'B91C1C']
                ],
            ];

            return $exporter->export(
                'Doctor Classifications & Frequency Points Scale',
                $metadata,
                $kpiCards,
                $columns,
                $classifications,
                'doctor-classifications-' . date('Y-m-d') . '.xlsx'
            );
        }

        return view('admin.mr.classifications.index', compact('classifications'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:contact_classifications,code',
            'label' => 'required|string|max:100',
            'points' => 'required|integer|min:1',
            'required_visits' => 'required|integer|min:1',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? (bool) $request->is_active : true;
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        ContactClassification::create($validated);

        return redirect()->route('admin.mr.classifications.index')
            ->with('success', __('admin.mr.classification_saved_successfully'));
    }

    public function update(Request $request, int $id)
    {
        $classification = ContactClassification::findOrFail($id);

        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:contact_classifications,code,' . $classification->id,
            'label' => 'required|string|max:100',
            'points' => 'required|integer|min:1',
            'required_visits' => 'required|integer|min:1',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? (bool) $request->is_active : false;

        $classification->update($validated);

        return redirect()->route('admin.mr.classifications.index')
            ->with('success', __('admin.mr.classification_updated_successfully'));
    }

    public function destroy(int $id)
    {
        $classification = ContactClassification::withCount('contacts')->findOrFail($id);

        if ($classification->contacts_count > 0) {
            return redirect()->route('admin.mr.classifications.index')
                ->with('error', __('admin.mr.cannot_delete_classification_with_doctors'));
        }

        $classification->delete();

        return redirect()->route('admin.mr.classifications.index')
            ->with('success', __('admin.mr.classification_deleted_successfully'));
    }
}

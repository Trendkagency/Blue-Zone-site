<?php

namespace App\Http\Controllers\Admin\Mr;

use App\Http\Controllers\Controller;
use App\Models\Mr\ContactSpecialty;
use Illuminate\Http\Request;

class SpecialtyController extends Controller
{
    public function index(Request $request)
    {
        $specialties = ContactSpecialty::withCount('contacts')->latest()->get();

        if ($request->has('export')) {
            $exporter = app(\App\Services\Mr\Export\MrTableExcelExporter::class);
            $metadata = ['Total Specialties' => $specialties->count()];
            $totalDoctors = $specialties->sum('contacts_count');

            $kpiCards = [
                ['label' => 'Total Specialties', 'val' => (string)$specialties->count(), 'bg' => 'F1F5F9', 'fg' => '0F172A', 'border' => 'CBD5E1'],
                ['label' => 'Total Registered Doctors', 'val' => (string)$totalDoctors, 'bg' => 'E0F2FE', 'fg' => '0369A1', 'border' => 'BAE6FD'],
            ];

            $columns = [
                ['key' => fn($s) => $s->code, 'header' => 'Specialty Code', 'width' => 16, 'align' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                ['key' => fn($s) => $s->name, 'header' => 'Specialty Name', 'width' => 26, 'align' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT],
                ['key' => fn($s) => (int)$s->contacts_count, 'header' => 'Doctors Registered', 'width' => 18, 'type' => 'number', 'align' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
                ['key' => fn($s) => $s->description ?: '—', 'header' => 'Description', 'width' => 30, 'align' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT],
                [
                    'key' => fn($s) => $s->is_active ? 'Active' : 'Inactive',
                    'header' => 'Status',
                    'width' => 12,
                    'type' => 'badge',
                    'align' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'badgeColors' => fn($val) => $val === 'Active' ? ['bg' => 'DCFCE7', 'fg' => '15803D'] : ['bg' => 'FEE2E2', 'fg' => 'B91C1C']
                ],
            ];

            return $exporter->export(
                'Medical Specialties Directory',
                $metadata,
                $kpiCards,
                $columns,
                $specialties,
                'medical-specialties-' . date('Y-m-d') . '.xlsx'
            );
        }

        return view('admin.mr.specialties.index', compact('specialties'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:20|unique:contact_specialties,code',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? (bool) $request->is_active : true;

        ContactSpecialty::create($validated);

        return redirect()->route('admin.mr.specialties.index')
            ->with('success', __('admin.mr.specialty_created_successfully'));
    }

    public function update(Request $request, int $id)
    {
        $specialty = ContactSpecialty::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:20|unique:contact_specialties,code,' . $specialty->id,
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? (bool) $request->is_active : false;

        $specialty->update($validated);

        return redirect()->route('admin.mr.specialties.index')
            ->with('success', __('admin.mr.specialty_updated_successfully'));
    }

    public function destroy(int $id)
    {
        $specialty = ContactSpecialty::withCount('contacts')->findOrFail($id);

        if ($specialty->contacts_count > 0) {
            return redirect()->route('admin.mr.specialties.index')
                ->with('error', __('admin.mr.cannot_delete_specialty_with_doctors'));
        }

        $specialty->delete();

        return redirect()->route('admin.mr.specialties.index')
            ->with('success', __('admin.mr.specialty_deleted_successfully'));
    }
}

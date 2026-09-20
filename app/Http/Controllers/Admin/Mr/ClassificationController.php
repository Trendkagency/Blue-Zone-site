<?php

namespace App\Http\Controllers\Admin\Mr;

use App\Http\Controllers\Controller;
use App\Models\Mr\ContactClassification;
use Illuminate\Http\Request;

class ClassificationController extends Controller
{
    public function index()
    {
        $classifications = ContactClassification::withCount('contacts')
            ->orderBy('sort_order')
            ->get();

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

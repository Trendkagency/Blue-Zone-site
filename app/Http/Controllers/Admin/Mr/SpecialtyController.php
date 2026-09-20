<?php

namespace App\Http\Controllers\Admin\Mr;

use App\Http\Controllers\Controller;
use App\Models\Mr\ContactSpecialty;
use Illuminate\Http\Request;

class SpecialtyController extends Controller
{
    public function index()
    {
        $specialties = ContactSpecialty::withCount('contacts')->latest()->get();

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

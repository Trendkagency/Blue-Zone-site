<?php

namespace App\Http\Controllers\Admin\Hr;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class EmployeeDocumentController extends Controller
{
    public function index(Request $request): View
    {
        $query = EmployeeDocument::with('employee');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('document_number', 'like', "%{$search}%")
                    ->orWhereHas('employee', function ($eq) use ($search) {
                        $eq->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('employee_number', 'like', "%{$search}%");
                    });
            });
        }

        if ($type = $request->input('document_type')) {
            $query->where('document_type', $type);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($request->boolean('expiring_soon')) {
            $query->where('expiry_date', '<=', now()->addDays(30)->toDateString())
                ->where('expiry_date', '>=', now()->toDateString());
        }

        $documents = $query->latest()->paginate(20)->withQueryString();
        $employees = Employee::where('employment_status', 'active')->get();

        return view('admin.hr.employees.documents', compact('documents', 'employees'));
    }

    public function store(Request $request, int $employeeId): RedirectResponse
    {
        $employee = Employee::findOrFail($employeeId);

        $validated = $request->validate([
            'document_type' => ['required', 'string'],
            'title' => ['required', 'string', 'max:150'],
            'document_number' => ['nullable', 'string', 'max:100'],
            'issue_date' => ['nullable', 'date'],
            'expiry_date' => ['nullable', 'date', 'after_or_equal:issue_date'],
            'document_file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:10240'],
            'notes' => ['nullable', 'string'],
        ]);

        $file = $request->file('document_file');
        $path = $file->store("hr/documents/{$employee->employee_number}", 'public');

        EmployeeDocument::create([
            'employee_id' => $employee->id,
            'document_type' => $validated['document_type'],
            'title' => $validated['title'],
            'document_number' => $validated['document_number'] ?? null,
            'issue_date' => $validated['issue_date'] ?? null,
            'expiry_date' => $validated['expiry_date'] ?? null,
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => round($file->getSize() / 1024, 2) . ' KB',
            'mime_type' => $file->getClientMimeType(),
            'status' => 'valid',
            'notes' => $validated['notes'] ?? null,
            'uploaded_by' => auth()->id(),
        ]);

        return back()->with('success', __('hr.document_uploaded_successfully', ['default' => 'Document securely uploaded.']));
    }

    public function download(int $id): BinaryFileResponse
    {
        $doc = EmployeeDocument::findOrFail($id);
        $fullPath = storage_path('app/public/' . $doc->file_path);

        if (!file_exists($fullPath)) {
            abort(404, 'Document file not found on disk.');
        }

        return response()->download($fullPath, $doc->file_name ?? basename($doc->file_path));
    }

    public function destroy(int $id): RedirectResponse
    {
        $doc = EmployeeDocument::findOrFail($id);
        if ($doc->file_path && Storage::disk('public')->exists($doc->file_path)) {
            Storage::disk('public')->delete($doc->file_path);
        }
        $doc->delete();

        return back()->with('success', __('hr.document_deleted_successfully', ['default' => 'Document deleted.']));
    }
}

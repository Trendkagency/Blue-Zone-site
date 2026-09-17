<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CrmLeadSource;
use App\Models\CrmPipeline;
use App\Models\User;
use App\Services\CrmLeadImportService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CrmLeadImportController extends Controller
{
    public function __construct(
        protected CrmLeadImportService $importService
    ) {}

    /**
     * Show CRM Lead & Task Import interface.
     */
    public function show()
    {
        $pipelines = CrmPipeline::active()->orderBy('sort_order')->get();
        $owners = User::where('status', 'active')->select('id', 'name', 'email')->get();
        $sources = CrmLeadSource::active()->orderBy('sort_order')->get();

        return view('admin.crm.leads.import', compact('pipelines', 'owners', 'sources'));
    }

    /**
     * Process uploaded Excel/CSV file.
     */
    public function import(Request $request)
    {
        $request->validate([
            'sheet_file' => [
                'required',
                'file',
                'max:20480', // 20 MB max
                function ($attribute, $value, $fail) {
                    $ext = strtolower($value->getClientOriginalExtension());
                    if (!in_array($ext, ['xlsx', 'xls', 'csv', 'txt'])) {
                        $fail(__('The file must be a valid Excel (.xlsx) or CSV (.csv) document.'));
                    }
                },
            ],
            'default_owner_id' => ['nullable', 'exists:users,id'],
            'pipeline_id' => ['nullable', 'exists:crm_pipelines,id'],
            'duplicate_action' => ['required', 'in:skip,update'],
            'create_tasks' => ['nullable'],
        ]);

        $options = [
            'default_owner_id' => $request->input('default_owner_id'),
            'pipeline_id' => $request->input('pipeline_id'),
            'duplicate_action' => $request->input('duplicate_action', 'skip'),
            'create_tasks' => $request->boolean('create_tasks', true),
        ];

        try {
            $results = $this->importService->import(
                $request->file('sheet_file'),
                $options,
                auth()->id() ?? User::value('id')
            );

            $msg = "Import completed successfully! Processed {$results['total_rows']} rows. Created: {$results['imported_leads']} leads, {$results['created_tasks']} tasks. Skipped duplicates: {$results['skipped_duplicates']}.";
            if ($results['updated_leads'] > 0) {
                $msg .= " Updated: {$results['updated_leads']} existing leads.";
            }

            return back()->with('import_results', $results)->with('success', $msg);
        } catch (\Throwable $e) {
            return back()->with('error', 'Import Failed: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Download sample Excel template pre-populated with realistic leads and tasks.
     */
    public function downloadTemplate(Request $request): BinaryFileResponse
    {
        $format = in_array(strtolower($request->query('format', 'xlsx')), ['xlsx', 'csv']) ? strtolower($request->query('format', 'xlsx')) : 'xlsx';
        $filePath = $this->importService->generateSampleTemplate($format);

        $downloadName = 'blue_zone_crm_leads_with_tasks_sample.' . $format;

        return response()->download($filePath, $downloadName, [
            'Content-Type' => $format === 'csv' ? 'text/csv' : 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use App\Models\CrmActivity;
use App\Models\CrmCampaign;
use App\Models\CrmLead;
use App\Models\CrmLeadSource;
use App\Models\CrmOpportunity;
use App\Models\CrmPipeline;
use App\Models\CrmPipelineStage;
use App\Models\User;
use App\Services\CrmLeadImportService;
use App\Services\CrmLeadService;
use App\Services\CrmOpportunityService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use OpenSpout\Reader\CSV\Reader as CsvReader;
use OpenSpout\Reader\XLSX\Reader as XlsxReader;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CrmSalesExcelController extends Controller
{
    public function __construct(
        protected CrmLeadImportService $importService,
        protected CrmLeadService $leadService,
        protected CrmOpportunityService $opportunityService
    ) {}

    /**
     * Display the comprehensive Sales Excel Hub (Import, Export, and Manual Grid).
     */
    public function index(Request $request)
    {
        $pipelines = CrmPipeline::active()->with('stages')->orderBy('sort_order')->get();
        $defaultPipeline = $pipelines->where('is_default', true)->first() ?? $pipelines->first();
        $stages = $defaultPipeline ? $defaultPipeline->stages : CrmPipelineStage::where('is_active', true)->orderBy('sort_order')->get();
        $owners = User::where('status', 'active')->select('id', 'name', 'email')->orderBy('name')->get();
        $sources = CrmLeadSource::active()->orderBy('sort_order')->get();
        $campaigns = CrmCampaign::active()->orderBy('name')->get();
        $countries = Country::where('is_active', true)->orderBy('name_en')->get();

        // Metrics for Quick Stats Header
        $stats = [
            'total_leads' => CrmLead::count(),
            'total_opportunities' => CrmOpportunity::count(),
            'pipeline_value' => (float) CrmOpportunity::where('status', 'open')->sum('value'),
            'won_deals_value' => (float) CrmOpportunity::where('status', 'won')->sum('value'),
            'active_reps' => $owners->count(),
        ];

        return view('admin.crm.excel.index', compact(
            'pipelines',
            'stages',
            'owners',
            'sources',
            'campaigns',
            'countries',
            'stats',
            'defaultPipeline'
        ));
    }

    /**
     * Download the official, professionally styled Excel (.xlsx) or CSV template.
     */
    public function downloadTemplate(Request $request)
    {
        $format = strtolower($request->query('format', 'xlsx'));

        if ($format === 'csv') {
            $filePath = $this->importService->generateSampleTemplate('csv');
            return response()->download($filePath, 'blue_zone_crm_sales_sample.csv', [
                'Content-Type' => 'text/csv; charset=UTF-8',
            ])->deleteFileAfterSend(true);
        }

        // Generate styled .xlsx via PhpSpreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Sales Leads & Deals');

        // Headers
        $headers = [
            'A1' => 'First Name *',
            'B1' => 'Last Name',
            'C1' => 'Company / Clinic *',
            'D1' => 'Job Title',
            'E1' => 'Email',
            'F1' => 'Phone *',
            'G1' => 'City',
            'H1' => 'Country',
            'I1' => 'Status (new/contacted/qualified)',
            'J1' => 'Priority (low/medium/high/urgent)',
            'K1' => 'Estimated Value (SAR)',
            'L1' => 'Currency',
            'M1' => 'Lead Source',
            'N1' => 'Marketing Campaign',
            'O1' => 'Assigned Owner Email',
            'P1' => 'Follow-up Task Subject',
            'Q1' => 'Task Type (call/meeting/task)',
            'R1' => 'Task Due Date (YYYY-MM-DD)',
            'S1' => 'Notes / Regimen Interest',
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        // Style Header Row
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
                'name' => 'Calibri',
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0A4F78'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '062B49'],
                ],
            ],
        ];
        $sheet->getStyle('A1:S1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(32);

        // Realistic Samples
        $samples = [
            [
                'Dr. Hisham', 'Al-Khatib', 'Oasis Longevity & Wellness Clinic', 'Chief Medical Officer',
                'dr.hisham@oasisclinic.sa', '+966 50 112 3344', 'Riyadh', 'Saudi Arabia',
                'qualified', 'high', 45000.00, 'SAR', 'Clinic Outreach', 'Cellular Health 2026',
                'admin@bluezone.com', 'Zoom Meeting: Longevity Protocol Presentation', 'meeting',
                Carbon::now()->addDays(2)->format('Y-m-d'), 'Seeking bulk NMN 18000 and Resveratrol longevity protocol for 50 clinic members.',
            ],
            [
                'Dr. Sarah', 'Al-Mansoor', 'Vitality Medical Center', 'Head of Clinical Anti-Aging',
                's.mansoor@vitalitymed.sa', '+966 55 987 6543', 'Jeddah', 'Saudi Arabia',
                'new', 'urgent', 72000.00, 'SAR', 'Medical Conference', 'Biotech Longevity Summit',
                'admin@bluezone.com', 'Deliver Clinical Sample Kit to Clinic', 'task',
                Carbon::now()->addDays(1)->format('Y-m-d'), 'Interested in prescribing NAD+ boosters and Blue Mind cognitive enhancers for executive patients.',
            ],
            [
                'Eng. Tariq', 'Al-Ghamdi', 'Al-Nokhba Health & Wellness Group', 'Procurement Director',
                'tariq@alnokhbagroup.com', '+966 54 332 1199', 'Dammam', 'Saudi Arabia',
                'contacted', 'medium', 120000.00, 'SAR', 'Partner Referral', '',
                'admin@bluezone.com', 'Introductory Call: Bulk Wholesale Terms', 'call',
                Carbon::now()->addDays(3)->format('Y-m-d'), 'Annual corporate wellness supply contract for 3 medical branches across the Eastern Province.',
            ],
            [
                'Reem', 'Al-Zahrani', 'Private VIP Longevity Client', 'Executive Director',
                'reem.z@vip-wellness.sa', '+966 50 445 5667', 'Khobar', 'Saudi Arabia',
                'new', 'high', 18500.00, 'SAR', 'WhatsApp', '',
                'admin@bluezone.com', 'Phone Consultation: Personal Regimen', 'call',
                Carbon::now()->addDays(4)->format('Y-m-d'), 'Personalized annual protocol inquiry for cellular rejuvenation and mitochondrial support.',
            ],
        ];

        $rowIdx = 2;
        foreach ($samples as $row) {
            $colIdx = 1;
            foreach ($row as $val) {
                $sheet->setCellValueExplicit(
                    Coordinate::stringFromColumnIndex($colIdx) . $rowIdx,
                    $val,
                    is_numeric($val) ? \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC : \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING
                );
                $colIdx++;
            }
            $sheet->getRowDimension($rowIdx)->setRowHeight(24);
            $rowIdx++;
        }

        // Zebra striping & cell styling
        $dataStyle = [
            'font' => ['size' => 10, 'name' => 'Calibri'],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'E2E8F0'],
                ],
            ],
        ];
        $sheet->getStyle('A2:S' . ($rowIdx - 1))->applyFromArray($dataStyle);

        // Auto-fit column widths
        foreach (range(1, 19) as $col) {
            $colLetter = Coordinate::stringFromColumnIndex($col);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        $fileName = 'blue_zone_crm_sales_sample_' . date('Y-m-d') . '.xlsx';
        $tempPath = storage_path('app/' . $fileName);

        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        return response()->download($tempPath, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Interactive AJAX preview of uploaded spreadsheet rows before committing.
     */
    public function preview(Request $request): JsonResponse
    {
        $request->validate([
            'sheet_file' => ['required', 'file', 'max:20480'],
        ]);

        $file = $request->file('sheet_file');
        $ext = strtolower($file->getClientOriginalExtension());

        if (!in_array($ext, ['xlsx', 'xls', 'csv', 'txt'])) {
            return response()->json(['error' => 'Invalid file format. Please upload .xlsx or .csv'], 422);
        }

        try {
            $reader = in_array($ext, ['csv', 'txt']) ? new CsvReader() : new XlsxReader();
            $reader->open($file->getRealPath());

            $headers = null;
            $rows = [];
            $totalCount = 0;
            $validCount = 0;
            $invalidCount = 0;
            $duplicateCount = 0;

            foreach ($reader->getSheetIterator() as $sheet) {
                foreach ($sheet->getRowIterator() as $row) {
                    $cells = $row->toArray();

                    if ($headers === null) {
                        $headers = array_map(fn ($h) => trim((string) $h), $cells);
                        continue;
                    }

                    if (empty(array_filter($cells, fn ($v) => $v !== null && trim((string)$v) !== ''))) {
                        continue;
                    }

                    $totalCount++;
                    $name = trim(($cells[0] ?? '') . ' ' . ($cells[1] ?? ''));
                    $company = trim($cells[2] ?? '');
                    $email = trim($cells[4] ?? '');
                    $phone = trim($cells[5] ?? '');
                    $value = is_numeric($cells[10] ?? null) ? (float) $cells[10] : 0.0;

                    $errors = [];
                    if (empty($name) && empty($company)) {
                        $errors[] = 'Missing Name & Company';
                    }

                    $isDuplicate = false;
                    if (!empty($phone)) {
                        $normalized = preg_replace('/[^\d+]/', '', $phone);
                        if (CrmLead::where('phone_normalized', $normalized)->orWhere('phone', $phone)->exists()) {
                            $isDuplicate = true;
                            $duplicateCount++;
                        }
                    }

                    if (!empty($errors)) {
                        $invalidCount++;
                    } else {
                        $validCount++;
                    }

                    if (count($rows) < 25) {
                        $rows[] = [
                            'row_number' => $totalCount + 1,
                            'name' => $name ?: ($company ?: 'N/A'),
                            'company' => $company ?: 'Individual Client',
                            'email' => $email ?: '—',
                            'phone' => $phone ?: '—',
                            'value' => number_format($value, 2) . ' SAR',
                            'status' => ucfirst(trim($cells[8] ?? 'new')),
                            'is_duplicate' => $isDuplicate,
                            'errors' => $errors,
                            'is_valid' => empty($errors),
                        ];
                    }
                }
                break;
            }

            $reader->close();

            return response()->json([
                'success' => true,
                'total_rows' => $totalCount,
                'valid_count' => $validCount,
                'invalid_count' => $invalidCount,
                'duplicate_count' => $duplicateCount,
                'preview_rows' => $rows,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Failed to parse file: ' . $e->getMessage()], 422);
        }
    }

    /**
     * Process batch Excel import.
     */
    public function import(Request $request)
    {
        $request->validate([
            'sheet_file' => [
                'required',
                'file',
                'max:20480',
                function ($attribute, $value, $fail) {
                    $ext = strtolower($value->getClientOriginalExtension());
                    if (!in_array($ext, ['xlsx', 'xls', 'csv', 'txt'])) {
                        $fail(__('The file must be a valid Excel (.xlsx) or CSV (.csv) document.'));
                    }
                },
            ],
            'default_owner_id' => ['nullable', 'exists:users,id'],
            'pipeline_id' => ['nullable', 'exists:crm_pipelines,id'],
            'duplicate_action' => ['required', 'in:skip,update,create_new'],
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
                $msg .= " Updated: {$results['updated_leads']} existing records.";
            }

            return back()->with('import_results', $results)->with('success', $msg);
        } catch (\Throwable $e) {
            return back()->with('error', 'Import Failed: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Advanced Export of CRM Sales Data to styled Excel (.xlsx) or CSV.
     */
    public function export(Request $request)
    {
        $type = $request->query('type', 'all_sales'); // 'leads', 'opportunities', 'all_sales'
        $format = strtolower($request->query('format', 'xlsx'));
        $dateRange = $request->query('date_range', 'all');
        $status = $request->query('status');
        $stageId = $request->query('stage_id');
        $ownerId = $request->query('owner_id');
        $sourceId = $request->query('source_id');
        $minValue = $request->query('min_value');
        $maxValue = $request->query('max_value');

        // Apply Date Filters
        $startDate = null;
        $endDate = null;
        if ($dateRange === 'today') {
            $startDate = Carbon::today()->startOfDay();
            $endDate = Carbon::today()->endOfDay();
        } elseif ($dateRange === 'yesterday') {
            $startDate = Carbon::yesterday()->startOfDay();
            $endDate = Carbon::yesterday()->endOfDay();
        } elseif ($dateRange === 'this_week') {
            $startDate = Carbon::now()->startOfWeek();
            $endDate = Carbon::now()->endOfWeek();
        } elseif ($dateRange === 'this_month') {
            $startDate = Carbon::now()->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();
        } elseif ($dateRange === 'this_quarter') {
            $startDate = Carbon::now()->firstOfQuarter();
            $endDate = Carbon::now()->lastOfQuarter();
        } elseif ($dateRange === 'custom') {
            if ($request->query('start_date')) {
                $startDate = Carbon::parse($request->query('start_date'))->startOfDay();
            }
            if ($request->query('end_date')) {
                $endDate = Carbon::parse($request->query('end_date'))->endOfDay();
            }
        }

        // Query Leads
        $leadsQuery = CrmLead::with(['owner:id,name', 'source:id,name_en,name_ar', 'campaign:id,name', 'country', 'city'])->latest();
        if ($startDate && $endDate) {
            $leadsQuery->whereBetween('created_at', [$startDate, $endDate]);
        }
        if (!empty($status)) {
            $leadsQuery->where('status', $status);
        }
        if (!empty($ownerId)) {
            $leadsQuery->where('owner_id', $ownerId);
        }
        if (!empty($sourceId)) {
            $leadsQuery->where('source_id', $sourceId);
        }
        if (is_numeric($minValue)) {
            $leadsQuery->where('estimated_value', '>=', (float) $minValue);
        }
        if (is_numeric($maxValue)) {
            $leadsQuery->where('estimated_value', '<=', (float) $maxValue);
        }

        // Query Opportunities
        $oppsQuery = CrmOpportunity::with(['lead', 'customer', 'pipeline', 'stage', 'owner:id,name', 'source:id,name_en,name_ar'])->latest();
        if ($startDate && $endDate) {
            $oppsQuery->whereBetween('created_at', [$startDate, $endDate]);
        }
        if (!empty($stageId)) {
            $oppsQuery->where('stage_id', $stageId);
        }
        if (!empty($ownerId)) {
            $oppsQuery->where('owner_id', $ownerId);
        }
        if (is_numeric($minValue)) {
            $oppsQuery->where('value', '>=', (float) $minValue);
        }
        if (is_numeric($maxValue)) {
            $oppsQuery->where('value', '<=', (float) $maxValue);
        }

        $timestamp = date('Y-m-d_His');
        $fileName = "blue_zone_sales_export_{$type}_{$timestamp}";

        // Handle CSV format stream
        if ($format === 'csv') {
            return $this->streamCsvExport($type, $leadsQuery, $oppsQuery, $fileName . '.csv');
        }

        // Handle Styled .xlsx format via PhpSpreadsheet
        return $this->generateStyledXlsxExport($type, $leadsQuery, $oppsQuery, $fileName . '.xlsx');
    }

    /**
     * Save rows submitted from the Interactive Manual Excel Grid in the browser.
     */
    public function saveManualGrid(Request $request): JsonResponse
    {
        $request->validate([
            'rows' => ['required', 'array', 'min:1'],
            'rows.*.name' => ['required', 'string', 'max:255'],
            'pipeline_id' => ['nullable', 'exists:crm_pipelines,id'],
            'default_owner_id' => ['nullable', 'exists:users,id'],
            'create_tasks' => ['nullable', 'boolean'],
        ]);

        $rows = $request->input('rows', []);
        $pipelineId = $request->input('pipeline_id') ?: CrmPipeline::where('is_default', true)->value('id') ?: CrmPipeline::value('id');
        $ownerId = $request->input('default_owner_id') ?: auth()->id() ?: User::value('id');
        $createTasks = $request->boolean('create_tasks', true);

        $createdLeadsCount = 0;
        $createdOppsCount = 0;
        $createdTasksCount = 0;

        DB::beginTransaction();
        try {
            $pipeline = CrmPipeline::with('stages')->find($pipelineId);
            $initialStage = $pipeline ? ($pipeline->stages->sortBy('sort_order')->first()) : null;

            foreach ($rows as $row) {
                $name = trim($row['name'] ?? '');
                if (empty($name)) {
                    continue;
                }

                $phone = !empty($row['phone']) ? trim($row['phone']) : null;
                $phoneNormalized = $phone ? preg_replace('/[^\d+]/', '', $phone) : null;
                $email = !empty($row['email']) ? strtolower(trim($row['email'])) : null;
                $company = !empty($row['company']) ? trim($row['company']) : null;
                $value = is_numeric($row['value'] ?? null) ? (float) $row['value'] : 0.0;
                $status = in_array(strtolower($row['status'] ?? 'new'), ['new', 'contacted', 'qualified', 'unqualified', 'lost', 'won']) ? strtolower($row['status']) : 'new';
                $priority = in_array(strtolower($row['priority'] ?? 'medium'), ['low', 'medium', 'high', 'urgent']) ? strtolower($row['priority']) : 'medium';
                $notes = !empty($row['notes']) ? trim($row['notes']) : null;
                $rowOwnerId = !empty($row['owner_id']) ? (int) $row['owner_id'] : $ownerId;

                // Split name
                $parts = explode(' ', $name, 2);
                $firstName = $parts[0] ?? $name;
                $lastName = $parts[1] ?? '';

                // Create CrmLead
                $lead = CrmLead::create([
                    'lead_number' => $this->leadService->generateLeadNumber(),
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'full_name' => $name,
                    'company_name' => $company ?: $name,
                    'email' => $email,
                    'phone' => $phone,
                    'phone_normalized' => $phoneNormalized,
                    'status' => $status,
                    'priority' => $priority,
                    'estimated_value' => $value,
                    'currency' => 'SAR',
                    'owner_id' => $rowOwnerId,
                    'assigned_at' => now(),
                    'notes' => $notes,
                    'score' => 60,
                ]);
                $createdLeadsCount++;

                // Create CrmOpportunity if deal value or pipeline is active
                if ($pipeline && $initialStage) {
                    CrmOpportunity::create([
                        'opportunity_number' => $this->opportunityService->generateOpportunityNumber(),
                        'lead_id' => $lead->id,
                        'name' => ($company ?: $name) . ' - Longevity Protocol',
                        'pipeline_id' => $pipeline->id,
                        'stage_id' => $initialStage->id,
                        'owner_id' => $rowOwnerId,
                        'value' => $value,
                        'currency' => 'SAR',
                        'probability' => $initialStage->probability ?? 25,
                        'status' => 'open',
                        'expected_close_date' => Carbon::now()->addDays(30),
                        'description' => "Manually entered deal via CRM Excel Grid: {$notes}",
                    ]);
                    $createdOppsCount++;
                }

                // Create follow up task if requested
                if ($createTasks && !empty($row['task_subject'])) {
                    $taskType = !empty($row['task_type']) && in_array(strtolower($row['task_type']), ['call', 'meeting', 'task', 'email'], true) ? strtolower($row['task_type']) : 'call';
                    CrmActivity::create([
                        'activity_type' => $taskType,
                        'subject' => trim($row['task_subject']),
                        'description' => $notes ?: 'Follow up with prospective client regarding bioceutical protocols.',
                        'lead_id' => $lead->id,
                        'assigned_to' => $rowOwnerId,
                        'created_by' => auth()->id() ?? $rowOwnerId,
                        'due_at' => !empty($row['task_due']) ? Carbon::parse($row['task_due']) : Carbon::now()->addDays(2)->setHour(14),
                        'status' => 'pending',
                        'priority' => $priority,
                    ]);
                    $createdTasksCount++;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'created_leads' => $createdLeadsCount,
                'created_opportunities' => $createdOppsCount,
                'created_tasks' => $createdTasksCount,
                'message' => "Successfully imported and saved {$createdLeadsCount} records into CRM with {$createdOppsCount} deals and {$createdTasksCount} tasks!",
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('CRM Manual Grid Save Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 422);
        }
    }

    /**
     * Download manual grid data directly as a formatted .xlsx file.
     */
    public function exportManualGrid(Request $request)
    {
        $rows = json_decode($request->input('grid_data', '[]'), true) ?: [];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Manual CRM Grid');

        $headers = [
            'A1' => 'Client Name',
            'B1' => 'Company / Clinic',
            'C1' => 'Phone',
            'D1' => 'Email',
            'E1' => 'City',
            'F1' => 'Deal Value (SAR)',
            'G1' => 'Status',
            'H1' => 'Priority',
            'I1' => 'Next Action / Task',
            'J1' => 'Task Due',
            'K1' => 'Notes',
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        $sheet->getStyle('A1:K1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0A4F78']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(28);

        $rowIdx = 2;
        foreach ($rows as $row) {
            $sheet->setCellValue('A' . $rowIdx, $row['name'] ?? '');
            $sheet->setCellValue('B' . $rowIdx, $row['company'] ?? '');
            $sheet->setCellValue('C' . $rowIdx, $row['phone'] ?? '');
            $sheet->setCellValue('D' . $rowIdx, $row['email'] ?? '');
            $sheet->setCellValue('E' . $rowIdx, $row['city'] ?? '');
            $sheet->setCellValue('F' . $rowIdx, is_numeric($row['value'] ?? null) ? (float) $row['value'] : 0);
            $sheet->setCellValue('G' . $rowIdx, ucfirst($row['status'] ?? 'new'));
            $sheet->setCellValue('H' . $rowIdx, ucfirst($row['priority'] ?? 'medium'));
            $sheet->setCellValue('I' . $rowIdx, $row['task_subject'] ?? '');
            $sheet->setCellValue('J' . $rowIdx, $row['task_due'] ?? '');
            $sheet->setCellValue('K' . $rowIdx, $row['notes'] ?? '');
            $rowIdx++;
        }

        foreach (range(1, 11) as $c) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($c))->setAutoSize(true);
        }

        $fileName = 'blue_zone_manual_crm_grid_' . date('Y-m-d_His') . '.xlsx';
        $tempPath = storage_path('app/' . $fileName);

        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        return response()->download($tempPath, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Generate styled Excel export (.xlsx).
     */
    protected function generateStyledXlsxExport(string $type, $leadsQuery, $oppsQuery, string $fileName)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(substr("CRM " . ucfirst(str_replace('_', ' ', $type)), 0, 31));

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11, 'name' => 'Calibri'],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '031827']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '133957']]],
        ];

        $rowIdx = 2;

        if ($type === 'leads' || $type === 'all_sales') {
            $headers = ['A1' => 'Lead #', 'B1' => 'Client Name', 'C1' => 'Company', 'D1' => 'Phone', 'E1' => 'Email', 'F1' => 'Country', 'G1' => 'City', 'H1' => 'Status', 'I1' => 'Priority', 'J1' => 'Est. Value (SAR)', 'K1' => 'Source', 'L1' => 'Owner / Rep', 'M1' => 'Created Date'];
            foreach ($headers as $cell => $txt) {
                $sheet->setCellValue($cell, $txt);
            }
            $sheet->getStyle('A1:M1')->applyFromArray($headerStyle);
            $sheet->getRowDimension(1)->setRowHeight(30);

            $leads = $leadsQuery->get();
            foreach ($leads as $l) {
                $sheet->setCellValue('A' . $rowIdx, $l->lead_number);
                $sheet->setCellValue('B' . $rowIdx, $l->full_name);
                $sheet->setCellValue('C' . $rowIdx, $l->company_name ?? '—');
                $sheet->setCellValue('D' . $rowIdx, $l->phone ?? '—');
                $sheet->setCellValue('E' . $rowIdx, $l->email ?? '—');
                $sheet->setCellValue('F' . $rowIdx, $l->country?->name_en ?? '—');
                $sheet->setCellValue('G' . $rowIdx, $l->city?->name_en ?? '—');
                $sheet->setCellValue('H' . $rowIdx, ucfirst($l->status));
                $sheet->setCellValue('I' . $rowIdx, ucfirst($l->priority));
                $sheet->setCellValue('J' . $rowIdx, (float) ($l->estimated_value ?? 0));
                $sheet->setCellValue('K' . $rowIdx, $l->source?->name_en ?? 'Direct');
                $sheet->setCellValue('L' . $rowIdx, $l->owner?->name ?? 'Unassigned');
                $sheet->setCellValue('M' . $rowIdx, $l->created_at?->format('Y-m-d H:i'));
                $rowIdx++;
            }

            // Total row
            $sheet->setCellValue('I' . $rowIdx, 'TOTAL PIPELINE:');
            $sheet->setCellValue('J' . $rowIdx, "=SUM(J2:J" . ($rowIdx - 1) . ")");
            $sheet->getStyle('I' . $rowIdx . ':J' . $rowIdx)->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => '0A4F78']],
            ]);

            foreach (range(1, 13) as $c) {
                $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($c))->setAutoSize(true);
            }
        } else {
            // Opportunities
            $headers = ['A1' => 'Deal #', 'B1' => 'Opportunity Name', 'C1' => 'Associated Lead / Client', 'D1' => 'Pipeline', 'E1' => 'Stage', 'F1' => 'Status', 'G1' => 'Deal Value (SAR)', 'H1' => 'Probability (%)', 'I1' => 'Expected Close', 'J1' => 'Owner / Rep', 'K1' => 'Created Date'];
            foreach ($headers as $cell => $txt) {
                $sheet->setCellValue($cell, $txt);
            }
            $sheet->getStyle('A1:K1')->applyFromArray($headerStyle);
            $sheet->getRowDimension(1)->setRowHeight(30);

            $opps = $oppsQuery->get();
            foreach ($opps as $o) {
                $sheet->setCellValue('A' . $rowIdx, $o->opportunity_number);
                $sheet->setCellValue('B' . $rowIdx, $o->name);
                $sheet->setCellValue('C' . $rowIdx, $o->lead?->full_name ?? ($o->customer?->name ?? '—'));
                $sheet->setCellValue('D' . $rowIdx, $o->pipeline?->name ?? 'Default');
                $sheet->setCellValue('E' . $rowIdx, $o->stage?->name_en ?? 'Initial');
                $sheet->setCellValue('F' . $rowIdx, strtoupper($o->status));
                $sheet->setCellValue('G' . $rowIdx, (float) $o->value);
                $sheet->setCellValue('H' . $rowIdx, (int) $o->probability);
                $sheet->setCellValue('I' . $rowIdx, $o->expected_close_date?->format('Y-m-d') ?? '—');
                $sheet->setCellValue('J' . $rowIdx, $o->owner?->name ?? 'Unassigned');
                $sheet->setCellValue('K' . $rowIdx, $o->created_at?->format('Y-m-d H:i'));
                $rowIdx++;
            }

            // Total row
            $sheet->setCellValue('F' . $rowIdx, 'TOTAL VALUE:');
            $sheet->setCellValue('G' . $rowIdx, "=SUM(G2:G" . ($rowIdx - 1) . ")");
            $sheet->getStyle('F' . $rowIdx . ':G' . $rowIdx)->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => '0A4F78']],
            ]);

            foreach (range(1, 11) as $c) {
                $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($c))->setAutoSize(true);
            }
        }

        $tempPath = storage_path('app/' . $fileName);
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        return response()->download($tempPath, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Stream CSV Export with UTF-8 BOM.
     */
    protected function streamCsvExport(string $type, $leadsQuery, $oppsQuery, string $fileName): StreamedResponse
    {
        return response()->streamDownload(function () use ($type, $leadsQuery, $oppsQuery) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF"); // UTF-8 BOM for Excel Arabic support

            if ($type === 'leads' || $type === 'all_sales') {
                fputcsv($handle, [
                    'Lead Number', 'Client Name', 'Company', 'Phone', 'Email',
                    'Country', 'City', 'Status', 'Priority', 'Estimated Value (SAR)',
                    'Source', 'Owner', 'Created At'
                ]);

                $leadsQuery->chunk(200, function ($leads) use ($handle) {
                    foreach ($leads as $l) {
                        fputcsv($handle, [
                            $l->lead_number,
                            $l->full_name,
                            $l->company_name ?? '',
                            $l->phone ?? '',
                            $l->email ?? '',
                            $l->country?->name_en ?? '',
                            $l->city?->name_en ?? '',
                            ucfirst($l->status),
                            ucfirst($l->priority),
                            number_format($l->estimated_value ?? 0, 2),
                            $l->source?->name_en ?? '',
                            $l->owner?->name ?? 'Unassigned',
                            $l->created_at?->format('Y-m-d H:i') ?? '',
                        ]);
                    }
                });
            } else {
                fputcsv($handle, [
                    'Opportunity Number', 'Name', 'Client', 'Pipeline', 'Stage',
                    'Status', 'Value (SAR)', 'Probability (%)', 'Expected Close', 'Owner', 'Created At'
                ]);

                $oppsQuery->chunk(200, function ($opps) use ($handle) {
                    foreach ($opps as $o) {
                        fputcsv($handle, [
                            $o->opportunity_number,
                            $o->name,
                            $o->lead?->full_name ?? ($o->customer?->name ?? ''),
                            $o->pipeline?->name ?? '',
                            $o->stage?->name_en ?? '',
                            strtoupper($o->status),
                            number_format($o->value ?? 0, 2),
                            $o->probability,
                            $o->expected_close_date?->format('Y-m-d') ?? '',
                            $o->owner?->name ?? 'Unassigned',
                            $o->created_at?->format('Y-m-d H:i') ?? '',
                        ]);
                    }
                });
            }

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }
}

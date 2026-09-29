<?php

namespace App\Http\Controllers\Admin\Mr;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\AreaBreak;
use App\Models\City;
use App\Models\Country;
use App\Models\Mr\Contact;
use App\Models\Mr\ContactAssignment;
use App\Models\Mr\ContactClassification;
use App\Models\Mr\ContactSpecialty;
use App\Models\Mr\ScheduledVisit;
use App\Models\Mr\Visit;
use App\Models\Mr\VisitCycle;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use OpenSpout\Reader\CSV\Reader as CsvReader;
use OpenSpout\Reader\XLSX\Reader as XlsxReader;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MrExcelController extends Controller
{
    /**
     * Display the Medical Representative CRM Excel Hub & Data Center.
     */
    public function index(Request $request)
    {
        $currentUser = auth()->user();
        $isManager = $currentUser ? $currentUser->canManageAllMr() : false;

        $specialties = ContactSpecialty::where('is_active', true)->orderBy('name')->get();
        $classifications = ContactClassification::where('is_active', true)->orderBy('sort_order')->get();
        $countries = Country::where('is_active', true)->orderBy('name_en')->get();
        $cities = City::where('is_active', true)->orderBy('name_en')->get();
        $areas = Area::where('is_active', true)->orderBy('name_en')->get();
        $breaks = AreaBreak::where('is_active', true)->orderBy('name_en')->get();

        $medicalReps = User::where(function ($q) {
            $q->whereHas('role', fn ($r) => $r->whereIn('name', ['mr', 'medical_rep', 'medical_representative']))
              ->orWhere('role_id', 1)
              ->orWhere('role_id', 2);
        })->where('status', 'active')->select('id', 'name', 'email', 'phone')->orderBy('name')->get();

        if ($medicalReps->isEmpty()) {
            $medicalReps = User::where('status', 'active')->select('id', 'name', 'email', 'phone')->orderBy('name')->limit(20)->get();
        }

        $cycles = VisitCycle::latest('start_date')->get();
        $activeCycle = $cycles->firstWhere('status', 'active') ?? $cycles->first();

        // Calculate KPI Metrics for Header
        $totalContacts = Contact::count();
        $activeContacts = Contact::where('is_active', true)->count();
        $totalVisitsMonth = Visit::whereMonth('checkin_at', now()->month)
            ->whereYear('checkin_at', now()->year)
            ->count();
        $totalScheduledMonth = ScheduledVisit::whereMonth('scheduled_at', now()->month)
            ->whereYear('scheduled_at', now()->year)
            ->count();
        
        $assignedCount = ContactAssignment::when($activeCycle, fn ($q) => $q->where('cycle_id', $activeCycle->id))
            ->distinct('contact_id')
            ->count('contact_id');
        $visitedCount = Visit::when($activeCycle, fn ($q) => $q->where('cycle_id', $activeCycle->id))
            ->distinct('contact_id')
            ->count('contact_id');
        $coverageRate = $assignedCount > 0 ? round(($visitedCount / $assignedCount) * 100, 1) : 0;

        $stats = [
            'total_doctors' => $totalContacts,
            'active_doctors' => $activeContacts,
            'total_visits_month' => $totalVisitsMonth,
            'total_scheduled_month' => $totalScheduledMonth,
            'active_reps_count' => $medicalReps->count(),
            'coverage_rate' => $coverageRate,
        ];

        // Load 15 recent contacts for live in-browser spreadsheet grid
        $sampleDoctors = Contact::with(['specialty', 'classification', 'city', 'area', 'break', 'assignments.representative'])
            ->latest('id')
            ->limit(20)
            ->get();

        return view('admin.mr.excel.index', compact(
            'specialties',
            'classifications',
            'countries',
            'cities',
            'areas',
            'breaks',
            'medicalReps',
            'cycles',
            'activeCycle',
            'stats',
            'sampleDoctors',
            'isManager'
        ));
    }

    /**
     * Download styled Excel (.xlsx) or CSV template for Doctors or Visits.
     */
    public function downloadTemplate(Request $request)
    {
        $type = $request->query('type', 'doctors'); // 'doctors' or 'visits'
        $format = strtolower($request->query('format', 'xlsx'));

        if ($type === 'visits') {
            return $this->downloadVisitsTemplate($format);
        }

        return $this->downloadDoctorsTemplate($format);
    }

    /**
     * Generate and stream Doctors & Clinics Directory Template.
     */
    protected function downloadDoctorsTemplate(string $format)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Doctors Import Template');

        $headers = [
            'A1' => 'Doctor / HCP Code',
            'B1' => 'Doctor Full Name (English) *',
            'C1' => 'Doctor Name (Arabic)',
            'D1' => 'Medical Title (Dr/Prof/Consultant)',
            'E1' => 'Medical Specialty *',
            'F1' => 'Classification (A+/A/B/C) *',
            'G1' => 'Hospital / Clinic / Center *',
            'H1' => 'Country',
            'I1' => 'City / Governorate *',
            'J1' => 'Territory / Area',
            'K1' => 'Sector / Break',
            'L1' => 'Phone / WhatsApp *',
            'M1' => 'Email Address',
            'N1' => 'Full Clinic Address',
            'O1' => 'Assigned Medical Rep Email',
            'P1' => 'Target Visits per Month',
            'Q1' => 'Best Visiting Hours',
            'R1' => 'Strategic Notes / Focus Products',
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        // Header Styling (Blue Zone Executive Deep Navy)
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
        $sheet->getStyle('A1:R1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(34);
        $sheet->freezePane('A2');

        // Realistic Pharmaceutical Healthcare Samples
        $samples = [
            [
                'DOC-EG-001', 'Dr. Ahmed El-Sherif', 'د. أحمد الشريف', 'Consultant',
                'Cardiology', 'A+', 'Cairo Heart Center & Cleopatra Hospital', 'Egypt', 'Cairo',
                'Nasr City Territory', 'Abbas El-Akkad St', '+201001122331', 'ahmed.elsherif@cairo-heart.com',
                '12 Abbas El-Akkad St, 4th Floor, Nasr City, Cairo', 'admin@bluezone.com', 4,
                '10:00 - 13:00 (Sun, Tue, Thu)', 'Key Opinion Leader for CoQ10 & Cellular Antioxidants.',
            ],
            [
                'DOC-EG-002', 'Dr. Sarah Mansour', 'د. سارة منصور', 'Senior Specialist',
                'Pediatrics & Neonatology', 'A+', 'Cleopatra Hospitals Pediatric Unit', 'Egypt', 'Cairo',
                'Heliopolis & Sheraton Territory', 'El-Korba Heritage District', '+201002233442', 'sarah.mansour@cleopatra-hospitals.com',
                'Cleopatra Medical Tower, Suite 204, Heliopolis, Cairo', 'admin@bluezone.com', 4,
                '17:00 - 20:30 (Mon, Wed)', 'High prescriber in juvenile metabolic wellness and micronutrients.',
            ],
            [
                'DOC-EG-003', 'Dr. Tarek El-Kady', 'د. طارق القاضي', 'Prof. & Consultant',
                'Orthopedic Surgery & Trauma', 'A', 'As-Salam International Hospital, Ortho Wing', 'Egypt', 'Cairo',
                'Maadi Territory', 'Corniche El-Maadi Hospitals Zone', '+201003344553', 'tarek.elkady@assalam-hospital.com',
                'Corniche El-Nile, Maadi Health Center, Cairo', 'admin@bluezone.com', 2,
                '09:30 - 12:30 (Daily)', 'Prescribes joint collagen peptides and cellular anti-inflammatory regimens.',
            ],
            [
                'DOC-EG-004', 'Dr. Laila Hosny', 'د. ليلى حسني', 'Consultant',
                'Dermatology & Cosmetology', 'A', 'Laran Elite Derma', 'Egypt', 'Alexandria',
                'Loran & Ramleh Territory', 'Loran Medical Center', '+201004455664', 'laila.hosny@elitederma-eg.com',
                '45 El-Iqbal St, Loran, Alexandria', 'admin@bluezone.com', 2,
                '16:00 - 19:00 (Sun, Wed)', 'Interested in NAD+ boosters, cellular hydration, and skin rejuvenation.',
            ],
            [
                'DOC-SA-005', 'Dr. Tariq Al-Ghamdi', 'د. طارق الغامدي', 'Consultant',
                'Cardiology', 'A+', 'Al-Amal Specialized Heart Hospital', 'Saudi Arabia', 'Riyadh',
                'North Riyadh Territory', 'Al-Malqa Sector', '+966501234567', 'tariq.cardio@alamal.sa',
                'King Fahd Rd, Building 4, 3rd Floor, Riyadh', 'admin@bluezone.com', 4,
                '10:00 - 13:00 (Sun, Tue, Thu)', 'High volume prescriber for CoQ10 & Cellular Antioxidants.',
            ],
            [
                'DOC-EG-006', 'Dr. Mohamed Farouk', 'د. محمد فاروق', 'Consultant',
                'Internal Medicine', 'B', 'Dar Al Fouad Hospital Clinics', 'Egypt', 'Giza',
                '6th of October City', 'Central Spine Medical Zone', '+201005566778', 'm.farouk@daralfouad.eg',
                '26th of July Corridor, 6th of October City, Giza', 'admin@bluezone.com', 2,
                '18:00 - 21:00 (Sat, Tue)', 'Metabolic syndrome & diabetes management protocols.',
            ],
            [
                'DOC-SA-007', 'Dr. Reem Al-Otaibi', 'د. ريم العتيبي', 'Specialist',
                'Obstetrics & Gynecology', 'C', 'Al-Hayat Maternity & Wellness Center', 'Saudi Arabia', 'Jeddah',
                'Rawdah District', 'Prince Sultan Medical Complex', '+966559876543', 'dr.reem@hayatclinics.com',
                'Prince Sultan St, Clinic 12, Jeddah', 'admin@bluezone.com', 1,
                '11:00 - 14:00 (Daily)', 'Prenatal multivitamins and maternal cellular nutrition.',
            ],
        ];

        $rowIdx = 2;
        foreach ($samples as $row) {
            $colIdx = 1;
            foreach ($row as $val) {
                $cellCoord = Coordinate::stringFromColumnIndex($colIdx) . $rowIdx;
                $sheet->setCellValueExplicit(
                    $cellCoord,
                    $val,
                    is_numeric($val) ? DataType::TYPE_NUMERIC : DataType::TYPE_STRING
                );
                $colIdx++;
            }
            $sheet->getRowDimension($rowIdx)->setRowHeight(24);
            $rowIdx++;
        }

        // Data Row Styling with Subtle Alternating Striping
        for ($r = 2; $r < $rowIdx; $r++) {
            $bgColor = ($r % 2 === 0) ? 'FFFFFF' : 'F8FAFC';
            $sheet->getStyle('A' . $r . ':R' . $r)->applyFromArray([
                'font' => ['size' => 10, 'name' => 'Calibri'],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $bgColor],
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'E2E8F0'],
                    ],
                ],
            ]);
        }

        // Add Excel Data Validations for XLSX
        if ($format === 'xlsx') {
            // 1. Classification Dropdown (Column F)
            $classValidation = $sheet->getCell('F2')->getDataValidation();
            $classValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $classValidation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
            $classValidation->setAllowBlank(false);
            $classValidation->setShowInputMessage(true);
            $classValidation->setShowErrorMessage(true);
            $classValidation->setShowDropDown(true);
            $classValidation->setErrorTitle('Invalid Classification');
            $classValidation->setError('Please choose a valid class: A+, A, B, or C');
            $classValidation->setPromptTitle('Doctor Classification');
            $classValidation->setPrompt('Select Class (A+, A, B, C)');
            $classValidation->setFormula1('"A+,A,B,C"');

            // 2. Target Visits Quota Validation (Column P)
            $quotaValidation = $sheet->getCell('P2')->getDataValidation();
            $quotaValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_WHOLE);
            $quotaValidation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
            $quotaValidation->setOperator(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::OPERATOR_BETWEEN);
            $quotaValidation->setFormula1(1);
            $quotaValidation->setFormula2(20);
            $quotaValidation->setErrorTitle('Invalid Quota');
            $quotaValidation->setError('Monthly visits quota must be a number between 1 and 20.');
            $quotaValidation->setShowInputMessage(true);
            $quotaValidation->setPrompt('Monthly visits quota (1 - 20)');

            for ($r = 2; $r <= 120; $r++) {
                $sheet->getCell('F' . $r)->setDataValidation(clone $classValidation);
                $sheet->getCell('P' . $r)->setDataValidation(clone $quotaValidation);
            }

            // Create Sheet 2: Reference Guide & Data Lists
            $guideSheet = $spreadsheet->createSheet();
            $guideSheet->setTitle('Reference Guide & Data');

            // Banner
            $guideSheet->setCellValue('A1', 'Blue Zone MR CRM — Doctors Import Reference Guide & Data Dictionaries');
            $guideSheet->mergeCells('A1:E1');
            $guideSheet->getStyle('A1')->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 13, 'name' => 'Calibri'],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0A4F78']],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);
            $guideSheet->getRowDimension(1)->setRowHeight(32);

            // Instructions Box
            $guideSheet->setCellValue('A3', 'IMPORT RULES & PROTOCOL');
            $guideSheet->getStyle('A3')->getFont()->setBold(true)->setSize(11)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('0A4F78'));
            
            $rules = [
                '1. Mandatory Columns: Doctor Full Name, Medical Specialty, Classification, Hospital/Clinic, City, and Phone/WhatsApp are strictly required.',
                '2. Unique Doctor Identification: The system automatically matches existing doctors by Phone Number, Doctor Code, or Exact Name.',
                '3. Duplicate Handling: When uploading, you can choose to Skip Existing, Update Missing Fields, or Overwrite Record.',
                '4. Medical Rep Assignment: Provide a registered rep email in Column O. If empty, the default rep selected during import will be assigned.',
                '5. Auto-Cycle Enrollment: All imported doctors will automatically receive an agenda assignment in the active visit cycle.',
            ];
            $gRow = 4;
            foreach ($rules as $r) {
                $guideSheet->setCellValue('A' . $gRow, $r);
                $guideSheet->mergeCells('A' . $gRow . ':E' . $gRow);
                $guideSheet->getStyle('A' . $gRow)->getFont()->setSize(9)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('475569'));
                $gRow++;
            }

            // Table: System Classifications
            $gRow += 2;
            $guideSheet->setCellValue('A' . $gRow, 'SYSTEM CLASSIFICATIONS (Choose one in Column F)');
            $guideSheet->getStyle('A' . $gRow)->getFont()->setBold(true)->setSize(10)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('0A4F78'));
            $gRow++;

            $guideSheet->setCellValue('A' . $gRow, 'Class Code');
            $guideSheet->setCellValue('B' . $gRow, 'Required Monthly Visits');
            $guideSheet->setCellValue('C' . $gRow, 'Cycle Points');
            $guideSheet->setCellValue('D' . $gRow, 'Strategic Priority Description');
            $guideSheet->getStyle('A' . $gRow . ':D' . $gRow)->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0284C7']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
            $gRow++;

            $classifications = ContactClassification::where('is_active', true)->orderBy('sort_order')->get();
            foreach ($classifications as $cl) {
                $guideSheet->setCellValue('A' . $gRow, $cl->code);
                $guideSheet->setCellValue('B' . $gRow, $cl->required_visits);
                $guideSheet->setCellValue('C' . $gRow, $cl->points);
                $guideSheet->setCellValue('D' . $gRow, $cl->description ?: ($cl->code . ' Tier Physician'));
                $guideSheet->getStyle('A' . $gRow . ':D' . $gRow)->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']]],
                    'font' => ['size' => 9],
                ]);
                $gRow++;
            }

            // Table: Active Medical Specialties
            $gRow += 2;
            $guideSheet->setCellValue('A' . $gRow, 'SAMPLE ACTIVE SPECIALTIES IN SYSTEM');
            $guideSheet->getStyle('A' . $gRow)->getFont()->setBold(true)->setSize(10)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('0A4F78'));
            $gRow++;

            $guideSheet->setCellValue('A' . $gRow, 'Specialty Name');
            $guideSheet->setCellValue('B' . $gRow, 'System Code');
            $guideSheet->getStyle('A' . $gRow . ':B' . $gRow)->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '10B981']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
            $gRow++;

            $specs = ContactSpecialty::where('is_active', true)->limit(15)->get();
            foreach ($specs as $sp) {
                $guideSheet->setCellValue('A' . $gRow, $sp->name);
                $guideSheet->setCellValue('B' . $gRow, $sp->code ?: 'SPEC');
                $guideSheet->getStyle('A' . $gRow . ':B' . $gRow)->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']]],
                    'font' => ['size' => 9],
                ]);
                $gRow++;
            }

            // Auto-fit Sheet 2 columns
            foreach (range(1, 5) as $col) {
                $guideSheet->getColumnDimension(Coordinate::stringFromColumnIndex($col))->setAutoSize(true);
            }

            // Return to Sheet 1 as the active view
            $spreadsheet->setActiveSheetIndex(0);
        }

        // Auto-fit column widths on Sheet 1
        foreach (range(1, 18) as $col) {
            $colLetter = Coordinate::stringFromColumnIndex($col);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        $fileName = 'blue_zone_mr_doctors_template_' . date('Y-m-d') . '.' . $format;
        $tempPath = storage_path('app/' . $fileName);

        if ($format === 'csv') {
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Csv($spreadsheet);
            $writer->setUseBOM(true);
            $writer->save($tempPath);
            return response()->download($tempPath, $fileName, ['Content-Type' => 'text/csv; charset=UTF-8'])->deleteFileAfterSend(true);
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        return response()->download($tempPath, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Generate and stream Field Visits & Activity Log Template.
     */
    protected function downloadVisitsTemplate(string $format)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Field Visits Log');

        $headers = [
            'A1' => 'Doctor Code / Name *',
            'B1' => 'Medical Rep Email *',
            'C1' => 'Visit Date & Time (YYYY-MM-DD HH:MM) *',
            'D1' => 'Visit Outcome (completed/rescheduled/doctor_busy/cancelled) *',
            'E1' => 'Visit Duration (Minutes)',
            'F1' => 'Promoted Focus Products',
            'G1' => 'Samples Given / Units',
            'H1' => 'Doctor Feedback & Call Notes',
            'I1' => 'GPS Verified (yes/no)',
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11, 'name' => 'Calibri'],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0284C7']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '0A4F78']]],
        ];
        $sheet->getStyle('A1:I1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(32);

        $samples = [
            ['Dr. Tariq Al-Ghamdi', 'admin@bluezone.com', Carbon::now()->subDays(1)->format('Y-m-d 10:30'), 'completed', 25, 'Cellular CoQ10 Max, NAD+ Booster', '3 Starter Sample Boxes', 'Doctor expressed strong interest in clinical trial results. Scheduled follow-up visit.', 'yes'],
            ['Dr. Laila Al-Husseini', 'admin@bluezone.com', Carbon::now()->subDays(2)->format('Y-m-d 17:15'), 'completed', 20, 'Pediatric Vitality Drops', '5 Trial Kits', 'Accepted samples for clinical trial with 5 children. Requested promotional brochures.', 'yes'],
            ['Dr. Khaled Mansour', 'admin@bluezone.com', Carbon::now()->subDays(3)->format('Y-m-d 11:00'), 'doctor_busy', 5, 'Joint Collagen Repair', 'None', 'Doctor was in emergency surgery. Rescheduled for next Tuesday morning.', 'no'],
        ];

        $rowIdx = 2;
        foreach ($samples as $row) {
            $colIdx = 1;
            foreach ($row as $val) {
                $sheet->setCellValueExplicit(Coordinate::stringFromColumnIndex($colIdx) . $rowIdx, $val, is_numeric($val) ? DataType::TYPE_NUMERIC : DataType::TYPE_STRING);
                $colIdx++;
            }
            $rowIdx++;
        }

        foreach (range(1, 9) as $col) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($col))->setAutoSize(true);
        }

        $fileName = 'blue_zone_mr_visits_template_' . date('Y-m-d') . '.' . $format;
        $tempPath = storage_path('app/' . $fileName);

        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        return response()->download($tempPath, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Inspect and preview uploaded Excel/CSV file before database insertion.
     */
    public function preview(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv,txt|max:15360',
        ]);

        $file = $request->file('file');
        $ext = strtolower($file->getClientOriginalExtension());
        $filePath = $file->getRealPath();

        $rows = [];
        $headers = [];

        try {
            if ($ext === 'csv' || $ext === 'txt') {
                $reader = new CsvReader();
                $reader->open($filePath);
            } else {
                $reader = new XlsxReader();
                $reader->open($filePath);
            }

            $rowCount = 0;
            foreach ($reader->getSheetIterator() as $sheet) {
                foreach ($sheet->getRowIterator() as $row) {
                    $rowCells = $row->toArray();
                    if ($rowCount === 0) {
                        $headers = array_map(fn ($h) => trim((string)$h), $rowCells);
                    } else {
                        if (count(array_filter($rowCells)) > 0) {
                            $rows[] = $rowCells;
                        }
                    }
                    $rowCount++;
                    if ($rowCount > 500) {
                        break;
                    }
                }
                break;
            }
            $reader->close();
        } catch (\Exception $e) {
            Log::error('MR Excel Preview Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to read spreadsheet file: ' . $e->getMessage(),
            ], 422);
        }

        // Detect expected columns
        $expectedMap = [
            'name' => ['name', 'doctor name', 'doctor full name', 'full name', 'اسم الطبيب', 'الطبيب'],
            'specialty' => ['specialty', 'medical specialty', 'speciality', 'التخصص', 'التخصص الطبي'],
            'classification' => ['classification', 'class', 'doctor class', 'التصنيف', 'الفئة'],
            'clinic' => ['clinic', 'hospital', 'center', 'workplace', 'hospital / clinic', 'المركز', 'العيادة', 'المستشفى'],
            'phone' => ['phone', 'mobile', 'whatsapp', 'tel', 'الهاتف', 'الجوال', 'رقم الهاتف'],
            'city' => ['city', 'governorate', 'المدينة', 'المحافظة'],
            'area' => ['area', 'territory', 'المنطقة', 'المربع'],
            'break' => ['break', 'sector', 'البريك', 'القطاع'],
            'rep' => ['rep', 'representative', 'mr', 'المندوب', 'المندوب الطبي'],
        ];

        $matchedMapping = [];
        foreach ($headers as $idx => $h) {
            $hLower = strtolower(trim($h));
            foreach ($expectedMap as $key => $synonyms) {
                foreach ($synonyms as $syn) {
                    if (str_contains($hLower, $syn)) {
                        $matchedMapping[$key] = $idx;
                        break 2;
                    }
                }
            }
        }

        // Check duplicate count in database
        $phoneColIdx = $matchedMapping['phone'] ?? null;
        $potentialDupes = 0;
        if ($phoneColIdx !== null) {
            $previewPhones = array_filter(array_column(array_slice($rows, 0, 100), $phoneColIdx));
            if (!empty($previewPhones)) {
                $potentialDupes = Contact::whereIn('phone', $previewPhones)->count();
            }
        }

        return response()->json([
            'success' => true,
            'total_rows' => count($rows),
            'headers' => $headers,
            'preview_rows' => array_slice($rows, 0, 8),
            'matched_mapping' => $matchedMapping,
            'potential_duplicates' => $potentialDupes,
        ]);
    }

    /**
     * Batch import doctors or visit logs into the database.
     */
    public function import(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv,txt|max:20480',
            'import_type' => 'required|string|in:doctors,visits',
            'duplicate_action' => 'required|string|in:skip,update,overwrite',
            'default_rep_id' => 'nullable|exists:users,id',
            'auto_assign_cycle' => 'nullable|boolean',
            'target_visits_default' => 'nullable|integer|min:1|max:20',
        ]);

        $file = $request->file('file');
        $ext = strtolower($file->getClientOriginalExtension());
        $filePath = $file->getRealPath();
        $importType = $request->input('import_type');
        $duplicateAction = $request->input('duplicate_action');
        $defaultRepId = $request->input('default_rep_id');
        $autoAssignCycle = $request->boolean('auto_assign_cycle', true);
        $defaultTargetVisits = $request->integer('target_visits_default', 2);

        $activeCycle = VisitCycle::where('status', 'active')->first() ?? VisitCycle::latest('start_date')->first();

        $imported = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];

        try {
            if ($ext === 'csv' || $ext === 'txt') {
                $reader = new CsvReader();
                $reader->open($filePath);
            } else {
                $reader = new XlsxReader();
                $reader->open($filePath);
            }

            $headers = [];
            $headerMap = [];
            $rowNum = 0;

            DB::beginTransaction();

            foreach ($reader->getSheetIterator() as $sheet) {
                foreach ($sheet->getRowIterator() as $row) {
                    $cells = $row->toArray();
                    $rowNum++;

                    if ($rowNum === 1) {
                        $headers = array_map(fn ($h) => strtolower(trim((string)$h)), $cells);
                        // Map headers to indexes
                        foreach ($headers as $idx => $h) {
                            if (str_contains($h, 'code')) $headerMap['code'] = $idx;
                            elseif (str_contains($h, 'english') || str_contains($h, 'doctor') || str_contains($h, 'name') || str_contains($h, 'طبيب')) {
                                if (!isset($headerMap['name_en'])) $headerMap['name_en'] = $idx;
                            }
                            elseif (str_contains($h, 'arabic') || str_contains($h, 'عربي')) $headerMap['name_ar'] = $idx;
                            elseif (str_contains($h, 'title') || str_contains($h, 'لقب')) $headerMap['title'] = $idx;
                            elseif (str_contains($h, 'specialty') || str_contains($h, 'تخصص')) $headerMap['specialty'] = $idx;
                            elseif (str_contains($h, 'class') || str_contains($h, 'تصنيف')) $headerMap['class'] = $idx;
                            elseif (str_contains($h, 'clinic') || str_contains($h, 'hospital') || str_contains($h, 'مستشفى') || str_contains($h, 'عيادة')) $headerMap['clinic'] = $idx;
                            elseif (str_contains($h, 'country') || str_contains($h, 'دولة')) $headerMap['country'] = $idx;
                            elseif (str_contains($h, 'city') || str_contains($h, 'مدينة')) $headerMap['city'] = $idx;
                            elseif (str_contains($h, 'area') || str_contains($h, 'منطقة')) $headerMap['area'] = $idx;
                            elseif (str_contains($h, 'break') || str_contains($h, 'قطاع') || str_contains($h, 'بريك')) $headerMap['break'] = $idx;
                            elseif (str_contains($h, 'phone') || str_contains($h, 'mobile') || str_contains($h, 'هاتف')) $headerMap['phone'] = $idx;
                            elseif (str_contains($h, 'email') || str_contains($h, 'بريد')) $headerMap['email'] = $idx;
                            elseif (str_contains($h, 'address') || str_contains($h, 'عنوان')) $headerMap['address'] = $idx;
                            elseif (str_contains($h, 'rep') || str_contains($h, 'مندوب')) $headerMap['rep'] = $idx;
                            elseif (str_contains($h, 'target') || str_contains($h, 'quota')) $headerMap['target_visits'] = $idx;
                            elseif (str_contains($h, 'notes') || str_contains($h, 'ملاحظات')) $headerMap['notes'] = $idx;
                        }
                        continue;
                    }

                    if (empty(array_filter($cells))) {
                        continue;
                    }

                    // Extract row values
                    $nameEn = trim((string)($cells[$headerMap['name_en'] ?? 1] ?? ''));
                    $phone = trim((string)($cells[$headerMap['phone'] ?? 11] ?? ''));

                    if (empty($nameEn) && empty($phone)) {
                        $skipped++;
                        continue;
                    }

                    $code = trim((string)($cells[$headerMap['code'] ?? 0] ?? ''));
                    $clinic = trim((string)($cells[$headerMap['clinic'] ?? 6] ?? ''));
                    $specialtyRaw = trim((string)($cells[$headerMap['specialty'] ?? 4] ?? ''));
                    $classRaw = trim((string)($cells[$headerMap['class'] ?? 5] ?? 'B'));
                    $cityName = trim((string)($cells[$headerMap['city'] ?? 8] ?? ''));
                    $areaName = trim((string)($cells[$headerMap['area'] ?? 9] ?? ''));
                    $breakName = trim((string)($cells[$headerMap['break'] ?? 10] ?? ''));
                    $repEmail = trim((string)($cells[$headerMap['rep'] ?? 14] ?? ''));
                    $targetVisits = (int)($cells[$headerMap['target_visits'] ?? 15] ?? $defaultTargetVisits) ?: $defaultTargetVisits;
                    $address = trim((string)($cells[$headerMap['address'] ?? 13] ?? ''));
                    $notes = trim((string)($cells[$headerMap['notes'] ?? 17] ?? ''));

                    // Resolve Specialty
                    $specialty = null;
                    if (!empty($specialtyRaw)) {
                        $specialty = ContactSpecialty::where('name', 'like', "%{$specialtyRaw}%")
                            ->orWhere('code', 'like', "%{$specialtyRaw}%")
                            ->first();
                        if (!$specialty) {
                            $specialty = ContactSpecialty::create([
                                'name' => $specialtyRaw,
                                'code' => strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $specialtyRaw) ?: 'SPEC', 0, 6)),
                                'is_active' => true,
                            ]);
                        }
                    }

                    // Resolve Classification
                    $classification = null;
                    if (!empty($classRaw)) {
                        $cleanClass = strtoupper(trim(str_replace(['class', 'Class', 'فئة'], '', $classRaw)));
                        $classification = ContactClassification::where('code', $cleanClass)->first();
                    }
                    if (!$classification) {
                        $classification = ContactClassification::first();
                    }

                    // Resolve Location: City, Area, Break
                    $city = null;
                    if (!empty($cityName)) {
                        $city = City::where('name_en', 'like', "%{$cityName}%")
                            ->orWhere('name_ar', 'like', "%{$cityName}%")
                            ->first();
                    }

                    $area = null;
                    if (!empty($areaName)) {
                        $area = Area::where('name_en', 'like', "%{$areaName}%")
                            ->orWhere('name_ar', 'like', "%{$areaName}%")
                            ->when($city, fn ($q) => $q->where('city_id', $city->id))
                            ->first();
                    }

                    $break = null;
                    if (!empty($breakName)) {
                        $break = AreaBreak::where('name_en', 'like', "%{$breakName}%")
                            ->orWhere('name_ar', 'like', "%{$breakName}%")
                            ->when($area, fn ($q) => $q->where('area_id', $area->id))
                            ->first();
                    }

                    // Check for existing contact
                    $existing = null;
                    if (!empty($phone)) {
                        $existing = Contact::where('phone', $phone)->first();
                    }
                    if (!$existing && !empty($code)) {
                        $existing = Contact::where('code', $code)->first();
                    }
                    if (!$existing && !empty($nameEn)) {
                        $existing = Contact::where('name', $nameEn)->first();
                    }

                    if ($existing) {
                        if ($duplicateAction === 'skip') {
                            $skipped++;
                            continue;
                        }

                        // Update existing doctor
                        $existing->update([
                            'code' => $code ?: $existing->code,
                            'name' => $nameEn ?: $existing->name,
                            'specialty_id' => $specialty ? $specialty->id : $existing->specialty_id,
                            'classification_id' => $classification ? $classification->id : $existing->classification_id,
                            'city_id' => $city ? $city->id : $existing->city_id,
                            'area_id' => $area ? $area->id : $existing->area_id,
                            'break_id' => $break ? $break->id : $existing->break_id,
                            'hospital_clinic_name' => $clinic ?: $existing->hospital_clinic_name,
                            'address' => $address ?: $existing->address,
                            'notes' => $notes ? ($existing->notes . " | " . $notes) : $existing->notes,
                        ]);

                        $contactId = $existing->id;
                        $updated++;
                    } else {
                        // Create new doctor
                        $newCode = $code ?: 'DR-' . strtoupper(substr(uniqid(), -6));
                        $newContact = Contact::create([
                            'code' => $newCode,
                            'name' => $nameEn ?: 'Dr. ' . $phone,
                            'specialty_id' => $specialty?->id,
                            'classification_id' => $classification?->id,
                            'country_id' => $city?->country_id ?? Country::first()?->id,
                            'city_id' => $city?->id,
                            'area_id' => $area?->id,
                            'break_id' => $break?->id,
                            'phone' => $phone,
                            'email' => $cells[$headerMap['email'] ?? 12] ?? null,
                            'hospital_clinic_name' => $clinic ?: 'Private Clinic',
                            'address' => $address,
                            'notes' => $notes,
                            'is_active' => true,
                        ]);

                        $contactId = $newContact->id;
                        $imported++;
                    }

                    // Resolve Representative & Assign
                    $assignedRepId = $defaultRepId;
                    if (!empty($repEmail)) {
                        $foundRep = User::where('email', $repEmail)->orWhere('name', 'like', "%{$repEmail}%")->first();
                        if ($foundRep) {
                            $assignedRepId = $foundRep->id;
                        }
                    }

                    if ($assignedRepId && $autoAssignCycle && $activeCycle) {
                        ContactAssignment::updateOrCreate(
                            [
                                'cycle_id' => $activeCycle->id,
                                'contact_id' => $contactId,
                            ],
                            [
                                'mr_id' => $assignedRepId,
                                'target_visits' => $targetVisits,
                                'target_points' => ($classification?->points ?? 10) * $targetVisits,
                                'is_active' => true,
                            ]
                        );
                    }
                }
                break;
            }

            $reader->close();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Import completed successfully! {$imported} doctors created, {$updated} updated, {$skipped} skipped.",
                'stats' => [
                    'imported' => $imported,
                    'updated' => $updated,
                    'skipped' => $skipped,
                    'errors_count' => count($errors),
                ],
                'errors' => $errors,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('MR Excel Batch Import Failed: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Import failed on row ' . ($rowNum ?? 0) . ': ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Multi-criteria dynamic export of MR CRM field data.
     */
    public function export(Request $request)
    {
        $exportType = $request->query('export_type', 'contacts'); // 'contacts', 'visits', 'coverage', 'territory'
        $format = strtolower($request->query('format', 'xlsx'));

        if ($exportType === 'visits') {
            return $this->exportVisits($request, $format);
        }

        if ($exportType === 'coverage') {
            return $this->exportCoverage($request, $format);
        }

        return $this->exportContacts($request, $format);
    }

    /**
     * Export Doctors & Clinics Directory to Excel / CSV.
     */
    protected function exportContacts(Request $request, string $format)
    {
        $query = Contact::with(['specialty', 'classification', 'country', 'city', 'area', 'break', 'assignments.representative'])
            ->withCount(['visits', 'scheduledVisits']);

        if ($request->filled('mr_id')) {
            $repId = $request->integer('mr_id');
            $query->whereHas('assignments', fn ($q) => $q->where('mr_id', $repId));
        }

        if ($request->filled('specialty_id')) {
            $query->where('specialty_id', $request->integer('specialty_id'));
        }

        if ($request->filled('classification_id')) {
            $query->where('classification_id', $request->integer('classification_id'));
        }

        if ($request->filled('city_id')) {
            $query->where('city_id', $request->integer('city_id'));
        }

        if ($request->filled('area_id')) {
            $query->where('area_id', $request->integer('area_id'));
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $contacts = $query->latest('id')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('MR Doctors Directory');

        // Header Title Block
        $sheet->setCellValue('A1', 'BLUE ZONE MEDICAL CRM — DOCTORS & HEALTHCARE DIRECTORY EXPORT');
        $sheet->mergeCells('A1:P1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF'));
        $sheet->getStyle('A1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('031827');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(1)->setRowHeight(36);

        // Subtitle / Metadata
        $sheet->setCellValue('A2', 'Generated: ' . now()->toDayDateTimeString() . ' | Total Records: ' . $contacts->count() . ' | Exported by: ' . (auth()->user()?->name ?? 'Admin'));
        $sheet->mergeCells('A2:P2');
        $sheet->getStyle('A2')->getFont()->setSize(9)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('94A3B8'));
        $sheet->getStyle('A2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('062B49');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(2)->setRowHeight(20);

        // Column Headers
        $headers = [
            'A3' => '#',
            'B3' => 'Code',
            'C3' => 'Doctor / HCP Name',
            'D3' => 'Medical Specialty',
            'E3' => 'Class',
            'F3' => 'Hospital / Clinic',
            'G3' => 'City',
            'H3' => 'Territory Area',
            'I3' => 'Break / Sector',
            'J3' => 'Phone',
            'K3' => 'Email',
            'L3' => 'Assigned Medical Rep',
            'M3' => 'Total Visits Executed',
            'N3' => 'Upcoming Scheduled',
            'O3' => 'Status',
            'P3' => 'Created At',
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10, 'name' => 'Calibri'],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0A4F78']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '062B49']]],
        ];
        $sheet->getStyle('A3:P3')->applyFromArray($headerStyle);
        $sheet->getRowDimension(3)->setRowHeight(28);

        $rowIdx = 4;
        foreach ($contacts as $index => $c) {
            $assignedRep = $c->assignments->first()?->representative?->name ?? 'Unassigned';

            $sheet->setCellValue('A' . $rowIdx, $index + 1);
            $sheet->setCellValue('B' . $rowIdx, $c->code ?? '—');
            $sheet->setCellValue('C' . $rowIdx, $c->name);
            $sheet->setCellValue('D' . $rowIdx, $c->specialty?->name ?? '—');
            $sheet->setCellValue('E' . $rowIdx, $c->classification?->code ?? '—');
            $sheet->setCellValue('F' . $rowIdx, $c->hospital_clinic_name ?? '—');
            $sheet->setCellValue('G' . $rowIdx, $c->city?->name_en ?? '—');
            $sheet->setCellValue('H' . $rowIdx, $c->area?->name_en ?? '—');
            $sheet->setCellValue('I' . $rowIdx, $c->break?->name_en ?? '—');
            $sheet->setCellValueExplicit('J' . $rowIdx, $c->phone ?? '—', DataType::TYPE_STRING);
            $sheet->setCellValue('K' . $rowIdx, $c->email ?? '—');
            $sheet->setCellValue('L' . $rowIdx, $assignedRep);
            $sheet->setCellValue('M' . $rowIdx, $c->visits_count);
            $sheet->setCellValue('N' . $rowIdx, $c->scheduled_visits_count);
            $sheet->setCellValue('O' . $rowIdx, $c->is_active ? 'Active' : 'Inactive');
            $sheet->setCellValue('P' . $rowIdx, $c->created_at ? $c->created_at->format('Y-m-d') : '—');

            // Zebra striping
            $rowBg = ($rowIdx % 2 === 0) ? 'F8FAFC' : 'FFFFFF';
            $sheet->getStyle("A{$rowIdx}:P{$rowIdx}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($rowBg);
            $sheet->getStyle("A{$rowIdx}:P{$rowIdx}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('E2E8F0');
            $sheet->getRowDimension($rowIdx)->setRowHeight(22);
            $rowIdx++;
        }

        // Summary Row
        $summaryRow = $rowIdx;
        $sheet->setCellValue("A{$summaryRow}", 'TOTAL RECORD COUNT');
        $sheet->mergeCells("A{$summaryRow}:L{$summaryRow}");
        $sheet->setCellValue("M{$summaryRow}", "=SUM(M4:M" . ($summaryRow - 1) . ")");
        $sheet->setCellValue("N{$summaryRow}", "=SUM(N4:N" . ($summaryRow - 1) . ")");
        $sheet->getStyle("A{$summaryRow}:P{$summaryRow}")->getFont()->setBold(true);
        $sheet->getStyle("A{$summaryRow}:P{$summaryRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E2E8F0');
        $sheet->getRowDimension($summaryRow)->setRowHeight(26);

        // Auto-width
        foreach (range(1, 16) as $col) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($col))->setAutoSize(true);
        }

        $fileName = 'blue_zone_mr_doctors_export_' . date('Y-m-d_His') . '.' . $format;
        $tempPath = storage_path('app/' . $fileName);

        if ($format === 'csv') {
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Csv($spreadsheet);
            $writer->setUseBOM(true);
            $writer->save($tempPath);
            return response()->download($tempPath, $fileName, ['Content-Type' => 'text/csv; charset=UTF-8'])->deleteFileAfterSend(true);
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        return response()->download($tempPath, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Export Executed Visits to Excel / CSV.
     */
    protected function exportVisits(Request $request, string $format)
    {
        $query = Visit::with(['contact.specialty', 'contact.city', 'contact.area', 'representative']);

        if ($request->filled('mr_id')) {
            $query->where('mr_id', $request->integer('mr_id'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('checkin_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('checkin_at', '<=', $request->date_to);
        }

        if ($request->filled('outcome')) {
            $query->where('outcome', $request->outcome);
        }

        $visits = $query->latest('checkin_at')->limit(3000)->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Field Visits Log');

        $headers = [
            'A1' => '#',
            'B1' => 'Visit Date & Time',
            'C1' => 'Medical Representative',
            'D1' => 'Doctor / HCP Name',
            'E1' => 'Specialty',
            'F1' => 'Hospital / Clinic',
            'G1' => 'City',
            'H1' => 'Territory Area',
            'I1' => 'Outcome',
            'J1' => 'Duration (Min)',
            'K1' => 'GPS Verified',
            'L1' => 'Call Notes & Feedback',
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        $sheet->getStyle('A1:L1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0284C7']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        $rowIdx = 2;
        foreach ($visits as $idx => $v) {
            $sheet->setCellValue('A' . $rowIdx, $idx + 1);
            $sheet->setCellValue('B' . $rowIdx, $v->checkin_at ? $v->checkin_at->format('Y-m-d H:i') : '—');
            $sheet->setCellValue('C' . $rowIdx, $v->representative?->name ?? '—');
            $sheet->setCellValue('D' . $rowIdx, $v->contact?->name ?? '—');
            $sheet->setCellValue('E' . $rowIdx, $v->contact?->specialty?->name ?? '—');
            $sheet->setCellValue('F' . $rowIdx, $v->contact?->hospital_clinic_name ?? '—');
            $sheet->setCellValue('G' . $rowIdx, $v->contact?->city?->name_en ?? '—');
            $sheet->setCellValue('H' . $rowIdx, $v->contact?->area?->name_en ?? '—');
            $sheet->setCellValue('I' . $rowIdx, ucfirst(str_replace('_', ' ', $v->outcome ?? 'completed')));
            $sheet->setCellValue('J' . $rowIdx, $v->duration_minutes ?? 0);
            $sheet->setCellValue('K' . $rowIdx, $v->gps_verified ? 'Yes (Verified)' : 'No');
            $sheet->setCellValue('L' . $rowIdx, $v->notes ?? '—');

            $rowIdx++;
        }

        foreach (range(1, 12) as $col) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($col))->setAutoSize(true);
        }

        $fileName = 'blue_zone_mr_visits_export_' . date('Y-m-d') . '.' . $format;
        $tempPath = storage_path('app/' . $fileName);

        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        return response()->download($tempPath, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Export Doctor Coverage & Unvisited HCPs Report.
     */
    protected function exportCoverage(Request $request, string $format)
    {
        $activeCycle = VisitCycle::where('status', 'active')->first() ?? VisitCycle::latest('start_date')->first();

        $assignments = ContactAssignment::with(['contact.specialty', 'contact.classification', 'contact.city', 'contact.area', 'representative', 'cycle'])
            ->when($activeCycle, fn ($q) => $q->where('cycle_id', $activeCycle->id))
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rep Coverage Report');

        $headers = [
            'A1' => '#',
            'B1' => 'Medical Representative',
            'C1' => 'Doctor / HCP Name',
            'D1' => 'Classification',
            'E1' => 'Specialty',
            'F1' => 'City',
            'G1' => 'Territory Area',
            'H1' => 'Target Visits (Cycle)',
            'I1' => 'Visits Executed',
            'J1' => 'Visits Deficit / Pending',
            'K1' => 'Coverage Status',
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        $sheet->getStyle('A1:K1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0A4F78']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $rowIdx = 2;
        foreach ($assignments as $idx => $a) {
            $target = $a->target_visits ?: 2;
            $done = $a->visits_done ?: 0;
            $deficit = max(0, $target - $done);
            $status = ($done >= $target) ? 'Fully Covered' : (($done > 0) ? 'Partially Visited' : 'Unvisited (0 Visits)');

            $sheet->setCellValue('A' . $rowIdx, $idx + 1);
            $sheet->setCellValue('B' . $rowIdx, $a->representative?->name ?? '—');
            $sheet->setCellValue('C' . $rowIdx, $a->contact?->name ?? '—');
            $sheet->setCellValue('D' . $rowIdx, $a->contact?->classification?->code ?? '—');
            $sheet->setCellValue('E' . $rowIdx, $a->contact?->specialty?->name ?? '—');
            $sheet->setCellValue('F' . $rowIdx, $a->contact?->city?->name_en ?? '—');
            $sheet->setCellValue('G' . $rowIdx, $a->contact?->area?->name_en ?? '—');
            $sheet->setCellValue('H' . $rowIdx, $target);
            $sheet->setCellValue('I' . $rowIdx, $done);
            $sheet->setCellValue('J' . $rowIdx, $deficit);
            $sheet->setCellValue('K' . $rowIdx, $status);

            $rowIdx++;
        }

        foreach (range(1, 11) as $col) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($col))->setAutoSize(true);
        }

        $fileName = 'blue_zone_mr_coverage_report_' . date('Y-m-d') . '.' . $format;
        $tempPath = storage_path('app/' . $fileName);

        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        return response()->download($tempPath, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Direct batch save of rows typed into the live in-browser manual spreadsheet grid.
     */
    public function saveManualGrid(Request $request): JsonResponse
    {
        $request->validate([
            'rows' => 'required|array|min:1',
            'rows.*.name' => 'required|string|max:190',
            'rows.*.phone' => 'nullable|string|max:50',
            'rows.*.specialty_id' => 'nullable',
            'rows.*.classification_id' => 'nullable',
            'rows.*.city_id' => 'nullable',
            'rows.*.area_id' => 'nullable',
            'rows.*.break_id' => 'nullable',
            'rows.*.clinic' => 'nullable|string|max:190',
            'rows.*.rep_id' => 'nullable',
            'rows.*.target_visits' => 'nullable|integer',
        ]);

        $rows = $request->input('rows');
        $activeCycle = VisitCycle::where('status', 'active')->first() ?? VisitCycle::latest('start_date')->first();

        $saved = 0;
        $updated = 0;

        try {
            DB::beginTransaction();

            foreach ($rows as $item) {
                $name = trim($item['name'] ?? '');
                $phone = trim($item['phone'] ?? '');

                if (empty($name) && empty($phone)) {
                    continue;
                }

                $existing = null;
                if (!empty($item['id'])) {
                    $existing = Contact::find($item['id']);
                }
                if (!$existing && !empty($phone)) {
                    $existing = Contact::where('phone', $phone)->first();
                }

                $specId = !empty($item['specialty_id']) ? (int)$item['specialty_id'] : null;
                if ($specId && !ContactSpecialty::where('id', $specId)->exists()) {
                    $specId = null;
                }

                $classId = !empty($item['classification_id']) ? (int)$item['classification_id'] : null;
                if ($classId && !ContactClassification::where('id', $classId)->exists()) {
                    $classId = ContactClassification::first()?->id;
                }

                $cityId = !empty($item['city_id']) ? (int)$item['city_id'] : null;
                if ($cityId && !City::where('id', $cityId)->exists()) {
                    $cityId = null;
                }

                $areaId = !empty($item['area_id']) ? (int)$item['area_id'] : null;
                if ($areaId && !Area::where('id', $areaId)->exists()) {
                    $areaId = null;
                }

                $breakId = !empty($item['break_id']) ? (int)$item['break_id'] : null;
                if ($breakId && !AreaBreak::where('id', $breakId)->exists()) {
                    $breakId = null;
                }

                $data = [
                    'name' => $name,
                    'phone' => $phone ?: null,
                    'specialty_id' => $specId,
                    'classification_id' => $classId,
                    'country_id' => $cityId ? (City::find($cityId)?->country_id ?? Country::first()?->id) : Country::first()?->id,
                    'city_id' => $cityId,
                    'area_id' => $areaId,
                    'break_id' => $breakId,
                    'hospital_clinic_name' => !empty($item['clinic']) ? $item['clinic'] : 'Private Clinic',
                    'notes' => !empty($item['notes']) ? $item['notes'] : null,
                    'is_active' => true,
                ];

                if ($existing) {
                    $existing->update($data);
                    $contactId = $existing->id;
                    $updated++;
                } else {
                    $data['code'] = 'DOC-' . strtoupper(substr(uniqid(), -5));
                    $new = Contact::create($data);
                    $contactId = $new->id;
                    $saved++;
                }

                // If rep is assigned in grid
                if (!empty($item['rep_id']) && $activeCycle) {
                    $repId = (int)$item['rep_id'];
                    if (User::where('id', $repId)->exists()) {
                        $target = !empty($item['target_visits']) ? (int)$item['target_visits'] : 2;
                        ContactAssignment::updateOrCreate(
                            [
                                'cycle_id' => $activeCycle->id,
                                'contact_id' => $contactId,
                            ],
                            [
                                'mr_id' => $repId,
                                'target_visits' => $target,
                                'is_active' => true,
                            ]
                        );
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Spreadsheet data saved successfully! {$saved} new doctors created, {$updated} updated.",
                'saved' => $saved,
                'updated' => $updated,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('MR Manual Grid Save Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to save spreadsheet data: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Instantly export live spreadsheet grid to Excel file.
     */
    public function exportManualGrid(Request $request): BinaryFileResponse
    {
        $rows = json_decode($request->input('grid_data', '[]'), true) ?: [];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Live Grid Export');

        $headers = [
            'A1' => 'Doctor / HCP Name',
            'B1' => 'Phone / WhatsApp',
            'C1' => 'Specialty',
            'D1' => 'Classification',
            'E1' => 'Hospital / Clinic',
            'F1' => 'City',
            'G1' => 'Territory Area',
            'H1' => 'Assigned Medical Rep',
            'I1' => 'Target Monthly Visits',
            'J1' => 'Notes',
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        $sheet->getStyle('A1:J1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0A4F78']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $rowIdx = 2;
        foreach ($rows as $r) {
            $sheet->setCellValue('A' . $rowIdx, $r['name'] ?? '');
            $sheet->setCellValueExplicit('B' . $rowIdx, $r['phone'] ?? '', DataType::TYPE_STRING);
            $sheet->setCellValue('C' . $rowIdx, $r['specialty_label'] ?? ($r['specialty_id'] ?? ''));
            $sheet->setCellValue('D' . $rowIdx, $r['classification_label'] ?? ($r['classification_id'] ?? ''));
            $sheet->setCellValue('E' . $rowIdx, $r['clinic'] ?? '');
            $sheet->setCellValue('F' . $rowIdx, $r['city_label'] ?? ($r['city_id'] ?? ''));
            $sheet->setCellValue('G' . $rowIdx, $r['area_label'] ?? ($r['area_id'] ?? ''));
            $sheet->setCellValue('H' . $rowIdx, $r['rep_label'] ?? ($r['rep_id'] ?? ''));
            $sheet->setCellValue('I' . $rowIdx, $r['target_visits'] ?? 2);
            $sheet->setCellValue('J' . $rowIdx, $r['notes'] ?? '');

            $rowIdx++;
        }

        foreach (range(1, 10) as $col) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($col))->setAutoSize(true);
        }

        $fileName = 'mr_live_spreadsheet_' . date('Y-m-d_His') . '.xlsx';
        $tempPath = storage_path('app/' . $fileName);

        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        return response()->download($tempPath, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }
}

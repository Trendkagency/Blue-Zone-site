<?php

namespace App\Services\Mr;

use App\Models\Mr\VisitCycle;
use App\Models\User;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DoctorCoverageExcelExporter
{
    /**
     * Generate and stream a professionally designed Excel spreadsheet with native filters.
     *
     * @param  Collection  $rows
     * @param  int|null    $cycleId
     * @param  int|null    $mrId
     * @param  string      $filterStatus
     * @return StreamedResponse
     */
    public function export(Collection $rows, ?int $cycleId = null, ?int $mrId = null, string $filterStatus = 'all'): StreamedResponse
    {
        $cycle = $cycleId ? VisitCycle::find($cycleId) : VisitCycle::where('status', 'active')->first();
        $cycleName = $cycle ? $cycle->name . ' (' . ucfirst($cycle->status) . ')' : 'All Cycles';
        $cycleCode = $cycle ? $cycle->code : 'all';

        $repName = 'All Medical Representatives';
        if ($mrId) {
            $repUser = User::find($mrId);
            if ($repUser) {
                $repName = $repUser->name;
            }
        }

        $statusLabel = match ($filterStatus) {
            'unvisited' => 'Unvisited Only (0 Visits)',
            'behind' => 'Behind Frequency (<100%)',
            'completed' => 'Target Completed (>=100%)',
            default => 'All Doctor Statuses',
        };

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Doctor Coverage');
        $sheet->setShowGridLines(true);

        // Global font
        $spreadsheet->getDefaultStyle()->getFont()->setName('Segoe UI')->setSize(10);

        // 1. BRAND HEADER BANNER (Row 1)
        $sheet->mergeCells('A1:N1');
        $sheet->setCellValue('A1', 'BLUE ZONE™  |  DOCTOR COVERAGE & UNVISITED AUDIT REPORT');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 14,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0F172A'], // Deep Navy / Slate 900
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(34);

        // 2. REPORT METADATA (Row 2)
        $metaText = sprintf(
            'Visit Cycle: %s   |   Medical Rep: %s   |   Status: %s   |   Exported: %s   |   Total Records: %d',
            $cycleName,
            $repName,
            $statusLabel,
            now()->format('Y-m-d H:i'),
            $rows->count()
        );
        $sheet->mergeCells('A2:N2');
        $sheet->setCellValue('A2', $metaText);
        $sheet->getStyle('A2')->applyFromArray([
            'font' => [
                'color' => ['rgb' => 'E2E8F0'],
                'size' => 9,
                'italic' => false,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E293B'], // Slate 800
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(20);

        // Blank separator row
        $sheet->getRowDimension(3)->setRowHeight(8);

        // 3. EXECUTIVE KPI CARDS (Row 4)
        $totalDoctors = $rows->count();
        $targetMetCount = $rows->filter(fn($r) => (int)$r['visits_done'] >= (int)$r['required_frequency'] && (int)$r['required_frequency'] > 0)->count();
        $behindCount = $rows->filter(fn($r) => (int)$r['visits_done'] > 0 && (int)$r['visits_done'] < (int)$r['required_frequency'])->count();
        $unvisitedCount = $rows->filter(fn($r) => (int)$r['visits_done'] === 0)->count();
        $totalVisitsDone = (int) $rows->sum('visits_done');
        $totalTargetPoints = (int) $rows->sum('target_points');
        $totalAchievedPoints = (int) $rows->sum('achieved_points');

        $kpiCards = [
            ['range' => 'A4:B4', 'label' => 'Total Doctors', 'val' => (string)$totalDoctors, 'bg' => 'F1F5F9', 'fg' => '0F172A', 'border' => 'CBD5E1'],
            ['range' => 'C4:D4', 'label' => 'Target Met (>=100%)', 'val' => (string)$targetMetCount, 'bg' => 'DCFCE7', 'fg' => '15803D', 'border' => '86EFAC'],
            ['range' => 'E4:F4', 'label' => 'Behind Frequency', 'val' => (string)$behindCount, 'bg' => 'FEF9C3', 'fg' => 'A16207', 'border' => 'FDE047'],
            ['range' => 'G4:H4', 'label' => 'Unvisited (0 Visits)', 'val' => (string)$unvisitedCount, 'bg' => 'FEE2E2', 'fg' => 'B91C1C', 'border' => 'FCA5A5'],
            ['range' => 'I4:J4', 'label' => 'Visits Executed', 'val' => (string)$totalVisitsDone, 'bg' => 'E0F2FE', 'fg' => '0369A1', 'border' => '7DD3FC'],
            ['range' => 'K4:L4', 'label' => 'Target Points', 'val' => (string)$totalTargetPoints, 'bg' => 'F1F5F9', 'fg' => '334155', 'border' => 'CBD5E1'],
            ['range' => 'M4:N4', 'label' => 'Achieved Points', 'val' => (string)$totalAchievedPoints, 'bg' => 'EDE9FE', 'fg' => '6D28D9', 'border' => 'C4B5FD'],
        ];

        foreach ($kpiCards as $card) {
            $sheet->mergeCells($card['range']);
            $firstCell = explode(':', $card['range'])[0];
            $sheet->setCellValue($firstCell, $card['label'] . "\n" . $card['val']);
            $sheet->getStyle($card['range'])->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => $card['fg']],
                    'size' => 10,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $card['bg']],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
                'borders' => [
                    'outline' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => $card['border']],
                    ],
                ],
            ]);
        }
        $sheet->getRowDimension(4)->setRowHeight(36);

        // Blank separator row
        $sheet->getRowDimension(5)->setRowHeight(8);

        // 4. TABLE HEADERS (Row 6)
        $headers = [
            'A' => 'Contact Code',
            'B' => 'Doctor / Contact Name',
            'C' => 'Specialty',
            'D' => 'Class',
            'E' => 'Region / City',
            'F' => 'Clinic / Hospital Address',
            'G' => 'Assigned Medical Rep',
            'H' => 'Req. Frequency',
            'I' => 'Visits Done',
            'J' => 'Target Points',
            'K' => 'Achieved Points',
            'L' => 'Points Balance',
            'M' => 'Compliance %',
            'N' => 'Coverage Status',
        ];

        foreach ($headers as $col => $title) {
            $sheet->setCellValue($col . '6', $title);
        }

        $sheet->getStyle('A6:N6')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 10,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0F172A'], // Corporate Navy Header
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '0284C7'],
                ],
            ],
        ]);
        $sheet->getStyle('B6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('F6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('G6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getRowDimension(6)->setRowHeight(28);

        // 5. DATA ROWS (Starting from Row 7)
        $currentRow = 7;
        $thinBorder = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'E2E8F0'],
                ],
            ],
        ];

        foreach ($rows as $r) {
            $isEven = ($currentRow % 2 === 0);
            $zebraBg = $isEven ? 'F8FAFC' : 'FFFFFF';

            $reqVisits = (int) ($r['required_frequency'] ?? 0);
            $visitsDone = (int) ($r['visits_done'] ?? 0);
            $compliancePctRaw = (float) ($r['compliance_pct'] ?? $r['visit_compliance_pct'] ?? 0);

            // Determine status text and colors
            if ($visitsDone === 0) {
                $statusText = 'Unvisited (0 Visits)';
                $statusBg = 'FEE2E2';
                $statusFg = 'B91C1C';
            } elseif ($visitsDone >= $reqVisits && $reqVisits > 0) {
                $statusText = 'Target Met (100%+)';
                $statusBg = 'DCFCE7';
                $statusFg = '15803D';
            } else {
                $statusText = 'Behind Frequency';
                $statusBg = 'FEF9C3';
                $statusFg = 'A16207';
            }

            // Doctor class colors
            $classCode = strtoupper((string) ($r['class'] ?? 'C'));
            $classBg = match ($classCode) {
                'A+' => 'FEF3C7',
                'A' => 'E0F2FE',
                'B' => 'F1F5F9',
                default => 'F8FAFC',
            };
            $classFg = match ($classCode) {
                'A+' => '92400E',
                'A' => '0369A1',
                'B' => '334155',
                default => '64748B',
            };

            // Set cell values
            $sheet->setCellValue('A' . $currentRow, $r['contact_code'] ?? 'N/A');
            $sheet->setCellValue('B' . $currentRow, $r['contact_name'] ?? 'N/A');
            $sheet->setCellValue('C' . $currentRow, $r['specialty'] ?? 'General');
            $sheet->setCellValue('D' . $currentRow, $classCode);
            $sheet->setCellValue('E' . $currentRow, $r['region_city'] ?? 'N/A');
            $sheet->setCellValue('F' . $currentRow, $r['address'] ?? 'N/A');
            $sheet->setCellValue('G' . $currentRow, $r['assigned_user'] ?? 'Unassigned');
            $sheet->setCellValue('H' . $currentRow, $reqVisits);
            $sheet->setCellValue('I' . $currentRow, $visitsDone);
            $sheet->setCellValue('J' . $currentRow, (int) ($r['target_points'] ?? 0));
            $sheet->setCellValue('K' . $currentRow, (int) ($r['achieved_points'] ?? 0));
            $sheet->setCellValue('L' . $currentRow, "=J{$currentRow}-K{$currentRow}");
            $sheet->setCellValue('M' . $currentRow, $compliancePctRaw / 100);
            $sheet->setCellValue('N' . $currentRow, $statusText);

            // Row zebra background and borders
            $sheet->getStyle("A{$currentRow}:N{$currentRow}")->applyFromArray(array_merge($thinBorder, [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $zebraBg],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]));

            // Specific cell alignments and styles
            $sheet->getStyle("A{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("B{$currentRow}")->getFont()->setBold(true);
            $sheet->getStyle("C{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Class badge style
            $sheet->getStyle("D{$currentRow}")->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $classBg],
                ],
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => $classFg],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ]);

            // Numbers formatting
            $sheet->getStyle("H{$currentRow}:L{$currentRow}")->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle("H{$currentRow}:L{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Visits Done highlight
            if ($visitsDone === 0) {
                $sheet->getStyle("I{$currentRow}")->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('B91C1C'))->setBold(true);
            } else {
                $sheet->getStyle("I{$currentRow}")->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('0F172A'))->setBold(true);
            }

            // Compliance % formatting
            $sheet->getStyle("M{$currentRow}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_0);
            $sheet->getStyle("M{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("M{$currentRow}")->getFont()->setBold(true);

            // Coverage Status badge style
            $sheet->getStyle("N{$currentRow}")->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $statusBg],
                ],
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => $statusFg],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ]);

            $sheet->getRowDimension($currentRow)->setRowHeight(22);
            $currentRow++;
        }

        $lastDataRow = max(7, $currentRow - 1);

        // 6. SUMMARY / TOTAL ROW (at bottom)
        $summaryRow = $currentRow;
        $sheet->mergeCells("A{$summaryRow}:G{$summaryRow}");
        $sheet->setCellValue("A{$summaryRow}", 'PORTFOLIO TOTALS & COMPLIANCE SUMMARY');
        $sheet->setCellValue("H{$summaryRow}", "=SUM(H7:H{$lastDataRow})");
        $sheet->setCellValue("I{$summaryRow}", "=SUM(I7:I{$lastDataRow})");
        $sheet->setCellValue("J{$summaryRow}", "=SUM(J7:J{$lastDataRow})");
        $sheet->setCellValue("K{$summaryRow}", "=SUM(K7:K{$lastDataRow})");
        $sheet->setCellValue("L{$summaryRow}", "=SUM(L7:L{$lastDataRow})");
        $sheet->setCellValue("M{$summaryRow}", "=IF(COUNT(M7:M{$lastDataRow})>0, AVERAGE(M7:M{$lastDataRow}), 0)");
        $sheet->setCellValue("N{$summaryRow}", "=IF(M{$summaryRow}>=1, \"Compliant\", \"Needs Follow-up\")");

        $sheet->getStyle("A{$summaryRow}:N{$summaryRow}")->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 10,
                'color' => ['rgb' => '0F172A'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F1F5F9'], // Slate 100
            ],
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '0F172A'],
                ],
                'bottom' => [
                    'borderStyle' => Border::BORDER_DOUBLE,
                    'color' => ['rgb' => '0F172A'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->getStyle("A{$summaryRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("H{$summaryRow}:L{$summaryRow}")->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle("H{$summaryRow}:L{$summaryRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("M{$summaryRow}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_0);
        $sheet->getStyle("M{$summaryRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("N{$summaryRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getRowDimension($summaryRow)->setRowHeight(26);

        // 7. ENABLE NATIVE EXCEL AUTOFILTER (Crucial User Requirement: "can be option Filter in Excel")
        $sheet->setAutoFilter("A6:N{$lastDataRow}");

        // 8. FREEZE PANES (Pin header row and KPI cards so user can scroll effortlessly)
        $sheet->freezePane('A7');

        // 9. PROFESSIONAL COLUMN SIZING
        $columnWidths = [
            'A' => 14, // Contact Code
            'B' => 26, // Doctor Name
            'C' => 18, // Specialty
            'D' => 10, // Class
            'E' => 22, // Region / City
            'F' => 30, // Clinic Address
            'G' => 24, // Assigned Rep
            'H' => 14, // Req. Frequency
            'I' => 13, // Visits Done
            'J' => 14, // Target Points
            'K' => 15, // Achieved Points
            'L' => 15, // Points Balance
            'M' => 15, // Compliance %
            'N' => 22, // Coverage Status
        ];

        foreach ($columnWidths as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        // 10. GENERATE CLEAN STREAMED DOWNLOAD
        $filename = 'doctor-coverage-report-' . $cycleCode . '-' . date('Y-m-d') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            // Save directly to output buffer
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0, no-cache, must-revalidate, proxy-revalidate',
        ]);
    }
}

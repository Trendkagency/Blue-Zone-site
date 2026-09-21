<?php

namespace App\Services\Mr\Export;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MrTableExcelExporter
{
    /**
     * Build and stream an executive, professionally styled Excel report with native AutoFilter.
     *
     * @param  string    $reportTitle   Title banner (e.g. "BLUE ZONE™ | DOCTOR PORTFOLIO DIRECTORY")
     * @param  array     $metadata      Array of key-values displayed in subheader
     * @param  array     $kpiCards      Optional metric cards for top summary
     * @param  array     $columns       Column definitions [key, header, width, type, align, format, badge]
     * @param  iterable  $records       Data collection or array
     * @param  string    $filename      Download filename (e.g. "doctors-directory-2026-09-21.xlsx")
     * @param  array     $summaryConfig Optional summary formulas [column => formula/label]
     * @return StreamedResponse
     */
    public function export(
        string $reportTitle,
        array $metadata,
        array $kpiCards,
        array $columns,
        iterable $records,
        string $filename,
        array $summaryConfig = []
    ): StreamedResponse {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(substr(preg_replace('/[^a-zA-Z0-9 ]/', '', $reportTitle), 0, 30) ?: 'Report');
        $sheet->setShowGridLines(true);

        // Global font
        $spreadsheet->getDefaultStyle()->getFont()->setName('Segoe UI')->setSize(10);

        $totalCols = count($columns);
        $lastColLetter = Coordinate::stringFromColumnIndex($totalCols);

        // 1. BRAND HEADER BANNER (Row 1)
        $sheet->mergeCells("A1:{$lastColLetter}1");
        $sheet->setCellValue('A1', 'BLUE ZONE™  |  ' . strtoupper($reportTitle));
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 13,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0F172A'], // Corporate Navy / Slate 900
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(32);

        // 2. METADATA SUBHEADER (Row 2)
        $metaParts = [];
        foreach ($metadata as $k => $v) {
            if ($v !== null && $v !== '') {
                $metaParts[] = "{$k}: {$v}";
            }
        }
        $metaParts[] = 'Exported: ' . now()->format('Y-m-d H:i');
        $metaString = implode('   |   ', $metaParts);

        $sheet->mergeCells("A2:{$lastColLetter}2");
        $sheet->setCellValue('A2', $metaString);
        $sheet->getStyle('A2')->applyFromArray([
            'font' => [
                'color' => ['rgb' => 'E2E8F0'],
                'size' => 9,
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

        $headerRow = 4;

        // 3. EXECUTIVE KPI METRIC CARDS (Optional Row 4)
        if (!empty($kpiCards)) {
            $sheet->getRowDimension(3)->setRowHeight(8);
            
            // Distribute cards evenly across columns
            $cardCount = count($kpiCards);
            $colsPerCard = max(1, (int) floor($totalCols / $cardCount));
            $cardColIndex = 1;

            foreach ($kpiCards as $idx => $card) {
                if ($cardColIndex > $totalCols) break;

                $startCol = Coordinate::stringFromColumnIndex($cardColIndex);
                $endColIndex = ($idx === $cardCount - 1) 
                    ? $totalCols 
                    : min($totalCols, $cardColIndex + $colsPerCard - 1);
                $endCol = Coordinate::stringFromColumnIndex($endColIndex);
                
                $range = "{$startCol}4:{$endCol}4";
                if ($startCol !== $endCol) {
                    $sheet->mergeCells($range);
                }

                $sheet->setCellValue("{$startCol}4", ($card['label'] ?? '') . "\n" . ($card['val'] ?? ''));
                $sheet->getStyle($range)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => $card['fg'] ?? '0F172A'],
                        'size' => 9.5,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => $card['bg'] ?? 'F8FAFC'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                    'borders' => [
                        'outline' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => $card['border'] ?? 'CBD5E1'],
                        ],
                    ],
                ]);

                $cardColIndex = $endColIndex + 1;
            }

            $sheet->getRowDimension(4)->setRowHeight(34);
            $sheet->getRowDimension(5)->setRowHeight(8);
            $headerRow = 6;
        } else {
            $sheet->getRowDimension(3)->setRowHeight(8);
            $headerRow = 4;
        }

        // 4. TABLE HEADERS (Row $headerRow)
        $colIndex = 1;
        foreach ($columns as $colDef) {
            $colLetter = Coordinate::stringFromColumnIndex($colIndex);
            $sheet->setCellValue("{$colLetter}{$headerRow}", $colDef['header']);
            $colIndex++;
        }

        $sheet->getStyle("A{$headerRow}:{$lastColLetter}{$headerRow}")->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 10,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0F172A'], // Corporate Slate 900
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '0284C7'], // Blue accent border
                ],
            ],
        ]);
        $sheet->getRowDimension($headerRow)->setRowHeight(28);

        // 5. DATA ROWS
        $startDataRow = $headerRow + 1;
        $currentRow = $startDataRow;

        $thinBorder = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'E2E8F0'],
                ],
            ],
        ];

        foreach ($records as $record) {
            $isEven = ($currentRow % 2 === 0);
            $zebraBg = $isEven ? 'F8FAFC' : 'FFFFFF';

            $colIndex = 1;
            foreach ($columns as $colDef) {
                $colLetter = Coordinate::stringFromColumnIndex($colIndex);
                $key = $colDef['key'];

                // Extract value
                $val = null;
                if (is_callable($key)) {
                    $val = $key($record);
                } elseif (is_array($record)) {
                    $val = $record[$key] ?? null;
                } elseif (is_object($record)) {
                    $val = data_get($record, $key);
                }

                // Value formatting
                $type = $colDef['type'] ?? 'string';
                $cellCoordinate = "{$colLetter}{$currentRow}";

                if ($type === 'number') {
                    $sheet->setCellValue($cellCoordinate, (float) ($val ?? 0));
                    $sheet->getStyle($cellCoordinate)->getNumberFormat()->setFormatCode($colDef['format'] ?? '#,##0');
                } elseif ($type === 'percent') {
                    $sheet->setCellValue($cellCoordinate, ((float) ($val ?? 0)) / 100);
                    $sheet->getStyle($cellCoordinate)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_0);
                } elseif ($type === 'date') {
                    $dateVal = $val instanceof \DateTimeInterface ? $val->format('Y-m-d H:i') : (string)$val;
                    $sheet->setCellValue($cellCoordinate, $dateVal ?: '—');
                } elseif ($type === 'badge') {
                    $sheet->setCellValue($cellCoordinate, (string)$val);
                    if (isset($colDef['badgeColors']) && is_callable($colDef['badgeColors'])) {
                        $colors = $colDef['badgeColors']($val, $record);
                        if (!empty($colors['bg'])) {
                            $sheet->getStyle($cellCoordinate)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($colors['bg']);
                        }
                        if (!empty($colors['fg'])) {
                            $sheet->getStyle($cellCoordinate)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color($colors['fg']))->setBold(true);
                        }
                    }
                } else {
                    $sheet->setCellValue($cellCoordinate, $val !== null ? (string)$val : '—');
                }

                // Cell Alignment
                $align = $colDef['align'] ?? Alignment::HORIZONTAL_LEFT;
                $sheet->getStyle($cellCoordinate)->getAlignment()->setHorizontal($align);

                $colIndex++;
            }

            // Zebra background & thin borders
            $sheet->getStyle("A{$currentRow}:{$lastColLetter}{$currentRow}")->applyFromArray(array_merge($thinBorder, [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $zebraBg],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]));

            $sheet->getRowDimension($currentRow)->setRowHeight(22);
            $currentRow++;
        }

        $lastDataRow = max($startDataRow, $currentRow - 1);

        // 6. TOTALS / SUMMARY ROW (Optional)
        if (!empty($summaryConfig) && $lastDataRow >= $startDataRow) {
            $summaryRow = $currentRow;
            
            // Default styling for summary row
            $sheet->getStyle("A{$summaryRow}:{$lastColLetter}{$summaryRow}")->applyFromArray([
                'font' => [
                    'bold' => true,
                    'size' => 10,
                    'color' => ['rgb' => '0F172A'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F1F5F9'],
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

            // Set summary cells
            foreach ($summaryConfig as $colKey => $config) {
                // Find column letter
                $targetColLetter = null;
                $colIndex = 1;
                foreach ($columns as $c) {
                    if (($c['key'] ?? '') === $colKey || ($c['id'] ?? '') === $colKey) {
                        $targetColLetter = Coordinate::stringFromColumnIndex($colIndex);
                        break;
                    }
                    $colIndex++;
                }

                if (!$targetColLetter && isset($config['col'])) {
                    $targetColLetter = $config['col'];
                }

                if ($targetColLetter) {
                    if (isset($config['mergeTo'])) {
                        $sheet->mergeCells("{$targetColLetter}{$summaryRow}:{$config['mergeTo']}{$summaryRow}");
                    }
                    
                    if (isset($config['label'])) {
                        $sheet->setCellValue("{$targetColLetter}{$summaryRow}", $config['label']);
                    } elseif (isset($config['type'])) {
                        if ($config['type'] === 'sum') {
                            $sheet->setCellValue("{$targetColLetter}{$summaryRow}", "=SUM({$targetColLetter}{$startDataRow}:{$targetColLetter}{$lastDataRow})");
                            $sheet->getStyle("{$targetColLetter}{$summaryRow}")->getNumberFormat()->setFormatCode('#,##0');
                        } elseif ($config['type'] === 'avg_percent') {
                            $sheet->setCellValue("{$targetColLetter}{$summaryRow}", "=IF(COUNT({$targetColLetter}{$startDataRow}:{$targetColLetter}{$lastDataRow})>0, AVERAGE({$targetColLetter}{$startDataRow}:{$targetColLetter}{$lastDataRow}), 0)");
                            $sheet->getStyle("{$targetColLetter}{$summaryRow}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_0);
                        }
                    }

                    if (isset($config['align'])) {
                        $sheet->getStyle("{$targetColLetter}{$summaryRow}")->getAlignment()->setHorizontal($config['align']);
                    }
                }
            }

            $sheet->getRowDimension($summaryRow)->setRowHeight(26);
        }

        // 7. ENABLE NATIVE EXCEL AUTOFILTER (Crucial User Requirement: "can be option Filter in Excel")
        $sheet->setAutoFilter("A{$headerRow}:{$lastColLetter}{$lastDataRow}");

        // 8. FREEZE PANES (Pin header row and KPI cards)
        $sheet->freezePane("A" . ($headerRow + 1));

        // 9. COLUMN WIDTHS
        $colIndex = 1;
        foreach ($columns as $colDef) {
            $colLetter = Coordinate::stringFromColumnIndex($colIndex);
            $width = $colDef['width'] ?? 18;
            $sheet->getColumnDimension($colLetter)->setWidth($width);
            $colIndex++;
        }

        // 10. GENERATE CLEAN STREAMED DOWNLOAD
        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0, no-cache, must-revalidate, proxy-revalidate',
        ]);
    }
}

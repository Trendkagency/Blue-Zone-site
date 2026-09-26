<?php

namespace App\Http\Controllers\Admin\Mr;

use App\Http\Controllers\Controller;
use App\Models\Mr\RepPerformanceSnapshot;
use App\Models\Mr\VisitCycle;
use App\Models\User;
use App\Services\Mr\CrmMrReportService;
use App\Services\Mr\DoctorCoverageExcelExporter;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MrReportController extends Controller
{
    protected CrmMrReportService $reportService;

    public function __construct(CrmMrReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Unvisited / Doctor Coverage Report (§8)
     */
    public function coverage(Request $request)
    {
        $currentUser = auth()->user();
        $isManager = $currentUser ? $currentUser->canManageAllMr() : false;
        $isRep = $currentUser ? ($currentUser->isMedicalRep() && !$isManager) : false;

        $cycles = VisitCycle::latest('start_date')->get();
        $selectedCycleId = $request->integer('cycle_id') ?: ($cycles->firstWhere('status', 'active')?->id ?? $cycles->first()?->id);
        $selectedMrId = $isRep ? $currentUser->id : ($request->filled('mr_id') ? $request->integer('mr_id') : null);
        $filterStatus = $request->get('status', 'all');

        $rows = $this->reportService->getUnvisitedCoverageReport($selectedCycleId, $selectedMrId);

        // Filter status in collection
        if ($filterStatus === 'unvisited') {
            $rows = $rows->where('visits_done', 0);
        } elseif ($filterStatus === 'behind') {
            $rows = $rows->filter(function ($r) {
                $comp = (float) ($r['compliance_pct'] ?? $r['visit_compliance_pct'] ?? 0);
                return $r['visits_done'] > 0 && $comp < 100;
            });
        } elseif ($filterStatus === 'completed') {
            $rows = $rows->filter(function ($r) {
                $comp = (float) ($r['compliance_pct'] ?? $r['visit_compliance_pct'] ?? 0);
                return $comp >= 100;
            });
        }

        // Export check (Default to professional formatted Excel sheet)
        if ($request->has('export')) {
            if ($request->export === 'csv') {
                return $this->exportCoverageCsv($rows, $selectedCycleId);
            }
            return app(DoctorCoverageExcelExporter::class)->export($rows, $selectedCycleId, $selectedMrId, $filterStatus);
        }

        if ($isRep) {
            $medicalReps = User::where('id', $currentUser->id)->select('id', 'name')->get();
        } else {
            $medicalReps = User::whereHas('role', function ($q) {
                $q->where('name', 'mr');
            })->orWhere('role_id', 2)->select('id', 'name')->get();
        }

        return view('admin.mr.reports.coverage', compact('rows', 'cycles', 'selectedCycleId', 'selectedMrId', 'filterStatus', 'medicalReps', 'isRep', 'isManager', 'currentUser'));
    }

    /**
     * Rep Performance Scorecard Report (§8)
     */
    public function performance(Request $request)
    {
        $currentUser = auth()->user();
        $isManager = $currentUser ? $currentUser->canManageAllMr() : false;
        $isRep = $currentUser ? ($currentUser->isMedicalRep() && !$isManager) : false;

        $cycles = VisitCycle::latest('start_date')->get();
        $selectedCycleId = $request->integer('cycle_id') ?: ($cycles->firstWhere('status', 'active')?->id ?? $cycles->first()?->id);

        if ($request->has('recalculate') && $selectedCycleId) {
            if ($isRep) {
                $this->reportService->recalculateForRepAndCycle($currentUser->id, $selectedCycleId);
            } else {
                $this->reportService->recalculateAllSnapshotsForCycle($selectedCycleId);
            }
            return redirect()->route('admin.mr.reports.performance', ['cycle_id' => $selectedCycleId])
                ->with('success', __('admin.mr.kpis_recalculated_successfully'));
        }

        $query = RepPerformanceSnapshot::with(['representative', 'cycle'])
            ->where('cycle_id', $selectedCycleId);

        if ($isRep) {
            $query->where('mr_id', $currentUser->id);
        }

        $snapshots = $query->get();

        // If no snapshots yet, generate on the fly
        if ($snapshots->isEmpty() && $selectedCycleId) {
            if ($isRep) {
                $this->reportService->recalculateForRepAndCycle($currentUser->id, $selectedCycleId);
            } else {
                $this->reportService->recalculateAllSnapshotsForCycle($selectedCycleId);
            }
            $snapshots = $query->get();
        }

        if ($request->has('export')) {
            if ($request->export === 'csv') {
                return $this->exportPerformanceCsv($snapshots, $selectedCycleId);
            }
            return $this->exportPerformanceExcel($snapshots, $selectedCycleId);
        }

        return view('admin.mr.reports.performance', compact('snapshots', 'cycles', 'selectedCycleId', 'isRep', 'isManager', 'currentUser'));
    }

    protected function exportPerformanceExcel($snapshots, ?int $cycleId)
    {
        $exporter = app(\App\Services\Mr\Export\MrTableExcelExporter::class);
        $cycle = VisitCycle::find($cycleId);

        $metadata = [
            'Visit Cycle' => $cycle ? $cycle->name . ' (' . ucfirst($cycle->status) . ')' : 'All Cycles',
            'Cycle Code' => $cycle ? $cycle->code : 'all',
            'Total Reps Evaluated' => $snapshots->count(),
        ];

        $repsCount = $snapshots->count();
        $avgCoverage = $snapshots->count() > 0 ? round($snapshots->avg('coverage_rate_pct'), 1) : 0;
        $totalVisits = $snapshots->sum('visits_done');
        $avgGps = $snapshots->count() > 0 ? round($snapshots->avg('gps_accuracy_pct'), 1) : 0;
        $totalAchievedPoints = $snapshots->sum('achieved_points');

        $kpiCards = [
            ['label' => 'Reps Evaluated', 'val' => (string)$repsCount, 'bg' => 'F1F5F9', 'fg' => '0F172A', 'border' => 'CBD5E1'],
            ['label' => 'Avg Coverage Rate', 'val' => $avgCoverage . '%', 'bg' => 'DCFCE7', 'fg' => '15803D', 'border' => '86EFAC'],
            ['label' => 'Total Visits Done', 'val' => (string)$totalVisits, 'bg' => 'E0F2FE', 'fg' => '0369A1', 'border' => 'BAE6FD'],
            ['label' => 'Avg GPS Accuracy', 'val' => $avgGps . '%', 'bg' => 'EDE9FE', 'fg' => '6D28D9', 'border' => 'C4B5FD'],
            ['label' => 'Total Achieved Pts', 'val' => (string)$totalAchievedPoints, 'bg' => 'FEF3C7', 'fg' => '92400E', 'border' => 'FDE68A'],
        ];

        $columns = [
            ['key' => fn($s) => $s->representative?->name ?? 'Rep #' . $s->mr_id, 'header' => 'Medical Representative', 'width' => 26, 'align' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT],
            ['key' => fn($s) => (float)$s->coverage_rate_pct, 'header' => 'Coverage Rate %', 'width' => 16, 'type' => 'percent', 'align' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            ['key' => fn($s) => (int)$s->visits_done, 'header' => 'Visits Done', 'width' => 14, 'type' => 'number', 'align' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            ['key' => fn($s) => (int)$s->planned_visits, 'header' => 'Planned Visits', 'width' => 14, 'type' => 'number', 'align' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            ['key' => fn($s) => (float)$s->gps_accuracy_pct, 'header' => 'GPS Accuracy %', 'width' => 16, 'type' => 'percent', 'align' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            ['key' => fn($s) => (float)$s->visit_compliance_pct, 'header' => 'Visit Compliance %', 'width' => 18, 'type' => 'percent', 'align' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            ['key' => fn($s) => (int)$s->target_points, 'header' => 'Target Points', 'width' => 14, 'type' => 'number', 'align' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            ['key' => fn($s) => (int)$s->achieved_points, 'header' => 'Achieved Points', 'width' => 15, 'type' => 'number', 'align' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            ['key' => fn($s) => (float)$s->points_achieved_pct, 'header' => 'Points %', 'width' => 14, 'type' => 'percent', 'align' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            ['key' => fn($s) => (int)$s->unreported_days_count, 'header' => 'Unreported Days', 'width' => 16, 'type' => 'number', 'align' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            [
                'key' => function ($s) {
                    $score = (float)$s->coverage_rate_pct;
                    if ($score >= 90) return 'Top Tier (>=90%)';
                    if ($score >= 75) return 'On Target (75-89%)';
                    return 'Needs Follow-up (<75%)';
                },
                'header' => 'Performance Tier',
                'width' => 22,
                'type' => 'badge',
                'align' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'badgeColors' => fn($val) => match ($val) {
                    'Top Tier (>=90%)' => ['bg' => 'DCFCE7', 'fg' => '15803D'],
                    'On Target (75-89%)' => ['bg' => 'E0F2FE', 'fg' => '0369A1'],
                    default => ['bg' => 'FEE2E2', 'fg' => 'B91C1C'],
                }
            ],
        ];

        return $exporter->export(
            'Rep Performance Scorecard & Field KPIs Audit',
            $metadata,
            $kpiCards,
            $columns,
            $snapshots,
            'rep-performance-' . ($cycle ? $cycle->code : 'all') . '-' . date('Y-m-d') . '.xlsx',
            [
                'col' => 'A',
                'mergeTo' => 'A',
                'label' => 'PORTFOLIO AVERAGES & TOTALS',
                'align' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
            ]
        );
    }

    protected function exportCoverageCsv($rows, ?int $cycleId): StreamedResponse
    {
        $cycle = VisitCycle::find($cycleId);
        $filename = 'doctor-coverage-report-' . ($cycle ? $cycle->code : 'all') . '-' . date('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($rows) {
            $file = fopen('php://output', 'w');
            // Add UTF-8 BOM
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, [
                'Contact Code',
                'Contact Name',
                'Region / City',
                'Specialty',
                'Address',
                'Class',
                'Required Frequency',
                'Assigned User',
                'No. of Visits Done',
                'Target Points',
                'Achieved Points',
                'Visit Compliance %'
            ]);

            foreach ($rows as $r) {
                fputcsv($file, [
                    $r['contact_code'],
                    $r['contact_name'],
                    $r['region_city'],
                    $r['specialty'],
                    $r['address'],
                    $r['class'],
                    $r['required_frequency'],
                    $r['assigned_user'],
                    $r['visits_done'],
                    $r['target_points'],
                    $r['achieved_points'],
                    ($r['compliance_pct'] ?? $r['visit_compliance_pct'] ?? 0) . '%',
                ]);
            }

            fclose($file);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    protected function exportPerformanceCsv($snapshots, ?int $cycleId): StreamedResponse
    {
        $cycle = VisitCycle::find($cycleId);
        $filename = 'rep-performance-report-' . ($cycle ? $cycle->code : 'all') . '-' . date('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($snapshots) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, [
                'Rep Name',
                'Coverage Rate %',
                'Visits Done',
                'Planned Visits',
                'Accuracy %',
                'Visit Compliance %',
                'Target Points',
                'Achieved Points',
                'Points %',
                'No. of Unreported Days',
            ]);

            foreach ($snapshots as $s) {
                fputcsv($file, [
                    $s->representative?->name ?? 'Rep #' . $s->mr_id,
                    $s->coverage_rate_pct . '%',
                    $s->visits_done,
                    $s->planned_visits,
                    $s->gps_accuracy_pct . '%',
                    $s->visit_compliance_pct . '%',
                    $s->target_points,
                    $s->achieved_points,
                    $s->points_achieved_pct . '%',
                    $s->unreported_days_count,
                ]);
            }

            fclose($file);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}

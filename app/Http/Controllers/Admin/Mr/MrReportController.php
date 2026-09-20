<?php

namespace App\Http\Controllers\Admin\Mr;

use App\Http\Controllers\Controller;
use App\Models\Mr\RepPerformanceSnapshot;
use App\Models\Mr\VisitCycle;
use App\Models\User;
use App\Services\Mr\CrmMrReportService;
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
        $cycles = VisitCycle::latest('start_date')->get();
        $selectedCycleId = $request->integer('cycle_id') ?: ($cycles->firstWhere('status', 'active')?->id ?? $cycles->first()?->id);
        $selectedMrId = $request->filled('mr_id') ? $request->integer('mr_id') : null;
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

        // CSV Export check
        if ($request->has('export') && $request->export === 'csv') {
            return $this->exportCoverageCsv($rows, $selectedCycleId);
        }

        $medicalReps = User::whereHas('role', function ($q) {
            $q->where('name', 'mr');
        })->orWhere('role_id', 2)->select('id', 'name')->get();

        return view('admin.mr.reports.coverage', compact('rows', 'cycles', 'selectedCycleId', 'selectedMrId', 'filterStatus', 'medicalReps'));
    }

    /**
     * Rep Performance Scorecard Report (§8)
     */
    public function performance(Request $request)
    {
        $cycles = VisitCycle::latest('start_date')->get();
        $selectedCycleId = $request->integer('cycle_id') ?: ($cycles->firstWhere('status', 'active')?->id ?? $cycles->first()?->id);

        if ($request->has('recalculate') && $selectedCycleId) {
            $this->reportService->recalculateAllSnapshotsForCycle($selectedCycleId);
            return redirect()->route('admin.mr.reports.performance', ['cycle_id' => $selectedCycleId])
                ->with('success', __('admin.mr.kpis_recalculated_successfully'));
        }

        $snapshots = RepPerformanceSnapshot::with(['representative', 'cycle'])
            ->where('cycle_id', $selectedCycleId)
            ->get();

        // If no snapshots yet, generate on the fly
        if ($snapshots->isEmpty() && $selectedCycleId) {
            $this->reportService->recalculateAllSnapshotsForCycle($selectedCycleId);
            $snapshots = RepPerformanceSnapshot::with(['representative', 'cycle'])
                ->where('cycle_id', $selectedCycleId)
                ->get();
        }

        if ($request->has('export') && $request->export === 'csv') {
            return $this->exportPerformanceCsv($snapshots, $selectedCycleId);
        }

        return view('admin.mr.reports.performance', compact('snapshots', 'cycles', 'selectedCycleId'));
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

<?php

namespace App\Filament\Pages;

use App\Models\Mr\VisitCycle;
use App\Services\Mr\CrmMrReportService;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;
use UnitEnum;

class RepPerformanceReport extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar-square';

    protected static string|UnitEnum|null $navigationGroup = 'Medical Rep Reports';

    protected static ?string $navigationLabel = 'Rep Performance Scorecard';

    protected static ?int $navigationSort = 2;

    protected static ?string $title = 'Medical Representative KPI & Performance Scorecard';

    protected string $view = 'filament.pages.rep-performance-report';

    public ?int $selectedCycleId = null;

    public function mount(): void
    {
        $activeCycle = VisitCycle::where('status', 'active')->first();
        $this->selectedCycleId = $activeCycle?->id;
    }

    public function getPerformanceDataProperty(): Collection
    {
        $service = app(CrmMrReportService::class);
        return $service->getRepPerformanceReport($this->selectedCycleId);
    }

    public function getCyclesProperty(): Collection
    {
        return VisitCycle::orderBy('start_date', 'desc')->get();
    }

    public function recalculateSnapshots(): void
    {
        if (!$this->selectedCycleId) {
            return;
        }

        $service = app(CrmMrReportService::class);
        $service->recalculateAllSnapshotsForCycle($this->selectedCycleId);

        Notification::make()
            ->title('KPI Snapshots Recalculated')
            ->body('All representative coverage rates, accuracy %, points, and compliance % have been freshly computed.')
            ->success()
            ->send();
    }

    public function exportCsv(): StreamedResponse
    {
        $data = $this->performance_data;
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="rep_performance_report_' . date('Y-m-d') . '.csv"',
        ];

        return response()->stream(function () use ($data) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Rep Name',
                'Email',
                'Total Doctors Assigned',
                'Unique Doctors Visited',
                'Coverage Rate %',
                'Visits Done',
                'Planned Visits',
                'Visit Compliance %',
                'GPS Accuracy %',
                'Target Points',
                'Achieved Points',
                'Points %',
                'Unreported Days',
                'Last Calculated',
            ]);

            foreach ($data as $row) {
                fputcsv($handle, [
                    $row['rep_name'],
                    $row['rep_email'],
                    $row['total_assigned_contacts'],
                    $row['unique_contacts_visited'],
                    $row['coverage_rate_pct'] . '%',
                    $row['visits_done'],
                    $row['planned_visits'],
                    $row['visit_compliance_pct'] . '%',
                    $row['accuracy_pct'] . '%',
                    $row['target_points'],
                    $row['achieved_points'],
                    $row['points_achieved_pct'] . '%',
                    $row['unreported_days_count'],
                    $row['calculated_at'],
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}

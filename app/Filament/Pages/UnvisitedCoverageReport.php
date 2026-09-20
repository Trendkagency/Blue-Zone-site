<?php

namespace App\Filament\Pages;

use App\Models\Mr\VisitCycle;
use App\Models\User;
use App\Services\Mr\CrmMrReportService;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;
use UnitEnum;

class UnvisitedCoverageReport extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static string|UnitEnum|null $navigationGroup = 'Medical Rep Reports';

    protected static ?string $navigationLabel = 'Doctor Coverage Report';

    protected static ?int $navigationSort = 1;

    protected static ?string $title = 'Doctor Visit Coverage & Compliance Report';

    protected string $view = 'filament.pages.unvisited-coverage-report';

    public ?int $selectedCycleId = null;
    public ?int $selectedMrId = null;
    public string $filterStatus = 'all'; // all, unvisited, behind, completed

    public function mount(): void
    {
        $activeCycle = VisitCycle::where('status', 'active')->first();
        $this->selectedCycleId = $activeCycle?->id;
    }

    public function getReportDataProperty(): Collection
    {
        $service = app(CrmMrReportService::class);
        $data = $service->getUnvisitedCoverageReport($this->selectedCycleId, $this->selectedMrId);

        if ($this->filterStatus === 'unvisited') {
            return $data->where('visits_done', 0);
        } elseif ($this->filterStatus === 'behind') {
            return $data->where('is_behind', true)->where('visits_done', '>', 0);
        } elseif ($this->filterStatus === 'completed') {
            return $data->where('is_behind', false);
        }

        return $data;
    }

    public function getCyclesProperty(): Collection
    {
        return VisitCycle::orderBy('start_date', 'desc')->get();
    }

    public function getRepsProperty(): Collection
    {
        return User::whereHas('mrAssignments')->orderBy('name')->get();
    }

    public function exportCsv(): StreamedResponse
    {
        $data = $this->report_data;
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="doctor_coverage_report_' . date('Y-m-d') . '.csv"',
        ];

        return response()->stream(function () use ($data) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Contact Code',
                'Doctor Name',
                'Region / City',
                'Specialty',
                'Address',
                'Class',
                'Required Frequency',
                'Assigned MR',
                'Visits Done',
                'Target Points',
                'Achieved Points',
                'Compliance %',
            ]);

            foreach ($data as $row) {
                fputcsv($handle, [
                    $row['contact_code'],
                    $row['contact_name'],
                    $row['region_city'],
                    $row['specialty'],
                    $row['address'],
                    $row['class'],
                    $row['required_frequency'],
                    $row['assigned_user'],
                    $row['visits_done'],
                    $row['target_points'],
                    $row['achieved_points'],
                    $row['visit_compliance_pct'] . '%',
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}

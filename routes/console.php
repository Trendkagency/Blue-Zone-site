<?php

use App\Jobs\CalculateRepPerformanceSnapshotsJob;
use App\Jobs\EvaluateDailyRepLogsJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule MR Daily Inactivity and Unreported Day Evaluation at 23:00 daily
Schedule::job(new EvaluateDailyRepLogsJob())->dailyAt('23:00');

// Schedule Nightly Recalculation of Rep Performance Snapshots at 00:30
Schedule::job(new CalculateRepPerformanceSnapshotsJob())->dailyAt('00:30');

// Artisan Command to trigger recalculation manually
Artisan::command('mr:recalculate-kpis {cycle_id?}', function (?int $cycle_id = null) {
    $service = app(\App\Services\Mr\CrmMrReportService::class);
    if ($cycle_id) {
        $service->recalculateAllSnapshotsForCycle($cycle_id);
        $this->info("Recalculated MR KPIs for cycle {$cycle_id}.");
    } else {
        $activeCycles = \App\Models\Mr\VisitCycle::where('status', 'active')->get();
        foreach ($activeCycles as $cycle) {
            $service->recalculateAllSnapshotsForCycle($cycle->id);
            $this->info("Recalculated MR KPIs for active cycle {$cycle->name}.");
        }
    }
})->purpose('Recalculate Medical Representative Performance Snapshots and KPIs');

// Schedule Daily HR Lifecycle Checks at 06:00 daily
Schedule::command('hr:daily-checks')->dailyAt('06:00');

Artisan::command('hr:daily-checks', function () {
    $today = now()->startOfDay();
    $notificationService = \App\Services\Hr\HrNotificationService::getInstance();

    // 1. Check contracts expiring in 90, 60, 30, 7 days
    $thresholds = [90, 60, 30, 7];
    foreach ($thresholds as $days) {
        $targetDate = $today->copy()->addDays($days)->toDateString();
        $contracts = \App\Models\EmployeeContract::where('status', 'active')
            ->whereDate('end_date', $targetDate)
            ->with('employee')
            ->get();

        foreach ($contracts as $c) {
            $empName = $c->employee?->full_name ?? 'Employee';
            $msg = "Contract {$c->contract_number} for {$empName} expires in {$days} days ({$targetDate}).";
            $notificationService->notifyHrAdmins('Contract Expiring Soon', $msg, [
                'type' => 'contract',
                'action_url' => url('/admin/hr/employees/' . $c->employee_id),
            ]);
        }
    }

    // 2. Check documents expiring within 30 days
    $docTarget = $today->copy()->addDays(30)->toDateString();
    $documents = \App\Models\EmployeeDocument::where('status', 'valid')
        ->whereDate('expiry_date', '<=', $docTarget)
        ->whereDate('expiry_date', '>=', $today->toDateString())
        ->with('employee')
        ->get();

    foreach ($documents as $doc) {
        $empName = $doc->employee?->full_name ?? 'Employee';
        $msg = "Document '{$doc->title}' for {$empName} expires on {$doc->expiry_date->format('Y-m-d')}.";
        $notificationService->notifyHrAdmins('Document Expiring Soon', $msg, [
            'type' => 'document',
            'action_url' => url('/admin/hr/employees/' . $doc->employee_id),
        ]);
    }

    // 3. Check employees with probation ending within 14 days
    $probTarget = $today->copy()->addDays(14)->toDateString();
    $probEmployees = \App\Models\Employee::where('employment_status', 'probation')
        ->whereDate('probation_end_date', '<=', $probTarget)
        ->whereDate('probation_end_date', '>=', $today->toDateString())
        ->get();

    foreach ($probEmployees as $emp) {
        $msg = "Employee {$emp->full_name} is completing probation period on {$emp->probation_end_date->format('Y-m-d')}.";
        $notificationService->notifyHrAdmins('Probation Period Ending', $msg, [
            'type' => 'probation',
            'action_url' => url('/admin/hr/employees/' . $emp->id),
        ]);
    }

    $this->info('Daily HR lifecycle checks completed successfully.');
})->purpose('Check and alert for contract/document expirations and probation deadlines');



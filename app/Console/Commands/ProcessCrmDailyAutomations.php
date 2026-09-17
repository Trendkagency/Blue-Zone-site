<?php

namespace App\Console\Commands;

use App\Models\CrmActivity;
use App\Models\Customer;
use App\Services\CrmAutomationService;
use App\Services\CrmSegmentService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessCrmDailyAutomations extends Command
{
    protected $signature = 'crm:daily-automations';
    protected $description = 'Process daily CRM automations, customer reactivation checks, and segment evaluations';

    public function handle(
        CrmAutomationService $automationService,
        CrmSegmentService $segmentService
    ): int {
        $this->info('Starting CRM daily automations...');

        // 1. Refresh dynamic segment counts
        $segmentService->refreshSegmentCounts();
        $this->info('Segments re-evaluated.');

        // 2. Identify inactive customers (120+ days without purchase) and create reactivation outreach
        $inactiveCustomers = Customer::whereHas('orders', function ($q) {
            $q->where('status', 'delivered');
        })->whereDoesntHave('orders', function ($q) {
            $q->where('status', 'delivered')->where('created_at', '>=', now()->subDays(90));
        })->take(20)->get();

        $reactivationsCount = 0;
        foreach ($inactiveCustomers as $customer) {
            $act = $automationService->handleInactiveCustomer($customer, 90);
            if ($act) {
                $reactivationsCount++;
            }
        }
        $this->info("Created {$reactivationsCount} new customer reactivation tasks.");

        // 3. Check overdue activities
        $overdueCount = CrmActivity::overdue()->count();
        $this->info("Currently {$overdueCount} activities marked overdue.");

        Log::info("CRM: Daily automations executed successfully. Reactivations: {$reactivationsCount}, Overdue: {$overdueCount}");

        return self::SUCCESS;
    }
}

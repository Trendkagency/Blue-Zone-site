<?php

namespace App\Observers;

use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\Cache;

class PurchaseOrderObserver
{
    public function creating(PurchaseOrder $order): void
    {
        if (empty($order->po_number)) {
            $order->po_number = PurchaseOrder::generatePoNumber();
        }
    }

    public function saved(PurchaseOrder $order): void
    {
        Cache::forget('bz_report_po_aging');
        Cache::forget('bz_report_vendor_scorecard');
        Cache::forget('bz_widget_overdue_pos');
    }
}

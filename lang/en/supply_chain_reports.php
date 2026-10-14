<?php

return [
    'title' => 'Supply Chain & Manufacturing Reports',
    'subtitle' => 'Audit scorecards, stock valuation, FEFO expiry risks, MRP forecasts, and regulatory lot recall traceability.',
    'lot_traceability' => 'Lot Traceability & Rapid Recall Engine',
    'reports' => [
        'vendor_scorecard' => 'Vendor Quality Scorecard',
        'valuation_expiry' => 'Raw Material Valuation & Expiry Risk',
        'po_aging' => 'Open Purchase Orders & Aging',
        'qc_log' => 'QC Clearance & Testing Log',
        'mrp_forecast' => 'MRP & Stockout Forecast',
        'scrap_variance' => 'Production Scrap & Variance',
        'lot_traceability' => 'Lot Traceability & Recall Center',
    ],
    'filters' => [
        'all' => 'All',
        'search' => 'Search by Code, Name, or Lot...',
        'export_excel' => 'Export to Excel (XLSX)',
        'print' => 'Print Report',
    ],
    'kpis' => [
        'total_capital' => 'Total Raw Material Capital',
        'expiring_30' => 'Expiring within 30 Days',
        'expiring_60' => 'Expiring within 60 Days',
        'expiring_90' => 'Expiring within 90 Days',
        'expired_value' => 'Expired Inventory Capital',
        'open_po_liability' => 'Committed Procurement Liability',
        'overdue_pos' => 'Overdue Purchase Orders',
        'qc_pass_rate' => 'Overall QC Pass Rate',
        'urgent_reorders' => 'Critical Reorder Materials',
    ],
    'traceability' => [
        'mode_backward' => 'Backward Search: Finished Product Lot ➔ Raw Ingredients',
        'mode_forward' => 'Forward Recall: Contaminated Raw Lot ➔ Shipped Customer Orders',
        'enter_lot' => 'Enter Lot / Batch Number...',
        'execute_search' => 'Search Traceability (Under 60s)',
        'affected_orders' => 'Affected Customer & Clinic Orders',
        'affected_work_orders' => 'Associated Compounding Work Orders',
        'ingredients_used' => 'Consumed Raw Material Lots',
    ],
];

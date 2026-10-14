<?php

return [
    'title' => 'Production Work Orders & Manufacturing',
    'subtitle' => 'Compounding execution, gravimetric clean-room dispensing, and finished good costing.',
    'create_order' => 'Create Work Order',
    'order_number' => 'Work Order #',
    'product' => 'Target Product',
    'finished_lot' => 'Destination Finished Lot #',
    'planned_qty' => 'Planned Units',
    'produced_qty' => 'Actually Produced Units',
    'rejected_qty' => 'In-Process Scrap / Rejects',
    'status' => 'Status',
    'scheduled_start' => 'Scheduled Start Date',
    'started_at' => 'Production Commenced At',
    'completed_at' => 'Finished At',
    'total_material_cost' => 'Total Materials Cost (AED)',
    'unit_cost' => 'Final Unit Landed Cost (AED)',
    'location' => 'Target Stock Warehouse',
    'recipe' => 'Formulation Bill of Materials (BOM)',
    'dispense' => 'Gravimetric Dispense',
    'scanned_barcode' => 'Scan Batch Barcode',
    'actions' => [
        'schedule' => 'Schedule & FEFO Reserve',
        'start_dispensing' => 'Begin Dispensing & Staging',
        'complete' => 'Sign Off & Stock Finished Product',
    ],
    'messages' => [
        'created' => 'Production Work Order drafted successfully.',
        'scheduled' => 'Work order scheduled. FEFO batches successfully reserved for compounding.',
        'dispensing_started' => 'Clean-room dispensing initiated.',
        'material_dispensed' => 'Material successfully dispensed and deducted from lot inventory.',
        'completed' => 'Work Order completed. Finished goods stock auto-credited to warehouse inventory and unit cost calculated.',
    ],
    'errors' => [
        'insufficient_raw_material' => 'Insufficient approved FEFO stock for raw material requirements.',
    ],
];

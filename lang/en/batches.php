<?php

return [
    'title' => 'Raw Material Batches & FEFO Ledger',
    'subtitle' => 'Track batch lots, First-Expired First-Out priorities, bin locations, and movements.',
    'batch_number' => 'Batch Number',
    'supplier_lot' => 'Supplier Lot',
    'material' => 'Raw Material',
    'supplier' => 'Manufacturer',
    'location' => 'Storage Warehouse',
    'bin' => 'Storage Bin / Shelf',
    'current_qty' => 'On-Hand Qty',
    'available_qty' => 'Available (FEFO)',
    'reserved_qty' => 'Reserved for WOs',
    'quarantine_qty' => 'In Quarantine',
    'unit_cost' => 'Landed Cost (AED)',
    'expiry_date' => 'Expiry Date',
    'retest_date' => 'Retest Date',
    'status' => 'Batch Status',
    'actions' => [
        'transfer' => 'Transfer Bin / Warehouse',
        'adjust' => 'Adjust Stock Balance',
        'qc_test' => 'Create QC Inspection',
    ],
    'messages' => [
        'transferred' => 'Batch successfully moved to target warehouse / bin.',
        'adjusted' => 'Batch stock level adjusted and logged into the audit ledger.',
    ],
];

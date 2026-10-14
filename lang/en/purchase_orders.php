<?php

return [
    'title' => 'Purchase Orders & Procurement',
    'subtitle' => 'Manage raw material procurement, shipping freight, customs, and supplier orders.',
    'create_order' => 'Draft Purchase Order',
    'po_number' => 'PO Number',
    'supplier' => 'Supplier',
    'order_date' => 'Order Date',
    'expected_date' => 'Promised Dock Date',
    'destination' => 'Receiving Warehouse',
    'status' => 'Order Status',
    'subtotal' => 'Items Subtotal',
    'freight' => 'Inbound Freight',
    'customs' => 'Customs & Duties',
    'tax' => 'Tax / VAT',
    'grand_total' => 'Grand Total',
    'currency' => 'Currency',
    'line_items' => 'Itemized Materials',
    'ordered_qty' => 'Ordered Qty',
    'received_qty' => 'Received Qty',
    'unit_price' => 'Unit Price',
    'actions' => [
        'submit' => 'Submit for Approval',
        'approve' => 'Approve & Confirm Order',
        'cancel' => 'Cancel Order',
        'print' => 'Print Official PO',
        'receive' => 'Receive at Dock (GRN)',
    ],
    'messages' => [
        'created' => 'Purchase order drafted successfully.',
        'submitted' => 'Purchase order submitted for approval.',
        'approved' => 'Purchase order approved and confirmed with supplier.',
        'cancelled' => 'Purchase order cancelled.',
    ],
    'errors' => [
        'creator_approval_forbidden' => 'Separation of duties: The creator of a purchase order cannot approve orders valued above AED 25,000.',
    ],
];

@props([
    'status' => 'active',
    'label' => null,
])

@php
    $normalized = strtolower(trim($status));
    $type = 'badge-neutral';

    switch ($normalized) {
        case 'active':
        case 'in_stock':
        case 'paid':
        case 'delivered':
        case 'confirmed':
            $type = 'badge-success';
            break;
        case 'processing':
        case 'shipped':
            $type = 'badge-accent';
            break;
        case 'pending':
        case 'low_stock':
        case 'unpaid':
            $type = 'badge-warning';
            break;
        case 'cancelled':
        case 'out_of_stock':
        case 'inactive':
        case 'damaged':
            $type = 'badge-danger';
            break;
    }

    $text = $label ?? __('app.status.' . $normalized, [], null);
    if (!$text || $text === 'app.status.' . $normalized) {
        $text = ucfirst(str_replace('_', ' ', $status));
    }
@endphp

<span class="badge {{ $type }}">
    {{ $text }}
</span>

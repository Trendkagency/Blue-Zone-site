@props([
    'text' => null,
    'title' => null,
    'position' => 'top',
    'type' => 'info', // info, accounting, inventory, warning
])

@php
    $isAr = app()->getLocale() === 'ar';
    $infoText = trim($text ?? (string) $slot);
    
    $typeConfig = match($type) {
        'accounting' => [
            'icon' => 'fa-solid fa-calculator',
            'btnClass' => 'is-accounting',
            'defaultTitle' => $isAr ? 'أثر محاسبي' : 'Accounting Rule',
        ],
        'inventory' => [
            'icon' => 'fa-solid fa-warehouse',
            'btnClass' => 'is-inventory',
            'defaultTitle' => $isAr ? 'أثر مخزني' : 'Inventory Impact',
        ],
        'warning' => [
            'icon' => 'fa-solid fa-triangle-exclamation',
            'btnClass' => 'is-warning',
            'defaultTitle' => $isAr ? 'تنبيه إلزامي' : 'Important Notice',
        ],
        default => [
            'icon' => 'fa-solid fa-circle-info',
            'btnClass' => '',
            'defaultTitle' => $isAr ? 'إرشادات الإدخال' : 'Input Guidance',
        ],
    };
    
    $resolvedTitle = $title ?? $typeConfig['defaultTitle'];
@endphp

@if(!empty($infoText))
<span class="bz-info-wrapper" data-bz-info-bound="true">
    <button type="button" 
            class="bz-info-btn {{ $typeConfig['btnClass'] }}"
            data-bz-info="{{ $infoText }}"
            data-bz-info-title="{{ $resolvedTitle }}"
            data-bz-info-type="{{ $type }}"
            data-bz-info-bound="true"
            aria-label="{{ $resolvedTitle }}"
            tabindex="0">
        <i class="{{ $typeConfig['icon'] }}"></i>
    </button>
</span>
@endif

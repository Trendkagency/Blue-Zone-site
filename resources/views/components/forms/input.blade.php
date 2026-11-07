@props([
    'name',
    'label' => null,
    'type' => 'text',
    'value' => null,
    'placeholder' => null,
    'required' => false,
    'hint' => null,
    'error' => null,
    'unit' => null,
])

@php
    $inputId = $attributes->get('id', $name);
    $isNumeric = ($type === 'number');
    $step = $attributes->get('step', '1');
    $allowDecimal = ($step !== '1' && $step !== 1);
    $isAr = app()->getLocale() === 'ar';

    // Auto-detect unit if not explicitly provided
    if ($isNumeric && !$unit) {
        if (str_contains($name, 'price') || str_contains($name, 'cost') || str_contains($name, 'overhead')) {
            $unit = $isAr ? 'ر.س' : 'SAR';
        } elseif (str_contains($name, 'discount')) {
            $unit = '%';
        } elseif (str_contains($name, 'stock') || str_contains($name, 'qty') || str_contains($name, 'threshold')) {
            $unit = $isAr ? 'وحدة' : 'Units';
        } elseif (str_contains($name, 'month')) {
            $unit = $isAr ? 'شهر' : 'mo';
        } elseif (str_contains($name, 'day')) {
            $unit = $isAr ? 'يوم' : 'days';
        }
    }
@endphp

<div class="form-group" style="position: relative;">
    @if($label)
        <label for="{{ $inputId }}" class="form-label" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
            <span>
                {{ $label }}
                @if($required)
                    <span class="required-mark" style="color: #ef4444; font-weight: 800;">*</span>
                @endif
            </span>
            @if($isNumeric)
                <span class="badge" style="font-size: 0.65rem; font-family: monospace; padding: 0.15rem 0.45rem; background: rgba(42, 143, 194, 0.12); color: #2A8FC2; border: 1px solid rgba(42, 143, 194, 0.25); border-radius: 4px; font-weight: 700;">
                    <i class="fa-solid fa-hashtag mr-0.5 ml-0.5"></i> {{ $isAr ? 'حقل أرقام فقط' : 'Numbers Only' }}
                </span>
            @endif
        </label>
    @endif

    <div style="position: relative; display: flex; align-items: center; width: 100%;">
        <input 
            type="{{ $type }}" 
            name="{{ $name }}" 
            id="{{ $inputId }}" 
            value="{{ $value }}"
            placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }}
            @if($isNumeric)
                inputmode="{{ $allowDecimal ? 'decimal' : 'numeric' }}"
                onkeydown="return (window.bzEnforceNumberKey ? window.bzEnforceNumberKey(event, this, {{ $allowDecimal ? 'true' : 'false' }}) : true);"
                oninput="if(window.bzSanitizeNumberField) window.bzSanitizeNumberField(this, {{ $allowDecimal ? 'true' : 'false' }});"
            @endif
            {{ $attributes->merge(['class' => 'form-control ' . ($error ? 'is-invalid' : '') . ($isNumeric ? ' font-mono font-bold' : '')]) }}
            style="{{ $isNumeric && $unit ? ($isAr ? 'padding-left: 2.85rem;' : 'padding-right: 2.85rem;') : '' }}"
        >
        @if($isNumeric && $unit)
            <span style="position: absolute; {{ $isAr ? 'left: 0.85rem;' : 'right: 0.85rem;' }} top: 50%; transform: translateY(-50%); font-size: 0.75rem; font-weight: 800; color: var(--color-muted, #94a3b8); pointer-events: none; text-transform: uppercase;">
                {{ $unit }}
            </span>
        @endif
    </div>

    @if($hint)
        <span class="form-hint" style="display: block; font-size: 0.75rem; color: var(--color-muted, #94a3b8); margin-top: 0.25rem;">{{ $hint }}</span>
    @endif

    @if($error)
        <span class="invalid-feedback" style="display: block; color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem; font-weight: 700;">{{ $error }}</span>
    @endif
</div>

@props([
    'name',
    'label' => null,
    'value' => null,
    'rows' => 4,
    'placeholder' => null,
    'required' => false,
    'hint' => null,
    'error' => null,
    'info' => null,
    'infoType' => 'info',
])

<div class="form-group">
    @if($label)
        <label for="{{ $name }}" class="form-label">
            <span style="display: inline-flex; align-items: center; gap: 0.25rem;">
                {{ $label }}
                @if($required)
                    <span class="required-mark">*</span>
                @endif
                @if($info)
                    <x-info-tooltip :text="$info" :type="$infoType" />
                @endif
            </span>
        </label>
    @endif

    <textarea 
        name="{{ $name }}" 
        id="{{ $name }}" 
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes->merge(['class' => 'form-textarea ' . ($error ? 'is-invalid' : '')]) }}
    >{{ old($name, $value ?? ($slot->isNotEmpty() ? $slot : '')) }}</textarea>

    @if($hint)
        <span class="form-hint">{{ $hint }}</span>
    @endif

    @if($error)
        <span class="invalid-feedback">{{ $error }}</span>
    @endif
</div>

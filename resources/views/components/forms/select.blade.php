@props([
    'name',
    'label' => null,
    'options' => [],
    'selected' => null,
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

    <select 
        name="{{ $name }}" 
        id="{{ $name }}" 
        {{ $required ? 'required' : '' }}
        {{ $attributes->merge(['class' => 'form-select ' . ($error ? 'is-invalid' : '')]) }}
    >
        @if($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif

        @foreach($options as $val => $optLabel)
            <option value="{{ $val }}" {{ (string)$selected === (string)$val ? 'selected' : '' }}>
                {{ $optLabel }}
            </option>
        @endforeach
    </select>

    @if($hint)
        <span class="form-hint">{{ $hint }}</span>
    @endif

    @if($error)
        <span class="invalid-feedback">{{ $error }}</span>
    @endif
</div>

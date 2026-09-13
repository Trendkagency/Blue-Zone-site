@props([
    'name',
    'label' => null,
    'type' => 'text',
    'value' => null,
    'placeholder' => null,
    'required' => false,
    'hint' => null,
    'error' => null,
])

@php
    $inputId = $attributes->get('id', $name);
@endphp

<div class="form-group">
    @if($label)
        <label for="{{ $inputId }}" class="form-label">
            <span>{{ $label }}</span>
            @if($required)
                <span class="required-mark">*</span>
            @endif
        </label>
    @endif

    <input 
        type="{{ $type }}" 
        name="{{ $name }}" 
        id="{{ $inputId }}" 
        value="{{ $value }}"
        placeholder="{{ $placeholder }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes->merge(['class' => 'form-control ' . ($error ? 'is-invalid' : '')]) }}
    >

    @if($hint)
        <span class="form-hint">{{ $hint }}</span>
    @endif

    @if($error)
        <span class="invalid-feedback">{{ $error }}</span>
    @endif
</div>

@props([
    'name',
    'label' => null,
    'value' => null,
    'rows' => 4,
    'placeholder' => null,
    'required' => false,
    'hint' => null,
    'error' => null,
])

<div class="form-group">
    @if($label)
        <label for="{{ $name }}" class="form-label">
            <span>{{ $label }}</span>
            @if($required)
                <span class="required-mark">*</span>
            @endif
        </label>
    @endif

    <textarea 
        name="{{ $name }}" 
        id="{{ $name }}" 
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes->merge(['class' => 'form-textarea ' . ($error ? 'is-invalid' : '')]) }}
    >{{ $value }}</textarea>

    @if($hint)
        <span class="form-hint">{{ $hint }}</span>
    @endif

    @if($error)
        <span class="invalid-feedback">{{ $error }}</span>
    @endif
</div>

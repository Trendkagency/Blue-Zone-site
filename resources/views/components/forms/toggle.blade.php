@props([
    'name',
    'label' => null,
    'checked' => false,
    'description' => null,
])

<div style="display: flex; align-items: center; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid var(--color-border);">
    <div>
        @if($label)
            <div style="font-size: 0.9375rem; font-weight: 600; color: var(--color-text-primary);">
                {{ $label }}
            </div>
        @endif
        @if($description)
            <div style="font-size: 0.8125rem; color: var(--color-text-muted);">
                {{ $description }}
            </div>
        @endif
    </div>

    <label class="toggle-switch">
        <input type="checkbox" name="{{ $name }}" value="1" {{ $checked ? 'checked' : '' }}>
        <span class="toggle-slider"></span>
    </label>
</div>

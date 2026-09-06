@props([
    'title' => null,
    'description' => null,
    'actionLabel' => null,
    'actionUrl' => null,
])

<div class="empty-state">
    <div class="empty-state-icon">
        <svg width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
        </svg>
    </div>

    <h3 class="empty-state-title">
        {{ $title ?? __('app.empty.title') }}
    </h3>

    <p class="empty-state-desc">
        {{ $description ?? __('app.empty.description') }}
    </p>

    @if($actionLabel && $actionUrl)
        <a href="{{ $actionUrl }}" class="btn btn-secondary btn-sm" style="margin-top: 0.5rem;">
            {{ $actionLabel }}
        </a>
    @endif
</div>

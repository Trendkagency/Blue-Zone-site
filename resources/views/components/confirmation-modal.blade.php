@props([
    'id' => 'confirmModal',
    'title' => 'Confirm Destructive Action',
    'message' => 'Are you sure you want to proceed with this operation? This action cannot be reversed.',
    'confirmText' => 'Confirm & Execute',
    'confirmType' => 'btn-danger',
])

<div id="{{ $id }}" class="modal-backdrop">
    <div class="modal-dialog">
        <div class="modal-header">
            <h4 class="modal-title" style="display: flex; align-items: center; gap: 0.5rem;">
                <i class="fa-solid fa-triangle-exclamation text-danger"></i>
                {{ $title }}
            </h4>
            <button type="button" class="btn btn-ghost btn-sm" onclick="closeModal('{{ $id }}')" aria-label="Close">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="modal-body">
            <p style="color: var(--color-text-secondary); margin: 0; line-height: 1.6;">
                {{ $message }}
            </p>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeModal('{{ $id }}')">
                {{ __('app.actions.cancel') }}
            </button>
            <button type="button" class="btn {{ $confirmType }}" onclick="closeModal('{{ $id }}')">
                {{ $confirmText }}
            </button>
        </div>
    </div>
</div>

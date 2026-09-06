@props([
    'currentPage' => 1,
    'totalPages' => 1,
    'totalItems' => 0,
])

<div class="pagination-container">
    <div class="pagination-meta">
        Showing Page <strong>{{ $currentPage }}</strong> of <strong>{{ max(1, $totalPages) }}</strong>
    </div>

    <ul class="pagination-list">
        <li class="pagination-item {{ $currentPage <= 1 ? 'disabled' : '' }}">
            <a href="#" class="pagination-link" aria-label="Previous Page">
                ‹
            </a>
        </li>

        @for($i = 1; $i <= max(1, $totalPages); $i++)
            <li class="pagination-item {{ $i === $currentPage ? 'active' : '' }}">
                <a href="#" class="pagination-link">
                    {{ $i }}
                </a>
            </li>
        @endfor

        <li class="pagination-item {{ $currentPage >= $totalPages ? 'disabled' : '' }}">
            <a href="#" class="pagination-link" aria-label="Next Page">
                ›
            </a>
        </li>
    </ul>
</div>

@props([
    'paginator' => null,
    'currentPage' => 1,
    'totalPages' => 1,
    'totalItems' => 0,
    'pageName' => 'page',
])

@php
    // If a LengthAwarePaginator is passed directly
    if ($paginator instanceof \Illuminate\Contracts\Pagination\Paginator || $paginator instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator) {
        $curPage = $paginator->currentPage();
        $lastPage = $paginator->lastPage();
        $total = method_exists($paginator, 'total') ? $paginator->total() : $paginator->count();
        $from = method_exists($paginator, 'firstItem') ? $paginator->firstItem() : (($curPage - 1) * $paginator->perPage() + 1);
        $to = method_exists($paginator, 'lastItem') ? $paginator->lastItem() : ($from + $paginator->count() - 1);
    } else {
        $curPage = (int) ($currentPage ?: 1);
        $lastPage = max(1, (int) ($totalPages ?: 1));
        $total = (int) ($totalItems ?: 0);
        $from = $total > 0 ? (($curPage - 1) * 15 + 1) : 0;
        $to = $total > 0 ? min($total, $curPage * 15) : 0;
    }

    $isAr = app()->getLocale() === 'ar';

    // Helper for URL generation with preserved query strings
    $getPageUrl = function ($pageNum) use ($paginator, $pageName) {
        if ($paginator && method_exists($paginator, 'url')) {
            return $paginator->url($pageNum);
        }
        return request()->fullUrlWithQuery([$pageName => $pageNum]);
    };

    // Calculate smart windowed page numbers
    $pages = [];
    if ($lastPage <= 7) {
        for ($i = 1; $i <= $lastPage; $i++) {
            $pages[] = $i;
        }
    } else {
        if ($curPage <= 4) {
            for ($i = 1; $i <= 5; $i++) {
                $pages[] = $i;
            }
            $pages[] = '...';
            $pages[] = $lastPage;
        } elseif ($curPage >= $lastPage - 3) {
            $pages[] = 1;
            $pages[] = '...';
            for ($i = $lastPage - 4; $i <= $lastPage; $i++) {
                $pages[] = $i;
            }
        } else {
            $pages[] = 1;
            $pages[] = '...';
            $pages[] = $curPage - 1;
            $pages[] = $curPage;
            $pages[] = $curPage + 1;
            $pages[] = '...';
            $pages[] = $lastPage;
        }
    }
@endphp

<div class="pagination-wrapper flex items-center justify-between flex-wrap gap-4 px-4 py-3.5 bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300">
    <!-- Meta Info -->
    <div class="pagination-meta text-xs font-medium text-slate-500 dark:text-slate-400">
        @if($total > 0)
            @if($isAr)
                عرض الصفحة <strong class="text-slate-900 dark:text-white font-bold">{{ $curPage }}</strong> من <strong class="text-slate-900 dark:text-white font-bold">{{ $lastPage }}</strong> (إجمالي <strong class="text-slate-900 dark:text-white font-bold">{{ number_format($total) }}</strong> سجل)
            @else
                Showing Page <strong class="text-slate-900 dark:text-white font-bold">{{ $curPage }}</strong> of <strong class="text-slate-900 dark:text-white font-bold">{{ $lastPage }}</strong> (Total <strong class="text-slate-900 dark:text-white font-bold">{{ number_format($total) }}</strong> records)
            @endif
        @else
            @if($isAr)
                الصفحة <strong class="text-slate-900 dark:text-white font-bold">{{ $curPage }}</strong> من <strong class="text-slate-900 dark:text-white font-bold">{{ $lastPage }}</strong>
            @else
                Page <strong class="text-slate-900 dark:text-white font-bold">{{ $curPage }}</strong> of <strong class="text-slate-900 dark:text-white font-bold">{{ $lastPage }}</strong>
            @endif
        @endif
    </div>

    <!-- Navigation Buttons -->
    @if($lastPage > 1)
        <nav class="pagination-nav inline-flex items-center gap-1" aria-label="Pagination Navigation">
            <!-- Previous Button -->
            @if($curPage > 1)
                <a href="{{ $getPageUrl($curPage - 1) }}" 
                   class="pagination-btn inline-flex items-center justify-center min-w-[34px] h-[34px] px-2.5 rounded-lg text-xs font-bold border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 hover:border-slate-300 dark:hover:border-slate-600 transition-all shadow-sm"
                   aria-label="{{ $isAr ? 'الصفحة السابقة' : 'Previous Page' }}">
                    <i class="fa-solid fa-chevron-right rtl:rotate-180"></i>
                </a>
            @else
                <span class="pagination-btn disabled inline-flex items-center justify-center min-w-[34px] h-[34px] px-2.5 rounded-lg text-xs font-bold border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50 text-slate-300 dark:text-slate-600 cursor-not-allowed pointer-events-none">
                    <i class="fa-solid fa-chevron-right rtl:rotate-180"></i>
                </span>
            @endif

            <!-- Numbered Pages -->
            @foreach($pages as $p)
                @if($p === '...')
                    <span class="inline-flex items-center justify-center min-w-[30px] h-[34px] text-xs font-bold text-slate-400">...</span>
                @elseif($p == $curPage)
                    <span class="pagination-btn active inline-flex items-center justify-center min-w-[34px] h-[34px] px-3 rounded-lg text-xs font-black bg-[#0A4F78] dark:bg-[#0284C7] text-white shadow-md cursor-default">
                        {{ $p }}
                    </span>
                @else
                    <a href="{{ $getPageUrl($p) }}" 
                       class="pagination-btn inline-flex items-center justify-center min-w-[34px] h-[34px] px-3 rounded-lg text-xs font-bold border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 hover:border-slate-300 dark:hover:border-slate-600 transition-all shadow-sm">
                        {{ $p }}
                    </a>
                @endif
            @endforeach

            <!-- Next Button -->
            @if($curPage < $lastPage)
                <a href="{{ $getPageUrl($curPage + 1) }}" 
                   class="pagination-btn inline-flex items-center justify-center min-w-[34px] h-[34px] px-2.5 rounded-lg text-xs font-bold border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 hover:border-slate-300 dark:hover:border-slate-600 transition-all shadow-sm"
                   aria-label="{{ $isAr ? 'الصفحة التالية' : 'Next Page' }}">
                    <i class="fa-solid fa-chevron-left rtl:rotate-180"></i>
                </a>
            @else
                <span class="pagination-btn disabled inline-flex items-center justify-center min-w-[34px] h-[34px] px-2.5 rounded-lg text-xs font-bold border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50 text-slate-300 dark:text-slate-600 cursor-not-allowed pointer-events-none">
                    <i class="fa-solid fa-chevron-left rtl:rotate-180"></i>
                </span>
            @endif
        </nav>
    @endif
</div>

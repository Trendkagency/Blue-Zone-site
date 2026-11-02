@forelse($collections as $item)
    <tr class="collection-row border-b border-slate-200/80 dark:border-slate-800 hover:bg-sky-50/50 dark:hover:bg-slate-800/50 transition-colors duration-150 group"
        data-id="{{ $item->id }}"
        data-excl="{{ (float)$item->amount_excl_vat }}"
        data-vat="{{ (float)$item->vat_amount }}"
        data-gross="{{ (float)$item->gross_amount }}">
        
        <!-- 1. Entry Date -->
        <td class="py-3 px-3.5 text-center whitespace-nowrap">
            <span class="inline-flex items-center gap-1.5 font-semibold text-slate-800 dark:text-slate-200 tabular-nums text-xs">
                <i class="fa-regular fa-calendar text-slate-400 group-hover:text-sky-500 transition text-[11px]"></i>
                {{ $item->entry_date ? $item->entry_date->format('Y-m-d') : '-' }}
            </span>
        </td>

        <!-- 2. Collection No. -->
        <td class="py-3 px-3.5 text-center whitespace-nowrap">
            <span class="inline-flex items-center gap-1 font-mono font-bold text-xs px-2.5 py-1 rounded-md bg-sky-50 dark:bg-sky-950/40 text-[#0A4F78] dark:text-sky-300 border border-sky-200/70 dark:border-sky-800/70 shadow-2xs">
                <i class="fa-solid fa-receipt text-[10px] text-sky-500"></i>
                {{ $item->collection_number }}
            </span>
        </td>

        <!-- 3. Category -->
        <td class="py-3 px-3.5 text-center whitespace-nowrap">
            @php
                $catMeta = $item->category_meta;
                $catIcons = [
                    'online_sales_auto' => 'fa-cart-shopping',
                    'sales' => 'fa-briefcase',
                    'supplier_refund' => 'fa-arrow-rotate-left',
                    'assets_disposal' => 'fa-landmark',
                    'non_operational_revenue' => 'fa-coins',
                    'equities' => 'fa-scale-balanced',
                ];
                $catIcon = $catIcons[$item->category] ?? 'fa-tag';
            @endphp
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold shadow-2xs transition-transform group-hover:scale-[1.02]"
                  style="background-color: {{ $catMeta['bg_color'] }}; color: {{ $catMeta['badge_color'] }}; border: 1px solid {{ $catMeta['badge_color'] }}33;">
                @if($item->category === 'online_sales_auto')
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-sky-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-sky-500"></span>
                    </span>
                @else
                    <i class="fa-solid {{ $catIcon }} text-[10px] opacity-80"></i>
                @endif
                <span>{{ app()->getLocale() === 'ar' ? ($catMeta['name_ar'] ?? $item->category) : ($catMeta['name_en'] ?? $item->category) }}</span>
            </span>
        </td>

        <!-- 4. Description & Linked Order -->
        <td class="py-3 px-3.5 font-medium text-slate-800 dark:text-slate-200">
            @if($item->order_id && $item->order)
                <a href="{{ route('admin.orders.show', $item->order_id) }}" class="inline-flex items-center gap-1.5 font-bold text-[#0A4F78] dark:text-sky-400 hover:text-sky-600 transition group/link" target="_blank" title="{{ app()->getLocale() === 'ar' ? 'عرض فاتورة الطلب' : 'View Order Invoice' }}">
                    <span class="w-5 h-5 rounded bg-sky-100 dark:bg-sky-900/50 text-sky-700 dark:text-sky-300 flex items-center justify-center text-[10px] group-hover/link:bg-sky-200 transition shrink-0">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    </span>
                    <span class="underline decoration-sky-300 dark:decoration-sky-600 underline-offset-2">{{ $item->description ?: ('Invoice #' . ($item->order->invoice_number ?? $item->order->order_number)) }}</span>
                </a>
            @else
                <span class="line-clamp-2 text-slate-700 dark:text-slate-300">{{ $item->description ?: '-' }}</span>
            @endif
        </td>

        <!-- 5. Customer Code -->
        <td class="py-3 px-3.5 text-center whitespace-nowrap">
            @if($item->customer_code)
                <span class="inline-block px-2 py-0.5 text-[11px] font-mono font-bold rounded bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                    {{ $item->customer_code }}
                </span>
            @else
                <span class="text-slate-400 text-xs">-</span>
            @endif
        </td>

        <!-- 6. Customer / Source -->
        <td class="py-3 px-3.5 font-bold text-slate-900 dark:text-white whitespace-nowrap">
            <div class="flex items-center gap-2">
                <div class="w-6 h-6 rounded-full bg-gradient-to-tr from-[#0A4F78] to-[#2A8FC2] text-white flex items-center justify-center text-[10px] font-bold shrink-0 shadow-2xs">
                    {{ mb_substr($item->customer_source ?: 'C', 0, 1) }}
                </div>
                <span class="truncate max-w-[200px]" title="{{ $item->customer_source }}">{{ $item->customer_source ?: '-' }}</span>
            </div>
        </td>

        <!-- 7. Amount Excl. VAT -->
        <td class="py-3 px-3.5 text-right whitespace-nowrap tabular-nums">
            <span class="font-bold text-slate-800 dark:text-slate-200 text-xs">{{ number_format($item->amount_excl_vat, 2) }}</span>
            <span class="text-[10px] text-slate-500 font-semibold ml-0.5">SAR</span>
        </td>

        <!-- 8. VAT (15%) -->
        <td class="py-3 px-3.5 text-right whitespace-nowrap tabular-nums">
            <span class="font-bold text-amber-700 dark:text-amber-400 text-xs">{{ number_format($item->vat_amount, 2) }}</span>
            <span class="text-[10px] text-amber-600/70 font-semibold ml-0.5">SAR</span>
        </td>

        <!-- 9. Gross Amount (Creative Prominent Highlight) -->
        <td class="py-3 px-3.5 text-right whitespace-nowrap tabular-nums">
            <span class="inline-flex items-center gap-1 font-black text-slate-950 dark:text-white bg-slate-100 dark:bg-slate-800/90 px-2.5 py-1 rounded-md border border-slate-200/90 dark:border-slate-700 shadow-2xs group-hover:border-sky-300 dark:group-hover:border-sky-700 transition">
                <span class="text-xs sm:text-[13px] font-black tracking-tight">{{ number_format($item->gross_amount, 2) }}</span>
                <span class="text-[10px] font-bold text-sky-700 dark:text-sky-300">SAR</span>
            </span>
        </td>

        <!-- 10. Payment Method / Account -->
        <td class="py-3 px-3.5 text-center whitespace-nowrap">
            @if($item->financialAccount)
                <div class="flex flex-col items-center gap-0.5">
                    <span class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-800 dark:text-slate-200">
                        @if($item->financialAccount->type === 'bank')
                            <i class="fa-solid fa-building-columns text-sky-600 dark:text-sky-400 text-xs"></i>
                        @elseif($item->financialAccount->type === 'cash')
                            <i class="fa-solid fa-cash-register text-emerald-600 dark:text-emerald-400 text-xs"></i>
                        @else
                            <i class="fa-solid fa-credit-card text-purple-600 dark:text-purple-400 text-xs"></i>
                        @endif
                        <span>{{ app()->getLocale() === 'ar' ? ($item->financialAccount->name_ar ?: $item->financialAccount->name_en) : ($item->financialAccount->name_en ?: $item->financialAccount->name_ar) }}</span>
                    </span>
                    @if($item->cheque_number)
                        <span class="text-[10px] font-bold text-purple-700 dark:text-purple-300 bg-purple-50 dark:bg-purple-950/40 px-1.5 py-0.5 rounded border border-purple-200 dark:border-purple-800 inline-flex items-center gap-1">
                            <i class="fa-solid fa-money-check-dollar text-[9px]"></i> CHQ #{{ $item->cheque_number }} ({{ $item->cheque_bank }})
                        </span>
                    @endif
                </div>
            @else
                <span class="inline-flex items-center gap-1 text-slate-700 dark:text-slate-300 text-xs font-semibold capitalize">
                    <i class="fa-solid fa-wallet text-slate-400 text-xs"></i>
                    {{ str_replace('_', ' ', $item->payment_method) }}
                </span>
            @endif
        </td>

        <!-- 11. Action Buttons -->
        <td class="py-3 px-3.5 text-center whitespace-nowrap">
            <a href="{{ route('admin.finance.collections.voucher', $item->id) }}" target="_blank"
               class="inline-flex items-center justify-center gap-1.5 px-2.5 py-1.5 rounded-lg text-xs font-bold bg-amber-500/10 hover:bg-amber-500 text-amber-700 dark:text-amber-300 hover:text-white transition duration-150 border border-amber-300/40 dark:border-amber-700/40 shadow-2xs group/btn"
               title="{{ app()->getLocale() === 'ar' ? 'طباعة سند القبض الرسمي' : 'Print Official Receipt Voucher' }}">
                <i class="fa-solid fa-receipt text-xs group-hover/btn:scale-110 transition"></i>
                <span class="hidden sm:inline text-[11px]">{{ app()->getLocale() === 'ar' ? 'السند' : 'Voucher' }}</span>
            </a>
        </td>
    </tr>
@empty
    <tr id="emptyTableRow">
        <td colspan="11" class="py-14 text-center text-slate-500 dark:text-slate-400 font-bold">
            <div class="flex flex-col items-center justify-center gap-2.5 max-w-sm mx-auto">
                <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center text-2xl shadow-inner">
                    <i class="fa-solid fa-inbox"></i>
                </div>
                <div class="text-sm font-black text-slate-800 dark:text-slate-200">
                    {{ app()->getLocale() === 'ar' ? 'لا توجد قيود تحصيل مسجلة تطابق البحث' : 'No collection entries found' }}
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 text-center m-0">
                    {{ app()->getLocale() === 'ar' ? 'جرب تغيير معايير التصفية أو قم بتسجيل قيد تحصيل جديد' : 'Try adjusting your filters or record a new collection entry above.' }}
                </p>
            </div>
        </td>
    </tr>
@endforelse


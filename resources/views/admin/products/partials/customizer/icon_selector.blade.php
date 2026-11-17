@php
    $rawVal = $value ?? 'fa-microchip';
    $cleanVal = preg_replace('/^(fa-solid|fa-regular|fa-brands|fas|far|fab)\s+/', '', trim($rawVal)) ?: 'fa-microchip';
    $fieldId = $id ?? ('bz_icon_' . uniqid());
@endphp

<div class="bz-icon-picker-field relative {{ $wrapperClass ?? '' }}">
    @if(!empty($label))
        <label class="block text-[10px] font-bold text-slate-500 mb-0.5" for="{{ $fieldId }}">
            {{ $label }}
        </label>
    @endif
    
    <input type="hidden" 
           name="{{ $name }}" 
           id="{{ $fieldId }}" 
           value="{{ $cleanVal }}" 
           class="bz-icon-input"
           @if(!empty($onchange)) onchange="{{ $onchange }}" @endif>

    <button type="button" 
            onclick="BZIconPicker.open(this)"
            title="{{ $isAr ?? false ? 'اضغط لتغيير الأيقونة من المكتبة' : 'Click to select icon from clinical library' }}"
            class="w-full flex items-center justify-between gap-1.5 px-2.5 py-1.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-[#031827] text-slate-800 dark:text-slate-200 hover:border-teal-500 hover:shadow-sm transition-all text-xs font-mono cursor-pointer group shadow-inner">
        <span class="flex items-center gap-2 truncate">
            <span class="w-6 h-6 rounded-lg bg-teal-500/10 text-teal-600 dark:text-teal-400 flex items-center justify-center text-xs shrink-0 group-hover:scale-110 transition-transform bz-icon-preview">
                <i class="fa-solid {{ $cleanVal }}"></i>
            </span>
            <span class="font-bold text-[11px] truncate bz-icon-name text-slate-700 dark:text-slate-300">{{ $cleanVal }}</span>
        </span>
        <i class="fa-solid fa-chevron-down text-[9px] text-slate-400 group-hover:text-teal-500 transition-colors shrink-0"></i>
    </button>
</div>

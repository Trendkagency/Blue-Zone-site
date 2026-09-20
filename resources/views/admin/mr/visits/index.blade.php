<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'سجل الزيارات الميدانية وتتبع الـ GPS' : 'Executed Visits & GPS Audit Log'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'مراجعة وتدقيق الزيارات الميدانية المنفذة، أوقات الوصول والانصراف، والتحقق من النطاق الجغرافي' : 'Audit field visits check-in/out timestamps, duration, distance from clinic, and GPS verification flags.'"
    :breadcrumbs="[
        (app()->getLocale() === 'ar' ? 'نظام المندوب الطبي' : 'MR CRM') => route('admin.mr.live-map'),
        (app()->getLocale() === 'ar' ? 'سجل الزيارات' : 'Visits Log') => route('admin.mr.visits.index')
    ]"
>
    <!-- Filter Card -->
    <div class="card p-4 mb-6">
        <form method="GET" action="{{ route('admin.mr.visits.index') }}" class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">
                        {{ app()->getLocale() === 'ar' ? 'دورة الزيارة' : 'Visit Cycle' }}
                    </label>
                    <select name="cycle_id" onchange="this.form.submit()" class="form-select text-sm py-1.5 px-3">
                        <option value="">{{ app()->getLocale() === 'ar' ? 'جميع الدورات' : 'All Cycles' }}</option>
                        @foreach($cycles as $c)
                            <option value="{{ $c->id }}" {{ $selectedCycleId == $c->id ? 'selected' : '' }}>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">
                        {{ app()->getLocale() === 'ar' ? 'المندوب الطبي' : 'Medical Representative' }}
                    </label>
                    <select name="mr_id" onchange="this.form.submit()" class="form-select text-sm py-1.5 px-3">
                        <option value="">{{ app()->getLocale() === 'ar' ? 'جميع المناديب' : 'All Reps' }}</option>
                        @foreach($medicalReps as $rep)
                            <option value="{{ $rep->id }}" {{ request('mr_id') == $rep->id ? 'selected' : '' }}>
                                {{ $rep->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">
                        {{ app()->getLocale() === 'ar' ? 'التحقق الجغرافي GPS' : 'GPS Verification' }}
                    </label>
                    <select name="gps_status" onchange="this.form.submit()" class="form-select text-sm py-1.5 px-3">
                        <option value="">{{ app()->getLocale() === 'ar' ? 'الكل' : 'All' }}</option>
                        <option value="verified" {{ request('gps_status') === 'verified' ? 'selected' : '' }}>
                            ✓ {{ app()->getLocale() === 'ar' ? 'مؤكد جغرافياً فقط' : 'Verified Within Radius' }}
                        </option>
                        <option value="unverified" {{ request('gps_status') === 'unverified' ? 'selected' : '' }}>
                            ⚠️ {{ app()->getLocale() === 'ar' ? 'تجاوز النطاق / غير مؤكد' : 'Flagged (Outside Radius)' }}
                        </option>
                    </select>
                </div>
            </div>

            <div class="pt-5">
                <a href="{{ route('admin.mr.visits.index') }}" class="btn btn-outline text-xs">
                    <i class="fa-solid fa-arrows-rotate mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'إعادة ضبط' : 'Reset' }}
                </a>
            </div>
        </form>
    </div>

    <!-- Visits Table -->
    <div class="card p-0 overflow-hidden shadow-sm border border-gray-100 dark:border-gray-800 mb-8">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800/60 text-gray-600 dark:text-gray-300 font-semibold border-b border-gray-200 dark:border-gray-700 text-xs">
                    <tr>
                        <th class="px-5 py-3.5">{{ app()->getLocale() === 'ar' ? 'المندوب' : 'Representative' }}</th>
                        <th class="px-5 py-3.5">{{ app()->getLocale() === 'ar' ? 'الطبيب / العيادة' : 'Doctor & Facility' }}</th>
                        <th class="px-5 py-3.5">{{ app()->getLocale() === 'ar' ? 'وقت تسجيل الوصول' : 'Check-In' }}</th>
                        <th class="px-5 py-3.5">{{ app()->getLocale() === 'ar' ? 'وقت الانصراف' : 'Check-Out' }}</th>
                        <th class="px-5 py-3.5 text-center">{{ app()->getLocale() === 'ar' ? 'المدة' : 'Duration' }}</th>
                        <th class="px-5 py-3.5 text-center">{{ app()->getLocale() === 'ar' ? 'التحقق GPS والمسافة' : 'GPS Verification & Distance' }}</th>
                        <th class="px-5 py-3.5 text-center">{{ app()->getLocale() === 'ar' ? 'النتيجة' : 'Outcome' }}</th>
                        <th class="px-5 py-3.5">{{ app()->getLocale() === 'ar' ? 'الملاحظات' : 'Visit Notes' }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($visits as $v)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/40 transition-colors">
                            <td class="px-5 py-4 font-bold text-gray-900 dark:text-white">
                                {{ $v->representative?->name ?? 'Rep' }}
                            </td>
                            <td class="px-5 py-4">
                                <a href="{{ route('admin.mr.contacts.show', $v->contact_id) }}" class="font-bold text-gray-900 dark:text-white hover:text-sky-500">
                                    {{ $v->contact?->name ?? 'Doctor' }}
                                </a>
                                <div class="text-xs text-gray-500 mt-0.5">
                                    {{ $v->contact?->specialty?->name }} • {{ $v->contact?->hospital_clinic_name }}
                                </div>
                            </td>
                            <td class="px-5 py-4 text-xs">
                                <div>{{ $v->checkin_at?->format('d M Y, H:i') }}</div>
                                <div class="text-[11px] text-gray-400">{{ $v->checkin_at?->diffForHumans() }}</div>
                            </td>
                            <td class="px-5 py-4 text-xs">
                                @if($v->checkout_at)
                                    <div>{{ $v->checkout_at->format('d M Y, H:i') }}</div>
                                    <div class="text-[11px] text-gray-400">{{ $v->checkout_at->diffForHumans() }}</div>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">
                                        In Progress
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center font-bold text-gray-700 dark:text-gray-300">
                                {{ $v->duration_minutes ? $v->duration_minutes . ' min' : '—' }}
                            </td>
                            <td class="px-5 py-4 text-center">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold {{ $v->gps_verified ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-400' : 'bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-400' }}">
                                    <i class="fa-solid {{ $v->gps_verified ? 'fa-circle-check' : 'fa-triangle-exclamation' }}"></i>
                                    {{ $v->gps_verified ? 'Verified' : 'Flagged' }}
                                </span>
                                @if($v->distance_from_contact_m)
                                    <div class="text-[11px] font-mono text-gray-500 mt-0.5">
                                        {{ $v->distance_from_contact_m }}m from clinic
                                    </div>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center">
                                @if($v->outcome)
                                    <span class="badge badge-outline text-[11px] font-bold">
                                        {{ ucfirst($v->outcome) }}
                                    </span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-xs text-gray-600 dark:text-gray-400 max-w-xs truncate">
                                {{ $v->notes ?: '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-12 text-center text-gray-400">
                                <i class="fa-solid fa-list-check text-4xl mb-2"></i>
                                <p class="text-sm font-semibold">{{ app()->getLocale() === 'ar' ? 'لا توجد زيارات مسجلة مطابقة للبحث.' : 'No visits recorded matching your criteria.' }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($visits->hasPages())
            <div class="p-4 border-t border-gray-100 dark:border-gray-800">
                {{ $visits->links() }}
            </div>
        @endif
    </div>
</x-layouts.admin>

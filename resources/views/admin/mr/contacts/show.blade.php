<x-layouts.admin 
    :pageTitle="$contact->name" 
    :pageSubtitle="($contact->hospital_clinic_name ? $contact->hospital_clinic_name . ' • ' : '') . ($contact->specialty?->name ?? 'Doctor') . ' • Class ' . ($contact->classification?->code ?? 'N/A')"
    :breadcrumbs="[
        (app()->getLocale() === 'ar' ? 'نظام المندوب الطبي' : 'MR CRM') => route('admin.mr.live-map'),
        (app()->getLocale() === 'ar' ? 'الأطباء والعيادات' : 'Doctors & Clinics') => route('admin.mr.contacts.index'),
        $contact->name => route('admin.mr.contacts.show', $contact->id)
    ]"
>
    <x-slot name="actions">
        <a href="{{ route('admin.mr.contacts.edit', $contact->id) }}" class="btn btn-primary text-sm font-bold shadow-sm">
            <i class="fa-solid fa-pen-to-square mr-1.5 ml-1.5"></i> {{ app()->getLocale() === 'ar' ? 'تعديل البيانات' : 'Edit Profile' }}
        </a>
    </x-slot>

    <!-- Top Profile Summary -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Main Card -->
        <div class="lg:col-span-2 card p-6">
            <div class="flex items-start justify-between gap-4 border-b border-gray-100 dark:border-gray-800 pb-4 mb-4">
                <div class="flex items-start gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-sky-100 dark:bg-sky-950/60 text-sky-700 dark:text-sky-300 flex items-center justify-center text-2xl font-bold">
                        <i class="fa-solid fa-user-doctor"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ $contact->name }}</h2>
                        <div class="text-sm text-gray-500 mt-0.5 flex items-center gap-2 flex-wrap">
                            <span class="font-mono text-xs text-gray-400">{{ $contact->code }}</span>
                            <span>•</span>
                            <span class="text-sky-600 dark:text-sky-400 font-semibold">{{ $contact->specialty?->name ?? 'General' }}</span>
                            @if($contact->hospital_clinic_name)
                                <span>•</span>
                                <span>{{ $contact->hospital_clinic_name }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div>
                    @if($contact->classification)
                        <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-sm font-black {{ $contact->classification->code === 'A+' ? 'bg-amber-100 text-amber-900 border border-amber-300 dark:bg-amber-950/60 dark:text-amber-300 dark:border-amber-700' : 'bg-sky-100 text-sky-900 dark:bg-sky-950/60 dark:text-sky-300' }}">
                            Class {{ $contact->classification->code }}
                        </span>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-xs text-gray-400 uppercase font-bold block mb-0.5">{{ app()->getLocale() === 'ar' ? 'الهاتف' : 'Phone' }}</span>
                    <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $contact->phone ?: '—' }}</span>
                </div>
                <div>
                    <span class="text-xs text-gray-400 uppercase font-bold block mb-0.5">{{ app()->getLocale() === 'ar' ? 'البريد الإلكتروني' : 'Email' }}</span>
                    <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $contact->email ?: '—' }}</span>
                </div>
                <div>
                    <span class="text-xs text-gray-400 uppercase font-bold block mb-0.5">{{ app()->getLocale() === 'ar' ? 'المدينة والمنطقة' : 'City & Region' }}</span>
                    <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $contact->city?->name ?? '—' }} {{ $contact->region ? '('.$contact->region.')' : '' }}</span>
                </div>
                <div>
                    <span class="text-xs text-gray-400 uppercase font-bold block mb-0.5">{{ app()->getLocale() === 'ar' ? 'العنوان' : 'Address' }}</span>
                    <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $contact->address ?: '—' }}</span>
                </div>
                @if($contact->notes)
                    <div class="sm:col-span-2 pt-2 border-t border-gray-100 dark:border-gray-800">
                        <span class="text-xs text-gray-400 uppercase font-bold block mb-0.5">{{ app()->getLocale() === 'ar' ? 'ملاحظات وتوجيهات' : 'Field Notes' }}</span>
                        <p class="text-xs text-gray-700 dark:text-gray-300">{{ $contact->notes }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- GPS Coordinates Card -->
        <div class="card p-6 flex flex-col justify-between">
            <div>
                <h3 class="font-bold text-sm text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-2 mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-location-dot text-emerald-500"></i>
                    {{ app()->getLocale() === 'ar' ? 'موقع العيادة الجغرافي (GPS)' : 'Clinic GPS Coordinates' }}
                </h3>

                @if($contact->latitude && $contact->longitude)
                    <div class="space-y-2 mb-4">
                        <div class="flex justify-between text-xs">
                            <span class="text-gray-500">Latitude:</span>
                            <span class="font-mono font-bold text-gray-800 dark:text-gray-200">{{ $contact->latitude }}</span>
                        </div>
                        <div class="flex justify-between text-xs">
                            <span class="text-gray-500">Longitude:</span>
                            <span class="font-mono font-bold text-gray-800 dark:text-gray-200">{{ $contact->longitude }}</span>
                        </div>
                        <div class="p-2.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 text-xs font-semibold flex items-center gap-2">
                            <i class="fa-solid fa-circle-check"></i>
                            {{ app()->getLocale() === 'ar' ? 'متاح للتحقق الجغرافي أثناء تسجيل وصول المندوب' : 'Ready for automatic check-in geofence validation' }}
                        </div>
                    </div>
                    <a href="https://www.google.com/maps?q={{ $contact->latitude }},{{ $contact->longitude }}" target="_blank" class="btn btn-outline text-xs w-full text-center">
                        <i class="fa-solid fa-arrow-up-right-from-square mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'فتح في خرائط Google ↗' : 'Open in Google Maps ↗' }}
                    </a>
                @else
                    <div class="p-4 rounded-xl bg-amber-50 dark:bg-amber-950/40 text-amber-800 dark:text-amber-300 text-xs mb-4">
                        <i class="fa-solid fa-triangle-exclamation mb-1 text-sm block"></i>
                        {{ app()->getLocale() === 'ar' ? 'لم يتم تسجيل إحداثيات GPS بعد. يرجى تعديل الطبيب وإضافة الإحداثيات للتحقق من وصول المندوب.' : 'No GPS coordinates stored yet. Please edit doctor to add coordinates for geofence check.' }}
                    </div>
                    <a href="{{ route('admin.mr.contacts.edit', $contact->id) }}" class="btn btn-primary text-xs w-full text-center">
                        <i class="fa-solid fa-location-crosshairs mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'إضافة الإحداثيات الآن' : 'Add GPS Coordinates' }}
                    </a>
                @endif
            </div>

            <div class="pt-4 border-t border-gray-100 dark:border-gray-800 text-xs text-gray-400">
                Created: {{ $contact->created_at->format('d M Y') }}
            </div>
        </div>
    </div>

    <!-- Cycle Assignments & Visit History Tabs -->
    <div class="space-y-6">
        <!-- Assignments Table -->
        <div class="card p-0 overflow-hidden shadow-sm border border-gray-100 dark:border-gray-800">
            <div class="p-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between bg-gray-50/50 dark:bg-gray-800/40">
                <h3 class="font-bold text-sm text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-clipboard-user text-sky-400"></i>
                    {{ app()->getLocale() === 'ar' ? 'تاريخ التكليفات بالزيارة (دورة / مندوب)' : 'Cycle Assignments History' }}
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800/60 text-gray-600 dark:text-gray-400 font-semibold border-b border-gray-200 dark:border-gray-700 text-xs">
                        <tr>
                            <th class="px-4 py-3">{{ app()->getLocale() === 'ar' ? 'الدورة' : 'Cycle' }}</th>
                            <th class="px-4 py-3">{{ app()->getLocale() === 'ar' ? 'المندوب الطبي المكلف' : 'Assigned MR' }}</th>
                            <th class="px-4 py-3 text-center">{{ app()->getLocale() === 'ar' ? 'الزيارات المنجزة' : 'Visits Done / Target' }}</th>
                            <th class="px-4 py-3 text-center">{{ app()->getLocale() === 'ar' ? 'النقاط المحققة' : 'Achieved Points / Target' }}</th>
                            <th class="px-4 py-3 text-center">{{ app()->getLocale() === 'ar' ? 'نسبة الالتزام' : 'Compliance %' }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-xs">
                        @forelse($contact->assignments as $a)
                            @php
                                $comp = $a->target_visits > 0 ? round(($a->visits_done / $a->target_visits) * 100, 1) : 0;
                            @endphp
                            <tr>
                                <td class="px-4 py-3 font-semibold">{{ $a->cycle?->name ?? 'Cycle' }}</td>
                                <td class="px-4 py-3 font-bold text-gray-900 dark:text-white">{{ $a->representative?->name ?? 'Rep' }}</td>
                                <td class="px-4 py-3 text-center font-bold">
                                    <span class="{{ $a->visits_done >= $a->target_visits ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400' }}">
                                        {{ $a->visits_done }} / {{ $a->target_visits }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center font-bold">
                                    {{ $a->achieved_points }} / {{ $a->target_points }} pts
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold {{ $comp >= 100 ? 'bg-emerald-100 text-emerald-800' : ($comp > 0 ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                                        {{ $comp }}%
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-gray-400">
                                    {{ app()->getLocale() === 'ar' ? 'لم يتم تكليف هذا الطبيب في أي دورة بعد.' : 'No cycle assignments recorded for this doctor yet.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Executed Visits Table -->
        <div class="card p-0 overflow-hidden shadow-sm border border-gray-100 dark:border-gray-800">
            <div class="p-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between bg-gray-50/50 dark:bg-gray-800/40">
                <h3 class="font-bold text-sm text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-list-check text-emerald-400"></i>
                    {{ app()->getLocale() === 'ar' ? 'سجل الزيارات الميدانية المنفذة' : 'Executed Visits & GPS Log' }}
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800/60 text-gray-600 dark:text-gray-400 font-semibold border-b border-gray-200 dark:border-gray-700 text-xs">
                        <tr>
                            <th class="px-4 py-3">{{ app()->getLocale() === 'ar' ? 'المندوب' : 'Representative' }}</th>
                            <th class="px-4 py-3">{{ app()->getLocale() === 'ar' ? 'تاريخ ووقت الوصول' : 'Check-In Time' }}</th>
                            <th class="px-4 py-3 text-center">{{ app()->getLocale() === 'ar' ? 'المدة' : 'Duration' }}</th>
                            <th class="px-4 py-3 text-center">{{ app()->getLocale() === 'ar' ? 'التحقق الجغرافي GPS' : 'GPS Verification' }}</th>
                            <th class="px-4 py-3">{{ app()->getLocale() === 'ar' ? 'النتيجة والملاحظات' : 'Outcome & Notes' }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-xs">
                        @forelse($contact->visits as $v)
                            <tr>
                                <td class="px-4 py-3 font-bold text-gray-900 dark:text-white">{{ $v->representative?->name ?? 'Rep' }}</td>
                                <td class="px-4 py-3">
                                    <div>{{ $v->checkin_at?->format('d M Y, H:i') }}</div>
                                    <div class="text-[10px] text-gray-400">{{ $v->checkin_at?->diffForHumans() }}</div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    {{ $v->duration_minutes ? $v->duration_minutes . ' min' : '—' }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-bold {{ $v->gps_verified ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                        <i class="fa-solid {{ $v->gps_verified ? 'fa-circle-check' : 'fa-triangle-exclamation' }}"></i>
                                        {{ $v->gps_verified ? 'Verified' : 'Flagged (' . ($v->distance_from_contact_m ?? 'N/A') . 'm)' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    @if($v->outcome)
                                        <span class="badge badge-outline text-[10px] font-bold">{{ ucfirst($v->outcome) }}</span>
                                    @endif
                                    <div class="text-gray-500 text-[11px] mt-0.5">{{ Str::limit($v->notes, 60) }}</div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-gray-400">
                                    {{ app()->getLocale() === 'ar' ? 'لا توجد زيارات مسجلة لهذا الطبيب بعد.' : 'No visits recorded for this doctor yet.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.admin>

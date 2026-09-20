<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'الأطباء والعيادات (قاعدة الاتصال)' : 'Doctors & Clinics (Contact Portfolio)'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'إدارة ملفات الأطباء والمراكز الطبية وتصنيفها الفئوي والتخصصي والموقع الجغرافي' : 'Manage medical professionals, clinic locations, classifications, specialties, and visit tracking.'"
    :breadcrumbs="[
        (app()->getLocale() === 'ar' ? 'نظام المندوب الطبي' : 'MR CRM') => route('admin.mr.live-map'),
        (app()->getLocale() === 'ar' ? 'الأطباء والعيادات' : 'Doctors & Clinics') => route('admin.mr.contacts.index')
    ]"
>
    <x-slot name="actions">
        <a href="{{ route('admin.mr.contacts.create') }}" class="btn btn-primary font-bold shadow-sm">
            <i class="fa-solid fa-user-plus mr-1.5 ml-1.5"></i> {{ app()->getLocale() === 'ar' ? 'إضافة طبيب جديد' : 'Add New Doctor' }}
        </a>
    </x-slot>

    <!-- Toolbar & Filter Card -->
    <div class="card p-4 mb-6">
        <form method="GET" action="{{ route('admin.mr.contacts.index') }}" class="flex flex-wrap items-center justify-between gap-4">
            <div class="search-wrapper flex-1 min-w-[260px] max-w-md">
                <svg class="search-icon" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control search-input text-sm" placeholder="{{ app()->getLocale() === 'ar' ? 'بحث بالاسم، الكود، الهاتف، العيادة...' : 'Search doctor name, code, phone, clinic...' }}">
            </div>

            <div class="flex items-center gap-3 flex-wrap">
                <select name="classification_id" onchange="this.form.submit()" class="form-select text-sm w-auto">
                    <option value="">{{ app()->getLocale() === 'ar' ? 'جميع الفئات (A+/A/B/C)' : 'All Classifications' }}</option>
                    @foreach($classifications as $c)
                        <option value="{{ $c->id }}" {{ request('classification_id') == $c->id ? 'selected' : '' }}>
                            Class {{ $c->code }} ({{ $c->points }} pts / {{ $c->required_visits }} req)
                        </option>
                    @endforeach
                </select>

                <select name="specialty_id" onchange="this.form.submit()" class="form-select text-sm w-auto">
                    <option value="">{{ app()->getLocale() === 'ar' ? 'جميع التخصصات' : 'All Specialties' }}</option>
                    @foreach($specialties as $sp)
                        <option value="{{ $sp->id }}" {{ request('specialty_id') == $sp->id ? 'selected' : '' }}>
                            {{ $sp->name }}
                        </option>
                    @endforeach
                </select>

                <select name="city_id" onchange="this.form.submit()" class="form-select text-sm w-auto">
                    <option value="">{{ app()->getLocale() === 'ar' ? 'جميع المدن' : 'All Cities' }}</option>
                    @foreach($cities as $ct)
                        <option value="{{ $ct->id }}" {{ request('city_id') == $ct->id ? 'selected' : '' }}>
                            {{ $ct->name }}
                        </option>
                    @endforeach
                </select>

                @if(request()->hasAny(['search', 'classification_id', 'specialty_id', 'city_id']))
                    <a href="{{ route('admin.mr.contacts.index') }}" class="btn btn-outline text-xs">
                        <i class="fa-solid fa-xmark mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'إلغاء الفلتر' : 'Reset' }}
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Doctors Table -->
    <div class="card p-0 overflow-hidden shadow-sm border border-gray-100 dark:border-gray-800">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50/75 dark:bg-gray-800/60 text-gray-600 dark:text-gray-300 font-semibold border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="px-5 py-3.5">{{ app()->getLocale() === 'ar' ? 'الكود' : 'Code' }}</th>
                        <th class="px-5 py-3.5">{{ app()->getLocale() === 'ar' ? 'اسم الطبيب / المركز' : 'Doctor & Facility' }}</th>
                        <th class="px-5 py-3.5">{{ app()->getLocale() === 'ar' ? 'التخصص' : 'Specialty' }}</th>
                        <th class="px-5 py-3.5 text-center">{{ app()->getLocale() === 'ar' ? 'التصنيف الفئوي' : 'Class' }}</th>
                        <th class="px-5 py-3.5">{{ app()->getLocale() === 'ar' ? 'المدينة / المنطقة' : 'Location' }}</th>
                        <th class="px-5 py-3.5">{{ app()->getLocale() === 'ar' ? 'الهاتف' : 'Contact' }}</th>
                        <th class="px-5 py-3.5 text-center">{{ app()->getLocale() === 'ar' ? 'إحداثيات GPS' : 'GPS Coords' }}</th>
                        <th class="px-5 py-3.5 text-center">{{ app()->getLocale() === 'ar' ? 'الزيارات' : 'Visits' }}</th>
                        <th class="px-5 py-3.5 text-right">{{ app()->getLocale() === 'ar' ? 'الإجراءات' : 'Actions' }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($contacts as $doc)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/40 transition-colors">
                            <td class="px-5 py-4 font-mono text-xs font-bold text-gray-500">
                                {{ $doc->code }}
                            </td>
                            <td class="px-5 py-4">
                                <a href="{{ route('admin.mr.contacts.show', $doc->id) }}" class="font-bold text-gray-900 dark:text-white hover:text-sky-500 transition-colors">
                                    {{ $doc->name }}
                                </a>
                                @if($doc->hospital_clinic_name)
                                    <div class="text-xs text-gray-500 mt-0.5">
                                        <i class="fa-solid fa-hospital text-gray-400 mr-1 ml-1 text-[11px]"></i> {{ $doc->hospital_clinic_name }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <span class="badge badge-outline text-xs">
                                    {{ $doc->specialty?->name ?? 'General' }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                @if($doc->classification)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-black {{ $doc->classification->code === 'A+' ? 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-400 border border-amber-300 dark:border-amber-700' : ($doc->classification->code === 'A' ? 'bg-sky-100 text-sky-800 dark:bg-sky-950/60 dark:text-sky-400' : 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300') }}">
                                        {{ $doc->classification->code }}
                                    </span>
                                    <div class="text-[10px] text-gray-400 mt-0.5">{{ $doc->classification->points }} pts • {{ $doc->classification->required_visits }}/cycle</div>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-xs text-gray-600 dark:text-gray-400">
                                <div>{{ $doc->city?->name ?? '—' }}</div>
                                @if($doc->region)
                                    <div class="text-gray-400 text-[11px]">{{ $doc->region }}</div>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-xs">
                                @if($doc->phone)
                                    <div class="text-gray-800 dark:text-gray-200"><i class="fa-solid fa-phone text-gray-400 mr-1 ml-1"></i> {{ $doc->phone }}</div>
                                @endif
                                @if($doc->email)
                                    <div class="text-gray-400 text-[11px]"><i class="fa-solid fa-envelope text-gray-400 mr-1 ml-1"></i> {{ $doc->email }}</div>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center text-xs">
                                @if($doc->latitude && $doc->longitude)
                                    <span class="inline-flex items-center gap-1 text-emerald-600 dark:text-emerald-400 font-bold" title="{{ $doc->latitude }}, {{ $doc->longitude }}">
                                        <i class="fa-solid fa-location-crosshairs"></i> Geocoded
                                    </span>
                                @else
                                    <span class="text-amber-500 text-[11px]">
                                        <i class="fa-solid fa-triangle-exclamation"></i> No GPS
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center font-bold text-gray-700 dark:text-gray-300">
                                {{ $doc->visits_count }}
                            </td>
                            <td class="px-5 py-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.mr.contacts.show', $doc->id) }}" class="btn btn-sm btn-ghost p-1.5 text-gray-500 hover:text-sky-500" title="View Profile">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.mr.contacts.edit', $doc->id) }}" class="btn btn-sm btn-ghost p-1.5 text-gray-500 hover:text-amber-500" title="Edit Doctor">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.mr.contacts.destroy', $doc->id) }}" onsubmit="return confirm('Delete this doctor?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-ghost p-1.5 text-gray-500 hover:text-rose-500" title="Delete">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-5 py-12 text-center text-gray-400">
                                <i class="fa-solid fa-user-doctor text-4xl mb-2"></i>
                                <p class="text-sm font-semibold">{{ app()->getLocale() === 'ar' ? 'لم يتم العثور على أطباء مسجلين مطابقين للبحث.' : 'No doctors or clinics found matching the query.' }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($contacts->hasPages())
            <div class="p-4 border-t border-gray-100 dark:border-gray-800">
                {{ $contacts->links() }}
            </div>
        @endif
    </div>
</x-layouts.admin>

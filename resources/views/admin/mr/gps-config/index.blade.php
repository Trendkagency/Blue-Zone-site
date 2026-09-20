<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'إعدادات النطاق الجغرافي وقواعد الـ GPS' : 'GPS Geofencing & Location Policies'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'ضبط نصف قطر النطاق المسموح به لتسجيل وصول المندوب (Geofence) وقواعد كشف المواقع الوهمية' : 'Configure allowed check-in radius meters, mandatory GPS enforcement, and mock-location heuristics.'"
    :breadcrumbs="[
        (app()->getLocale() === 'ar' ? 'نظام المندوب الطبي' : 'MR CRM') => route('admin.mr.live-map'),
        (app()->getLocale() === 'ar' ? 'إعدادات GPS' : 'GPS Config') => route('admin.mr.gps-config.index')
    ]"
>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Global Settings Form (1 Col) -->
        <div class="card p-6 shadow-sm border border-gray-100 dark:border-gray-800">
            <h3 class="font-bold text-sm text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-2 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-satellite-dish text-sky-500"></i>
                {{ app()->getLocale() === 'ar' ? 'القواعد العامة للنظام' : 'Global System GPS Rules' }}
            </h3>

            <form method="POST" action="{{ route('admin.mr.gps-config.update-global') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                        {{ app()->getLocale() === 'ar' ? 'نصف القطر المسموح به (بالمتر) *' : 'Allowed Geofence Radius (Meters) *' }}
                    </label>
                    <div class="relative">
                        <input type="number" name="allowed_radius_m" min="20" max="5000" value="{{ $globalConfig->allowed_radius_m ?? 150 }}" required class="form-control text-sm font-bold pr-12">
                        <span class="absolute inset-y-0 right-3 flex items-center text-xs text-gray-400 font-bold">m</span>
                    </div>
                    <span class="text-[11px] text-gray-400 mt-1 block">
                        {{ app()->getLocale() === 'ar' ? 'المسافة القصوى المقبولة بين المندوب وإحداثيات العيادة عند تسجيل الوصول (افتراضياً 150م)' : 'Max distance allowed between rep and clinic GPS at check-in (Default: 150m).' }}
                    </span>
                </div>

                <div class="pt-2 space-y-3">
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_gps_required" value="1" id="is_gps_required" class="form-checkbox rounded text-sky-600" {{ ($globalConfig->is_gps_required ?? true) ? 'checked' : '' }}>
                        <label for="is_gps_required" class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                            {{ app()->getLocale() === 'ar' ? 'إلزام تفعيل الـ GPS لتسجيل الزيارة' : 'Mandate GPS to Check-In' }}
                        </label>
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="mock_detection_enabled" value="1" id="mock_detection" class="form-checkbox rounded text-sky-600" {{ ($globalConfig->mock_detection_enabled ?? true) ? 'checked' : '' }}>
                        <label for="mock_detection" class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                            {{ app()->getLocale() === 'ar' ? 'كشف تطبيقات المواقع الوهمية (Mock GPS)' : 'Enable Mock Location Detection' }}
                        </label>
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-100 dark:border-gray-800">
                    <button type="submit" class="btn btn-primary font-bold text-xs shadow-sm w-full">
                        <i class="fa-solid fa-floppy-disk mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'حفظ الإعدادات العامة' : 'Save Global Rules' }}
                    </button>
                </div>
            </form>
        </div>

        <!-- Per-Rep Overrides (2 Cols) -->
        <div class="lg:col-span-2 card p-6 shadow-sm border border-gray-100 dark:border-gray-800">
            <h3 class="font-bold text-sm text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-2 mb-4 flex items-center justify-between">
                <span class="flex items-center gap-2">
                    <i class="fa-solid fa-sliders text-purple-500"></i>
                    {{ app()->getLocale() === 'ar' ? 'تخصيص استثناءات لمندوب محدد (Overrides)' : 'Per-Rep Custom Geofence Overrides' }}
                </span>
            </h3>

            <!-- Form to add override -->
            <form method="POST" action="{{ route('admin.mr.gps-config.store-rep') }}" class="p-4 rounded-xl bg-gray-50/75 dark:bg-gray-800/40 border border-gray-100 dark:border-gray-800 mb-6 flex flex-wrap items-end gap-3">
                @csrf
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                        {{ app()->getLocale() === 'ar' ? 'المندوب الطبي' : 'Medical Representative' }}
                    </label>
                    <select name="user_id" required class="form-select text-sm py-1.5 px-3">
                        <option value="">{{ app()->getLocale() === 'ar' ? 'اختر المندوب' : 'Select Rep' }}</option>
                        @foreach($medicalReps as $rep)
                            <option value="{{ $rep->id }}">{{ $rep->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="w-32">
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                        {{ app()->getLocale() === 'ar' ? 'النطاق (متر)' : 'Radius (m)' }}
                    </label>
                    <input type="number" name="allowed_radius_m" min="20" max="5000" value="250" required class="form-control text-sm py-1.5 px-3 font-bold">
                </div>

                <button type="submit" class="btn btn-secondary font-bold text-xs shadow-sm">
                    <i class="fa-solid fa-plus mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'تطبيق الاستثناء' : 'Set Override' }}
                </button>
            </form>

            <!-- Overrides Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800/60 text-gray-600 dark:text-gray-400 font-semibold border-b border-gray-200 dark:border-gray-700 text-xs">
                        <tr>
                            <th class="px-4 py-3">{{ app()->getLocale() === 'ar' ? 'المندوب الطبي' : 'Representative' }}</th>
                            <th class="px-4 py-3 text-center">{{ app()->getLocale() === 'ar' ? 'نصف القطر المخصص' : 'Custom Radius' }}</th>
                            <th class="px-4 py-3 text-center">{{ app()->getLocale() === 'ar' ? 'إلزام الـ GPS' : 'GPS Required' }}</th>
                            <th class="px-4 py-3 text-right">{{ app()->getLocale() === 'ar' ? 'الإجراء' : 'Action' }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-xs">
                        @forelse($repConfigs as $rc)
                            <tr>
                                <td class="px-4 py-3 font-bold text-gray-900 dark:text-white">
                                    {{ $rc->user?->name ?? 'Rep #' . $rc->user_id }}
                                </td>
                                <td class="px-4 py-3 text-center font-mono font-bold text-sky-600 dark:text-sky-400">
                                    {{ $rc->allowed_radius_m }} meters
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold {{ $rc->is_gps_required ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $rc->is_gps_required ? 'Enforced' : 'Optional' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <form method="POST" action="{{ route('admin.mr.gps-config.destroy-rep', $rc->id) }}" onsubmit="return confirm('Remove this override?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-ghost p-1.5 text-gray-400 hover:text-rose-500" title="Delete Override">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-gray-400">
                                    {{ app()->getLocale() === 'ar' ? 'لا توجد استثناءات مخصصة. يتم تطبيق الإعدادات العامة على جميع المناديب.' : 'No custom overrides active. All reps follow the global system rules.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.admin>

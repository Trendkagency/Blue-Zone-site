<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'خريطة العمليات الميدانية والنشاط المباشر' : 'Live Ops Field Map & Activity Feed'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'متابعة حية ولحظية لتحركات مندوبي الدعاية الطبية وتسجيلات الوصول بنظام GPS' : 'Real-time GPS tracking and live check-in feed for medical representatives field operations.'"
    :breadcrumbs="[
        (app()->getLocale() === 'ar' ? 'نظام المندوب الطبي' : 'MR CRM') => route('admin.mr.live-map'),
        (app()->getLocale() === 'ar' ? 'الخريطة المباشرة' : 'Live Map') => route('admin.mr.live-map')
    ]"
>
    <x-slot name="actions">
        <a href="{{ route('admin.mr.visits.index') }}" class="btn btn-secondary text-sm">
            <i class="fa-solid fa-list-check mr-1.5 ml-1.5"></i> {{ app()->getLocale() === 'ar' ? 'سجل الزيارات الكامل' : 'All Visits Log' }}
        </a>
        <a href="{{ url('/mr') }}" target="_blank" class="btn btn-primary text-sm shadow-sm">
            <i class="fa-solid fa-mobile-screen-button mr-1.5 ml-1.5"></i> {{ app()->getLocale() === 'ar' ? 'بوابة المندوب ↗' : 'MR Mobile Portal ↗' }}
        </a>
    </x-slot>

    <!-- Stat Bar -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="card stat-card stat-accent">
            <div class="stat-header">
                <span class="stat-label">{{ app()->getLocale() === 'ar' ? 'الدورة الحالية' : 'Active Cycle' }}</span>
                <span class="stat-icon"><i class="fa-solid fa-calendar-check text-sky-400"></i></span>
            </div>
            <div class="stat-value text-lg font-bold">{{ $activeCycle?->name ?? 'No Active Cycle' }}</div>
            <div class="stat-footer text-xs text-muted">
                {{ $activeCycle ? $activeCycle->start_date->format('d M') . ' — ' . $activeCycle->end_date->format('d M Y') : '—' }}
            </div>
        </div>

        <div class="card stat-card stat-success">
            <div class="stat-header">
                <span class="stat-label">{{ app()->getLocale() === 'ar' ? 'تسجيلات الوصول المرصودة' : 'Logged Check-Ins' }}</span>
                <span class="stat-icon"><i class="fa-solid fa-location-dot text-emerald-400"></i></span>
            </div>
            <div class="stat-value text-2xl font-bold">{{ $latestVisits->count() }}</div>
            <div class="stat-footer text-xs text-muted">
                {{ app()->getLocale() === 'ar' ? 'آخر 50 نقطة GPS مسجلة' : 'Last 50 captured GPS locations' }}
            </div>
        </div>

        <div class="card stat-card stat-warning">
            <div class="stat-header">
                <span class="stat-label">{{ app()->getLocale() === 'ar' ? 'نسبة التحقق من النطاق' : 'GPS Verification Rate' }}</span>
                <span class="stat-icon"><i class="fa-solid fa-satellite-dish text-amber-400"></i></span>
            </div>
            @php
                $verifiedCount = $latestVisits->where('gps_verified', true)->count();
                $rate = $latestVisits->count() > 0 ? round(($verifiedCount / $latestVisits->count()) * 100, 1) : 100;
            @endphp
            <div class="stat-value text-2xl font-bold">{{ $rate }}%</div>
            <div class="stat-footer text-xs {{ $rate >= 80 ? 'text-emerald-500 font-bold' : 'text-amber-500 font-bold' }}">
                {{ $verifiedCount }} / {{ $latestVisits->count() }} {{ app()->getLocale() === 'ar' ? 'ضمن النطاق المسموح' : 'within geofence radius' }}
            </div>
        </div>

        <div class="card stat-card stat-danger">
            <div class="stat-header">
                <span class="stat-label">{{ !empty($isRep) && $isRep ? (app()->getLocale() === 'ar' ? 'حساب المندوب' : 'My Status') : (app()->getLocale() === 'ar' ? 'إجمالي المناديب النشطين' : 'Active Medical Reps') }}</span>
                <span class="stat-icon"><i class="fa-solid fa-user-doctor text-rose-400"></i></span>
            </div>
            <div class="stat-value text-2xl font-bold">{{ !empty($isRep) && $isRep ? 1 : $medicalReps->count() }}</div>
            <div class="stat-footer text-xs text-muted">
                {{ !empty($isRep) && $isRep ? ($currentUser->name ?? 'MR') : (app()->getLocale() === 'ar' ? 'مندوبين مصرح لهم بالميدان' : 'Field agents on duty') }}
            </div>
        </div>
    </div>

    <!-- Filter Toolbar -->
    <div class="card p-4 mb-6">
        <form method="GET" action="{{ route('admin.mr.live-map') }}" class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">
                        {{ app()->getLocale() === 'ar' ? 'تصفية حسب المندوب' : 'Filter by Rep' }}
                    </label>
                    @if(!empty($isRep) && $isRep)
                        <div class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-cyan-50 dark:bg-cyan-950/40 text-cyan-700 dark:text-cyan-300 font-bold text-xs rounded border border-cyan-200 dark:border-cyan-800">
                            <i class="fa-solid fa-user-check"></i>
                            <span>{{ $currentUser->name }}</span>
                        </div>
                        <input type="hidden" name="mr_id" value="{{ $currentUser->id }}">
                    @else
                        <select name="mr_id" onchange="this.form.submit()" class="form-select text-sm py-1.5 px-3">
                            <option value="">{{ app()->getLocale() === 'ar' ? 'جميع المناديب' : 'All Medical Reps' }}</option>
                            @foreach($medicalReps as $rep)
                                <option value="{{ $rep->id }}" {{ request('mr_id') == $rep->id ? 'selected' : '' }}>
                                    {{ $rep->name }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">
                        {{ app()->getLocale() === 'ar' ? 'حالة الموقع' : 'Location Status' }}
                    </label>
                    <select name="verified_only" onchange="this.form.submit()" class="form-select text-sm py-1.5 px-3">
                        <option value="">{{ app()->getLocale() === 'ar' ? 'الكل (المؤكد وغير المؤكد)' : 'All (Verified & Unverified)' }}</option>
                        <option value="1" {{ request('verified_only') == '1' ? 'selected' : '' }}>
                            {{ app()->getLocale() === 'ar' ? 'المؤكدة جغرافياً فقط' : 'GPS Verified Only' }}
                        </option>
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.mr.live-map') }}" class="btn btn-outline text-xs">
                    <i class="fa-solid fa-arrows-rotate mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'تحديث لحظي' : 'Live Refresh' }}
                </a>
            </div>
        </form>
    </div>

    <!-- Main Map & Activity Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Map Canvas (2 Columns) -->
        <div class="lg:col-span-2 card p-0 overflow-hidden shadow-sm border border-gray-200 dark:border-gray-800">
            <div class="p-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between bg-gray-50/50 dark:bg-gray-800/40">
                <div class="flex items-center gap-2">
                    <span class="inline-block w-3 h-3 bg-emerald-500 rounded-full animate-pulse"></span>
                    <h3 class="font-bold text-sm text-gray-900 dark:text-white">
                        {{ app()->getLocale() === 'ar' ? 'خريطة التغطية الميدانية المباشرة' : 'Active Field Operations Map' }}
                    </h3>
                </div>
                <span class="text-xs text-gray-500">
                    {{ app()->getLocale() === 'ar' ? 'اضغط على العلامة لتفاصيل الزيارة' : 'Click markers for visit & doctor details' }}
                </span>
            </div>

            <div id="mr-live-map" style="height: 540px; width: 100%; z-index: 10;"></div>
        </div>

        <!-- Real-Time Activity Feed (1 Column) -->
        <div class="card p-0 shadow-sm border border-gray-200 dark:border-gray-800 flex flex-col h-[590px]">
            <div class="p-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between bg-gray-50/50 dark:bg-gray-800/40">
                <h3 class="font-bold text-sm text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-timeline text-sky-400"></i>
                    {{ app()->getLocale() === 'ar' ? 'الجدول الزمني للنشاط اللحظي' : 'Real-Time Activity Feed' }}
                </h3>
                <span class="badge badge-outline text-xs">{{ $latestVisits->count() }}</span>
            </div>

            <div class="p-4 overflow-y-auto space-y-3 flex-1">
                @forelse($latestVisits as $v)
                    <div class="p-3 rounded-xl border border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30 hover:border-sky-300 dark:hover:border-sky-700 transition-all">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex items-start gap-2.5">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-xs {{ $v->gps_verified ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400' : 'bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-400' }}">
                                    <i class="fa-solid {{ $v->gps_verified ? 'fa-location-dot' : 'fa-triangle-exclamation' }}"></i>
                                </div>
                                <div>
                                    <div class="font-bold text-sm text-gray-900 dark:text-white leading-tight">
                                        {{ $v->representative?->name ?? 'MR' }}
                                    </div>
                                    <div class="text-xs text-sky-600 dark:text-sky-400 font-medium mt-0.5">
                                        {{ $v->contact?->name ?? 'Doctor' }}
                                        @if($v->contact?->classification)
                                            <span class="px-1.5 py-0.2 rounded text-[10px] font-bold bg-amber-100 dark:bg-amber-950/50 text-amber-700 dark:text-amber-400">
                                                {{ $v->contact->classification->code }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-[11px] text-gray-500 mt-1">
                                        {{ $v->contact?->hospital_clinic_name }} • {{ $v->contact?->city?->name ?? $v->contact?->region }}
                                    </div>
                                </div>
                            </div>

                            <span class="text-[11px] text-gray-400 whitespace-nowrap">
                                {{ $v->checkin_at?->diffForHumans() }}
                            </span>
                        </div>

                        <div class="mt-2.5 pt-2 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between text-[11px]">
                            <span class="inline-flex items-center gap-1 font-semibold {{ $v->gps_verified ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                <i class="fa-solid {{ $v->gps_verified ? 'fa-circle-check' : 'fa-circle-xmark' }}"></i>
                                {{ $v->gps_verified ? (app()->getLocale() === 'ar' ? 'مؤكد GPS' : 'Verified GPS') : (app()->getLocale() === 'ar' ? 'تجاوز النطاق' : 'Outside Radius') }}
                                @if($v->distance_from_contact_m)
                                    ({{ $v->distance_from_contact_m }}m)
                                @endif
                            </span>

                            @if($v->duration_minutes)
                                <span class="text-gray-500">
                                    <i class="fa-regular fa-clock mr-0.5 ml-0.5"></i> {{ $v->duration_minutes }} min
                                </span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 text-gray-400">
                        <i class="fa-solid fa-map-location-dot text-3xl mb-2"></i>
                        <p class="text-sm">{{ app()->getLocale() === 'ar' ? 'لا توجد تسجيلات وصول حديثة.' : 'No recent check-ins recorded yet.' }}</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Leaflet Assets & Map Script -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const mapEl = document.getElementById('mr-live-map');
            if (!mapEl) return;

            const markersData = @json($markers);

            // Default center Cairo/Egypt coordinates
            let defaultLat = 30.0444;
            let defaultLng = 31.2357;

            if (markersData.length > 0 && markersData[0].lat && markersData[0].lng) {
                defaultLat = markersData[0].lat;
                defaultLng = markersData[0].lng;
            }

            const map = L.map('mr-live-map', {
                scrollWheelZoom: true
            }).setView([defaultLat, defaultLng], 12);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19
            }).addTo(map);

            const bounds = [];

            markersData.forEach(m => {
                if (!m.lat || !m.lng) return;

                const color = m.verified ? '#10B981' : '#EF4444';
                const pulseColor = m.verified ? 'rgba(16, 185, 129, 0.3)' : 'rgba(239, 68, 68, 0.3)';

                const customIcon = L.divIcon({
                    className: 'custom-mr-marker',
                    html: `
                        <div style="
                            position: relative;
                            width: 32px;
                            height: 32px;
                            background-color: ${color};
                            border: 2px solid white;
                            border-radius: 50%;
                            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            color: white;
                            font-size: 13px;
                        ">
                            <i class="fa-solid fa-user-doctor"></i>
                        </div>
                    `,
                    iconSize: [32, 32],
                    iconAnchor: [16, 16],
                    popupAnchor: [0, -18]
                });

                const popupContent = `
                    <div style="font-family: inherit; font-size: 13px; min-width: 200px; color: #1e293b;">
                        <div style="font-weight: 700; font-size: 14px; margin-bottom: 2px;">
                            ${m.doctor_name}
                            ${m.classification ? '<span style="background: #fef3c7; color: #b45309; padding: 2px 6px; border-radius: 4px; font-size: 11px; margin-left: 4px;">Class ' + m.classification + '</span>' : ''}
                        </div>
                        <div style="color: #64748b; font-size: 12px; margin-bottom: 6px;">
                            ${m.clinic_name || ''} ${m.specialty ? '• ' + m.specialty : ''}
                        </div>
                        <div style="background: #f8fafc; padding: 6px 8px; border-radius: 6px; margin-bottom: 6px; border: 1px solid #e2e8f0;">
                            <div><strong>MR:</strong> ${m.rep_name}</div>
                            <div><strong>Time:</strong> ${m.time_formatted} (${m.time_ago})</div>
                            <div><strong>Distance:</strong> ${m.distance_m ? m.distance_m + 'm' : 'N/A'}</div>
                        </div>
                        <div style="display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 700; background: ${m.verified ? '#dcfce7; color: #166534;' : '#fee2e2; color: #991b1b;'}">
                            ${m.verified ? '✓ GPS Verified Check-In' : '⚠️ Flagged: ' + (m.flag || 'Outside Radius')}
                        </div>
                    </div>
                `;

                const marker = L.marker([m.lat, m.lng], { icon: customIcon }).addTo(map);
                marker.bindPopup(popupContent);
                bounds.push([m.lat, m.lng]);
            });

            if (bounds.length > 1) {
                map.fitBounds(bounds, { padding: [50, 50] });
            }
        });
    </script>
</x-layouts.admin>

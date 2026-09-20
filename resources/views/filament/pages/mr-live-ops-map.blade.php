<x-filament-panels::page>
    <div class="space-y-6">
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

        {{-- Interactive Map View --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 dark:bg-gray-900 dark:border-gray-800 overflow-hidden">
            <div class="p-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="inline-block w-3 h-3 bg-emerald-500 rounded-full animate-pulse"></span>
                    <h3 class="font-semibold text-gray-900 dark:text-white">Active Field Operations Tracking</h3>
                </div>
                <div class="text-xs text-gray-500">Showing last 50 GPS check-in points</div>
            </div>

            <div id="mr-live-map" style="height: 500px; width: 100%; z-index: 10;"></div>
        </div>

        {{-- Activity Timeline --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 dark:bg-gray-900 dark:border-gray-800 p-6">
            <h3 class="font-bold text-gray-900 dark:text-white mb-4">Real-Time Check-In Timeline</h3>
            <div class="space-y-4">
                @forelse($this->latest_visits as $visit)
                    <div class="flex items-start justify-between p-3 rounded-lg bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-800">
                        <div class="flex items-start gap-3">
                            <div class="p-2 rounded-lg {{ $visit->gps_verified ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <div>
                                <div class="font-bold text-sm text-gray-900 dark:text-white">
                                    {{ $visit->representative?->name }} <span class="font-normal text-gray-500">visited</span> {{ $visit->contact?->name }} ({{ $visit->contact?->classification?->code }})
                                </div>
                                <div class="text-xs text-gray-500 mt-0.5">
                                    {{ $visit->contact?->hospital_clinic_name }} • {{ $visit->contact?->specialty?->name }} • {{ $visit->contact?->city?->name }}
                                </div>
                                <div class="flex items-center gap-2 mt-1.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold {{ $visit->gps_verified ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $visit->gps_flag }} ({{ $visit->distance_from_contact_m ?? 'N/A' }}m)
                                    </span>
                                    @if($visit->duration_minutes)
                                        <span class="text-[11px] text-gray-500">{{ $visit->duration_minutes }} min duration</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="text-xs text-gray-400 font-mono">
                            {{ $visit->checkin_at?->diffForHumans() }}
                        </div>
                    </div>
                @empty
                    <div class="text-center py-6 text-gray-500">No recent check-ins recorded.</div>
                @endforelse
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const mapEl = document.getElementById('mr-live-map');
            if (!mapEl) return;

            const map = L.map('mr-live-map').setView([30.0444, 31.2357], 11);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            const visits = @json($this->latest_visits);
            const markers = [];

            visits.forEach(v => {
                if (v.checkin_lat && v.checkin_lng) {
                    const marker = L.marker([parseFloat(v.checkin_lat), parseFloat(v.checkin_lng)])
                        .bindPopup(`
                            <div style="font-family: sans-serif; font-size: 13px;">
                                <strong>${v.representative ? v.representative.name : 'MR'}</strong><br/>
                                <span>Doctor: ${v.contact ? v.contact.name : 'Clinic'}</span><br/>
                                <small>GPS Status: ${v.gps_flag} (${v.distance_from_contact_m || 0}m)</small><br/>
                                <small>${new Date(v.checkin_at).toLocaleString()}</small>
                            </div>
                        `);
                    marker.addTo(map);
                    markers.push(marker);
                }
            });

            if (markers.length > 0) {
                const group = new L.featureGroup(markers);
                map.fitBounds(group.getBounds().pad(0.1));
            }
        });
    </script>
</x-filament-panels::page>

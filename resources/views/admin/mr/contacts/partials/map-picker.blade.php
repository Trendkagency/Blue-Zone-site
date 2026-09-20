<!-- Interactive OpenMap & Google Maps Address Precision Picker for Doctors / Clinics -->
<div class="md:col-span-2 bg-gradient-to-br from-sky-50/70 via-white to-slate-50 dark:from-slate-900 dark:via-slate-900/90 dark:to-slate-950 p-4 sm:p-5 rounded-2xl border-2 border-sky-200/80 dark:border-sky-900/50 shadow-md space-y-4 my-2">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 pb-3 border-b border-slate-200 dark:border-slate-800">
        <div>
            <div class="flex items-center gap-2.5 flex-wrap">
                <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-amber-500 to-sky-600 text-white flex items-center justify-center font-bold text-base shadow-sm">
                    <i class="fa-brands fa-google"></i>
                </span>
                <h4 class="font-extrabold text-sm sm:text-base text-slate-900 dark:text-white">
                    {{ app()->getLocale() === 'ar' ? 'تحديد وتدقيق موقع العيادة عبر Google Maps' : 'Google Maps Precision Clinic & Address Locator' }}
                </h4>
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                    <i class="fa-solid fa-crosshairs text-[10px]"></i>
                    {{ app()->getLocale() === 'ar' ? 'دقة 100% معتمدة' : '100% True GPS Address' }}
                </span>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5 leading-relaxed">
                {{ app()->getLocale() === 'ar' 
                    ? 'افتح خرائط Google، أو الصق رابط الموقع (من WhatsApp أو Google Maps)، أو ابحث وحدد مدخل العيادة مباشرة على الخريطة.' 
                    : 'Search clinic on Google Maps, paste Google Maps link/coords, or click the map to capture the 100% true physical address.' }}
            </p>
        </div>

        <!-- Direct Actions -->
        <div class="flex items-center gap-2 flex-wrap shrink-0">
            <!-- Open Google Maps Search External -->
            <button type="button" onclick="openDirectGoogleMapsSearch()" class="px-3.5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 active:scale-95 text-white font-bold text-xs transition flex items-center gap-1.5 shadow-sm shadow-amber-500/20" title="{{ app()->getLocale() === 'ar' ? 'فتح بحث خرائط Google في نافذة جديدة' : 'Search this clinic on Google Maps' }}">
                <i class="fa-brands fa-google text-sm"></i>
                <span>{{ app()->getLocale() === 'ar' ? 'فتح في Google Maps' : 'Open in Google Maps' }}</span>
                <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
            </button>

            <!-- Locate Me -->
            <button type="button" id="btn-map-locate-me" class="px-3.5 py-2 rounded-xl text-xs font-bold bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-300 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 transition flex items-center gap-1.5 shadow-xs active:scale-95" title="{{ app()->getLocale() === 'ar' ? 'تحديد موقعي الحالي بالـ GPS' : 'Center on My Current GPS Location' }}">
                <i class="fa-solid fa-location-crosshairs text-sky-500 text-sm"></i>
                <span>{{ app()->getLocale() === 'ar' ? 'موقعي الحالي' : 'My GPS' }}</span>
            </button>

            <!-- Layer Switcher -->
            <div class="inline-flex rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-0.5 shadow-xs">
                <button type="button" onclick="setMapLayer('googleRoad')" id="layer-btn-googleRoad" class="px-2.5 py-1.5 text-xs font-bold rounded-lg transition bg-sky-600 text-white shadow-xs flex items-center gap-1">
                    <i class="fa-brands fa-google text-[11px]"></i> Google
                </button>
                <button type="button" onclick="setMapLayer('satellite')" id="layer-btn-satellite" class="px-2.5 py-1.5 text-xs font-semibold rounded-lg transition text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white flex items-center gap-1">
                    <i class="fa-solid fa-satellite text-[11px]"></i> {{ app()->getLocale() === 'ar' ? 'قمر صناعي' : 'Satellite' }}
                </button>
                <button type="button" onclick="setMapLayer('osm')" id="layer-btn-osm" class="px-2.5 py-1.5 text-xs font-semibold rounded-lg transition text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white flex items-center gap-1">
                    <i class="fa-solid fa-map text-[11px]"></i> OpenMap
                </button>
            </div>
        </div>
    </div>

    <!-- Quick Paste Google Maps Link or Coords Bar -->
    <div class="bg-amber-50/60 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-800/50 p-3 rounded-xl">
        <label class="block text-xs font-bold text-amber-900 dark:text-amber-300 mb-1.5 flex items-center gap-1.5">
            <i class="fa-brands fa-google text-amber-600"></i>
            <span>{{ app()->getLocale() === 'ar' ? 'لصق رابط Google Maps (من WhatsApp أو المتصفح) أو الإحداثيات المباشرة:' : 'Paste Google Maps Link / Share URL (from WhatsApp/browser) or Coordinates:' }}</span>
        </label>
        <div class="flex items-center gap-2">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 rtl:right-0 rtl:left-auto flex items-center pointer-events-none px-3 text-amber-500">
                    <i class="fa-solid fa-link text-xs"></i>
                </div>
                <input 
                    type="text" 
                    id="gmaps-paste-input" 
                    placeholder="e.g. https://maps.app.goo.gl/... or https://www.google.com/maps?q=30.0444,31.2357 or 30.044420, 31.235712" 
                    class="form-control text-xs sm:text-sm pl-9 rtl:pr-9 rtl:pl-3 py-2 w-full bg-white dark:bg-slate-800 border-amber-300 dark:border-amber-800/80 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 font-mono text-slate-800 dark:text-slate-200"
                    autocomplete="off"
                >
            </div>
            <button type="button" onclick="resolveAndApplyPastedGmapsLink()" id="btn-resolve-gmaps" class="px-4 py-2 rounded-xl bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-500 hover:to-orange-500 text-white font-bold text-xs active:scale-95 transition flex items-center gap-1.5 shadow-sm whitespace-nowrap">
                <i class="fa-solid fa-wand-magic-sparkles"></i>
                <span>{{ app()->getLocale() === 'ar' ? 'تطبيق الموقع 100%' : 'Apply Location 100%' }}</span>
            </button>
        </div>
    </div>

    <!-- Search Places Input -->
    <div class="relative">
        <div class="flex items-center gap-2">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 rtl:right-0 rtl:left-auto flex items-center pointer-events-none px-3 text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </div>
                <input 
                    type="text" 
                    id="map-search-query" 
                    placeholder="{{ app()->getLocale() === 'ar' ? 'أو ابحث هنا باسم العيادة، المستشفى، الشارع، الحي (مثال: مستشفى دار الفؤاد أو شارع مصطفى النحاس)...' : 'Or search by hospital name, street, district, or landmark...' }}" 
                    class="form-control text-xs sm:text-sm pl-9 rtl:pr-9 rtl:pl-3 py-2.5 w-full bg-white dark:bg-slate-800 border-slate-300 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500"
                    autocomplete="off"
                >
                <button type="button" id="map-search-clear" class="hidden absolute inset-y-0 right-0 rtl:left-0 rtl:right-auto flex items-center px-3 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>
            </div>

            <button type="button" id="map-search-trigger-btn" class="px-4 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-500 text-white font-bold text-xs active:scale-95 transition flex items-center gap-1.5 shadow-md shadow-sky-600/20 whitespace-nowrap">
                <i class="fa-solid fa-location-dot"></i>
                <span>{{ app()->getLocale() === 'ar' ? 'بحث وتحديد' : 'Locate' }}</span>
            </button>
        </div>

        <!-- Floating Autocomplete Suggestions Dropdown -->
        <div id="map-search-dropdown" class="hidden absolute z-30 left-0 right-0 top-full mt-1.5 bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-slate-200 dark:border-slate-700 max-h-60 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-700/50">
            <!-- Populated via JavaScript -->
        </div>
    </div>

    <!-- Map Canvas Container -->
    <div class="relative w-full rounded-2xl overflow-hidden border-2 border-slate-300 dark:border-slate-700 shadow-inner">
        <div id="doctor-address-map" class="w-full h-[380px] sm:h-[420px] z-10 bg-slate-200 dark:bg-slate-800"></div>

        <!-- Quick Help Overlay Pill -->
        <div class="absolute bottom-3 left-3 right-3 sm:right-auto z-20 pointer-events-none">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-900/90 backdrop-blur-md text-white text-[11px] font-medium shadow-lg border border-white/15">
                <i class="fa-solid fa-hand-pointer text-amber-400 animate-bounce"></i>
                <span>{{ app()->getLocale() === 'ar' ? 'انقر في أي مكان بالخريطة أو اسحب المؤشر لتعديل مدخل العيادة بدقة' : 'Click map or drag the pin to pinpoint the exact clinic entrance' }}</span>
            </div>
        </div>

        <!-- Live Google Map Link on top right of map -->
        <div class="absolute top-3 right-3 rtl:right-auto rtl:left-3 z-20">
            <a href="#" id="btn-external-google-maps" target="_blank" rel="noopener noreferrer" class="hidden px-3 py-1.5 rounded-xl text-xs font-bold bg-white/95 dark:bg-slate-900/95 text-slate-800 dark:text-white border border-slate-300 dark:border-slate-700 hover:bg-amber-50 hover:text-amber-700 hover:border-amber-300 transition shadow-md backdrop-blur-sm flex items-center gap-1.5">
                <i class="fa-brands fa-google text-amber-500 text-sm"></i>
                <span>{{ app()->getLocale() === 'ar' ? 'معاينة في خرائط Google' : 'View on Google Maps' }}</span>
                <i class="fa-solid fa-arrow-up-right-from-square text-[10px] text-slate-400"></i>
            </a>
        </div>
    </div>

    <!-- Geocoded Location Feedback Card -->
    <div id="geocoded-result-card" class="p-3.5 rounded-xl bg-white dark:bg-slate-800/90 border border-slate-200 dark:border-slate-700 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
        <div class="flex items-start gap-3">
            <span class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 mt-0.5 text-base">
                <i class="fa-solid fa-circle-check"></i>
            </span>
            <div>
                <div class="font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2 flex-wrap">
                    <span id="geo-card-coords" class="font-mono text-sm text-sky-700 dark:text-sky-400 font-extrabold">-- , --</span>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800">
                        <i class="fa-solid fa-shield-halved mr-0.5"></i> {{ app()->getLocale() === 'ar' ? 'سياج المندوب (150م)' : 'Rep Geofence (150m)' }}
                    </span>
                </div>
                <div id="geo-card-address" class="text-slate-600 dark:text-slate-300 mt-1 font-medium leading-relaxed">
                    {{ app()->getLocale() === 'ar' ? 'لم يتم تحديد موقع بعد. ابحث أو الصق رابط خرائط Google أو انقر على الخريطة.' : 'No location selected yet. Search, paste a Google Maps link, or click on the map.' }}
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2 shrink-0 self-end sm:self-center">
            <button type="button" onclick="applyGeocodedToAddressField()" id="btn-apply-address" class="hidden px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs transition active:scale-95 shadow-sm flex items-center gap-1.5">
                <i class="fa-solid fa-paste"></i>
                <span>{{ app()->getLocale() === 'ar' ? 'تحديث حقل العنوان' : 'Sync Address Field' }}</span>
            </button>
        </div>
    </div>
</div>

<!-- Leaflet CSS & JS Assets -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<style>
    /* Custom Doctor Clinic Pin Marker */
    .clinic-pin-wrapper {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .clinic-pin-beacon {
        position: absolute;
        width: 46px;
        height: 46px;
        border-radius: 50%;
        background: rgba(14, 165, 233, 0.35);
        animation: clinicPulse 2s infinite ease-out;
    }
    .clinic-pin-body {
        position: relative;
        width: 38px;
        height: 38px;
        background: linear-gradient(135deg, #0284c7, #0369a1);
        border: 2.5px solid #ffffff;
        border-radius: 50% 50% 50% 0;
        transform: rotate(-45deg);
        box-shadow: 0 8px 18px rgba(0,0,0,0.4);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: grab;
    }
    .clinic-pin-body i {
        transform: rotate(45deg);
        color: #ffffff;
        font-size: 15px;
    }
    @keyframes clinicPulse {
        0% { transform: scale(0.6); opacity: 0.9; }
        100% { transform: scale(1.6); opacity: 0; }
    }
    .leaflet-popup-content-wrapper {
        border-radius: 14px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.2);
    }
</style>

<script>
    (function () {
        let doctorMap = null;
        let doctorMarker = null;
        let doctorGeofenceCircle = null;
        let activeTileLayer = null;
        let lastReverseGeocodedData = null;
        let searchDebounceTimer = null;
        const appLocale = "{{ app()->getLocale() }}";

        // Tile layer definitions (Google Maps as primary default)
        const tileLayers = {
            googleRoad: L.tileLayer('https://mt1.google.com/vt/lyrs=m&hl=' + appLocale + '&x={x}&y={y}&z={z}', {
                attribution: '&copy; Google Maps',
                maxZoom: 20
            }),
            satellite: L.tileLayer('https://mt1.google.com/vt/lyrs=y&hl=' + appLocale + '&x={x}&y={y}&z={z}', {
                attribution: '&copy; Google Hybrid Satellite',
                maxZoom: 20
            }),
            osm: L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                maxZoom: 19
            })
        };

        window.setMapLayer = function (layerKey) {
            if (!doctorMap) return;
            if (activeTileLayer) {
                doctorMap.removeLayer(activeTileLayer);
            }
            activeTileLayer = tileLayers[layerKey] || tileLayers.googleRoad;
            activeTileLayer.addTo(doctorMap);

            // Update UI buttons
            ['googleRoad', 'satellite', 'osm'].forEach(k => {
                const btn = document.getElementById('layer-btn-' + k);
                if (!btn) return;
                if (k === layerKey) {
                    btn.className = 'px-2.5 py-1.5 text-xs font-bold rounded-lg transition bg-sky-600 text-white shadow-xs flex items-center gap-1';
                } else {
                    btn.className = 'px-2.5 py-1.5 text-xs font-semibold rounded-lg transition text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white flex items-center gap-1';
                }
            });
        };

        function getFormInputs() {
            return {
                latInput: document.querySelector('input[name="latitude"]'),
                lngInput: document.querySelector('input[name="longitude"]'),
                addressInput: document.querySelector('textarea[name="address"]'),
                regionInput: document.querySelector('input[name="region"]'),
                citySelect: document.querySelector('select[name="city_id"]'),
                clinicNameInput: document.querySelector('input[name="hospital_clinic_name"]')
            };
        }

        // Open Direct Google Maps search in a new window using current clinic name & city
        window.openDirectGoogleMapsSearch = function () {
            const { clinicNameInput, citySelect, latInput, lngInput } = getFormInputs();
            const lat = parseFloat(latInput?.value);
            const lng = parseFloat(lngInput?.value);

            if (!isNaN(lat) && !isNaN(lng) && lat !== 0 && lng !== 0) {
                window.open(`https://www.google.com/maps?q=${lat},${lng}`, '_blank');
                return;
            }

            const clinicName = clinicNameInput?.value?.trim() || '';
            const cityName = citySelect && citySelect.selectedIndex > 0 ? citySelect.options[citySelect.selectedIndex].text : '';
            const searchQuery = [clinicName, cityName].filter(Boolean).join(' ');

            if (searchQuery) {
                window.open(`https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(searchQuery)}`, '_blank');
            } else {
                window.open('https://www.google.com/maps', '_blank');
            }
        };

        // Resolve and Apply Google Maps links or coordinates from the paste box
        window.resolveAndApplyPastedGmapsLink = function () {
            const pasteInput = document.getElementById('gmaps-paste-input');
            const resolveBtn = document.getElementById('btn-resolve-gmaps');
            const val = pasteInput?.value?.trim();

            if (!val) {
                alert("{{ app()->getLocale() === 'ar' ? 'يرجى إدخال رابط خرائط Google أو الإحداثيات' : 'Please enter a Google Maps URL or coordinates' }}");
                pasteInput?.focus();
                return;
            }

            resolveBtn.disabled = true;
            resolveBtn.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i><span>{{ app()->getLocale() === 'ar' ? 'جاري فك الرابط...' : 'Resolving...' }}</span>`;

            // Call backend URL resolver endpoint
            fetch("{{ route('admin.mr.contacts.resolve_map_url') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ url: val })
            })
            .then(res => res.json())
            .then(data => {
                resolveBtn.disabled = false;
                resolveBtn.innerHTML = `<i class="fa-solid fa-wand-magic-sparkles"></i><span>{{ app()->getLocale() === 'ar' ? 'تطبيق الموقع 100%' : 'Apply Location 100%' }}</span>`;

                if (data.success && data.lat && data.lng) {
                    doctorMap.flyTo([data.lat, data.lng], 18, { duration: 1.5 });
                    placeOrUpdateMarker(data.lat, data.lng, true);
                    reverseGeocode(data.lat, data.lng, true);
                } else {
                    // Fallback to client-side parse
                    const localParse = parseGoogleMapsOrCoords(val);
                    if (localParse) {
                        doctorMap.flyTo([localParse.lat, localParse.lng], 18, { duration: 1.5 });
                        placeOrUpdateMarker(localParse.lat, localParse.lng, true);
                        reverseGeocode(localParse.lat, localParse.lng, true);
                    } else {
                        alert(data.message || "{{ app()->getLocale() === 'ar' ? 'تعذر استخراج الإحداثيات من هذا الرابط' : 'Could not extract coordinates from this link' }}");
                    }
                }
            })
            .catch(() => {
                resolveBtn.disabled = false;
                resolveBtn.innerHTML = `<i class="fa-solid fa-wand-magic-sparkles"></i><span>{{ app()->getLocale() === 'ar' ? 'تطبيق الموقع 100%' : 'Apply Location 100%' }}</span>`;
                const localParse = parseGoogleMapsOrCoords(val);
                if (localParse) {
                    doctorMap.flyTo([localParse.lat, localParse.lng], 18, { duration: 1.5 });
                    placeOrUpdateMarker(localParse.lat, localParse.lng, true);
                    reverseGeocode(localParse.lat, localParse.lng, true);
                } else {
                    alert("{{ app()->getLocale() === 'ar' ? 'تعذر الاتصال بالخادم لفك الرابط' : 'Failed to resolve map URL' }}");
                }
            });
        };

        function createCustomPinIcon() {
            return L.divIcon({
                className: 'clinic-pin-wrapper',
                html: `
                    <div class="clinic-pin-beacon"></div>
                    <div class="clinic-pin-body">
                        <i class="fa-solid fa-house-medical"></i>
                    </div>
                `,
                iconSize: [46, 46],
                iconAnchor: [23, 40],
                popupAnchor: [0, -42]
            });
        }

        function initDoctorMap() {
            const mapContainer = document.getElementById('doctor-address-map');
            if (!mapContainer || doctorMap) return;

            const { latInput, lngInput } = getFormInputs();

            let startLat = parseFloat(latInput?.value);
            let startLng = parseFloat(lngInput?.value);
            let hasExistingCoords = !isNaN(startLat) && !isNaN(startLng) && startLat !== 0 && startLng !== 0;

            if (!hasExistingCoords) {
                startLat = 30.0444;
                startLng = 31.2357;
            }

            doctorMap = L.map('doctor-address-map', {
                center: [startLat, startLng],
                zoom: hasExistingCoords ? 17 : 12,
                scrollWheelZoom: true
            });

            // Set Google Roadmap by default!
            setMapLayer('googleRoad');

            if (hasExistingCoords) {
                placeOrUpdateMarker(startLat, startLng, false);
                reverseGeocode(startLat, startLng, false);
            }

            // Click anywhere on map
            doctorMap.on('click', function (e) {
                const { lat, lng } = e.latlng;
                placeOrUpdateMarker(lat, lng, true);
                reverseGeocode(lat, lng, true);
            });

            // Locate Me Button
            const locateBtn = document.getElementById('btn-map-locate-me');
            if (locateBtn) {
                locateBtn.addEventListener('click', function () {
                    if (!("geolocation" in navigator)) {
                        alert("{{ app()->getLocale() === 'ar' ? 'تحديد الموقع غير مدعوم في متصفحك' : 'Geolocation not supported in browser' }}");
                        return;
                    }
                    locateBtn.disabled = true;
                    locateBtn.innerHTML = `<i class="fa-solid fa-spinner fa-spin text-sky-500"></i> <span>{{ app()->getLocale() === 'ar' ? 'جاري التحديد...' : 'Locating...' }}</span>`;

                    navigator.geolocation.getCurrentPosition(
                        (pos) => {
                            const lat = pos.coords.latitude;
                            const lng = pos.coords.longitude;
                            doctorMap.flyTo([lat, lng], 18, { duration: 1.5 });
                            placeOrUpdateMarker(lat, lng, true);
                            reverseGeocode(lat, lng, true);
                            locateBtn.disabled = false;
                            locateBtn.innerHTML = `<i class="fa-solid fa-location-crosshairs text-sky-500"></i> <span>{{ app()->getLocale() === 'ar' ? 'موقعي الحالي' : 'My GPS' }}</span>`;
                        },
                        (err) => {
                            locateBtn.disabled = false;
                            locateBtn.innerHTML = `<i class="fa-solid fa-location-crosshairs text-sky-500"></i> <span>{{ app()->getLocale() === 'ar' ? 'موقعي الحالي' : 'My GPS' }}</span>`;
                            alert("{{ app()->getLocale() === 'ar' ? 'تعذر الحصول على موقع الجهاز الحالي' : 'Unable to acquire device location' }}");
                        },
                        { enableHighAccuracy: true, timeout: 8000 }
                    );
                });
            }

            // Wire Search Input & Parser
            initSearchAndParser();

            // Wire input changes from manual typing in Lat / Lng inputs
            if (latInput && lngInput) {
                const syncFromInputs = () => {
                    const lat = parseFloat(latInput.value);
                    const lng = parseFloat(lngInput.value);
                    if (!isNaN(lat) && !isNaN(lng) && lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180) {
                        doctorMap.panTo([lat, lng]);
                        placeOrUpdateMarker(lat, lng, false);
                        reverseGeocode(lat, lng, false);
                    }
                };
                latInput.addEventListener('change', syncFromInputs);
                lngInput.addEventListener('change', syncFromInputs);
            }

            // Invalidate size after layout transition
            setTimeout(() => {
                if (doctorMap) doctorMap.invalidateSize();
            }, 300);
        }

        function placeOrUpdateMarker(lat, lng, shouldPan = true) {
            const roundedLat = parseFloat(lat).toFixed(6);
            const roundedLng = parseFloat(lng).toFixed(6);

            const { latInput, lngInput, clinicNameInput } = getFormInputs();
            if (latInput) latInput.value = roundedLat;
            if (lngInput) lngInput.value = roundedLng;

            if (!doctorMarker) {
                doctorMarker = L.marker([lat, lng], {
                    icon: createCustomPinIcon(),
                    draggable: true,
                    autoPan: true
                }).addTo(doctorMap);

                doctorMarker.on('dragend', function (e) {
                    const newLatLng = e.target.getLatLng();
                    placeOrUpdateMarker(newLatLng.lat, newLatLng.lng, false);
                    reverseGeocode(newLatLng.lat, newLatLng.lng, true);
                });
            } else {
                doctorMarker.setLatLng([lat, lng]);
            }

            // Visual Geofence Circle (150m perimeter)
            if (!doctorGeofenceCircle) {
                doctorGeofenceCircle = L.circle([lat, lng], {
                    radius: 150,
                    color: '#0284c7',
                    fillColor: '#38bdf8',
                    fillOpacity: 0.15,
                    weight: 1.5,
                    dashArray: '4, 4'
                }).addTo(doctorMap);
            } else {
                doctorGeofenceCircle.setLatLng([lat, lng]);
            }

            if (shouldPan) {
                doctorMap.panTo([lat, lng]);
            }

            // Update External Google Maps link
            const extGmapsBtn = document.getElementById('btn-external-google-maps');
            if (extGmapsBtn) {
                extGmapsBtn.href = `https://www.google.com/maps?q=${roundedLat},${roundedLng}`;
                extGmapsBtn.classList.remove('hidden');
            }

            // Update UI card coords display
            const coordsText = document.getElementById('geo-card-coords');
            if (coordsText) {
                coordsText.innerText = `${roundedLat}, ${roundedLng}`;
            }

            // Update paste input if empty
            const pasteInput = document.getElementById('gmaps-paste-input');
            if (pasteInput && !pasteInput.value) {
                pasteInput.value = `https://www.google.com/maps?q=${roundedLat},${roundedLng}`;
            }

            // Update marker popup
            const clinicName = clinicNameInput?.value || "{{ app()->getLocale() === 'ar' ? 'مقر العيادة / الطبيب' : 'Doctor / Clinic Location' }}";
            doctorMarker.bindPopup(`
                <div style="font-family: inherit; font-size: 12px; min-width: 190px;">
                    <strong style="color: #0369a1; font-size: 13px;">${clinicName}</strong>
                    <div style="color: #64748b; margin-top: 4px; font-family: monospace;">Lat: ${roundedLat}<br>Lng: ${roundedLng}</div>
                    <div style="margin-top: 6px;">
                        <a href="https://www.google.com/maps?q=${roundedLat},${roundedLng}" target="_blank" style="color: #d97706; font-weight: bold; text-decoration: none;">
                            <i class="fa-brands fa-google"></i> {{ app()->getLocale() === 'ar' ? 'عرض على خرائط Google ↗' : 'View on Google Maps ↗' }}
                        </a>
                    </div>
                </div>
            `);
        }

        // Reverse Geocoding with Nominatim OpenStreetMap
        function reverseGeocode(lat, lng, autoFillFields = true) {
            const addressCard = document.getElementById('geo-card-address');
            const applyBtn = document.getElementById('btn-apply-address');
            if (addressCard) {
                addressCard.innerHTML = `<span class="inline-flex items-center gap-1.5 text-slate-400"><i class="fa-solid fa-spinner fa-spin text-sky-500"></i> {{ app()->getLocale() === 'ar' ? 'جاري قراءة تفاصيل العنوان من الخريطة...' : 'Fetching address details from map...' }}</span>`;
            }

            const url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&addressdetails=1&accept-language=${appLocale}`;

            fetch(url, { headers: { 'Accept': 'application/json' } })
                .then(res => res.json())
                .then(data => {
                    lastReverseGeocodedData = data;
                    const formatted = data.display_name || `${lat}, ${lng}`;
                    if (addressCard) {
                        addressCard.innerText = formatted;
                    }
                    if (applyBtn) {
                        applyBtn.classList.remove('hidden');
                    }

                    if (autoFillFields) {
                        applyGeocodedDetails(data);
                    }
                })
                .catch(() => {
                    if (addressCard) {
                        addressCard.innerText = `GPS: ${lat.toFixed(6)}, ${lng.toFixed(6)}`;
                    }
                });
        }

        function applyGeocodedDetails(data) {
            if (!data) return;
            const { addressInput, regionInput, citySelect } = getFormInputs();
            const addr = data.address || {};

            const streetParts = [
                addr.amenity || addr.hospital || addr.clinic || addr.building || '',
                addr.house_number ? 'No. ' + addr.house_number : '',
                addr.road || addr.street || '',
                addr.neighbourhood || addr.suburb || ''
            ].filter(Boolean);

            const detailedAddress = streetParts.length > 0 
                ? streetParts.join(', ') + ' - ' + (addr.city || addr.town || addr.state || '')
                : data.display_name;

            if (addressInput && (!addressInput.value || addressInput.value.trim() === '')) {
                addressInput.value = detailedAddress;
            }

            const district = addr.suburb || addr.neighbourhood || addr.city_district || addr.quarter || '';
            if (regionInput && (!regionInput.value || regionInput.value.trim() === '') && district) {
                regionInput.value = district;
            }

            if (citySelect && (!citySelect.value || citySelect.value === '')) {
                const detectedCity = (addr.city || addr.town || addr.municipality || addr.state || '').toLowerCase().trim();
                if (detectedCity) {
                    for (let i = 0; i < citySelect.options.length; i++) {
                        const optText = citySelect.options[i].text.toLowerCase();
                        if (optText.includes(detectedCity) || detectedCity.includes(optText)) {
                            citySelect.selectedIndex = i;
                            break;
                        }
                    }
                }
            }
        }

        window.applyGeocodedToAddressField = function () {
            if (lastReverseGeocodedData) {
                const { addressInput } = getFormInputs();
                if (addressInput) {
                    addressInput.value = lastReverseGeocodedData.display_name;
                    addressInput.focus();
                }
            }
        };

        function parseGoogleMapsOrCoords(inputStr) {
            if (!inputStr) return null;
            const str = inputStr.trim();

            const rawCoordsRegex = /^[-+]?([1-8]?\d(\.\d+)?|90(\.0+)?)[,\s]+[-+]?(180(\.0+)?|((1[0-7]\d)|([1-9]?\d))(\.\d+)?)$/;
            if (rawCoordsRegex.test(str)) {
                const parts = str.split(/[,\s]+/).map(Number);
                if (parts.length >= 2 && !isNaN(parts[0]) && !isNaN(parts[1])) {
                    return { lat: parts[0], lng: parts[1] };
                }
            }

            const atCoordsMatch = str.match(/@(-?\d+\.\d+),(-?\d+\.\d+)/);
            if (atCoordsMatch) {
                return { lat: parseFloat(atCoordsMatch[1]), lng: parseFloat(atCoordsMatch[2]) };
            }

            const qCoordsMatch = str.match(/[?&]q=(-?\d+\.\d+),(-?\d+\.\d+)/);
            if (qCoordsMatch) {
                return { lat: parseFloat(qCoordsMatch[1]), lng: parseFloat(qCoordsMatch[2]) };
            }

            const llCoordsMatch = str.match(/[?&]ll=(-?\d+\.\d+),(-?\d+\.\d+)/);
            if (llCoordsMatch) {
                return { lat: parseFloat(llCoordsMatch[1]), lng: parseFloat(llCoordsMatch[2]) };
            }

            return null;
        }

        function initSearchAndParser() {
            const queryInput = document.getElementById('map-search-query');
            const searchBtn = document.getElementById('map-search-trigger-btn');
            const clearBtn = document.getElementById('map-search-clear');
            const dropdown = document.getElementById('map-search-dropdown');

            if (!queryInput || !searchBtn) return;

            const handleSearchOrParse = (query) => {
                if (!query) return;

                const parsedCoords = parseGoogleMapsOrCoords(query);
                if (parsedCoords) {
                    if (dropdown) dropdown.classList.add('hidden');
                    doctorMap.flyTo([parsedCoords.lat, parsedCoords.lng], 18, { duration: 1.5 });
                    placeOrUpdateMarker(parsedCoords.lat, parsedCoords.lng, true);
                    reverseGeocode(parsedCoords.lat, parsedCoords.lng, true);
                    return;
                }

                searchBtn.disabled = true;
                searchBtn.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i><span>{{ app()->getLocale() === 'ar' ? 'بحث...' : 'Searching...' }}</span>`;

                const searchUrl = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&addressdetails=1&limit=6&accept-language=${appLocale}`;

                fetch(searchUrl, { headers: { 'Accept': 'application/json' } })
                    .then(res => res.json())
                    .then(results => {
                        searchBtn.disabled = false;
                        searchBtn.innerHTML = `<i class="fa-solid fa-location-dot"></i><span>{{ app()->getLocale() === 'ar' ? 'بحث وتحديد' : 'Locate' }}</span>`;

                        if (!results || results.length === 0) {
                            if (dropdown) {
                                dropdown.innerHTML = `
                                    <div class="p-3 text-center text-xs text-slate-500 dark:text-slate-400">
                                        {{ app()->getLocale() === 'ar' ? 'لم يتم العثور على نتائج. جرب كتابة اسم الشارع أو الصق رابط Google Maps.' : 'No locations found. Try street name or paste Google Maps link.' }}
                                    </div>
                                `;
                                dropdown.classList.remove('hidden');
                            }
                            return;
                        }

                        renderDropdownResults(results);
                    })
                    .catch(() => {
                        searchBtn.disabled = false;
                        searchBtn.innerHTML = `<i class="fa-solid fa-location-dot"></i><span>{{ app()->getLocale() === 'ar' ? 'بحث وتحديد' : 'Locate' }}</span>`;
                    });
            };

            const renderDropdownResults = (results) => {
                if (!dropdown) return;
                dropdown.innerHTML = '';

                results.forEach(item => {
                    const lat = parseFloat(item.lat);
                    const lng = parseFloat(item.lon);
                    const itemBtn = document.createElement('button');
                    itemBtn.type = 'button';
                    itemBtn.className = 'w-full text-left rtl:text-right p-3 hover:bg-sky-50 dark:hover:bg-slate-700/50 transition flex items-start gap-2.5 text-xs';
                    itemBtn.innerHTML = `
                        <span class="w-6 h-6 rounded-md bg-sky-500/10 text-sky-600 dark:text-sky-400 flex items-center justify-center shrink-0 mt-0.5">
                            <i class="fa-solid fa-location-dot text-[11px]"></i>
                        </span>
                        <div class="flex-1 min-w-0">
                            <div class="font-bold text-slate-900 dark:text-white truncate">${item.display_name.split(',')[0]}</div>
                            <div class="text-[11px] text-slate-500 dark:text-slate-400 truncate mt-0.5">${item.display_name}</div>
                        </div>
                    `;

                    itemBtn.addEventListener('click', () => {
                        dropdown.classList.add('hidden');
                        queryInput.value = item.display_name.split(',')[0];
                        doctorMap.flyTo([lat, lng], 18, { duration: 1.5 });
                        placeOrUpdateMarker(lat, lng, true);
                        applyGeocodedDetails(item);
                        lastReverseGeocodedData = item;
                        const addressCard = document.getElementById('geo-card-address');
                        if (addressCard) addressCard.innerText = item.display_name;
                    });

                    dropdown.appendChild(itemBtn);
                });

                dropdown.classList.remove('hidden');
            };

            queryInput.addEventListener('input', function () {
                const val = this.value.trim();
                if (clearBtn) {
                    clearBtn.classList.toggle('hidden', val.length === 0);
                }

                if (parseGoogleMapsOrCoords(val)) {
                    handleSearchOrParse(val);
                    return;
                }

                clearTimeout(searchDebounceTimer);
                if (val.length >= 3) {
                    searchDebounceTimer = setTimeout(() => {
                        handleSearchOrParse(val);
                    }, 500);
                } else if (dropdown) {
                    dropdown.classList.add('hidden');
                }
            });

            queryInput.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    clearTimeout(searchDebounceTimer);
                    handleSearchOrParse(this.value.trim());
                }
            });

            searchBtn.addEventListener('click', function () {
                clearTimeout(searchDebounceTimer);
                handleSearchOrParse(queryInput.value.trim());
            });

            if (clearBtn) {
                clearBtn.addEventListener('click', function () {
                    queryInput.value = '';
                    clearBtn.classList.add('hidden');
                    if (dropdown) dropdown.classList.add('hidden');
                });
            }

            document.addEventListener('click', function (e) {
                if (dropdown && !dropdown.contains(e.target) && e.target !== queryInput && e.target !== searchBtn) {
                    dropdown.classList.add('hidden');
                }
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initDoctorMap);
        } else {
            initDoctorMap();
        }
    })();
</script>

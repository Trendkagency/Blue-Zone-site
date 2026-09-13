<x-layouts.auth :title="__('app.nav.register')">
    <div class="card p-6 sm:p-10 rounded-3xl bg-white dark:bg-[#062B49] border border-[#0A4F78]/15 dark:border-[#0A4F78]/30 shadow-2xl transition-all">
        <div class="text-center space-y-2 mb-8">
            <span class="text-[10px] font-black uppercase tracking-[0.25em] text-[#67B34A]">
                {{ app()->getLocale() === 'ar' ? 'إنشاء حساب عميل جديد' : 'CREATE ACCOUNT' }}
            </span>
            <h2 class="text-2xl sm:text-3xl font-black text-[#031827] dark:text-[#F6F5EF] tracking-tight">
                {{ __('app.nav.register') }}
            </h2>
            <p class="text-xs text-[#031827]/70 dark:text-[#F6F5EF]/70 font-medium">
                {{ app()->getLocale() === 'ar' ? 'سجل حسابك الطبي لإدارة الطلبات، العناوين، ونقاط الولاء الحيوية.' : 'Initiate your clinical longevity account to manage orders, addresses, and wellness subscriptions.' }}
            </p>
        </div>

        @if($errors->any())
            <div class="mb-6 p-4 rounded-2xl bg-red-500/15 border border-red-500/30 text-red-600 dark:text-red-400 text-xs space-y-1 font-semibold">
                @foreach($errors->all() as $error)
                    <div class="flex items-center gap-2">
                        <span>•</span>
                        <span>{{ $error }}</span>
                    </div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('customer.auth.register.submit') }}" method="POST" class="space-y-4" id="registerForm">
            @csrf

            <!-- Name -->
            <div class="space-y-1">
                <label for="name" class="block text-xs font-bold uppercase tracking-wider text-[#031827] dark:text-[#F6F5EF]">
                    {{ __('shop.checkout.full_name') }} *
                </label>
                <input 
                    id="name"
                    name="name" 
                    type="text" 
                    placeholder="e.g. Dr. Sarah Mansoor" 
                    value="{{ old('name') }}" 
                    required 
                    class="w-full px-4 py-3 rounded-xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/20 focus:border-[#2A8FC2] text-[#031827] dark:text-[#F6F5EF] placeholder-[#031827]/40 dark:placeholder-[#F6F5EF]/40 text-sm focus:outline-none transition-all"
                />
            </div>

            <!-- Email Address -->
            <div class="space-y-1">
                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-[#031827] dark:text-[#F6F5EF]">
                    {{ __('shop.checkout.email') }} *
                </label>
                <input 
                    id="email"
                    name="email" 
                    type="email" 
                    placeholder="name@example.com" 
                    value="{{ old('email') }}" 
                    required 
                    class="w-full px-4 py-3 rounded-xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/20 focus:border-[#2A8FC2] text-[#031827] dark:text-[#F6F5EF] placeholder-[#031827]/40 dark:placeholder-[#F6F5EF]/40 text-sm focus:outline-none transition-all"
                />
            </div>

            <!-- Country & City Row -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <!-- Country -->
                <div class="space-y-1">
                    <label for="country_select" class="block text-xs font-bold uppercase tracking-wider text-[#031827] dark:text-[#F6F5EF]">
                        {{ app()->getLocale() === 'ar' ? 'الدولة' : 'Country' }} *
                    </label>
                    <div class="relative">
                        <select 
                            id="country_select" 
                            name="country_id" 
                            required
                            class="w-full px-4 py-3 rounded-xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/20 focus:border-[#2A8FC2] text-[#031827] dark:text-[#F6F5EF] text-sm focus:outline-none transition-all cursor-pointer">
                            @foreach($countries as $c)
                                <option 
                                    value="{{ $c->id }}" 
                                    data-phone-code="{{ $c->phone_code }}"
                                    data-iso="{{ $c->iso2 }}"
                                    data-flag="{{ $c->flag_emoji }}"
                                    data-name-en="{{ $c->name_en }}"
                                    data-name-ar="{{ $c->name_ar }}"
                                    {{ (old('country_id', $defaultCountry?->id) == $c->id) ? 'selected' : '' }}>
                                    {{ $c->flag_emoji }} {{ app()->getLocale() === 'ar' ? $c->name_ar : $c->name_en }} ({{ $c->phone_code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <input type="hidden" name="country" id="country_name_hidden" value="{{ old('country', $defaultCountry?->name_en ?? 'Saudi Arabia') }}">
                </div>

                <!-- City (Dynamic Cascading + Custom input option) -->
                <div class="space-y-1">
                    <label for="city_select" class="block text-xs font-bold uppercase tracking-wider text-[#031827] dark:text-[#F6F5EF]">
                        {{ app()->getLocale() === 'ar' ? 'المدينة' : 'City' }} *
                    </label>
                    <div class="relative">
                        <select 
                            id="city_select" 
                            name="city_id" 
                            class="w-full px-4 py-3 rounded-xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/20 focus:border-[#2A8FC2] text-[#031827] dark:text-[#F6F5EF] text-sm focus:outline-none transition-all cursor-pointer">
                            <option value="">{{ app()->getLocale() === 'ar' ? 'جاري تحميل المدن...' : 'Loading cities...' }}</option>
                        </select>
                        <input 
                            type="text" 
                            id="custom_city_input" 
                            name="city" 
                            placeholder="{{ app()->getLocale() === 'ar' ? 'اكتب اسم المدينة هنا' : 'Enter city name' }}"
                            value="{{ old('city') }}"
                            class="hidden w-full mt-2 px-4 py-3 rounded-xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/20 focus:border-[#2A8FC2] text-[#031827] dark:text-[#F6F5EF] placeholder-[#031827]/40 dark:placeholder-[#F6F5EF]/40 text-sm focus:outline-none transition-all"
                        />
                    </div>
                </div>
            </div>

            <!-- Phone Number with Dynamic Dial Code Badge -->
            <div class="space-y-1">
                <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-[#031827] dark:text-[#F6F5EF]">
                    {{ app()->getLocale() === 'ar' ? 'رقم الجوال' : 'Mobile Number' }} *
                </label>
                <div class="flex rounded-xl overflow-hidden border border-[#0A4F78]/20 focus-within:border-[#2A8FC2] transition-all bg-[#F6F5EF] dark:bg-[#031827]" dir="ltr">
                    <div id="phone_code_badge" class="px-3.5 py-3 bg-[#0A4F78]/10 dark:bg-[#0A4F78]/20 text-[#031827] dark:text-[#F6F5EF] font-bold text-xs flex items-center gap-1.5 border-r border-[#0A4F78]/20 select-none whitespace-nowrap min-w-[90px] justify-center">
                        <span id="badge_flag">{{ $defaultCountry?->flag_emoji ?? '🇸🇦' }}</span>
                        <span id="badge_dial" class="font-mono">{{ $defaultCountry?->phone_code ?? '+966' }}</span>
                    </div>
                    <input 
                        id="phone"
                        name="phone" 
                        type="tel" 
                        dir="ltr"
                        placeholder="50 123 4567" 
                        value="{{ old('phone') }}" 
                        required
                        class="w-full px-4 py-3 bg-transparent text-[#031827] dark:text-[#F6F5EF] placeholder-[#031827]/40 dark:placeholder-[#F6F5EF]/40 text-sm focus:outline-none font-mono text-left"
                    />
                    <input type="hidden" name="phone_code" id="phone_code_hidden" value="{{ old('phone_code', $defaultCountry?->phone_code ?? '+966') }}">
                </div>
                <p class="text-[11px] text-[#031827]/60 dark:text-[#F6F5EF]/60 font-medium">
                    {{ app()->getLocale() === 'ar' ? 'سيتم ربط كود الدولة المختار برقم الجوال تلقائياً.' : 'Country dial code is automatically synchronized.' }}
                </p>
            </div>

            <!-- Address (Street) -->
            <div class="space-y-1">
                <label for="address" class="block text-xs font-bold uppercase tracking-wider text-[#031827] dark:text-[#F6F5EF]">
                    {{ app()->getLocale() === 'ar' ? 'العنوان الوطني / الشارع والحي' : 'Street Address / District' }}
                </label>
                <input 
                    id="address"
                    name="address" 
                    type="text" 
                    placeholder="e.g. King Fahd Road, Al-Olaya District" 
                    value="{{ old('address') }}" 
                    class="w-full px-4 py-3 rounded-xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/20 focus:border-[#2A8FC2] text-[#031827] dark:text-[#F6F5EF] placeholder-[#031827]/40 dark:placeholder-[#F6F5EF]/40 text-sm focus:outline-none transition-all"
                />
            </div>

            <!-- Password & Confirmation -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="space-y-1">
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-[#031827] dark:text-[#F6F5EF]">
                        {{ app()->getLocale() === 'ar' ? 'كلمة المرور' : 'Password' }} *
                    </label>
                    <input 
                        id="password"
                        name="password" 
                        type="password" 
                        placeholder="Min 8 characters" 
                        required 
                        class="w-full px-4 py-3 rounded-xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/20 focus:border-[#2A8FC2] text-[#031827] dark:text-[#F6F5EF] placeholder-[#031827]/40 dark:placeholder-[#F6F5EF]/40 text-sm focus:outline-none transition-all"
                    />
                </div>

                <div class="space-y-1">
                    <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-[#031827] dark:text-[#F6F5EF]">
                        {{ app()->getLocale() === 'ar' ? 'تأكيد كلمة المرور' : 'Confirm Password' }} *
                    </label>
                    <input 
                        id="password_confirmation"
                        name="password_confirmation" 
                        type="password" 
                        placeholder="••••••••" 
                        required 
                        class="w-full px-4 py-3 rounded-xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/20 focus:border-[#2A8FC2] text-[#031827] dark:text-[#F6F5EF] placeholder-[#031827]/40 dark:placeholder-[#F6F5EF]/40 text-sm focus:outline-none transition-all"
                    />
                </div>
            </div>

            <div class="pt-2">
                <label class="flex items-start gap-2.5 cursor-pointer">
                    <input type="checkbox" required checked class="mt-0.5 rounded border-[#0A4F78]/30 text-[#0A4F78] focus:ring-[#2A8FC2]">
                    <span class="text-xs text-[#031827]/70 dark:text-[#F6F5EF]/70">
                        {{ app()->getLocale() === 'ar' ? 'أوافق على' : 'I agree to the' }} <a href="{{ route('customer.pages.terms') }}" class="font-bold text-[#0A4F78] dark:text-[#2A8FC2] hover:underline">{{ app()->getLocale() === 'ar' ? 'شروط الخدمة' : 'Terms of Service' }}</a> {{ app()->getLocale() === 'ar' ? 'و' : 'and' }} <a href="{{ route('customer.pages.privacy') }}" class="font-bold text-[#0A4F78] dark:text-[#2A8FC2] hover:underline">{{ app()->getLocale() === 'ar' ? 'سياسة الخصوصية' : 'Privacy Policy' }}</a>.
                    </span>
                </label>
            </div>

            <x-captcha context="register" />

            <button type="submit" class="w-full py-4 bg-[#67B34A] hover:bg-[#589c3e] text-white text-xs font-black uppercase tracking-widest rounded-xl transition-all shadow-xl btn-sheen cursor-pointer mt-2">
                {{ __('app.nav.register') }} <i class="fa-solid fa-arrow-right rtl:rotate-180 ml-1.5"></i>
            </button>
        </form>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const countrySelect = document.getElementById('country_select');
                const citySelect = document.getElementById('city_select');
                const customCityInput = document.getElementById('custom_city_input');
                const badgeFlag = document.getElementById('badge_flag');
                const badgeDial = document.getElementById('badge_dial');
                const phoneCodeHidden = document.getElementById('phone_code_hidden');
                const countryNameHidden = document.getElementById('country_name_hidden');
                const isArabic = document.documentElement.lang === 'ar' || {{ app()->getLocale() === 'ar' ? 'true' : 'false' }};
                const oldCity = "{{ old('city') }}";
                const oldCityId = "{{ old('city_id') }}";

                function updatePhoneBadge() {
                    const selected = countrySelect.options[countrySelect.selectedIndex];
                    if (!selected) return;

                    const dial = selected.getAttribute('data-phone-code') || '+966';
                    const flag = selected.getAttribute('data-flag') || '🌐';
                    const name = isArabic ? (selected.getAttribute('data-name-ar') || selected.text) : (selected.getAttribute('data-name-en') || selected.text);

                    badgeFlag.textContent = flag;
                    badgeDial.textContent = dial;
                    phoneCodeHidden.value = dial;
                    countryNameHidden.value = selected.getAttribute('data-name-en') || selected.text;
                }

                function loadCities(countryId, preselectedCityId = null) {
                    citySelect.innerHTML = `<option value="">${isArabic ? 'جاري التحميل...' : 'Loading cities...'}</option>`;
                    
                    fetch(`/api/geo/countries/${countryId}/cities`)
                        .then(r => r.json())
                        .then(data => {
                            citySelect.innerHTML = '';
                            
                            if (data.cities && data.cities.length > 0) {
                                data.cities.forEach(city => {
                                    const opt = document.createElement('option');
                                    opt.value = city.id;
                                    opt.textContent = isArabic ? (city.name_ar || city.name_en) : (city.name_en || city.name_ar);
                                    opt.setAttribute('data-name-en', city.name_en);
                                    opt.setAttribute('data-name-ar', city.name_ar);
                                    if (preselectedCityId && preselectedCityId == city.id) {
                                        opt.selected = true;
                                    }
                                    citySelect.appendChild(opt);
                                });

                                // Add option for other / custom city
                                const otherOpt = document.createElement('option');
                                otherOpt.value = '__custom__';
                                otherOpt.textContent = isArabic ? '➕ مدينة أخرى (كتابة يدوية)' : '➕ Other City (Custom Entry)';
                                citySelect.appendChild(otherOpt);

                                customCityInput.classList.add('hidden');
                                customCityInput.required = false;
                            } else {
                                // No cities predefined in DB for this country, show custom input directly
                                const otherOpt = document.createElement('option');
                                otherOpt.value = '__custom__';
                                otherOpt.textContent = isArabic ? 'أدخل اسم مدينتك أدناه' : 'Enter your city below';
                                otherOpt.selected = true;
                                citySelect.appendChild(otherOpt);

                                customCityInput.classList.remove('hidden');
                                customCityInput.required = true;
                                if (!customCityInput.value && oldCity) {
                                    customCityInput.value = oldCity;
                                }
                            }

                            // If old city was custom or not matched
                            if (citySelect.value === '__custom__') {
                                customCityInput.classList.remove('hidden');
                                customCityInput.required = true;
                            }
                        })
                        .catch(() => {
                            citySelect.innerHTML = `<option value="__custom__">${isArabic ? 'أدخل اسم مدينتك يدوياً' : 'Enter city manually'}</option>`;
                            customCityInput.classList.remove('hidden');
                            customCityInput.required = true;
                        });
                }

                countrySelect.addEventListener('change', function() {
                    updatePhoneBadge();
                    loadCities(this.value);
                });

                citySelect.addEventListener('change', function() {
                    if (this.value === '__custom__') {
                        customCityInput.classList.remove('hidden');
                        customCityInput.required = true;
                        customCityInput.focus();
                    } else {
                        customCityInput.classList.add('hidden');
                        customCityInput.required = false;
                        const selectedCity = this.options[this.selectedIndex];
                        if (selectedCity) {
                            customCityInput.value = selectedCity.getAttribute('data-name-en') || selectedCity.text;
                        }
                    }
                });

                // Initial setup
                updatePhoneBadge();
                if (countrySelect.value) {
                    loadCities(countrySelect.value, oldCityId);
                }
            });
        </script>

        <div class="mt-8 pt-6 border-t border-[#0A4F78]/10 dark:border-[#0A4F78]/20 text-center text-xs text-[#031827]/70 dark:text-[#F6F5EF]/70">
            {{ app()->getLocale() === 'ar' ? 'لديك حساب بالفعل؟' : 'Already registered?' }}
            <a href="{{ route('customer.auth.login') }}" class="font-extrabold text-[#0A4F78] dark:text-[#2A8FC2] hover:underline ml-1">
                {{ __('app.nav.login') }}
            </a>
        </div>
    </div>
</x-layouts.auth>

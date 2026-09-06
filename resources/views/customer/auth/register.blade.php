<x-layouts.auth :title="__('app.nav.register')">
<<<<<<< HEAD
    <div class="card" style="padding: 2.5rem;">
        <h2 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 0.5rem; text-align: center;">
            {{ __('app.nav.register') }}
        </h2>
        <p class="text-sm text-muted text-center" style="margin-bottom: 2rem;">
            Initiate your clinical account to track biomarker goals and stack subscriptions.
        </p>

        <form action="{{ route('customer.account.dashboard') }}" method="GET">
            <x-forms.input 
                name="name" 
                :label="__('shop.checkout.full_name')" 
                placeholder="Dr. Zaid Al-Harbi" 
                required 
            />

            <x-forms.input 
                name="email" 
                type="email" 
                :label="__('shop.checkout.email')" 
                placeholder="name@example.com" 
                required 
            />

            <x-forms.input 
                name="password" 
                type="password" 
                label="Create Password" 
                placeholder="••••••••" 
                required 
                hint="Minimum 8 characters with at least one uppercase letter and symbol."
            />

            <x-forms.input 
                name="password_confirmation" 
                type="password" 
                label="Confirm Password" 
                placeholder="••••••••" 
                required 
            />

            <div style="margin-bottom: 1.5rem;">
                <label class="form-check">
                    <input type="checkbox" class="form-check-input" checked required>
                    <span class="text-xs">
                        I agree to the <a href="{{ route('customer.pages.terms') }}" class="font-bold" style="color: var(--color-primary);">Terms of Protocol Service</a> and <a href="{{ route('customer.pages.privacy') }}" class="font-bold" style="color: var(--color-primary);">Privacy Policy</a>.
=======
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

        <form action="{{ route('customer.auth.register.submit') }}" method="POST" class="space-y-4">
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

            <!-- Email & Mobile Number Row -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
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

                <div class="space-y-1">
                    <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-[#031827] dark:text-[#F6F5EF]">
                        {{ app()->getLocale() === 'ar' ? 'رقم الجوال' : 'Mobile Number' }} *
                    </label>
                    <input 
                        id="phone"
                        name="phone" 
                        type="tel" 
                        placeholder="+966 50 123 4567" 
                        value="{{ old('phone') }}" 
                        required
                        class="w-full px-4 py-3 rounded-xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/20 focus:border-[#2A8FC2] text-[#031827] dark:text-[#F6F5EF] placeholder-[#031827]/40 dark:placeholder-[#F6F5EF]/40 text-sm focus:outline-none transition-all"
                    />
                </div>
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

            <!-- City & Country Row -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="space-y-1">
                    <label for="city" class="block text-xs font-bold uppercase tracking-wider text-[#031827] dark:text-[#F6F5EF]">
                        {{ app()->getLocale() === 'ar' ? 'المدينة' : 'City' }}
                    </label>
                    <input 
                        id="city"
                        name="city" 
                        type="text" 
                        placeholder="Riyadh" 
                        value="{{ old('city', 'Riyadh') }}" 
                        class="w-full px-4 py-3 rounded-xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/20 focus:border-[#2A8FC2] text-[#031827] dark:text-[#F6F5EF] placeholder-[#031827]/40 dark:placeholder-[#F6F5EF]/40 text-sm focus:outline-none transition-all"
                    />
                </div>

                <div class="space-y-1">
                    <label for="country" class="block text-xs font-bold uppercase tracking-wider text-[#031827] dark:text-[#F6F5EF]">
                        {{ app()->getLocale() === 'ar' ? 'الدولة' : 'Country' }}
                    </label>
                    <select 
                        id="country" 
                        name="country" 
                        class="w-full px-4 py-3 rounded-xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/20 focus:border-[#2A8FC2] text-[#031827] dark:text-[#F6F5EF] text-sm focus:outline-none transition-all">
                        <option value="Saudi Arabia" {{ old('country') === 'Saudi Arabia' ? 'selected' : '' }}>🇸🇦 Saudi Arabia (المملكة العربية السعودية)</option>
                        <option value="United Arab Emirates" {{ old('country') === 'United Arab Emirates' ? 'selected' : '' }}>🇦🇪 United Arab Emirates (الإمارات)</option>
                        <option value="Kuwait" {{ old('country') === 'Kuwait' ? 'selected' : '' }}>🇰🇼 Kuwait (الكويت)</option>
                        <option value="Bahrain" {{ old('country') === 'Bahrain' ? 'selected' : '' }}>🇧🇭 Bahrain (البحرين)</option>
                        <option value="Qatar" {{ old('country') === 'Qatar' ? 'selected' : '' }}>🇶🇦 Qatar (قطر)</option>
                        <option value="Oman" {{ old('country') === 'Oman' ? 'selected' : '' }}>🇴🇲 Oman (عمان)</option>
                    </select>
                </div>
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
>>>>>>> origin/main
                    </span>
                </label>
            </div>

<<<<<<< HEAD
            <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">
=======
            <button type="submit" class="w-full py-4 bg-[#67B34A] hover:bg-[#589c3e] text-white text-xs font-black uppercase tracking-widest rounded-xl transition-all shadow-xl btn-sheen cursor-pointer mt-2">
>>>>>>> origin/main
                {{ __('app.nav.register') }} →
            </button>
        </form>

<<<<<<< HEAD
        <div style="margin-top: 1.75rem; text-align: center; font-size: 0.875rem; color: var(--color-text-muted);">
            Already registered? 
            <a href="{{ route('customer.auth.login') }}" class="font-bold" style="color: var(--color-primary);">
=======
        <div class="mt-8 pt-6 border-t border-[#0A4F78]/10 dark:border-[#0A4F78]/20 text-center text-xs text-[#031827]/70 dark:text-[#F6F5EF]/70">
            {{ app()->getLocale() === 'ar' ? 'لديك حساب بالفعل؟' : 'Already registered?' }}
            <a href="{{ route('customer.auth.login') }}" class="font-extrabold text-[#0A4F78] dark:text-[#2A8FC2] hover:underline ml-1">
>>>>>>> origin/main
                {{ __('app.nav.login') }}
            </a>
        </div>
    </div>
</x-layouts.auth>

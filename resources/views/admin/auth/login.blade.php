<x-layouts.app :title="__('admin.portal_title') . ' — ' . __('app.auth.login')">
    <div
        class="min-h-screen flex items-center justify-center bg-[#F6F5EF] dark:bg-[#031827] px-4 py-12 transition-colors duration-300">
        <div
            class="max-w-md w-full space-y-8 bg-white dark:bg-[#062B49] p-8 sm:p-10 rounded-3xl shadow-2xl border border-gray-100 dark:border-gray-800">
            <div class="text-center space-y-3">
                <a href="{{ route('customer.home') }}" class="inline-flex items-center justify-center">
                    <img src="{{ asset('assets/logo/logo-light.webp') }}" alt="{{ __('app.brand_name') }}"
                        class="h-9 mx-auto dark:hidden" onerror="this.onerror=null; this.src='{{ asset('assets/logo/logo-light.png') }}';">
                    <img src="{{ asset('assets/logo/logo-dark.webp') }}" alt="{{ __('app.brand_name') }}"
                        class="h-9 mx-auto hidden dark:block" onerror="this.onerror=null; this.src='{{ asset('assets/logo/logo-dark.png') }}';">
                </a>
                <h2 class="text-2xl font-black text-[#031827] dark:text-white tracking-tight">
                    {{ __('admin.portal_title') }}
                </h2>
                <p class="text-xs text-muted">
                    {{ app()->getLocale() === 'ar' ? 'سجل الدخول لإدارة الكتالوج، المخزون، والعمليات السريرية.' : 'Sign in to access clinical catalog, inventory and orders.' }}
                </p>
            </div>

            <form method="POST" action="{{ route('admin.login.submit') }}" class="mt-8 space-y-6">
                @csrf

                <div class="space-y-4">
                    <x-forms.input name="email" type="email" :label="__('app.auth.email')"
                        placeholder="admin@bluezone.com" :value="old('email')" required />

                    <x-forms.input name="password" type="password" :label="__('app.auth.password')"
                        placeholder="••••••••" :value="''" required />

                    <div class="flex items-center justify-between text-xs">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remember" class="form-check-input" checked>
                            <span class="text-gray-600 dark:text-gray-300">{{ __('app.auth.remember_me') }}</span>
                        </label>
                        <a href="{{ route('customer.home') }}" class="text-primary hover:underline font-semibold">
                            {{ __('app.nav.home') }}
                        </a>
                    </div>
                </div>

                <x-captcha context="admin" />

                <button type="submit"
                    class="w-full py-3.5 px-4 rounded-xl bg-[#0A4F78] hover:bg-[#062B49] text-white font-bold text-sm tracking-wider shadow-lg hover:shadow-xl transition-all active:scale-[0.99] cursor-pointer flex items-center justify-center gap-2">
                    <i class="fa-solid fa-arrow-right-to-bracket"></i>
                    <span>{{ __('app.auth.sign_in') }}</span>
                </button>

                @if(app()->environment('local', 'testing') || config('app.debug'))
                    <div class="p-3.5 bg-blue-50/70 dark:bg-[#031827]/70 border border-blue-100 dark:border-[#2A8FC2]/20 rounded-2xl text-xs space-y-2 text-gray-600 dark:text-gray-300">
                        <div class="flex items-center justify-between font-semibold text-[#0A4F78] dark:text-[#2A8FC2]">
                            <span class="flex items-center gap-1.5">
                                <i class="fa-solid fa-key text-[11px]"></i>
                                <span>{{ app()->getLocale() === 'ar' ? 'بيانات الدخول الافتراضية للمسؤول:' : 'Default Admin Credentials:' }}</span>
                            </span>
                            <button type="button" onclick="fillAdminDemo()" class="text-[11px] underline hover:text-[#062B49] dark:hover:text-blue-300 cursor-pointer font-bold">
                                {{ app()->getLocale() === 'ar' ? 'تعبئة تلقائية' : 'Auto Fill' }}
                            </button>
                        </div>
                        <div class="flex flex-col gap-1 font-mono text-[11px]">
                            <div>Email: <strong class="select-all text-[#031827] dark:text-white font-bold">admin@bluezone.com</strong> (or <strong class="select-all text-[#031827] dark:text-white font-bold">admin@admin.com</strong>)</div>
                            <div>Password: <strong class="select-all text-[#031827] dark:text-white font-bold">password</strong></div>
                        </div>
                    </div>
                    <script>
                        function fillAdminDemo() {
                            const emailInput = document.querySelector('input[name="email"]');
                            const passwordInput = document.querySelector('input[name="password"]');
                            if (emailInput) emailInput.value = 'admin@bluezone.com';
                            if (passwordInput) passwordInput.value = 'password';
                        }
                    </script>
                @endif
            </form>
        </div>
    </div>
</x-layouts.app>
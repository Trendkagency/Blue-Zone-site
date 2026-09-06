<x-layouts.auth :title="__('app.nav.login')">
    <div class="card p-6 sm:p-10 rounded-3xl bg-white dark:bg-[#062B49] border border-[#0A4F78]/15 dark:border-[#0A4F78]/30 shadow-2xl transition-all">
        <div class="text-center space-y-2 mb-8">
            <span class="text-[10px] font-black uppercase tracking-[0.25em] text-[#0A4F78] dark:text-[#2A8FC2]">
                CUSTOMER PORTAL
            </span>
            <h2 class="text-2xl sm:text-3xl font-black text-[#031827] dark:text-[#F6F5EF] tracking-tight">
                {{ __('app.nav.login') }}
            </h2>
            <p class="text-xs text-[#031827]/70 dark:text-[#F6F5EF]/70 font-medium">
                Access your personalized longevity protocols, orders, and clinical records.
            </p>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 rounded-2xl bg-[#67B34A]/15 border border-[#67B34A]/40 text-[#67B34A] text-xs font-bold flex items-center gap-3">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('warning'))
            <div class="mb-6 p-4 rounded-2xl bg-amber-500/15 border border-amber-500/40 text-amber-600 dark:text-amber-400 text-xs font-bold flex items-center gap-3">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>{{ session('warning') }}</span>
            </div>
        @endif

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

        <form action="{{ route('customer.auth.login.submit') }}" method="POST" class="space-y-5">
            @csrf

            <div class="space-y-1.5">
                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-[#031827] dark:text-[#F6F5EF]">
                    {{ __('shop.checkout.email') }}
                </label>
                <input 
                    id="email"
                    name="email" 
                    type="email" 
                    placeholder="name@example.com" 
                    value="{{ old('email', 'zaid.harbi@example.com') }}" 
                    required 
                    class="w-full px-4 py-3.5 rounded-xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/20 focus:border-[#2A8FC2] text-[#031827] dark:text-[#F6F5EF] placeholder-[#031827]/40 dark:placeholder-[#F6F5EF]/40 text-sm focus:outline-none transition-all"
                />
            </div>

            <div class="space-y-1.5">
                <label for="password" class="block text-xs font-bold uppercase tracking-wider text-[#031827] dark:text-[#F6F5EF]">
                    Password
                </label>
                <input 
                    id="password"
                    name="password" 
                    type="password" 
                    placeholder="••••••••" 
                    value="password"
                    required 
                    class="w-full px-4 py-3.5 rounded-xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/20 focus:border-[#2A8FC2] text-[#031827] dark:text-[#F6F5EF] placeholder-[#031827]/40 dark:placeholder-[#F6F5EF]/40 text-sm focus:outline-none transition-all"
                />
            </div>

            <div class="flex items-center justify-between pt-1">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="remember" value="1" class="rounded border-[#0A4F78]/30 text-[#0A4F78] focus:ring-[#2A8FC2]" checked>
                    <span class="text-xs font-bold text-[#031827]/70 dark:text-[#F6F5EF]/70">Remember this device</span>
                </label>

                <a href="{{ route('customer.auth.forgot-password') }}" class="text-xs font-bold text-[#0A4F78] dark:text-[#2A8FC2] hover:underline">
                    Forgot Password?
                </a>
            </div>

            <button type="submit" class="w-full py-4 bg-[#0A4F78] hover:bg-[#083f61] dark:bg-[#2A8FC2] dark:hover:bg-[#1f79a8] text-white text-xs font-black uppercase tracking-widest rounded-xl transition-all shadow-xl btn-sheen cursor-pointer">
                {{ __('app.nav.login') }} →
            </button>
        </form>

        <div class="mt-8 pt-6 border-t border-[#0A4F78]/10 dark:border-[#0A4F78]/20 text-center text-xs text-[#031827]/70 dark:text-[#F6F5EF]/70">
            Don't have a protocol profile? 
            <a href="{{ route('customer.auth.register') }}" class="font-extrabold text-[#0A4F78] dark:text-[#2A8FC2] hover:underline ml-1">
                {{ __('app.nav.register') }}
            </a>
        </div>
    </div>
</x-layouts.auth>

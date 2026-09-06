<<<<<<< HEAD
<x-layouts.auth title="Reset Password — Blue Zone">
    <div class="card" style="padding: 2.5rem;">
        <h2 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 0.5rem; text-align: center;">
            Recover Password
        </h2>
        <p class="text-sm text-muted text-center" style="margin-bottom: 2rem;">
            Provide your registered email address to receive a secure one-time cryptographic reset token.
        </p>

        <form action="{{ route('customer.auth.reset-password') }}" method="GET">
            <x-forms.input 
                name="email" 
                type="email" 
                :label="__('shop.checkout.email')" 
                placeholder="name@example.com" 
                required 
            />

            <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; margin-top: 1rem;">
                Send Recovery Instructions →
            </button>
        </form>

        <div style="margin-top: 1.75rem; text-align: center; font-size: 0.875rem; color: var(--color-text-muted);">
            Remembered your credentials? 
            <a href="{{ route('customer.auth.login') }}" class="font-bold" style="color: var(--color-primary);">
=======
<x-layouts.auth title="Recover Password — Blue Zone">
    <div class="card p-6 sm:p-10 rounded-3xl bg-white dark:bg-[#062B49] border border-[#0A4F78]/15 dark:border-[#0A4F78]/30 shadow-2xl transition-all">
        <div class="text-center space-y-2 mb-8">
            <span class="text-[10px] font-black uppercase tracking-[0.25em] text-[#0A4F78] dark:text-[#2A8FC2]">
                ACCOUNT RECOVERY
            </span>
            <h2 class="text-2xl sm:text-3xl font-black text-[#031827] dark:text-[#F6F5EF] tracking-tight">
                Recover Password
            </h2>
            <p class="text-xs text-[#031827]/70 dark:text-[#F6F5EF]/70 font-medium">
                Provide your registered email address to receive secure recovery instructions.
            </p>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 rounded-2xl bg-[#67B34A]/15 border border-[#67B34A]/40 text-[#67B34A] text-xs font-bold flex items-center gap-3">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('success') }}</span>
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

        <form action="{{ route('customer.auth.forgot-password.submit') }}" method="POST" class="space-y-5">
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
                    value="{{ old('email') }}" 
                    required 
                    class="w-full px-4 py-3.5 rounded-xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/20 focus:border-[#2A8FC2] text-[#031827] dark:text-[#F6F5EF] placeholder-[#031827]/40 dark:placeholder-[#F6F5EF]/40 text-sm focus:outline-none transition-all"
                />
            </div>

            <button type="submit" class="w-full py-4 bg-[#0A4F78] hover:bg-[#083f61] dark:bg-[#2A8FC2] dark:hover:bg-[#1f79a8] text-white text-xs font-black uppercase tracking-widest rounded-xl transition-all shadow-xl btn-sheen cursor-pointer">
                Send Recovery Link →
            </button>
        </form>

        <div class="mt-8 pt-6 border-t border-[#0A4F78]/10 dark:border-[#0A4F78]/20 text-center text-xs text-[#031827]/70 dark:text-[#F6F5EF]/70">
            Remembered your credentials? 
            <a href="{{ route('customer.auth.login') }}" class="font-extrabold text-[#0A4F78] dark:text-[#2A8FC2] hover:underline ml-1">
>>>>>>> origin/main
                Back to Sign In
            </a>
        </div>
    </div>
</x-layouts.auth>

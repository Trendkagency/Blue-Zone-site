<x-layouts.auth title="Set New Password — Blue Zone">
<<<<<<< HEAD
    <div class="card" style="padding: 2.5rem;">
        <h2 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 0.5rem; text-align: center;">
            Set New Password
        </h2>
        <p class="text-sm text-muted text-center" style="margin-bottom: 2rem;">
            Please choose a robust password for your account security.
        </p>

        <form action="{{ route('customer.auth.login') }}" method="GET">
            <x-forms.input 
                name="password" 
                type="password" 
                label="New Password" 
                placeholder="••••••••" 
                required 
            />

            <x-forms.input 
                name="password_confirmation" 
                type="password" 
                label="Confirm New Password" 
                placeholder="••••••••" 
                required 
            />

            <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; margin-top: 1rem;">
=======
    <div class="card p-6 sm:p-10 rounded-3xl bg-white dark:bg-[#062B49] border border-[#0A4F78]/15 dark:border-[#0A4F78]/30 shadow-2xl transition-all">
        <div class="text-center space-y-2 mb-8">
            <span class="text-[10px] font-black uppercase tracking-[0.25em] text-[#0A4F78] dark:text-[#2A8FC2]">
                CREDENTIAL SECURITY
            </span>
            <h2 class="text-2xl sm:text-3xl font-black text-[#031827] dark:text-[#F6F5EF] tracking-tight">
                Set New Password
            </h2>
            <p class="text-xs text-[#031827]/70 dark:text-[#F6F5EF]/70 font-medium">
                Please create a secure password for your protocol profile.
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

        <form action="{{ route('customer.auth.reset-password.submit') }}" method="POST" class="space-y-4">
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
                    value="{{ old('email', $email ?? '') }}" 
                    required 
                    class="w-full px-4 py-3.5 rounded-xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/20 focus:border-[#2A8FC2] text-[#031827] dark:text-[#F6F5EF] placeholder-[#031827]/40 dark:placeholder-[#F6F5EF]/40 text-sm focus:outline-none transition-all"
                />
            </div>

            <div class="space-y-1.5">
                <label for="password" class="block text-xs font-bold uppercase tracking-wider text-[#031827] dark:text-[#F6F5EF]">
                    New Password
                </label>
                <input 
                    id="password"
                    name="password" 
                    type="password" 
                    placeholder="Min 8 characters" 
                    required 
                    class="w-full px-4 py-3.5 rounded-xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/20 focus:border-[#2A8FC2] text-[#031827] dark:text-[#F6F5EF] placeholder-[#031827]/40 dark:placeholder-[#F6F5EF]/40 text-sm focus:outline-none transition-all"
                />
            </div>

            <div class="space-y-1.5">
                <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-[#031827] dark:text-[#F6F5EF]">
                    Confirm New Password
                </label>
                <input 
                    id="password_confirmation"
                    name="password_confirmation" 
                    type="password" 
                    placeholder="••••••••" 
                    required 
                    class="w-full px-4 py-3.5 rounded-xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/20 focus:border-[#2A8FC2] text-[#031827] dark:text-[#F6F5EF] placeholder-[#031827]/40 dark:placeholder-[#F6F5EF]/40 text-sm focus:outline-none transition-all"
                />
            </div>

            <button type="submit" class="w-full py-4 bg-[#0A4F78] hover:bg-[#083f61] dark:bg-[#2A8FC2] dark:hover:bg-[#1f79a8] text-white text-xs font-black uppercase tracking-widest rounded-xl transition-all shadow-xl btn-sheen cursor-pointer mt-2">
>>>>>>> origin/main
                Update Password & Sign In →
            </button>
        </form>
    </div>
</x-layouts.auth>

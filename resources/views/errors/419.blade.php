<x-layouts.app :title="__('app.session_expired', ['default' => '419 — Session Expired | Blue Zone'])">
    <div class="min-h-[80vh] flex items-center justify-center py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-xl w-full text-center space-y-8 bg-white dark:bg-[#062B49] p-8 sm:p-12 rounded-3xl border border-[#0A4F78]/15 dark:border-[#0A4F78]/30 shadow-2xl transition-all">
            
            <div class="mx-auto w-20 h-20 rounded-2xl bg-[#0A4F78]/10 border border-[#0A4F78]/30 flex items-center justify-center text-[#0A4F78] dark:text-[#2A8FC2] shadow-inner">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>

            <div class="space-y-3">
                <span class="inline-block px-3.5 py-1 rounded-full bg-[#0A4F78]/10 text-[#0A4F78] dark:text-[#2A8FC2] text-[10px] font-mono font-bold tracking-widest uppercase border border-[#0A4F78]/20">
                    HTTP 419 • SESSION TIMEOUT
                </span>
                <h1 class="text-3xl sm:text-4xl font-black text-[#031827] dark:text-[#F6F5EF] tracking-tight">
                    {{ app()->getLocale() === 'ar' ? 'انتهت صلاحية الجلسة' : 'Security Token Expired' }}
                </h1>
                <p class="text-xs sm:text-sm text-[#031827]/70 dark:text-[#F6F5EF]/70 font-medium leading-relaxed max-w-md mx-auto">
                    {{ app()->getLocale() === 'ar'
                        ? 'انتهت صلاحية رمز الأمان المؤقت بسبب فترة عدم النشاط. يرجى تحديث الصفحة والمحاولة مرة أخرى لحماية بياناتك.'
                        : 'Your CSRF cryptographic session token has expired due to inactivity. Please refresh the page to establish a secure updated session.' }}
                </p>
            </div>

            <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-3">
                <button onclick="window.location.reload();" class="w-full sm:w-auto px-8 py-3.5 rounded-xl bg-[#67B34A] hover:bg-[#589c3e] text-white text-xs font-black uppercase tracking-widest transition-all shadow-lg cursor-pointer">
                    ↻ {{ app()->getLocale() === 'ar' ? 'إعادة تحميل الصفحة' : 'Refresh Session' }}
                </button>
                <a href="{{ route('customer.home') }}" class="w-full sm:w-auto px-8 py-3.5 rounded-xl bg-[#0A4F78] hover:bg-[#083f61] text-white text-xs font-black uppercase tracking-widest transition-all shadow-lg text-center">
                    {{ app()->getLocale() === 'ar' ? 'الرئيسية' : 'Return Home' }}
                </a>
            </div>
        </div>
    </div>
</x-layouts.app>

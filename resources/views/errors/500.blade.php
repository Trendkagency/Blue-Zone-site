<x-layouts.app :title="__('app.server_error', ['default' => '500 — Server Diagnostic Error | Blue Zone'])">
    <div class="min-h-[80vh] flex items-center justify-center py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-xl w-full text-center space-y-8 bg-white dark:bg-[#062B49] p-8 sm:p-12 rounded-3xl border border-[#0A4F78]/15 dark:border-[#0A4F78]/30 shadow-2xl transition-all">
            
            <div class="mx-auto w-20 h-20 rounded-2xl bg-red-500/10 border border-red-500/30 flex items-center justify-center text-red-600 dark:text-red-400 shadow-inner">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>

            <div class="space-y-3">
                <span class="inline-block px-3.5 py-1 rounded-full bg-red-500/10 text-red-600 dark:text-red-400 text-[10px] font-mono font-bold tracking-widest uppercase border border-red-500/20">
                    HTTP 500 • INTERNAL ERROR
                </span>
                <h1 class="text-3xl sm:text-4xl font-black text-[#031827] dark:text-[#F6F5EF] tracking-tight">
                    {{ app()->getLocale() === 'ar' ? 'حدث خطأ غير متوقع في الخادم' : 'Unexpected System Diagnostic Alert' }}
                </h1>
                <p class="text-xs sm:text-sm text-[#031827]/70 dark:text-[#F6F5EF]/70 font-medium leading-relaxed max-w-md mx-auto">
                    {{ app()->getLocale() === 'ar'
                        ? 'واجه نظام المعالجة مشكلة فنية مؤقتة. تم تسجيل الحدث للمراجعة الفورية من قبل فريق الهندسة السريرية لدينا.'
                        : 'Our platform encountered an unexpected processing condition. This event has been logged for immediate triage by our engineering operations team.' }}
                </p>
            </div>

            <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ route('customer.home') }}" class="w-full sm:w-auto px-8 py-3.5 rounded-xl bg-[#67B34A] hover:bg-[#589c3e] text-white text-xs font-black uppercase tracking-widest transition-all shadow-lg text-center">
                    {{ app()->getLocale() === 'ar' ? 'الصفحة الرئيسية' : 'Return to Safety' }}
                </a>
                <a href="{{ route('customer.pages.contact') }}" class="w-full sm:w-auto px-8 py-3.5 rounded-xl bg-[#0A4F78] hover:bg-[#083f61] text-white text-xs font-black uppercase tracking-widest transition-all shadow-lg text-center">
                    {{ app()->getLocale() === 'ar' ? 'الإبلاغ عن المشكلة' : 'Report Incident' }}
                </a>
            </div>
        </div>
    </div>
</x-layouts.app>

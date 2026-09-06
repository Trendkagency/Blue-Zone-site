<x-layouts.app :title="__('admin.unauthorized_access', ['default' => '403 — Access Restricted | Blue Zone'])">
    <div class="min-h-[80vh] flex items-center justify-center py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-xl w-full text-center space-y-8 bg-white dark:bg-[#062B49] p-8 sm:p-12 rounded-3xl border border-[#0A4F78]/15 dark:border-[#0A4F78]/30 shadow-2xl transition-all">
            
            <!-- Icon Badge -->
            <div class="mx-auto w-20 h-20 rounded-2xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-amber-600 dark:text-amber-400 shadow-inner">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 15v2m0 0v2m0-2h2m-2 0H10m2-11a4 4 0 00-4 4v3H7a2 2 0 00-2 2v7a2 2 0 002 2h10a2 2 0 002-2v-7a2 2 0 00-2-2h-1V8a4 4 0 00-4-4z" />
                </svg>
            </div>

            <!-- Error Title & Description -->
            <div class="space-y-3">
                <span class="inline-block px-3.5 py-1 rounded-full bg-amber-500/10 text-amber-600 dark:text-amber-400 text-[10px] font-mono font-bold tracking-widest uppercase border border-amber-500/20">
                    HTTP 403 • ACCESS RESTRICTED
                </span>
                <h1 class="text-3xl sm:text-4xl font-black text-[#031827] dark:text-[#F6F5EF] tracking-tight">
                    {{ app()->getLocale() === 'ar' ? 'غير مصرح بالدخول إلى هذه الصفحة' : 'Access Authorization Required' }}
                </h1>
                <p class="text-xs sm:text-sm text-[#031827]/70 dark:text-[#F6F5EF]/70 font-medium leading-relaxed max-w-md mx-auto">
                    {{ app()->getLocale() === 'ar' 
                        ? 'عذراً، حسابك الحالي لا يمتلك الصلاحيات الإدارية أو السريرية اللازمة للوصول إلى هذا القسم. يرجى التواصل مع مسؤول النظام.' 
                        : 'Your authenticated session does not possess the clinical or administrative permissions required to access this protocol area. Please contact your system supervisor.' }}
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('customer.home') }}" class="w-full sm:w-auto px-7 py-3.5 rounded-xl bg-[#0A4F78] hover:bg-[#083f61] text-white text-xs font-black uppercase tracking-widest transition-all shadow-lg text-center">
                    ← {{ app()->getLocale() === 'ar' ? 'الرجوع للخلف' : 'Go Back' }}
                </a>
                <a href="{{ route('customer.home') }}" class="w-full sm:w-auto px-7 py-3.5 rounded-xl bg-[#67B34A] hover:bg-[#589c3e] text-white text-xs font-black uppercase tracking-widest transition-all shadow-lg text-center">
                    {{ app()->getLocale() === 'ar' ? 'الرئيسية' : 'Storefront Home' }}
                </a>
                <a href="{{ route('admin.dashboard') }}" class="w-full sm:w-auto px-6 py-3.5 rounded-xl bg-[#0A4F78]/10 hover:bg-[#0A4F78]/20 dark:bg-[#0A4F78]/40 text-[#0A4F78] dark:text-[#2A8FC2] text-xs font-bold uppercase tracking-wider transition-all text-center">
                    {{ app()->getLocale() === 'ar' ? 'لوحة التحكم' : 'Admin Portal' }}
                </a>
            </div>

            <!-- Footer Meta -->
            <div class="pt-4 border-t border-[#0A4F78]/10 dark:border-[#0A4F78]/20 text-[11px] text-[#031827]/40 dark:text-[#F6F5EF]/40 font-mono">
                BLUE ZONE SECURITY SUBSYSTEM • BZ-SEC-403
            </div>
        </div>
    </div>
</x-layouts.app>

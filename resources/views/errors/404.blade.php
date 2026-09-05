<x-layouts.app :title="__('app.not_found_title', ['default' => '404 — Protocol Not Found | Blue Zone'])">
    <div class="min-h-[85vh] flex items-center justify-center py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl w-full text-center space-y-8 bg-white dark:bg-[#062B49] p-8 sm:p-14 rounded-3xl border border-[#0A4F78]/15 dark:border-[#0A4F78]/30 shadow-2xl transition-all">
            
            <!-- Large Artistic 404 Badge -->
            <div class="relative flex items-center justify-center">
                <span class="text-7xl sm:text-9xl font-black tracking-tighter text-[#0A4F78]/10 dark:text-[#2A8FC2]/10 select-none font-mono">
                    404
                </span>
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-[#67B34A]/15 border border-[#67B34A]/40 flex items-center justify-center text-[#67B34A] shadow-lg backdrop-blur-sm">
                        <svg class="w-8 h-8 sm:w-10 sm:h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Error Headings -->
            <div class="space-y-3">
                <span class="inline-block px-3.5 py-1 rounded-full bg-[#2A8FC2]/15 text-[#0A4F78] dark:text-[#2A8FC2] text-[10px] font-mono font-extrabold tracking-[0.25em] uppercase border border-[#2A8FC2]/30">
                    DIAGNOSTIC • RESOURCE NOT FOUND
                </span>
                <h1 class="text-3xl sm:text-5xl font-black text-[#031827] dark:text-[#F6F5EF] tracking-tight">
                    {{ app()->getLocale() === 'ar' ? 'الصفحة المطلوبة غير موجودة' : 'The Path to Longevity Took a Detour' }}
                </h1>
                <p class="text-xs sm:text-sm text-[#031827]/70 dark:text-[#F6F5EF]/70 font-medium leading-relaxed max-w-lg mx-auto">
                    {{ app()->getLocale() === 'ar'
                        ? 'يبدو أن الرابط الذي تبحث عنه قد تم نقله أو حذفه أو أن العنوان المدخل غير صحيح. يمكنك البحث في بروتوكولاتنا أو العودة للرئيسية.'
                        : 'The clinical formulation, document, or page you requested could not be located in our registry. Explore our master catalog or navigate back to the main discovery hub.' }}
                </p>
            </div>

            <!-- Live Search Bar Trigger -->
            <div class="max-w-md mx-auto relative">
                <button onclick="if(window.BLUEZONE_SEARCH){BLUEZONE_SEARCH.open();}" class="w-full flex items-center justify-between px-5 py-3.5 rounded-2xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/25 text-[#031827]/60 dark:text-[#F6F5EF]/60 text-xs font-medium hover:border-[#2A8FC2] transition-all cursor-pointer shadow-sm">
                    <span class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-[#0A4F78] dark:text-[#2A8FC2]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <span>{{ app()->getLocale() === 'ar' ? 'ابحث في المكملات والتركيبات...' : 'Search clinical formulations & ingredients...' }}</span>
                    </span>
                    <kbd class="hidden sm:inline-block px-2 py-0.5 rounded bg-black/10 dark:bg-white/10 text-[10px] font-mono">⌘K</kbd>
                </button>
            </div>

            <!-- Fast Navigation Quick Links -->
            <div class="pt-4 border-t border-[#0A4F78]/10 dark:border-[#0A4F78]/20 flex flex-wrap items-center justify-center gap-3">
                <a href="{{ route('customer.home') }}" class="px-6 py-3 rounded-xl bg-[#67B34A] hover:bg-[#589c3e] text-white text-xs font-black uppercase tracking-widest transition-all shadow-md">
                    {{ app()->getLocale() === 'ar' ? 'الصفحة الرئيسية' : 'Home Discovery' }}
                </a>
                <a href="{{ route('customer.shop') }}" class="px-6 py-3 rounded-xl bg-[#0A4F78] hover:bg-[#083f61] text-white text-xs font-black uppercase tracking-widest transition-all shadow-md">
                    {{ app()->getLocale() === 'ar' ? 'جميع التركيبات' : 'All Formulations' }}
                </a>
                <a href="{{ route('customer.pages.science') }}" class="px-6 py-3 rounded-xl bg-[#0A4F78]/10 hover:bg-[#0A4F78]/20 dark:bg-[#0A4F78]/40 text-[#0A4F78] dark:text-[#2A8FC2] text-xs font-bold uppercase tracking-wider transition-all">
                    {{ app()->getLocale() === 'ar' ? 'أبحاثنا السريرية' : 'Our Science' }}
                </a>
                <a href="{{ route('customer.pages.contact') }}" class="px-6 py-3 rounded-xl bg-[#0A4F78]/10 hover:bg-[#0A4F78]/20 dark:bg-[#0A4F78]/40 text-[#031827]/80 dark:text-[#F6F5EF]/80 text-xs font-bold uppercase tracking-wider transition-all">
                    {{ app()->getLocale() === 'ar' ? 'تواصل معنا' : 'Contact Support' }}
                </a>
            </div>

            <div class="text-[10px] text-[#031827]/40 dark:text-[#F6F5EF]/40 font-mono">
                BLUE ZONE ROUTER ENGINE • STATUS 404 NOT FOUND
            </div>
        </div>
    </div>
</x-layouts.app>

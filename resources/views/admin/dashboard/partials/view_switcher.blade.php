@if(auth()->user() && (auth()->user()->isSuperAdmin() || auth()->user()->isAdmin() || auth()->user()->isMrLineManager()))
    <div class="mb-6 bg-white dark:bg-[#062B49] p-3.5 rounded-2xl border border-slate-200/80 dark:border-[#15456E] shadow-xs flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-2">
            <span class="text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                <i class="fa-solid fa-layer-group text-sky-500"></i>
                {{ app()->getLocale() === 'ar' ? 'عرض لوحة التحكم حسب القسم:' : 'Switch Role Dashboard:' }}
            </span>
        </div>

        <div class="inline-flex p-1 bg-slate-100 dark:bg-[#031827] rounded-xl border border-slate-200/70 dark:border-[#15456E] flex-wrap gap-1">
            @if(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin())
                <!-- Executive Overview -->
                <a href="{{ route('admin.dashboard', ['view' => 'executive']) }}" 
                   class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 {{ ($currentView ?? 'executive') === 'executive' ? 'bg-[#0A4F78] text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white' }}">
                    <i class="fa-solid fa-crown text-amber-300"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'نظرة تنفيذية شاملة' : 'Executive' }}</span>
                </a>

                <!-- Operations & Warehouse -->
                <a href="{{ route('admin.dashboard', ['view' => 'operations']) }}" 
                   class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 {{ ($currentView ?? '') === 'operations' ? 'bg-[#0A4F78] text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white' }}">
                    <i class="fa-solid fa-boxes-stacked text-emerald-400"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'العمليات والمخزون' : 'Operations' }}</span>
                </a>

                <!-- Commercial B2B Sales -->
                <a href="{{ route('admin.dashboard', ['view' => 'commercial']) }}" 
                   class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 {{ ($currentView ?? '') === 'commercial' ? 'bg-[#0A4F78] text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white' }}">
                    <i class="fa-solid fa-briefcase text-blue-400"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'المبيعات التجارية (B2B)' : 'Commercial' }}</span>
                </a>

                <!-- HR & Workforce -->
                <a href="{{ route('admin.dashboard', ['view' => 'hr']) }}" 
                   class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 {{ ($currentView ?? '') === 'hr' ? 'bg-[#0A4F78] text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white' }}">
                    <i class="fa-solid fa-users-gear text-purple-400"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'الموارد البشرية' : 'HR & Staff' }}</span>
                </a>
            @endif

            @if(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin() || auth()->user()->isMrLineManager())
                <!-- Field MR Manager Ops -->
                <a href="{{ route('admin.dashboard', ['view' => 'mr_manager']) }}" 
                   class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 {{ ($currentView ?? '') === 'mr_manager' ? 'bg-[#0A4F78] text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white' }}">
                    <i class="fa-solid fa-user-tie text-cyan-300"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'إشراف المناديب الميدانيين' : 'MR Supervision' }}</span>
                </a>

                <!-- Field MR Rep View -->
                <a href="{{ route('admin.dashboard', ['view' => 'mr']) }}" 
                   class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 {{ ($currentView ?? '') === 'mr' ? 'bg-[#0A4F78] text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white' }}">
                    <i class="fa-solid fa-user-doctor text-cyan-400"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'بوابة المندوب (MR)' : 'Rep Route View' }}</span>
                </a>
            @endif
        </div>
    </div>
@endif

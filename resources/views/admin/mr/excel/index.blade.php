<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'مركز إكسل واستيراد وتصدير بيانات المناديب والأطباء' : 'MR Excel Hub & Field Data Center'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'استيراد جماعي ذكي للأطباء والعيادات، تصدير متعدد المعايير لتقارير التغطية والزيارات، وجدول إكسل يدوي تفاعلي' : 'Batch Excel doctor onboarding, multi-criteria visit & coverage exports, and live in-browser manual spreadsheet grid for MR field CRM'"
    :breadcrumbs="[
        (app()->getLocale() === 'ar' ? 'لوحة تحكم المناديب' : 'MR CRM') => route('admin.mr.dashboard'),
        (app()->getLocale() === 'ar' ? 'الأطباء والعيادات' : 'Doctors') => route('admin.mr.contacts.index'),
        (app()->getLocale() === 'ar' ? 'مركز إكسل الميداني' : 'MR Excel Hub') => route('admin.mr.excel.index')
    ]"
>
    @php
        $isAr = app()->getLocale() === 'ar';
    @endphp

    <!-- Custom Styling for the MR Excel Hub (Dark and Light Mode) -->
    <style>
        .mr-tab-btn {
            padding: 0.75rem 1.25rem;
            font-size: 0.875rem;
            font-weight: 700;
            border-radius: 0.75rem;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border: 1px solid transparent;
            cursor: pointer;
            background: transparent;
            color: #64748B;
        }
        .mr-tab-btn:hover {
            color: #0A4F78;
            background: rgba(10, 79, 120, 0.05);
        }
        html.dark .mr-tab-btn {
            color: #94A3B8;
        }
        html.dark .mr-tab-btn:hover {
            color: #38BDF8;
            background: rgba(56, 189, 248, 0.1);
        }
        .mr-tab-btn.active {
            background: linear-gradient(135deg, #0A4F78 0%, #0284C7 100%);
            color: #FFFFFF !important;
            border-color: #0A4F78;
            box-shadow: 0 4px 14px rgba(10, 79, 120, 0.3);
        }
        html.dark .mr-tab-btn.active {
            background: linear-gradient(135deg, #0284C7 0%, #0A4F78 100%);
            border-color: #0284C7;
            color: #FFFFFF !important;
            box-shadow: 0 4px 14px rgba(2, 132, 199, 0.4);
        }

        /* Spreadsheet Table Inputs */
        .mr-grid-cell-input {
            width: 100%;
            background: transparent;
            border: 1px solid transparent;
            padding: 0.4rem 0.5rem;
            font-size: 0.8125rem;
            border-radius: 0.375rem;
            transition: all 0.15s ease;
            color: inherit;
        }
        .mr-grid-cell-input:hover {
            border-color: #CBD5E1;
            background: #FFFFFF;
        }
        html.dark .mr-grid-cell-input:hover {
            border-color: #1E4E73;
            background: #0B2942;
        }
        .mr-grid-cell-input:focus {
            outline: none;
            border-color: #0284C7;
            background: #FFFFFF;
            box-shadow: 0 0 0 2px rgba(2, 132, 199, 0.2);
        }
        html.dark .mr-grid-cell-input:focus {
            background: #071F33;
            border-color: #38BDF8;
            box-shadow: 0 0 0 2px rgba(56, 189, 248, 0.25);
        }

        .mr-dropzone-box {
            border: 2px dashed #CBD5E1;
            border-radius: 1.25rem;
            padding: 2.75rem 1.5rem;
            text-align: center;
            background: #F8FAFC;
            transition: all 0.25s ease;
            cursor: pointer;
        }
        .mr-dropzone-box:hover, .mr-dropzone-box.dragover {
            border-color: #0284C7;
            background: #F0F9FF;
        }
        html.dark .mr-dropzone-box {
            border-color: #1E4E73;
            background: #031827;
        }
        html.dark .mr-dropzone-box:hover, html.dark .mr-dropzone-box.dragover {
            border-color: #38BDF8;
            background: #06243C;
        }
    </style>

    <!-- Top Action Bar & Template Download Links -->
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-2xl bg-emerald-500/10 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-file-excel"></i>
                </div>
                <span>{{ $isAr ? 'مركز إكسل واستيراد الأطباء والزيارات' : 'MR CRM Excel Hub & Field Data Center' }}</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                {{ $isAr ? 'إدارة واستيراد بيانات الأطباء والعيادات، تصدير سجلات التغطية والزيارات، وتداول الملفات بدقة وسرعة فائقة' : 'Enterprise batch doctor onboarding, multi-criteria field visit export, and live in-browser manual spreadsheet grid for Medical Reps' }}
            </p>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            <!-- Download Doctors Template -->
            <a href="{{ route('admin.mr.excel.template', ['type' => 'doctors', 'format' => 'xlsx']) }}" 
                class="btn btn-sm rounded-xl font-bold bg-white dark:bg-[#071F33] border border-slate-200 dark:border-[#1E4E73] text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-[#0B2942] shadow-xs flex items-center gap-1.5 transition-all">
                <i class="fa-solid fa-user-doctor text-sky-500"></i>
                <span>{{ $isAr ? 'قالب الأطباء والعيادات (XLSX)' : 'Doctors Template (XLSX)' }}</span>
            </a>

            <!-- Download Visits Template -->
            <a href="{{ route('admin.mr.excel.template', ['type' => 'visits', 'format' => 'xlsx']) }}" 
                class="btn btn-sm rounded-xl font-bold bg-white dark:bg-[#071F33] border border-slate-200 dark:border-[#1E4E73] text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-[#0B2942] shadow-xs flex items-center gap-1.5 transition-all">
                <i class="fa-solid fa-location-dot text-emerald-500"></i>
                <span>{{ $isAr ? 'قالب سجل الزيارات (XLSX)' : 'Visits Template (XLSX)' }}</span>
            </a>

            <!-- CSV Option Dropdown or Link -->
            <a href="{{ route('admin.mr.excel.template', ['type' => 'doctors', 'format' => 'csv']) }}" 
                class="btn btn-sm rounded-xl font-bold bg-slate-100 hover:bg-slate-200 dark:bg-[#0B2942] dark:hover:bg-[#133957] text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-[#1E4E73]"
                title="Download CSV Format">
                <i class="fa-solid fa-file-csv text-slate-400"></i>
                <span>CSV</span>
            </a>
        </div>
    </div>

    <!-- Quick Stats & KPI Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3.5 mb-6">
        <!-- Total Doctors -->
        <div class="p-4 rounded-2xl bg-white dark:bg-[#071F33] border border-slate-200/90 dark:border-[#133957] shadow-xs">
            <div class="flex items-center justify-between text-xs font-bold text-slate-500 dark:text-slate-400 mb-1">
                <span>{{ $isAr ? 'إجمالي الأطباء' : 'Total HCPs' }}</span>
                <i class="fa-solid fa-stethoscope text-sky-500"></i>
            </div>
            <div class="text-xl font-black text-slate-900 dark:text-white">
                {{ number_format($stats['total_doctors']) }}
            </div>
            <div class="text-[11px] text-emerald-600 dark:text-emerald-400 mt-1 font-semibold flex items-center gap-1">
                <i class="fa-solid fa-circle-check text-[10px]"></i>
                <span>{{ number_format($stats['active_doctors']) }} {{ $isAr ? 'نشط ميدانياً' : 'Active' }}</span>
            </div>
        </div>

        <!-- Visits This Month -->
        <div class="p-4 rounded-2xl bg-white dark:bg-[#071F33] border border-slate-200/90 dark:border-[#133957] shadow-xs">
            <div class="flex items-center justify-between text-xs font-bold text-slate-500 dark:text-slate-400 mb-1">
                <span>{{ $isAr ? 'زيارات هذا الشهر' : 'Visits Done' }}</span>
                <i class="fa-solid fa-check-double text-emerald-500"></i>
            </div>
            <div class="text-xl font-black text-emerald-600 dark:text-emerald-400">
                {{ number_format($stats['total_visits_month']) }}
            </div>
            <div class="text-[11px] text-slate-400 mt-1">
                {{ $isAr ? 'خلال الشهر الجاري' : 'This calendar month' }}
            </div>
        </div>

        <!-- Scheduled Visits -->
        <div class="p-4 rounded-2xl bg-white dark:bg-[#071F33] border border-slate-200/90 dark:border-[#133957] shadow-xs">
            <div class="flex items-center justify-between text-xs font-bold text-slate-500 dark:text-slate-400 mb-1">
                <span>{{ $isAr ? 'الزيارات المجدولة' : 'Scheduled' }}</span>
                <i class="fa-solid fa-calendar-check text-indigo-500"></i>
            </div>
            <div class="text-xl font-black text-indigo-600 dark:text-indigo-400">
                {{ number_format($stats['total_scheduled_month']) }}
            </div>
            <div class="text-[11px] text-slate-400 mt-1">
                {{ $isAr ? 'زيارة قيد التنفيذ' : 'Upcoming in agenda' }}
            </div>
        </div>

        <!-- Active Reps -->
        <div class="p-4 rounded-2xl bg-white dark:bg-[#071F33] border border-slate-200/90 dark:border-[#133957] shadow-xs">
            <div class="flex items-center justify-between text-xs font-bold text-slate-500 dark:text-slate-400 mb-1">
                <span>{{ $isAr ? 'المناديب النشطين' : 'Field Reps' }}</span>
                <i class="fa-solid fa-user-doctor text-cyan-500"></i>
            </div>
            <div class="text-xl font-black text-slate-900 dark:text-white">
                {{ $stats['active_reps_count'] }}
            </div>
            <div class="text-[11px] text-slate-400 mt-1">
                {{ $isAr ? 'مناديب ميدانيين' : 'Active medical reps' }}
            </div>
        </div>

        <!-- Territory Coverage Rate -->
        <div class="p-4 rounded-2xl bg-white dark:bg-[#071F33] border border-slate-200/90 dark:border-[#133957] shadow-xs">
            <div class="flex items-center justify-between text-xs font-bold text-slate-500 dark:text-slate-400 mb-1">
                <span>{{ $isAr ? 'نسبة التغطية' : 'Coverage Rate' }}</span>
                <i class="fa-solid fa-chart-pie text-amber-500"></i>
            </div>
            <div class="text-xl font-black text-amber-500">
                {{ $stats['coverage_rate'] }}%
            </div>
            <div class="text-[11px] text-slate-400 mt-1">
                {{ $isAr ? 'من قائمة المستهدفين' : 'Of assigned HCPs' }}
            </div>
        </div>

        <!-- Active Cycle -->
        <div class="p-4 rounded-2xl bg-white dark:bg-[#071F33] border border-slate-200/90 dark:border-[#133957] shadow-xs">
            <div class="flex items-center justify-between text-xs font-bold text-slate-500 dark:text-slate-400 mb-1">
                <span>{{ $isAr ? 'دورة الزيارات' : 'Visit Cycle' }}</span>
                <i class="fa-solid fa-repeat text-purple-500"></i>
            </div>
            <div class="text-sm font-black text-purple-600 dark:text-purple-400 truncate" title="{{ $activeCycle?->name ?? 'Default Cycle' }}">
                {{ $activeCycle?->name ?? ($isAr ? 'الدورة الافتراضية' : 'Default Cycle') }}
            </div>
            <div class="text-[11px] text-slate-400 mt-1">
                {{ $activeCycle?->start_date ? \Carbon\Carbon::parse($activeCycle->start_date)->format('M d') : 'Ongoing' }}
            </div>
        </div>
    </div>

    <!-- Alpine Root Component for the MR Excel Hub -->
    <div x-data="mrExcelHub({
        specialties: {{ Js::from($specialties) }},
        classifications: {{ Js::from($classifications) }},
        cities: {{ Js::from($cities) }},
        areas: {{ Js::from($areas) }},
        breaks: {{ Js::from($breaks) }},
        medicalReps: {{ Js::from($medicalReps) }},
        sampleDoctors: {{ Js::from($sampleDoctors) }},
        activeCycleId: {{ $activeCycle?->id ?? 'null' }}
    })" class="space-y-6">

        <!-- Navigation Tabs Bar -->
        <div class="flex items-center justify-between border-b border-slate-200 dark:border-[#133957] pb-3 flex-wrap gap-3">
            <div class="flex items-center gap-2 p-1.5 rounded-2xl bg-slate-100/90 dark:bg-[#031827] border border-slate-200/70 dark:border-[#133957]">
                <button type="button" 
                    @click="activeTab = 'grid'" 
                    :class="activeTab === 'grid' ? 'active' : ''"
                    class="mr-tab-btn">
                    <i class="fa-solid fa-table-cells"></i>
                    <span>{{ $isAr ? 'جدول الإدخال التفاعلي المباشر' : 'Live In-Browser Grid' }}</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-white/20 text-white" x-text="gridRows.length"></span>
                </button>

                <button type="button" 
                    @click="activeTab = 'import'" 
                    :class="activeTab === 'import' ? 'active' : ''"
                    class="mr-tab-btn">
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                    <span>{{ $isAr ? 'استيراد جماعي (Excel / CSV)' : 'Batch File Import' }}</span>
                </button>

                <button type="button" 
                    @click="activeTab = 'export'" 
                    :class="activeTab === 'export' ? 'active' : ''"
                    class="mr-tab-btn">
                    <i class="fa-solid fa-file-export"></i>
                    <span>{{ $isAr ? 'تصدير التقارير والبيانات' : 'Multi-Filter Export' }}</span>
                </button>
            </div>

            <!-- Quick Action Info / Indicator -->
            <div class="text-xs font-semibold text-slate-500 dark:text-slate-400 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>{{ $isAr ? 'النظام متزامن مع قاعدة بيانات الأطباء' : 'Live Synced with MR Contacts' }}</span>
            </div>
        </div>

        <!-- ==================================================================== -->
        <!-- TAB 1: LIVE IN-BROWSER MANUAL SPREADSHEET GRID                        -->
        <!-- ==================================================================== -->
        <div x-show="activeTab === 'grid'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-4">
            
            <!-- Grid Toolbar -->
            <div class="p-4 rounded-2xl bg-white dark:bg-[#071F33] border border-slate-200/90 dark:border-[#133957] flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3 shadow-xs">
                <!-- Left Buttons: Add Rows, Clear, Reset -->
                <div class="flex items-center gap-2 flex-wrap">
                    <button type="button" @click="addRow(1)" class="btn btn-sm rounded-xl font-bold bg-slate-100 hover:bg-slate-200 dark:bg-[#0B2942] dark:hover:bg-[#133957] text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-[#1E4E73] cursor-pointer">
                        <i class="fa-solid fa-plus text-sky-500"></i>
                        <span>{{ $isAr ? 'إضافة طبيب' : '+1 Row' }}</span>
                    </button>
                    <button type="button" @click="addRow(5)" class="btn btn-sm rounded-xl font-bold bg-slate-100 hover:bg-slate-200 dark:bg-[#0B2942] dark:hover:bg-[#133957] text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-[#1E4E73] cursor-pointer">
                        <i class="fa-solid fa-layer-group text-indigo-500"></i>
                        <span>{{ $isAr ? '+5 أطباء' : '+5 Rows' }}</span>
                    </button>
                    <button type="button" @click="clearEmptyRows()" class="btn btn-sm rounded-xl font-bold bg-slate-100 hover:bg-slate-200 dark:bg-[#0B2942] dark:hover:bg-[#133957] text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-[#1E4E73] cursor-pointer" title="Remove blank rows">
                        <i class="fa-solid fa-eraser text-amber-500"></i>
                        <span>{{ $isAr ? 'مسح الفارغة' : 'Clean Blanks' }}</span>
                    </button>
                    <button type="button" @click="resetToSample()" class="btn btn-sm rounded-xl font-bold bg-slate-100 hover:bg-slate-200 dark:bg-[#0B2942] dark:hover:bg-[#133957] text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-[#1E4E73] cursor-pointer" title="Load latest 20 doctors">
                        <i class="fa-solid fa-arrows-rotate text-teal-500"></i>
                        <span>{{ $isAr ? 'استعادة الأطباء' : 'Reload HCPs' }}</span>
                    </button>
                </div>

                <!-- Right Buttons: Direct Save, Instant Export -->
                <div class="flex items-center gap-2.5 flex-wrap">
                    <!-- Export Live Grid to Excel -->
                    <button type="button" @click="exportCurrentGrid()" class="btn btn-sm rounded-xl font-bold bg-white dark:bg-[#071F33] hover:bg-slate-50 dark:hover:bg-[#0B2942] text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-[#1E4E73] cursor-pointer flex items-center gap-1.5">
                        <i class="fa-solid fa-file-excel text-emerald-500"></i>
                        <span>{{ $isAr ? 'تصدير الجدول (XLSX)' : 'Export Grid to Excel' }}</span>
                    </button>

                    <!-- Save Grid Directly to Database -->
                    <button type="button" @click="saveGridToDatabase()" :disabled="isSaving" class="btn btn-save btn-sm rounded-xl font-black px-5 flex items-center gap-2 cursor-pointer border-0">
                        <template x-if="isSaving">
                            <i class="fa-solid fa-spinner fa-spin text-white"></i>
                        </template>
                        <template x-if="!isSaving">
                            <i class="fa-solid fa-floppy-disk text-white"></i>
                        </template>
                        <span x-text="isSaving ? '{{ $isAr ? 'جاري الحفظ...' : 'Saving...' }}' : '{{ $isAr ? 'حفظ الأطباء في النظام' : 'Save HCPs to Database' }}'"></span>
                    </button>
                </div>
            </div>

            <!-- Spreadsheet Grid Container -->
            <div class="rounded-2xl border border-slate-200/90 dark:border-[#133957] bg-white dark:bg-[#071F33] overflow-hidden shadow-xs">
                <div class="overflow-x-auto max-h-[650px] custom-scrollbar">
                    <table class="w-full text-left rtl:text-right border-collapse text-xs">
                        <!-- Table Header -->
                        <thead class="sticky top-0 z-10 bg-slate-50 dark:bg-[#06243C] text-slate-700 dark:text-slate-200 uppercase tracking-wider font-extrabold border-b border-slate-200 dark:border-[#133957]">
                            <tr>
                                <th class="p-3 w-12 text-center text-slate-400">#</th>
                                <th class="p-3 min-w-[200px]">{{ $isAr ? 'اسم الطبيب / الممارس *' : 'Doctor / HCP Name *' }}</th>
                                <th class="p-3 min-w-[140px]">{{ $isAr ? 'رقم الهاتف / الواتساب' : 'Phone / WhatsApp' }}</th>
                                <th class="p-3 min-w-[160px]">{{ $isAr ? 'التخصص الطبي' : 'Specialty' }}</th>
                                <th class="p-3 min-w-[110px]">{{ $isAr ? 'التصنيف' : 'Class' }}</th>
                                <th class="p-3 min-w-[180px]">{{ $isAr ? 'المركز / المستشفى' : 'Hospital / Clinic' }}</th>
                                <th class="p-3 min-w-[130px]">{{ $isAr ? 'المدينة' : 'City' }}</th>
                                <th class="p-3 min-w-[130px]">{{ $isAr ? 'المنطقة' : 'Territory Area' }}</th>
                                <th class="p-3 min-w-[160px]">{{ $isAr ? 'المندوب المسؤول' : 'Assigned Rep' }}</th>
                                <th class="p-3 min-w-[100px] text-center">{{ $isAr ? 'الزيارات/شهر' : 'Visits/Mo' }}</th>
                                <th class="p-3 min-w-[180px]">{{ $isAr ? 'ملاحظات وتوجيهات' : 'Strategic Notes' }}</th>
                                <th class="p-3 w-12 text-center">{{ $isAr ? 'حذف' : 'Del' }}</th>
                            </tr>
                        </thead>

                        <!-- Table Body -->
                        <tbody class="divide-y divide-slate-100 dark:divide-[#133957] text-slate-800 dark:text-slate-200 font-medium">
                            <template x-for="(row, index) in gridRows" :key="row.uid">
                                <tr class="hover:bg-sky-50/40 dark:hover:bg-[#0B2942]/60 transition-colors">
                                    <!-- Row Number -->
                                    <td class="p-2.5 text-center font-mono text-slate-400 font-bold" x-text="index + 1"></td>

                                    <!-- Doctor Name -->
                                    <td class="p-1.5">
                                        <input type="text" x-model="row.name" placeholder="{{ $isAr ? 'د. أحمد ...' : 'Dr. Full Name' }}" class="mr-grid-cell-input font-bold" required>
                                    </td>

                                    <!-- Phone -->
                                    <td class="p-1.5">
                                        <input type="text" x-model="row.phone" placeholder="+966 ..." class="mr-grid-cell-input font-mono" dir="ltr">
                                    </td>

                                    <!-- Specialty -->
                                    <td class="p-1.5">
                                        <select x-model="row.specialty_id" class="mr-grid-cell-input cursor-pointer font-semibold text-sky-600 dark:text-sky-400">
                                            <option value="">{{ $isAr ? '— اختر التخصص —' : '— Specialty —' }}</option>
                                            <template x-for="s in specialties" :key="s.id">
                                                <option :value="s.id" :selected="s.id == row.specialty_id" x-text="s.name"></option>
                                            </template>
                                        </select>
                                    </td>

                                    <!-- Classification -->
                                    <td class="p-1.5">
                                        <select x-model="row.classification_id" class="mr-grid-cell-input cursor-pointer font-black text-amber-500">
                                            <option value="">{{ $isAr ? '— الفئة —' : '— Class —' }}</option>
                                            <template x-for="c in classifications" :key="c.id">
                                                <option :value="c.id" :selected="c.id == row.classification_id" x-text="c.code + ' (' + c.label + ')'"></option>
                                            </template>
                                        </select>
                                    </td>

                                    <!-- Hospital / Clinic -->
                                    <td class="p-1.5">
                                        <input type="text" x-model="row.clinic" placeholder="{{ $isAr ? 'مستشفى / مجمع ...' : 'Hospital or Clinic' }}" class="mr-grid-cell-input">
                                    </td>

                                    <!-- City -->
                                    <td class="p-1.5">
                                        <select x-model="row.city_id" @change="onCityChange(row)" class="mr-grid-cell-input cursor-pointer">
                                            <option value="">{{ $isAr ? '— المدينة —' : '— City —' }}</option>
                                            <template x-for="ct in cities" :key="ct.id">
                                                <option :value="ct.id" :selected="ct.id == row.city_id" x-text="'{{ $isAr }}' === '1' ? (ct.name_ar || ct.name_en) : (ct.name_en || ct.name_ar)"></option>
                                            </template>
                                        </select>
                                    </td>

                                    <!-- Territory Area -->
                                    <td class="p-1.5">
                                        <select x-model="row.area_id" class="mr-grid-cell-input cursor-pointer">
                                            <option value="">{{ $isAr ? '— المنطقة —' : '— Area —' }}</option>
                                            <template x-for="ar in getAreasForCity(row.city_id)" :key="ar.id">
                                                <option :value="ar.id" :selected="ar.id == row.area_id" x-text="'{{ $isAr }}' === '1' ? (ar.name_ar || ar.name_en) : (ar.name_en || ar.name_ar)"></option>
                                            </template>
                                        </select>
                                    </td>

                                    <!-- Assigned Medical Rep -->
                                    <td class="p-1.5">
                                        <select x-model="row.rep_id" class="mr-grid-cell-input cursor-pointer font-bold text-indigo-600 dark:text-indigo-400">
                                            <option value="">{{ $isAr ? '— بدون تعيين —' : '— Unassigned —' }}</option>
                                            <template x-for="r in medicalReps" :key="r.id">
                                                <option :value="r.id" :selected="r.id == row.rep_id" x-text="r.name"></option>
                                            </template>
                                        </select>
                                    </td>

                                    <!-- Target Visits -->
                                    <td class="p-1.5 text-center">
                                        <input type="number" x-model.number="row.target_visits" min="1" max="20" class="mr-grid-cell-input text-center font-bold font-mono">
                                    </td>

                                    <!-- Notes -->
                                    <td class="p-1.5">
                                        <input type="text" x-model="row.notes" placeholder="{{ $isAr ? 'ملاحظات الترويج أو أفضل موعد...' : 'Target products or notes' }}" class="mr-grid-cell-input">
                                    </td>

                                    <!-- Remove Row -->
                                    <td class="p-2 text-center">
                                        <button type="button" @click="removeRow(index)" class="w-7 h-7 rounded-lg text-slate-400 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/40 flex items-center justify-center transition-colors cursor-pointer" title="Delete Row">
                                            <i class="fa-solid fa-xmark text-sm"></i>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- Grid Footer Summary Bar -->
                <div class="p-3.5 bg-slate-50 dark:bg-[#06243C] border-t border-slate-200 dark:border-[#133957] flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
                    <div class="flex items-center gap-4 text-slate-500 dark:text-slate-400 font-semibold">
                        <span>{{ $isAr ? 'إجمالي الصفوف الحالية:' : 'Total Rows in Grid:' }} <strong class="text-slate-900 dark:text-white font-mono" x-text="gridRows.length"></strong></span>
                        <span>•</span>
                        <span>{{ $isAr ? 'تم تعيينهم لمناديب:' : 'Assigned to Reps:' }} <strong class="text-indigo-600 dark:text-indigo-400 font-mono" x-text="gridRows.filter(r => r.rep_id).length"></strong></span>
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="button" @click="addRow(1)" class="text-sky-600 dark:text-sky-400 font-bold hover:underline cursor-pointer flex items-center gap-1">
                            <i class="fa-solid fa-plus text-xs"></i>
                            <span>{{ $isAr ? 'إضافة صف جديد' : 'Add another row' }}</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Hidden Form for Exporting Spreadsheet Grid to Excel -->
            <form x-ref="exportGridForm" method="POST" action="{{ route('admin.mr.excel.manual.export') }}" class="hidden">
                @csrf
                <input type="hidden" name="grid_data" :value="JSON.stringify(gridRows)">
            </form>
        </div>

        <!-- ==================================================================== -->
        <!-- TAB 2: BATCH EXCEL / CSV FILE IMPORT                                  -->
        <!-- ==================================================================== -->
        <div x-show="activeTab === 'import'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Upload Dropzone Area (2 Columns) -->
                <div class="lg:col-span-2 space-y-5">
                    
                    <!-- File Dropzone -->
                    <div class="mr-dropzone-box"
                        @dragover.prevent="dragOver = true"
                        @dragleave.prevent="dragOver = false"
                        @drop.prevent="handleFileDrop($event)"
                        @click="$refs.fileInput.click()">
                        
                        <input type="file" x-ref="fileInput" @change="handleFileSelect($event)" accept=".xlsx,.xls,.csv,.txt" class="hidden">
                        
                        <div class="max-w-md mx-auto space-y-3 pointer-events-none">
                            <div class="w-16 h-16 rounded-3xl bg-emerald-500/10 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-3xl mx-auto shadow-inner">
                                <i class="fa-solid fa-cloud-arrow-up"></i>
                            </div>

                            <template x-if="!selectedFile">
                                <div>
                                    <h3 class="font-black text-base sm:text-lg text-slate-800 dark:text-white">
                                        {{ $isAr ? 'اسحب وأفلت ملف الإكسل هنا، أو انقر للاختيار' : 'Drag & drop your Excel file here, or browse' }}
                                    </h3>
                                    <p class="text-xs text-slate-400 mt-1">
                                        {{ $isAr ? 'يدعم ملفات (.xlsx, .xls, .csv) بحجم يصل حتى 20 ميغابايت' : 'Supports standard (.xlsx, .xls, .csv) files up to 20MB' }}
                                    </p>
                                </div>
                            </template>

                            <template x-if="selectedFile">
                                <div class="p-3.5 rounded-2xl bg-white dark:bg-[#071F33] border border-emerald-500/40 text-emerald-700 dark:text-emerald-300 font-bold text-sm inline-flex items-center gap-2">
                                    <i class="fa-solid fa-file-excel text-emerald-500 text-lg"></i>
                                    <span x-text="selectedFile.name"></span>
                                    <span class="text-xs text-slate-400 font-normal" x-text="'(' + (selectedFile.size / 1024).toFixed(1) + ' KB)'"></span>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Import Mode Selector Cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Mode 1: Doctors & Clinics -->
                        <div @click="importType = 'doctors'" 
                            :class="importType === 'doctors' ? 'border-sky-500 bg-sky-50/40 dark:bg-sky-950/20 dark:border-sky-500 ring-2 ring-sky-500/20' : 'border-slate-200/90 dark:border-[#133957] bg-white dark:bg-[#071F33]'"
                            class="p-4 rounded-2xl border transition-all cursor-pointer">
                            <div class="flex items-center justify-between mb-2">
                                <div class="w-9 h-9 rounded-xl bg-sky-500/10 text-sky-500 flex items-center justify-center text-base">
                                    <i class="fa-solid fa-user-doctor"></i>
                                </div>
                                <span :class="importType === 'doctors' ? 'bg-sky-500 text-white' : 'bg-slate-100 text-slate-400 dark:bg-[#0B2942]'" class="w-5 h-5 rounded-full flex items-center justify-center text-xs">
                                    <i class="fa-solid fa-check text-[10px]"></i>
                                </span>
                            </div>
                            <h4 class="font-black text-sm text-slate-900 dark:text-white">{{ $isAr ? 'قاعدة بيانات الأطباء والعيادات' : 'Doctors & HCPs Directory' }}</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                {{ $isAr ? 'استيراد أسماء الأطباء وتخصصاتهم وفئاتهم وتعييناتهم الميدانية' : 'Import doctors, specialties, classifications, and medical rep allocations' }}
                            </p>
                        </div>

                        <!-- Mode 2: Visit Logs & Call Reports -->
                        <div @click="importType = 'visits'" 
                            :class="importType === 'visits' ? 'border-emerald-500 bg-emerald-50/40 dark:bg-emerald-950/20 dark:border-emerald-500 ring-2 ring-emerald-500/20' : 'border-slate-200/90 dark:border-[#133957] bg-white dark:bg-[#071F33]'"
                            class="p-4 rounded-2xl border transition-all cursor-pointer">
                            <div class="flex items-center justify-between mb-2">
                                <div class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center text-base">
                                    <i class="fa-solid fa-location-dot"></i>
                                </div>
                                <span :class="importType === 'visits' ? 'bg-emerald-500 text-white' : 'bg-slate-100 text-slate-400 dark:bg-[#0B2942]'" class="w-5 h-5 rounded-full flex items-center justify-center text-xs">
                                    <i class="fa-solid fa-check text-[10px]"></i>
                                </span>
                            </div>
                            <h4 class="font-black text-sm text-slate-900 dark:text-white">{{ $isAr ? 'سجلات وتقارير الزيارات المنفذة' : 'Field Visits & Call Activity' }}</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                {{ $isAr ? 'استيراد سجلات وتقارير الزيارات وتفاصيل الترويج والعينات' : 'Import executed visit logs, rep outcomes, sample items, and call notes' }}
                            </p>
                        </div>
                    </div>

                    <!-- Pre-flight Inspect & Import Action Bar -->
                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button" @click="inspectUploadedFile()" :disabled="!selectedFile || isInspecting" class="btn btn-sm rounded-xl font-bold bg-white dark:bg-[#071F33] hover:bg-slate-50 dark:hover:bg-[#0B2942] text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-[#1E4E73] cursor-pointer flex items-center gap-1.5">
                            <template x-if="isInspecting">
                                <i class="fa-solid fa-spinner fa-spin text-sky-500"></i>
                            </template>
                            <template x-if="!isInspecting">
                                <i class="fa-solid fa-magnifying-glass-chart text-sky-500"></i>
                            </template>
                            <span>{{ $isAr ? 'فحص ومعاينة الملف قبل الحفظ' : 'Pre-flight Inspect File' }}</span>
                        </button>

                        <button type="button" @click="executeImport()" :disabled="!selectedFile || isImporting" class="btn btn-save btn-sm rounded-xl font-black px-6 flex items-center gap-2 cursor-pointer border-0">
                            <template x-if="isImporting">
                                <i class="fa-solid fa-spinner fa-spin text-white"></i>
                            </template>
                            <template x-if="!isImporting">
                                <i class="fa-solid fa-bolt text-white"></i>
                            </template>
                            <span x-text="isImporting ? '{{ $isAr ? 'جاري الاستيراد...' : 'Importing...' }}' : '{{ $isAr ? 'بدء الاستيراد المباشر' : 'Execute Import Now' }}'"></span>
                        </button>
                    </div>
                </div>

                <!-- Side Options: Duplicate Handling & Configuration (1 Column) -->
                <div class="space-y-4">
                    <div class="p-5 rounded-2xl bg-white dark:bg-[#071F33] border border-slate-200/90 dark:border-[#133957] space-y-4 shadow-xs">
                        <div class="flex items-center gap-2 text-xs font-black uppercase text-slate-400 dark:text-slate-400 tracking-wider">
                            <i class="fa-solid fa-sliders text-sky-500"></i>
                            <span>{{ $isAr ? 'خيارات وقواعد الاستيراد' : 'Import Configuration' }}</span>
                        </div>

                        <!-- Duplicate Strategy -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                {{ $isAr ? 'معالجة الأطباء المكررين' : 'Duplicate Resolution Rule' }}
                            </label>
                            <select x-model="duplicateAction" class="form-select text-xs w-full rounded-xl font-medium border border-slate-200 dark:border-[#1E4E73] bg-white dark:bg-[#031827] text-slate-900 dark:text-white">
                                <option value="skip">{{ $isAr ? 'تخطي الطبيب الموجود مسبقاً' : 'Skip Existing (Keep DB Record)' }}</option>
                                <option value="update">{{ $isAr ? 'تحديث البيانات الفارغة فقط' : 'Update & Complement Missing Fields' }}</option>
                                <option value="overwrite">{{ $isAr ? 'استبدال وتحديث السجل بالكامل' : 'Overwrite Existing Record' }}</option>
                            </select>
                        </div>

                        <!-- Default Rep Assignment -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                {{ $isAr ? 'تعيين لمندوب محدد (اختياري)' : 'Default Rep Assignment' }}
                            </label>
                            <select x-model="defaultRepId" class="form-select text-xs w-full rounded-xl font-medium border border-slate-200 dark:border-[#1E4E73] bg-white dark:bg-[#031827] text-slate-900 dark:text-white">
                                <option value="">{{ $isAr ? '— بناءً على الملف أو بدون تعيين —' : '— From file or unassigned —' }}</option>
                                <template x-for="r in medicalReps" :key="r.id">
                                    <option :value="r.id" x-text="r.name"></option>
                                </template>
                            </select>
                        </div>

                        <!-- Default Target Visits -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                {{ $isAr ? 'الحد الافتراضي للزيارات شهرياً' : 'Default Monthly Visit Quota' }}
                            </label>
                            <input type="number" x-model.number="targetVisitsDefault" min="1" max="20" class="form-input text-xs w-full rounded-xl font-bold font-mono border border-slate-200 dark:border-[#1E4E73] bg-white dark:bg-[#031827] text-slate-900 dark:text-white">
                        </div>

                        <!-- Auto-assign to Active Cycle -->
                        <div class="pt-2 border-t border-slate-100 dark:border-[#133957] flex items-center justify-between">
                            <div>
                                <span class="text-xs font-bold text-slate-900 dark:text-white block">{{ $isAr ? 'إدراج في الدورة النشطة' : 'Link Active Cycle' }}</span>
                                <span class="text-[11px] text-slate-400 block">{{ $isAr ? 'تضمين الطبيب فورياً في دورة الزيارات الحالية' : 'Add to current cycle agenda' }}</span>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" x-model="autoAssignCycle" class="sr-only peer">
                                <div class="w-10 h-5 bg-slate-200 peer-focus:outline-hidden rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-slate-600 peer-checked:bg-emerald-500"></div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==================================================================== -->
        <!-- TAB 3: MULTI-CRITERIA DYNAMIC EXPORT                                  -->
        <!-- ==================================================================== -->
        <div x-show="activeTab === 'export'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
            
            <form method="GET" action="{{ route('admin.mr.excel.export') }}" class="p-6 rounded-2xl bg-white dark:bg-[#071F33] border border-slate-200/90 dark:border-[#133957] space-y-6 shadow-xs">
                
                <!-- Dataset Selection Cards -->
                <div>
                    <label class="block text-xs font-black uppercase text-slate-400 dark:text-slate-400 tracking-wider mb-3">
                        <i class="fa-solid fa-database text-sky-500"></i>
                        <span>{{ $isAr ? 'اختر مجموعة البيانات المراد تصديرها' : 'Select Target MR Dataset to Export' }}</span>
                    </label>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <!-- Dataset 1: Contacts Directory -->
                        <label class="p-4 rounded-2xl border transition-all cursor-pointer flex items-start gap-3"
                            :class="exportType === 'contacts' ? 'border-sky-500 bg-sky-50/40 dark:bg-sky-950/20 ring-2 ring-sky-500/20' : 'border-slate-200 dark:border-[#133957] bg-white dark:bg-[#071F33]'">
                            <input type="radio" name="export_type" value="contacts" x-model="exportType" class="mt-1 text-sky-600">
                            <div>
                                <span class="font-black text-sm text-slate-900 dark:text-white block">{{ $isAr ? 'دليل الأطباء والعيادات الشامل' : 'Full HCPs & Clinics Directory' }}</span>
                                <span class="text-xs text-slate-400 mt-0.5 block">{{ $isAr ? 'كافة بيانات الأطباء والتصنيفات والمناطق والمناديب المكلفين' : 'Complete doctor profiles, classes, locations, and assigned reps' }}</span>
                            </div>
                        </label>

                        <!-- Dataset 2: Executed Visits Log -->
                        <label class="p-4 rounded-2xl border transition-all cursor-pointer flex items-start gap-3"
                            :class="exportType === 'visits' ? 'border-emerald-500 bg-emerald-50/40 dark:bg-emerald-950/20 ring-2 ring-emerald-500/20' : 'border-slate-200 dark:border-[#133957] bg-white dark:bg-[#071F33]'">
                            <input type="radio" name="export_type" value="visits" x-model="exportType" class="mt-1 text-emerald-600">
                            <div>
                                <span class="font-black text-sm text-slate-900 dark:text-white block">{{ $isAr ? 'سجل الزيارات والأنشطة الميدانية' : 'Field Visits & Activity Log' }}</span>
                                <span class="text-xs text-slate-400 mt-0.5 block">{{ $isAr ? 'تواريخ الزيارات، مخرجات المقابلة، وتأكيد الـ GPS' : 'Check-ins, outcomes, feedback notes, and GPS geofence audit' }}</span>
                            </div>
                        </label>

                        <!-- Dataset 3: Coverage & Deficit Report -->
                        <label class="p-4 rounded-2xl border transition-all cursor-pointer flex items-start gap-3"
                            :class="exportType === 'coverage' ? 'border-amber-500 bg-amber-50/40 dark:bg-amber-950/20 ring-2 ring-amber-500/20' : 'border-slate-200 dark:border-[#133957] bg-white dark:bg-[#071F33]'">
                            <input type="radio" name="export_type" value="coverage" x-model="exportType" class="mt-1 text-amber-600">
                            <div>
                                <span class="font-black text-sm text-slate-900 dark:text-white block">{{ $isAr ? 'تقرير التغطية والأطباء غير المزارين' : 'Rep Coverage & Deficit Scorecard' }}</span>
                                <span class="text-xs text-slate-400 mt-0.5 block">{{ $isAr ? 'تحليل الأطباء المستهدفين والزيارات المنجزة والمتأخرة' : 'Target vs actual visits, unvisited HCPs, and rep performance' }}</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Granular Filter Criteria -->
                <div class="space-y-4 pt-4 border-t border-slate-100 dark:border-[#133957]">
                    <div class="flex items-center gap-2 text-xs font-black uppercase text-slate-400 dark:text-slate-400 tracking-wider">
                        <i class="fa-solid fa-filter text-sky-500"></i>
                        <span>{{ $isAr ? 'تصفية البيانات المخصصة (اختياري)' : 'Granular Export Filters' }}</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Rep Filter -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">{{ $isAr ? 'المندوب الطبي' : 'Medical Representative' }}</label>
                            <select name="mr_id" class="form-select text-xs w-full rounded-xl font-medium border border-slate-200 dark:border-[#1E4E73] bg-white dark:bg-[#031827] text-slate-900 dark:text-white">
                                <option value="">{{ $isAr ? '— جميع المناديب —' : '— All Representatives —' }}</option>
                                @foreach($medicalReps as $r)
                                    <option value="{{ $r->id }}">{{ $r->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Specialty Filter -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">{{ $isAr ? 'التخصص الطبي' : 'Specialty' }}</label>
                            <select name="specialty_id" class="form-select text-xs w-full rounded-xl font-medium border border-slate-200 dark:border-[#1E4E73] bg-white dark:bg-[#031827] text-slate-900 dark:text-white">
                                <option value="">{{ $isAr ? '— جميع التخصصات —' : '— All Specialties —' }}</option>
                                @foreach($specialties as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Classification Filter -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">{{ $isAr ? 'فئة الطبيب' : 'Classification' }}</label>
                            <select name="classification_id" class="form-select text-xs w-full rounded-xl font-medium border border-slate-200 dark:border-[#1E4E73] bg-white dark:bg-[#031827] text-slate-900 dark:text-white">
                                <option value="">{{ $isAr ? '— جميع الفئات —' : '— All Classes —' }}</option>
                                @foreach($classifications as $cl)
                                    <option value="{{ $cl->id }}">{{ $cl->code }} ({{ $cl->label }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- City Filter -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">{{ $isAr ? 'المدينة / المحافظة' : 'City' }}</label>
                            <select name="city_id" class="form-select text-xs w-full rounded-xl font-medium border border-slate-200 dark:border-[#1E4E73] bg-white dark:bg-[#031827] text-slate-900 dark:text-white">
                                <option value="">{{ $isAr ? '— جميع المدن —' : '— All Cities —' }}</option>
                                @foreach($cities as $ct)
                                    <option value="{{ $ct->id }}">{{ $isAr ? ($ct->name_ar ?: $ct->name_en) : ($ct->name_en ?: $ct->name_ar) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Date Range for Visits -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 pt-2">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">{{ $isAr ? 'من تاريخ' : 'Date From' }}</label>
                            <input type="date" name="date_from" class="form-input text-xs w-full rounded-xl font-mono border border-slate-200 dark:border-[#1E4E73] bg-white dark:bg-[#031827] text-slate-900 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">{{ $isAr ? 'إلى تاريخ' : 'Date To' }}</label>
                            <input type="date" name="date_to" class="form-input text-xs w-full rounded-xl font-mono border border-slate-200 dark:border-[#1E4E73] bg-white dark:bg-[#031827] text-slate-900 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">{{ $isAr ? 'صيغة التصدير' : 'Export File Format' }}</label>
                            <select name="format" class="form-select text-xs w-full rounded-xl font-bold border border-slate-200 dark:border-[#1E4E73] bg-white dark:bg-[#031827] text-slate-900 dark:text-white">
                                <option value="xlsx">Excel Spreadsheet (.xlsx) — Branded & Styled</option>
                                <option value="csv">Standard CSV (.csv) — Plain Text</option>
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="btn btn-save text-xs sm:text-sm font-black px-6 py-2.5 rounded-xl w-full flex items-center justify-center gap-2 cursor-pointer border-0 shadow-lg">
                                <i class="fa-solid fa-download text-white"></i>
                                <span>{{ $isAr ? 'توليد وتنزيل الملف الآن' : 'Generate & Download Export' }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- ==================================================================== -->
        <!-- PRE-FLIGHT FILE INSPECTION MODAL                                     -->
        <!-- ==================================================================== -->
        <div x-show="showInspectModal" x-cloak style="display: none;" 
            class="fixed inset-0 z-[1050] overflow-y-auto bg-slate-950/75 backdrop-blur-md p-3 sm:p-6 flex items-center justify-center transition-all duration-300"
            @click.self="showInspectModal = false">
            
            <div class="max-w-4xl w-full shadow-2xl relative flex flex-col max-h-[92vh] border rounded-3xl overflow-hidden bg-white dark:bg-[#071F33] border-slate-200/90 dark:border-[#133957]">
                <!-- Header -->
                <div class="flex items-center justify-between border-b px-6 py-4.5 flex-shrink-0 bg-slate-50/80 dark:bg-[#06243C] border-slate-100 dark:border-[#133957]">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-2xl flex items-center justify-center text-white shadow-lg flex-shrink-0" style="background: linear-gradient(135deg, #0A4F78 0%, #0284C7 100%);">
                            <i class="fa-solid fa-magnifying-glass-chart text-base"></i>
                        </div>
                        <div>
                            <h3 class="font-black text-base sm:text-lg text-slate-900 dark:text-white">
                                {{ $isAr ? 'تقرير فحص ومعاينة ملف الإكسل' : 'Pre-flight Spreadsheet Inspection' }}
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                {{ $isAr ? 'التحقق من مطابقة الأعمدة وعدد السجلات قبل الحفظ في قاعدة البيانات' : 'Header mapping verification, duplicate check, and row preview' }}
                            </p>
                        </div>
                    </div>
                    <button type="button" @click="showInspectModal = false" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-[#0B2942] dark:hover:bg-[#133957] border border-transparent dark:border-[#1E4E73] flex items-center justify-center text-slate-400 hover:text-slate-700 dark:hover:text-white cursor-pointer">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                <!-- Body -->
                <div class="p-6 space-y-5 overflow-y-auto flex-1 custom-scrollbar">
                    <!-- Metrics Bar -->
                    <div class="grid grid-cols-3 gap-3">
                        <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-[#031827] border border-slate-200 dark:border-[#133957]">
                            <span class="text-xs text-slate-400 block">{{ $isAr ? 'إجمالي الصفوف بالملف' : 'Total Rows' }}</span>
                            <span class="text-lg font-black text-slate-900 dark:text-white font-mono" x-text="inspectData?.total_rows || 0"></span>
                        </div>
                        <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-[#031827] border border-slate-200 dark:border-[#133957]">
                            <span class="text-xs text-slate-400 block">{{ $isAr ? 'الأعمدة المتعرف عليها' : 'Matched Columns' }}</span>
                            <span class="text-lg font-black text-emerald-600 dark:text-emerald-400 font-mono" x-text="Object.keys(inspectData?.matched_mapping || {}).length"></span>
                        </div>
                        <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-[#031827] border border-slate-200 dark:border-[#133957]">
                            <span class="text-xs text-slate-400 block">{{ $isAr ? 'تكرارات محتملة بقاعدة البيانات' : 'Potential Duplicates' }}</span>
                            <span class="text-lg font-black text-amber-500 font-mono" x-text="inspectData?.potential_duplicates || 0"></span>
                        </div>
                    </div>

                    <!-- Preview Table -->
                    <div>
                        <h4 class="text-xs font-black uppercase text-slate-400 mb-2">{{ $isAr ? 'معاينة أولية لأولى الصفوف:' : 'First 8 Rows Preview:' }}</h4>
                        <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-[#133957]">
                            <table class="w-full text-left rtl:text-right text-xs">
                                <thead class="bg-slate-50 dark:bg-[#06243C] font-bold text-slate-700 dark:text-slate-300">
                                    <tr>
                                        <template x-for="h in (inspectData?.headers || [])">
                                            <th class="p-2.5 border-b border-slate-200 dark:border-[#133957] whitespace-nowrap" x-text="h"></th>
                                        </template>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-[#133957]">
                                    <template x-for="r in (inspectData?.preview_rows || [])">
                                        <tr class="hover:bg-slate-50/50 dark:hover:bg-[#0B2942]/40">
                                            <template x-for="cell in r">
                                                <td class="p-2.5 text-slate-600 dark:text-slate-300 whitespace-nowrap" x-text="cell || '—'"></td>
                                            </template>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="border-t px-6 py-4 flex-shrink-0 bg-slate-50/90 dark:bg-[#051A2C] border-slate-100 dark:border-[#133957] flex items-center justify-end gap-3">
                    <button type="button" @click="showInspectModal = false" class="btn btn-cancel text-xs sm:text-sm font-bold px-4 py-2.5 rounded-xl cursor-pointer">
                        {{ $isAr ? 'إلغاء' : 'Close' }}
                    </button>
                    <button type="button" @click="showInspectModal = false; executeImport();" class="btn btn-save text-xs sm:text-sm font-black px-6 py-2.5 rounded-xl flex items-center gap-2 cursor-pointer border-0">
                        <i class="fa-solid fa-circle-check text-white"></i>
                        <span>{{ $isAr ? 'تأكيد والبدء بالاستيراد' : 'Confirm & Proceed to Import' }}</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Hidden Form for Grid Direct Excel Export -->
        <form x-ref="exportGridForm" method="POST" action="{{ route('admin.mr.excel.manual.export') }}" class="hidden">
            @csrf
            <input type="hidden" name="grid_data" :value="JSON.stringify(gridRows)">
        </form>

    </div>

    <!-- Alpine Controller Logic -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('mrExcelHub', (config) => ({
                activeTab: 'grid', // 'grid', 'import', 'export'
                specialties: config.specialties || [],
                classifications: config.classifications || [],
                cities: config.cities || [],
                areas: config.areas || [],
                breaks: config.breaks || [],
                medicalReps: config.medicalReps || [],
                activeCycleId: config.activeCycleId,

                // Live Grid State
                gridRows: [],
                isSaving: false,

                // Import State
                dragOver: false,
                selectedFile: null,
                importType: 'doctors',
                duplicateAction: 'skip',
                defaultRepId: '',
                autoAssignCycle: true,
                targetVisitsDefault: 2,
                isInspecting: false,
                isImporting: false,
                showInspectModal: false,
                inspectData: null,

                // Export State
                exportType: 'contacts',

                init() {
                    this.loadInitialGrid(config.sampleDoctors);
                },

                loadInitialGrid(doctors) {
                    if (doctors && doctors.length > 0) {
                        this.gridRows = doctors.map(d => ({
                            uid: 'row_' + Math.random().toString(36).substr(2, 9),
                            id: d.id,
                            name: d.name,
                            phone: d.phone || '',
                            specialty_id: d.specialty_id || '',
                            classification_id: d.classification_id || '',
                            clinic: d.hospital_clinic_name || '',
                            city_id: d.city_id || '',
                            area_id: d.area_id || '',
                            break_id: d.break_id || '',
                            rep_id: (d.assignments && d.assignments.length > 0) ? d.assignments[0].mr_id : '',
                            target_visits: (d.assignments && d.assignments.length > 0) ? d.assignments[0].target_visits : 2,
                            notes: d.notes || '',
                        }));
                    } else {
                        for (let i = 0; i < 5; i++) {
                            this.addRow();
                        }
                    }
                },

                addRow(count = 1) {
                    for (let i = 0; i < count; i++) {
                        this.gridRows.push({
                            uid: 'row_' + Math.random().toString(36).substr(2, 9),
                            id: null,
                            name: '',
                            phone: '',
                            specialty_id: '',
                            classification_id: '',
                            clinic: '',
                            city_id: '',
                            area_id: '',
                            break_id: '',
                            rep_id: '',
                            target_visits: 2,
                            notes: '',
                        });
                    }
                },

                removeRow(index) {
                    if (this.gridRows.length > 1) {
                        this.gridRows.splice(index, 1);
                    } else {
                        this.gridRows[0] = {
                            uid: 'row_' + Math.random().toString(36).substr(2, 9),
                            id: null,
                            name: '',
                            phone: '',
                            specialty_id: '',
                            classification_id: '',
                            clinic: '',
                            city_id: '',
                            area_id: '',
                            break_id: '',
                            rep_id: '',
                            target_visits: 2,
                            notes: '',
                        };
                    }
                },

                clearEmptyRows() {
                    const filtered = this.gridRows.filter(r => r.name.trim() !== '' || r.phone.trim() !== '');
                    this.gridRows = filtered.length > 0 ? filtered : [{
                        uid: 'row_' + Math.random().toString(36).substr(2, 9),
                        id: null,
                        name: '',
                        phone: '',
                        specialty_id: '',
                        classification_id: '',
                        clinic: '',
                        city_id: '',
                        area_id: '',
                        break_id: '',
                        rep_id: '',
                        target_visits: 2,
                        notes: '',
                    }];
                },

                resetToSample() {
                    this.loadInitialGrid(config.sampleDoctors);
                },

                getAreasForCity(cityId) {
                    if (!cityId) return this.areas;
                    return this.areas.filter(a => a.city_id == cityId);
                },

                onCityChange(row) {
                    row.area_id = '';
                    row.break_id = '';
                },

                async saveGridToDatabase() {
                    const validRows = this.gridRows.filter(r => r.name.trim() !== '');
                    if (validRows.length === 0) {
                        alert('{{ $isAr ? 'يرجى إدخال اسم طبيب واحد على الأقل قبل الحفظ.' : 'Please enter at least one doctor name before saving.' }}');
                        return;
                    }

                    this.isSaving = true;
                    try {
                        const response = await fetch('{{ route('admin.mr.excel.manual.save') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ rows: validRows })
                        });

                        const data = await response.json();
                        if (data.success) {
                            alert(data.message || 'Saved successfully!');
                        } else {
                            alert(data.message || 'Failed to save spreadsheet data.');
                        }
                    } catch (e) {
                        alert('Network or server error occurred while saving.');
                    } finally {
                        this.isSaving = false;
                    }
                },

                exportCurrentGrid() {
                    // Enrich rows with human-readable labels before export
                    this.gridRows.forEach(r => {
                        const spec = this.specialties.find(s => s.id == r.specialty_id);
                        const cl = this.classifications.find(c => c.id == r.classification_id);
                        const ct = this.cities.find(c => c.id == r.city_id);
                        const ar = this.areas.find(a => a.id == r.area_id);
                        const rep = this.medicalReps.find(u => u.id == r.rep_id);

                        r.specialty_label = spec ? spec.name : '';
                        r.classification_label = cl ? cl.code : '';
                        r.city_label = ct ? (ct.name_en || ct.name_ar) : '';
                        r.area_label = ar ? (ar.name_en || ar.name_ar) : '';
                        r.rep_label = rep ? rep.name : '';
                    });

                    this.$refs.exportGridForm.submit();
                },

                handleFileSelect(e) {
                    if (e.target.files && e.target.files[0]) {
                        this.selectedFile = e.target.files[0];
                    }
                },

                handleFileDrop(e) {
                    this.dragOver = false;
                    if (e.dataTransfer.files && e.dataTransfer.files[0]) {
                        this.selectedFile = e.dataTransfer.files[0];
                    }
                },

                async inspectUploadedFile() {
                    if (!this.selectedFile) return;

                    this.isInspecting = true;
                    const formData = new FormData();
                    formData.append('file', this.selectedFile);

                    try {
                        const response = await fetch('{{ route('admin.mr.excel.preview') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: formData
                        });

                        const data = await response.json();
                        if (data.success) {
                            this.inspectData = data;
                            this.showInspectModal = true;
                        } else {
                            alert(data.message || 'File inspection failed.');
                        }
                    } catch (e) {
                        alert('Error during inspection: ' + e.toString());
                    } finally {
                        this.isInspecting = false;
                    }
                },

                async executeImport() {
                    if (!this.selectedFile) return;

                    this.isImporting = true;
                    const formData = new FormData();
                    formData.append('file', this.selectedFile);
                    formData.append('import_type', this.importType);
                    formData.append('duplicate_action', this.duplicateAction);
                    formData.append('default_rep_id', this.defaultRepId);
                    formData.append('auto_assign_cycle', this.autoAssignCycle ? '1' : '0');
                    formData.append('target_visits_default', this.targetVisitsDefault);

                    try {
                        const response = await fetch('{{ route('admin.mr.excel.import') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: formData
                        });

                        const data = await response.json();
                        if (data.success) {
                            alert(data.message);
                            window.location.reload();
                        } else {
                            alert(data.message || 'Import failed.');
                        }
                    } catch (e) {
                        alert('Network error occurred during import.');
                    } finally {
                        this.isImporting = false;
                    }
                }
            }));
        });
    </script>
</x-layouts.admin>

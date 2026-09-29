<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'مركز إكسل واستيراد وتصدير المبيعات' : 'Sales Excel Hub & Data Center'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'استيراد جماعي ذكي، تصدير متعدد المعايير، وجدول إكسل يدوي تفاعلي لإدخال الصفقات والمبيعات' : 'Batch Excel import, dynamic multi-filter export, and live in-browser manual spreadsheet grid for CRM sales'"
    :breadcrumbs="[
        (app()->getLocale() === 'ar' ? 'لوحة المبيعات' : 'CRM') => route('admin.crm.dashboard'),
        (app()->getLocale() === 'ar' ? 'العملاء المحتملون' : 'Leads') => route('admin.crm.leads.index'),
        (app()->getLocale() === 'ar' ? 'مركز الإكسل' : 'Excel Hub') => route('admin.crm.excel.index')
    ]"
>
    @php
        $isAr = app()->getLocale() === 'ar';
    @endphp

    <!-- Custom Styling for the Excel Hub (Dark and Light Mode Perfected) -->
    <style>
        .excel-tab-btn {
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
        .excel-tab-btn:hover {
            color: #0A4F78;
            background: rgba(10, 79, 120, 0.05);
        }
        html.dark .excel-tab-btn {
            color: #94A3B8;
        }
        html.dark .excel-tab-btn:hover {
            color: #38BDF8;
            background: rgba(56, 189, 248, 0.1);
        }
        .excel-tab-btn.active {
            background: #0A4F78;
            color: #FFFFFF !important;
            border-color: #0A4F78;
            box-shadow: 0 4px 12px rgba(10, 79, 120, 0.25);
        }
        html.dark .excel-tab-btn.active {
            background: #0284C7;
            border-color: #0284C7;
            color: #FFFFFF !important;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.35);
        }

        /* Spreadsheet Table Inputs */
        .grid-cell-input {
            width: 100%;
            background: transparent;
            border: 1px solid transparent;
            padding: 0.4rem 0.5rem;
            font-size: 0.8125rem;
            border-radius: 0.375rem;
            transition: all 0.15s ease;
            color: inherit;
        }
        .grid-cell-input:hover {
            border-color: #CBD5E1;
            background: #FFFFFF;
        }
        html.dark .grid-cell-input:hover {
            border-color: #1E3A5F;
            background: #0B2942;
        }
        .grid-cell-input:focus {
            outline: none;
            border-color: #0284C7;
            background: #FFFFFF;
            box-shadow: 0 0 0 2px rgba(2, 132, 199, 0.2);
        }
        html.dark .grid-cell-input:focus {
            background: #071F33;
            border-color: #38BDF8;
            box-shadow: 0 0 0 2px rgba(56, 189, 248, 0.25);
        }

        .dropzone-box {
            border: 2px dashed #CBD5E1;
            border-radius: 1rem;
            padding: 2.5rem 1.5rem;
            text-align: center;
            background: #F8FAFC;
            transition: all 0.25s ease;
            cursor: pointer;
        }
        .dropzone-box:hover, .dropzone-box.dragover {
            border-color: #0284C7;
            background: #F0F9FF;
        }
        html.dark .dropzone-box {
            border-color: #1E3A5F;
            background: #071F33;
        }
        html.dark .dropzone-box:hover, html.dark .dropzone-box.dragover {
            border-color: #38BDF8;
            background: #0A2942;
        }
    </style>

    <!-- Top Action Bar & Template Download Links -->
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white flex items-center gap-2.5">
                <i class="fa-solid fa-file-excel text-emerald-500 text-2xl"></i>
                <span>{{ $isAr ? 'مركز إكسل واستيراد وتصدير المبيعات' : 'CRM Sales Excel Hub & Data Center' }}</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                {{ $isAr ? 'إدارة وتداول بيانات المبيعات والعملاء المحتملين والصفقات بدقة فائقة' : 'Comprehensive batch Excel import, multi-criteria sales export, and live in-browser manual spreadsheet grid' }}
            </p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('admin.crm.excel.template', ['format' => 'xlsx']) }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800 hover:bg-emerald-100 transition-colors shadow-xs">
                <i class="fa-solid fa-file-arrow-down text-emerald-600 dark:text-emerald-400"></i>
                <span>{{ $isAr ? 'تحميل نموذج إكسل (.xlsx)' : 'Download Sample (.xlsx)' }}</span>
            </a>
            <a href="{{ route('admin.crm.excel.template', ['format' => 'csv']) }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-700 hover:bg-slate-200 transition-colors">
                <i class="fa-solid fa-file-csv text-slate-500"></i>
                <span>{{ $isAr ? 'نموذج CSV' : 'Sample CSV' }}</span>
            </a>
            <a href="{{ route('admin.crm.leads.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-700 hover:bg-slate-200 transition-colors">
                <i class="fa-solid fa-arrow-left rtl:rotate-180"></i>
                <span>{{ $isAr ? 'سجل العملاء' : 'Leads Table' }}</span>
            </a>
        </div>
    </div>

    <!-- KPI Summary Metrics Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="p-4 rounded-2xl bg-white dark:bg-[#071F33] border border-slate-200 dark:border-[#133957] shadow-xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 block mb-1">
                {{ $isAr ? 'إجمالي العملاء المحتملين' : 'Total Leads in CRM' }}
            </span>
            <div class="text-2xl font-black font-mono text-slate-900 dark:text-white">
                {{ number_format($stats['total_leads']) }}
            </div>
            <span class="text-[10px] text-sky-600 dark:text-cyan-400 mt-1 inline-flex items-center gap-1 font-bold">
                <i class="fa-solid fa-users"></i> {{ $isAr ? 'قاعدة بيانات نشطة' : 'Active Pipeline' }}
            </span>
        </div>

        <div class="p-4 rounded-2xl bg-white dark:bg-[#071F33] border border-slate-200 dark:border-[#133957] shadow-xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 block mb-1">
                {{ $isAr ? 'الصفقات المفتوحة' : 'Open Opportunities' }}
            </span>
            <div class="text-2xl font-black font-mono text-slate-900 dark:text-white">
                {{ number_format($stats['total_opportunities']) }}
            </div>
            <span class="text-[10px] text-emerald-600 dark:text-emerald-400 mt-1 inline-flex items-center gap-1 font-bold">
                <i class="fa-solid fa-handshake"></i> {{ $isAr ? 'قيد التفاوض' : 'Under Negotiation' }}
            </span>
        </div>

        <div class="p-4 rounded-2xl bg-white dark:bg-[#071F33] border border-slate-200 dark:border-[#133957] shadow-xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 block mb-1">
                {{ $isAr ? 'قيمة خط المبيعات (SAR)' : 'Pipeline Forecast' }}
            </span>
            <div class="text-2xl font-black font-mono text-sky-600 dark:text-cyan-400">
                @currency($stats['pipeline_value'])
            </div>
            <span class="text-[10px] text-slate-500 dark:text-slate-400 mt-1 block">
                {{ $isAr ? 'قيمة الصفقات المحتملة' : 'Expected Gross Pipeline' }}
            </span>
        </div>

        <div class="p-4 rounded-2xl bg-white dark:bg-[#071F33] border border-slate-200 dark:border-[#133957] shadow-xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 block mb-1">
                {{ $isAr ? 'الصفقات الرابحة المغلقة' : 'Closed & Won Deals' }}
            </span>
            <div class="text-2xl font-black font-mono text-emerald-600 dark:text-emerald-400">
                @currency($stats['won_deals_value'])
            </div>
            <span class="text-[10px] text-emerald-600 dark:text-emerald-400 mt-1 inline-flex items-center gap-1 font-bold">
                <i class="fa-solid fa-trophy"></i> {{ $isAr ? 'محقق بنجاح' : 'Secured Revenue' }}
            </span>
        </div>
    </div>

    <!-- Execution Report Flash (When Import Finishes) -->
    @if(session('import_results'))
        @php $res = session('import_results'); @endphp
        <div class="p-6 rounded-2xl bg-white dark:bg-[#071F33] border border-emerald-500/40 shadow-xl mb-6 space-y-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl flex-shrink-0">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-slate-900 dark:text-white m-0">
                        {{ $isAr ? 'تقرير إتمام عملية استيراد الإكسل' : 'Batch Excel Import Execution Report' }}
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 mb-0">
                        {{ $isAr ? 'تمت معالجة ملف الإكسل وإدراج السجلات والمهام بنجاح.' : 'The spreadsheet was parsed and records were integrated into the CRM database.' }}
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
                <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 text-center">
                    <span class="text-[10px] font-bold text-slate-500 uppercase">{{ $isAr ? 'إجمالي الصفوف' : 'Total Rows' }}</span>
                    <strong class="block text-xl font-mono font-black text-slate-900 dark:text-white">{{ $res['total_rows'] }}</strong>
                </div>
                <div class="p-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-300 dark:border-emerald-800 text-center">
                    <span class="text-[10px] font-bold text-emerald-700 dark:text-emerald-300 uppercase">{{ $isAr ? 'عملاء تم إنشاؤهم' : 'Leads Created' }}</span>
                    <strong class="block text-xl font-mono font-black text-emerald-600 dark:text-emerald-400">+{{ $res['imported_leads'] }}</strong>
                </div>
                <div class="p-3 rounded-xl bg-sky-50 dark:bg-sky-950/40 border border-sky-300 dark:border-sky-800 text-center">
                    <span class="text-[10px] font-bold text-sky-700 dark:text-sky-300 uppercase">{{ $isAr ? 'مهام مجدولة' : 'Tasks Scheduled' }}</span>
                    <strong class="block text-xl font-mono font-black text-sky-600 dark:text-cyan-400">+{{ $res['created_tasks'] }}</strong>
                </div>
                <div class="p-3 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-300 dark:border-amber-800 text-center">
                    <span class="text-[10px] font-bold text-amber-700 dark:text-amber-300 uppercase">{{ $isAr ? 'سجلات تم تحديثها' : 'Updated Existing' }}</span>
                    <strong class="block text-xl font-mono font-black text-amber-600 dark:text-amber-400">{{ $res['updated_leads'] }}</strong>
                </div>
                <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 text-center">
                    <span class="text-[10px] font-bold text-slate-500 uppercase">{{ $isAr ? 'مكررات تم تخطيها' : 'Skipped Duplicates' }}</span>
                    <strong class="block text-xl font-mono font-black text-slate-600 dark:text-slate-400">{{ $res['skipped_duplicates'] }}</strong>
                </div>
            </div>

            @if(!empty($res['errors']))
                <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-300 dark:border-rose-900">
                    <h4 class="text-xs font-bold text-rose-700 dark:text-rose-300 flex items-center gap-1.5 mb-2">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span>{{ $isAr ? 'تنبيهات وأخطاء بعض الصفوف (' . count($res['errors']) . ')' : 'Row-Level Warnings & Errors (' . count($res['errors']) . ')' }}</span>
                    </h4>
                    <div class="max-h-40 overflow-y-auto text-xs space-y-1">
                        @foreach($res['errors'] as $err)
                            <div class="text-rose-800 dark:text-rose-200 flex items-start gap-2">
                                <span class="font-mono font-bold">[{{ $isAr ? 'صف ' : 'Row ' }}{{ $err['row'] }}]</span>
                                <span>{{ $err['name'] ? $err['name'] . ': ' : '' }}{{ $err['error'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- Main Tabs Navigation Bar -->
    <div class="flex items-center gap-2 p-1.5 rounded-2xl bg-white dark:bg-[#071F33] border border-slate-200 dark:border-[#133957] mb-6 shadow-xs overflow-x-auto">
        <button type="button" class="excel-tab-btn active" onclick="switchExcelTab('tab-manual')">
            <i class="fa-solid fa-table-cells text-sky-500"></i>
            <span>{{ $isAr ? 'إكسل يدوي تفاعلي (Manual Grid)' : 'Interactive Manual Excel Grid' }}</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase bg-sky-500/15 text-sky-700 dark:text-sky-300 border border-sky-500/20">
                PRO
            </span>
        </button>

        <button type="button" class="excel-tab-btn" onclick="switchExcelTab('tab-import')">
            <i class="fa-solid fa-file-import text-emerald-500"></i>
            <span>{{ $isAr ? 'استيراد ملفات الإكسل (Batch Import)' : 'Batch Excel File Import' }}</span>
        </button>

        <button type="button" class="excel-tab-btn" onclick="switchExcelTab('tab-export')">
            <i class="fa-solid fa-file-export text-amber-500"></i>
            <span>{{ $isAr ? 'تصدير متقدم لمبيعات CRM (Export)' : 'Advanced Sales Export' }}</span>
        </button>
    </div>

    <!-- ========================================== -->
    <!-- TAB 1: INTERACTIVE MANUAL EXCEL GRID (PRO) -->
    <!-- ========================================== -->
    <div id="tab-manual" class="excel-tab-content space-y-6">
        
        <!-- Controls Toolbar -->
        <div class="p-5 rounded-2xl bg-white dark:bg-[#071F33] border border-slate-200 dark:border-[#133957] shadow-xs flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div>
                <h2 class="text-base font-black text-slate-900 dark:text-white m-0 flex items-center gap-2">
                    <i class="fa-solid fa-table text-sky-500"></i>
                    <span>{{ $isAr ? 'لوحة الإكسل اليدوي لإدخال الصفقات والمبيعات' : 'Live In-Browser CRM Spreadsheet Grid' }}</span>
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 mb-0">
                    {{ $isAr ? 'أدخل عدة صفقات وعملاء معاً أو انسخ مباشرة من ملف الإكسل بجهازك ثم اضغط حفظ في CRM' : 'Batch enter multiple sales records directly, paste from desktop Excel, and sync seamlessly to CRM' }}
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-2 flex-wrap w-full md:w-auto">
                <button type="button" onclick="addManualRow()" class="px-3.5 py-2 rounded-xl text-xs font-bold bg-sky-50 dark:bg-sky-950/40 text-sky-700 dark:text-cyan-300 border border-sky-300 dark:border-sky-800 hover:bg-sky-100 transition-colors flex items-center gap-1.5 shadow-xs">
                    <i class="fa-solid fa-plus"></i>
                    <span>{{ $isAr ? 'إضافة صف (+)' : 'Add Row (+)' }}</span>
                </button>

                <button type="button" onclick="openPasteModal()" class="px-3.5 py-2 rounded-xl text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-300 dark:border-slate-700 hover:bg-slate-200 transition-colors flex items-center gap-1.5">
                    <i class="fa-solid fa-paste text-sky-500"></i>
                    <span>{{ $isAr ? 'لصق من إكسل' : 'Paste from Excel' }}</span>
                </button>

                <button type="button" onclick="loadMedicalSamples()" class="px-3 py-2 rounded-xl text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-700 hover:bg-slate-200 transition-colors flex items-center gap-1.5">
                    <i class="fa-solid fa-flask-vial text-emerald-500"></i>
                    <span>{{ $isAr ? 'تحميل عينات طبية' : 'Demo Samples' }}</span>
                </button>

                <button type="button" onclick="downloadManualGridXlsx()" class="px-3 py-2 rounded-xl text-xs font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800 hover:bg-emerald-100 transition-colors flex items-center gap-1.5">
                    <i class="fa-solid fa-download text-emerald-600"></i>
                    <span>{{ $isAr ? 'تصدير الجدول' : 'Export Grid' }}</span>
                </button>

                <button type="button" onclick="saveManualGridToCrm()" id="btn-save-manual-grid" class="px-5 py-2 rounded-xl text-xs font-black bg-gradient-to-r from-sky-600 via-cyan-600 to-sky-700 hover:from-sky-500 hover:to-cyan-500 text-white shadow-md shadow-sky-600/25 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                    <span>{{ $isAr ? 'حفظ الكل في CRM' : 'Save All to CRM' }}</span>
                </button>
            </div>
        </div>

        <!-- Settings Bar for Manual Grid (Target Pipeline & Owner) -->
        <div class="p-4 rounded-2xl bg-white dark:bg-[#071F33] border border-slate-200 dark:border-[#133957] shadow-xs grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                    {{ $isAr ? 'مسار المبيعات المستهدف (Pipeline):' : 'Target Sales Pipeline:' }}
                </label>
                <select id="manual_pipeline_id" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-[#0A2942] border border-slate-300 dark:border-[#1E4E73] text-slate-900 dark:text-white font-semibold">
                    @foreach($pipelines as $p)
                        <option value="{{ $p->id }}" {{ $defaultPipeline && $defaultPipeline->id === $p->id ? 'selected' : '' }}>
                            {{ $p->name }} ({{ $p->stages->count() }} {{ $isAr ? 'مراحل' : 'stages' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                    {{ $isAr ? 'مسؤول المبيعات الافتراضي:' : 'Default Sales Owner / Rep:' }}
                </label>
                <select id="manual_owner_id" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-[#0A2942] border border-slate-300 dark:border-[#1E4E73] text-slate-900 dark:text-white font-semibold">
                    @foreach($owners as $owner)
                        <option value="{{ $owner->id }}" {{ auth()->id() === $owner->id ? 'selected' : '' }}>
                            {{ $owner->name }} ({{ $owner->email }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2 pt-5">
                <input type="checkbox" id="manual_create_tasks" checked class="w-4 h-4 rounded text-sky-600 focus:ring-sky-500 border-slate-300 dark:border-slate-700">
                <label for="manual_create_tasks" class="font-bold text-slate-700 dark:text-slate-300 cursor-pointer">
                    {{ $isAr ? 'إنشاء وتعيين مهام المتابعة تلقائياً لممثل المبيعات' : 'Auto-schedule sales follow-up tasks' }}
                </label>
            </div>
        </div>

        <!-- Editable Grid Table Container -->
        <div class="rounded-2xl bg-white dark:bg-[#071F33] border border-slate-200 dark:border-[#133957] shadow-xl overflow-hidden">
            <div class="overflow-x-auto max-h-[580px]">
                <table class="w-full text-xs text-start border-collapse" id="manual-excel-table">
                    <thead class="sticky top-0 z-10 bg-slate-100 dark:bg-[#0A2942] text-slate-600 dark:text-slate-300 font-extrabold uppercase tracking-wider text-[11px] border-b border-slate-200 dark:border-[#133957]">
                        <tr>
                            <th class="w-10 py-3 px-2 text-center">#</th>
                            <th class="min-w-[170px] py-3 px-3 text-start">{{ $isAr ? 'اسم العميل / الطبيب *' : 'Client / Doctor Name *' }}</th>
                            <th class="min-w-[170px] py-3 px-3 text-start">{{ $isAr ? 'المنشأة / العيادة / الصيدلية' : 'Clinic / Company' }}</th>
                            <th class="min-w-[140px] py-3 px-3 text-start">{{ $isAr ? 'رقم الجوال *' : 'Phone *' }}</th>
                            <th class="min-w-[160px] py-3 px-3 text-start">{{ $isAr ? 'البريد الإلكتروني' : 'Email' }}</th>
                            <th class="min-w-[110px] py-3 px-3 text-start">{{ $isAr ? 'المدينة' : 'City' }}</th>
                            <th class="min-w-[120px] py-3 px-3 text-end">{{ $isAr ? 'قيمة الصفقة (SAR)' : 'Value (SAR)' }}</th>
                            <th class="min-w-[120px] py-3 px-3 text-start">{{ $isAr ? 'حالة العميل' : 'Lead Status' }}</th>
                            <th class="min-w-[110px] py-3 px-3 text-start">{{ $isAr ? 'الأولوية' : 'Priority' }}</th>
                            <th class="min-w-[180px] py-3 px-3 text-start">{{ $isAr ? 'مهمة المتابعة' : 'Follow-up Task' }}</th>
                            <th class="min-w-[120px] py-3 px-3 text-start">{{ $isAr ? 'تاريخ المتابعة' : 'Due Date' }}</th>
                            <th class="min-w-[160px] py-3 px-3 text-start">{{ $isAr ? 'ملاحظات / البروتوكول' : 'Regimen Notes' }}</th>
                            <th class="w-12 py-3 px-2 text-center"></th>
                        </tr>
                    </thead>
                    <tbody id="manual-grid-tbody" class="divide-y divide-slate-200 dark:divide-slate-800 text-slate-800 dark:text-slate-200">
                        <!-- Populated dynamically via JavaScript -->
                    </tbody>
                </table>
            </div>

            <!-- Grid Footer Status & Summary -->
            <div class="p-4 bg-slate-50 dark:bg-[#0B2942] border-t border-slate-200 dark:border-[#133957] flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
                <div class="flex items-center gap-3">
                    <span class="font-bold text-slate-600 dark:text-slate-400">
                        {{ $isAr ? 'إجمالي الصفوف الحالية:' : 'Total Active Rows:' }} 
                        <span id="grid-row-count" class="font-mono text-slate-900 dark:text-white font-extrabold text-sm">0</span>
                    </span>
                    <button type="button" onclick="clearManualGrid()" class="text-rose-600 hover:text-rose-700 font-bold transition-colors">
                        <i class="fa-solid fa-trash-can mr-1 ml-1"></i> {{ $isAr ? 'تفريغ الجدول' : 'Clear All Rows' }}
                    </button>
                </div>

                <div class="flex items-center gap-4">
                    <span class="font-bold text-slate-600 dark:text-slate-400">
                        {{ $isAr ? 'إجمالي قيمة الصفقات بالجدول:' : 'Grid Total Deal Value:' }}
                    </span>
                    <span id="grid-total-value" class="font-mono text-base font-black text-emerald-600 dark:text-emerald-400">
                        0.00 SAR
                    </span>
                </div>
            </div>
        </div>

    </div>

    <!-- ========================================== -->
    <!-- TAB 2: BATCH EXCEL FILE IMPORT             -->
    <!-- ========================================== -->
    <div id="tab-import" class="excel-tab-content space-y-6 hidden">
        
        <form action="{{ route('admin.crm.excel.import') }}" method="POST" enctype="multipart/form-data" id="excel-import-form">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                
                <!-- Left: Drag and Drop Upload Zone (7 cols) -->
                <div class="lg:col-span-7 space-y-5">
                    <div class="p-6 rounded-2xl bg-white dark:bg-[#071F33] border border-slate-200 dark:border-[#133957] shadow-xs space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-black uppercase tracking-wider text-slate-900 dark:text-white m-0 flex items-center gap-2">
                                <i class="fa-solid fa-cloud-arrow-up text-emerald-500"></i>
                                <span>{{ $isAr ? 'رفع ملف الإكسل (.xlsx أو .csv)' : 'Upload Excel / CSV File' }}</span>
                            </h3>
                            <span class="text-[10px] font-bold text-slate-400 uppercase">Max: 20 MB</span>
                        </div>

                        <!-- Dropzone -->
                        <div class="dropzone-box" id="excel-dropzone" onclick="document.getElementById('sheet_file_input').click()">
                            <input type="file" name="sheet_file" id="sheet_file_input" class="hidden" accept=".xlsx,.xls,.csv,.txt" onchange="handleFileSelected(this)">
                            
                            <div class="w-16 h-16 rounded-2xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-3xl mx-auto mb-3 shadow-inner">
                                <i class="fa-solid fa-file-excel"></i>
                            </div>

                            <h4 class="text-sm font-black text-slate-900 dark:text-white m-0" id="dropzone-title">
                                {{ $isAr ? 'اسحب ملف الإكسل هنا أو اضغط للاختيار من جهازك' : 'Drag & Drop your Excel file here or click to browse' }}
                            </h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 mb-0" id="dropzone-subtitle">
                                {{ $isAr ? 'يدعم تنسيقات Microsoft Excel (.xlsx, .xls) والملفات المجدولة (.csv)' : 'Supports Microsoft Excel (.xlsx, .xls) and Tab/Comma Delimited (.csv)' }}
                            </p>
                        </div>

                        <!-- Selected File Preview Bar -->
                        <div id="file-info-bar" class="hidden p-3.5 rounded-xl bg-slate-50 dark:bg-[#0A2942] border border-slate-200 dark:border-[#1E4E73] flex items-center justify-between text-xs">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-file-circle-check text-emerald-500 text-lg"></i>
                                <div>
                                    <strong id="selected-file-name" class="font-bold text-slate-900 dark:text-white block truncate max-w-[280px]">filename.xlsx</strong>
                                    <span id="selected-file-size" class="text-[10px] text-slate-500 dark:text-slate-400">0 KB</span>
                                </div>
                            </div>
                            <button type="button" onclick="runFilePreview()" id="btn-preview-file" class="px-3.5 py-1.5 rounded-lg bg-sky-600 hover:bg-sky-500 text-white font-bold text-xs transition-colors flex items-center gap-1 shadow-sm">
                                <i class="fa-solid fa-eye"></i>
                                <span>{{ $isAr ? 'معاينة وفحص الملف' : 'Preview Data' }}</span>
                            </button>
                        </div>
                    </div>

                    <!-- Live File Preview Container (Shows after preview click) -->
                    <div id="preview-results-card" class="hidden p-5 rounded-2xl bg-white dark:bg-[#071F33] border border-slate-200 dark:border-[#133957] shadow-xl space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-magnifying-glass-chart text-sky-500"></i>
                                <h4 class="text-xs font-black uppercase text-slate-900 dark:text-white m-0">
                                    {{ $isAr ? 'نتيجة الفحص والمعاينة الأولية' : 'Spreadsheet Pre-Flight Inspection' }}
                                </h4>
                            </div>
                            <span id="preview-total-badge" class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-sky-500/15 text-sky-600 dark:text-sky-400">
                                0 Rows Detected
                            </span>
                        </div>

                        <!-- Mini Stats of File -->
                        <div class="grid grid-cols-3 gap-3 text-center text-xs">
                            <div class="p-2.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800">
                                <span class="text-[10px] text-emerald-700 dark:text-emerald-300 font-bold uppercase block">{{ $isAr ? 'صفوف سليمة' : 'Valid Rows' }}</span>
                                <strong id="preview-valid-count" class="text-base font-mono text-emerald-600 dark:text-emerald-400">0</strong>
                            </div>
                            <div class="p-2.5 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800">
                                <span class="text-[10px] text-amber-700 dark:text-amber-300 font-bold uppercase block">{{ $isAr ? 'أرقام مكررة' : 'Duplicates' }}</span>
                                <strong id="preview-duplicate-count" class="text-base font-mono text-amber-600 dark:text-amber-400">0</strong>
                            </div>
                            <div class="p-2.5 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800">
                                <span class="text-[10px] text-rose-700 dark:text-rose-300 font-bold uppercase block">{{ $isAr ? 'صفوف بها أخطاء' : 'Invalid Rows' }}</span>
                                <strong id="preview-invalid-count" class="text-base font-mono text-rose-600 dark:text-rose-400">0</strong>
                            </div>
                        </div>

                        <!-- Preview Table -->
                        <div class="overflow-x-auto max-h-56 rounded-xl border border-slate-200 dark:border-slate-800">
                            <table class="w-full text-xs text-start" id="preview-table">
                                <thead class="bg-slate-100 dark:bg-[#0A2942] text-[10px] font-bold text-slate-500 uppercase">
                                    <tr>
                                        <th class="py-2 px-3 text-center">Row</th>
                                        <th class="py-2 px-3 text-start">Client / Company</th>
                                        <th class="py-2 px-3 text-start">Phone</th>
                                        <th class="py-2 px-3 text-end">Est. Value</th>
                                        <th class="py-2 px-3 text-center">Audit Status</th>
                                    </tr>
                                </thead>
                                <tbody id="preview-tbody" class="divide-y divide-slate-200 dark:divide-slate-800 font-mono text-[11px]">
                                    <!-- Populated via AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Right: Import Logic Configuration (5 cols) -->
                <div class="lg:col-span-5 space-y-5">
                    <div class="p-6 rounded-2xl bg-white dark:bg-[#071F33] border border-slate-200 dark:border-[#133957] shadow-xs space-y-4">
                        <h3 class="text-sm font-black uppercase tracking-wider text-slate-900 dark:text-white m-0 flex items-center gap-2">
                            <i class="fa-solid fa-sliders text-sky-500"></i>
                            <span>{{ $isAr ? 'إعدادات وخيارات الاستيراد' : 'Import Execution Logic' }}</span>
                        </h3>

                        <!-- Target Pipeline -->
                        <div class="text-xs space-y-1">
                            <label class="block font-bold text-slate-700 dark:text-slate-300">
                                {{ $isAr ? 'مسار الصفقات المستهدف (Sales Pipeline):' : 'Destination Sales Pipeline:' }}
                            </label>
                            <select name="pipeline_id" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-[#0A2942] border border-slate-300 dark:border-[#1E4E73] text-slate-900 dark:text-white font-semibold">
                                @foreach($pipelines as $p)
                                    <option value="{{ $p->id }}" {{ $defaultPipeline && $defaultPipeline->id === $p->id ? 'selected' : '' }}>
                                        {{ $p->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Default Owner -->
                        <div class="text-xs space-y-1">
                            <label class="block font-bold text-slate-700 dark:text-slate-300">
                                {{ $isAr ? 'مسؤول المبيعات (Owner / Sales Rep):' : 'Default Assigned Owner / Rep:' }}
                            </label>
                            <select name="default_owner_id" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-[#0A2942] border border-slate-300 dark:border-[#1E4E73] text-slate-900 dark:text-white font-semibold">
                                @foreach($owners as $owner)
                                    <option value="{{ $owner->id }}" {{ auth()->id() === $owner->id ? 'selected' : '' }}>
                                        {{ $owner->name }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-[10px] text-slate-400 m-0">
                                {{ $isAr ? 'سيتم استخدام هذا المسؤول في حال لم يحدد الملف بريد مسؤول لكل صف.' : 'Fallback owner if row does not specify an assigned owner email.' }}
                            </p>
                        </div>

                        <!-- Duplicate Strategy -->
                        <div class="text-xs space-y-2 pt-2 border-t border-slate-200 dark:border-slate-800">
                            <label class="block font-bold text-slate-700 dark:text-slate-300">
                                {{ $isAr ? 'استراتيجية التعامل مع السجلات المكررة:' : 'Duplicate Detection Strategy:' }}
                            </label>
                            
                            <div class="space-y-2">
                                <label class="p-3 rounded-xl border border-slate-200 dark:border-slate-800 flex items-start gap-3 cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800/40">
                                    <input type="radio" name="duplicate_action" value="skip" checked class="mt-0.5 text-sky-600 focus:ring-sky-500">
                                    <div>
                                        <strong class="block text-slate-900 dark:text-white text-xs font-bold">{{ $isAr ? 'تخطي السجلات المكررة (مستحسن)' : 'Skip Duplicates (Recommended)' }}</strong>
                                        <span class="text-[11px] text-slate-500 dark:text-slate-400 block">{{ $isAr ? 'تجاهل أي صف يحمل نفس الهاتف أو البريد الموجود مسبقاً.' : 'Ignores rows with matching phone or email in CRM database.' }}</span>
                                    </div>
                                </label>

                                <label class="p-3 rounded-xl border border-slate-200 dark:border-slate-800 flex items-start gap-3 cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800/40">
                                    <input type="radio" name="duplicate_action" value="update" class="mt-0.5 text-sky-600 focus:ring-sky-500">
                                    <div>
                                        <strong class="block text-slate-900 dark:text-white text-xs font-bold">{{ $isAr ? 'تحديث السجلات الحالية' : 'Update Existing Records' }}</strong>
                                        <span class="text-[11px] text-slate-500 dark:text-slate-400 block">{{ $isAr ? 'تحديث القيمة والملاحظات وحالة العميل مع الحفاظ على المعرف.' : 'Overwrites empty fields, updates status, and appends notes.' }}</span>
                                    </div>
                                </label>

                                <label class="p-3 rounded-xl border border-slate-200 dark:border-slate-800 flex items-start gap-3 cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800/40">
                                    <input type="radio" name="duplicate_action" value="create_new" class="mt-0.5 text-sky-600 focus:ring-sky-500">
                                    <div>
                                        <strong class="block text-slate-900 dark:text-white text-xs font-bold">{{ $isAr ? 'إنشاء صفقة/عميل جديد دائماً' : 'Always Create New' }}</strong>
                                        <span class="text-[11px] text-slate-500 dark:text-slate-400 block">{{ $isAr ? 'إدراج الصف دائماً حتى لو تكرر رقم الهاتف.' : 'Force insertion as a distinct prospective lead.' }}</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Auto-create Follow-up Tasks -->
                        <div class="pt-2 border-t border-slate-200 dark:border-slate-800 flex items-center gap-2">
                            <input type="checkbox" name="create_tasks" id="create_tasks" value="1" checked class="w-4 h-4 rounded text-sky-600 focus:ring-sky-500 border-slate-300 dark:border-slate-700">
                            <label for="create_tasks" class="font-bold text-slate-700 dark:text-slate-300 text-xs cursor-pointer">
                                {{ $isAr ? 'جدولة مهام المتابعة التلقائية المرفقة بالملف' : 'Auto-create follow-up tasks from Excel columns' }}
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-4">
                            <button type="submit" class="w-full py-3 rounded-xl text-xs font-black bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-700 hover:from-emerald-500 hover:to-teal-500 text-white shadow-lg shadow-emerald-600/25 transition-all flex items-center justify-center gap-2">
                                <i class="fa-solid fa-cloud-arrow-up"></i>
                                <span>{{ $isAr ? 'بدء معالجة واستيراد الملف إلى CRM' : 'Execute Batch Excel Import' }}</span>
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </form>

    </div>

    <!-- ========================================== -->
    <!-- TAB 3: ADVANCED SALES DATA EXPORT          -->
    <!-- ========================================== -->
    <div id="tab-export" class="excel-tab-content space-y-6 hidden">
        
        <form action="{{ route('admin.crm.excel.export') }}" method="GET" target="_blank" class="p-6 rounded-2xl bg-white dark:bg-[#071F33] border border-slate-200 dark:border-[#133957] shadow-xs space-y-6">
            
            <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-4">
                <div>
                    <h3 class="text-base font-black text-slate-900 dark:text-white m-0 flex items-center gap-2">
                        <i class="fa-solid fa-file-export text-amber-500"></i>
                        <span>{{ $isAr ? 'تصدير بيانات المبيعات والعملاء المحتملين إلى إكسل' : 'Multi-Criteria CRM Sales Export Engine' }}</span>
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 mb-0">
                        {{ $isAr ? 'حدد نوع البيانات والمرشحات المطلوبة للحصول على تقرير إكسل مهيأ رسمياً مع التنسيقات والمعادلات' : 'Filter by date range, stage, sales owner, and minimum value to generate a styled Excel workbook with totals' }}
                    </p>
                </div>

                <span class="px-3 py-1 rounded-xl text-xs font-bold bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 border border-amber-300 dark:border-amber-800">
                    {{ $isAr ? 'تصدير فوري مباشر' : 'Realtime Stream' }}
                </span>
            </div>

            <!-- 1. Select Export Dataset Entity -->
            <div>
                <label class="block text-xs font-black uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                    {{ $isAr ? '1. اختر نوع البيانات المطلوب تصديرها:' : '1. Select Dataset to Export:' }}
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <label class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 flex items-center gap-3 cursor-pointer hover:border-sky-500 transition-colors bg-slate-50 dark:bg-[#0A2942]">
                        <input type="radio" name="type" value="all_sales" checked class="text-sky-600 focus:ring-sky-500">
                        <div>
                            <strong class="block text-xs font-black text-slate-900 dark:text-white">{{ $isAr ? 'سجل المبيعات الشامل' : 'All Sales & Inquiries' }}</strong>
                            <span class="text-[10px] text-slate-500 dark:text-slate-400">{{ $isAr ? 'العملاء المحتملين + الصفقات والفرص' : 'Combined Leads + Deals Pipeline' }}</span>
                        </div>
                    </label>

                    <label class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 flex items-center gap-3 cursor-pointer hover:border-sky-500 transition-colors bg-slate-50 dark:bg-[#0A2942]">
                        <input type="radio" name="type" value="leads" class="text-sky-600 focus:ring-sky-500">
                        <div>
                            <strong class="block text-xs font-black text-slate-900 dark:text-white">{{ $isAr ? 'العملاء المحتملون فقط' : 'Leads & Inquiries Only' }}</strong>
                            <span class="text-[10px] text-slate-500 dark:text-slate-400">{{ $isAr ? 'بيانات الاتصال، المنشأة، المصدر، الأولوية' : 'Contact info, sources, score & status' }}</span>
                        </div>
                    </label>

                    <label class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 flex items-center gap-3 cursor-pointer hover:border-sky-500 transition-colors bg-slate-50 dark:bg-[#0A2942]">
                        <input type="radio" name="type" value="opportunities" class="text-sky-600 focus:ring-sky-500">
                        <div>
                            <strong class="block text-xs font-black text-slate-900 dark:text-white">{{ $isAr ? 'الصفقات والفرص البيعية' : 'Deals & Opportunities Only' }}</strong>
                            <span class="text-[10px] text-slate-500 dark:text-slate-400">{{ $isAr ? 'المراحل، قيمة العقود، نسبة الإغلاق' : 'Stages, monetary values, closing dates' }}</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- 2. Advanced Multi-Criteria Filters Grid -->
            <div>
                <label class="block text-xs font-black uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                    {{ $isAr ? '2. تحديد معايير التصفية والفرز:' : '2. Refine by Criteria & Attributes:' }}
                </label>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
                    
                    <!-- Date Range Preset -->
                    <div>
                        <label class="block font-bold text-slate-600 dark:text-slate-400 mb-1">{{ $isAr ? 'الفترة الزمنية:' : 'Time Period:' }}</label>
                        <select name="date_range" id="export_date_range" onchange="toggleCustomDateInputs(this.value)" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-[#0A2942] border border-slate-300 dark:border-[#1E4E73] text-slate-900 dark:text-white font-semibold">
                            <option value="all">{{ $isAr ? 'كافة الفترات (All Time)' : 'All Time' }}</option>
                            <option value="today">{{ $isAr ? 'اليوم فقط' : 'Today Only' }}</option>
                            <option value="yesterday">{{ $isAr ? 'أمس' : 'Yesterday' }}</option>
                            <option value="this_week">{{ $isAr ? 'هذا الأسبوع' : 'This Week' }}</option>
                            <option value="this_month" selected>{{ $isAr ? 'هذا الشهر الحسابي' : 'This Month' }}</option>
                            <option value="this_quarter">{{ $isAr ? 'هذا الربع السنوي' : 'This Quarter' }}</option>
                            <option value="custom">{{ $isAr ? 'تحديد فترة مخصصة...' : 'Custom Date Range...' }}</option>
                        </select>
                    </div>

                    <!-- Custom Start Date -->
                    <div id="custom-date-start-col" class="hidden">
                        <label class="block font-bold text-slate-600 dark:text-slate-400 mb-1">{{ $isAr ? 'من تاريخ:' : 'From Date:' }}</label>
                        <input type="date" name="start_date" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-[#0A2942] border border-slate-300 dark:border-[#1E4E73] text-slate-900 dark:text-white">
                    </div>

                    <!-- Custom End Date -->
                    <div id="custom-date-end-col" class="hidden">
                        <label class="block font-bold text-slate-600 dark:text-slate-400 mb-1">{{ $isAr ? 'إلى تاريخ:' : 'To Date:' }}</label>
                        <input type="date" name="end_date" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-[#0A2942] border border-slate-300 dark:border-[#1E4E73] text-slate-900 dark:text-white">
                    </div>

                    <!-- Stage / Status -->
                    <div>
                        <label class="block font-bold text-slate-600 dark:text-slate-400 mb-1">{{ $isAr ? 'حالة العميل / المرحلة:' : 'Stage / Status:' }}</label>
                        <select name="status" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-[#0A2942] border border-slate-300 dark:border-[#1E4E73] text-slate-900 dark:text-white font-semibold">
                            <option value="">{{ $isAr ? 'كافة الحالات والمراحل' : 'All Statuses & Stages' }}</option>
                            <option value="new">{{ $isAr ? 'جديد (New)' : 'New' }}</option>
                            <option value="contacted">{{ $isAr ? 'تم التواصل (Contacted)' : 'Contacted' }}</option>
                            <option value="qualified">{{ $isAr ? 'مؤهل (Qualified)' : 'Qualified' }}</option>
                            <option value="won">{{ $isAr ? 'صفقة رابحة (Won)' : 'Won' }}</option>
                            <option value="lost">{{ $isAr ? 'صفقة خاسرة (Lost)' : 'Lost' }}</option>
                        </select>
                    </div>

                    <!-- Assigned Sales Rep -->
                    <div>
                        <label class="block font-bold text-slate-600 dark:text-slate-400 mb-1">{{ $isAr ? 'مسؤول المبيعات:' : 'Sales Rep / Owner:' }}</label>
                        <select name="owner_id" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-[#0A2942] border border-slate-300 dark:border-[#1E4E73] text-slate-900 dark:text-white font-semibold">
                            <option value="">{{ $isAr ? 'كافة الممثلين والمسؤولين' : 'All Sales Representatives' }}</option>
                            @foreach($owners as $o)
                                <option value="{{ $o->id }}">{{ $o->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Lead Source -->
                    <div>
                        <label class="block font-bold text-slate-600 dark:text-slate-400 mb-1">{{ $isAr ? 'مصدر العميل (Source):' : 'Lead Source:' }}</label>
                        <select name="source_id" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-[#0A2942] border border-slate-300 dark:border-[#1E4E73] text-slate-900 dark:text-white font-semibold">
                            <option value="">{{ $isAr ? 'كافة المصادر' : 'All Sources' }}</option>
                            @foreach($sources as $s)
                                <option value="{{ $s->id }}">{{ $isAr && $s->name_ar ? $s->name_ar : $s->name_en }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Minimum Deal Value -->
                    <div>
                        <label class="block font-bold text-slate-600 dark:text-slate-400 mb-1">{{ $isAr ? 'الحد الأدنى للصفقة (SAR):' : 'Min Deal Value (SAR):' }}</label>
                        <input type="number" name="min_value" placeholder="0.00" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-[#0A2942] border border-slate-300 dark:border-[#1E4E73] text-slate-900 dark:text-white">
                    </div>

                    <!-- Export File Format -->
                    <div>
                        <label class="block font-bold text-slate-600 dark:text-slate-400 mb-1">{{ $isAr ? 'تنسيق الملف النهائي:' : 'Target File Format:' }}</label>
                        <select name="format" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-[#0A2942] border border-slate-300 dark:border-[#1E4E73] text-slate-900 dark:text-white font-semibold">
                            <option value="xlsx" selected>Microsoft Excel Workbook (.xlsx) — Styled</option>
                            <option value="csv">Standard CSV Sheet (.csv) — UTF-8 BOM</option>
                        </select>
                    </div>

                </div>
            </div>

            <!-- Export Trigger Button -->
            <div class="pt-4 border-t border-slate-200 dark:border-slate-800 flex items-center justify-end">
                <button type="submit" class="px-6 py-3 rounded-xl text-xs font-black bg-gradient-to-r from-amber-600 via-orange-600 to-amber-700 hover:from-amber-500 hover:to-orange-500 text-white shadow-lg shadow-amber-600/25 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-file-arrow-down text-sm"></i>
                    <span>{{ $isAr ? 'تصدير وتحميل تقرير الإكسل الآن' : 'Generate & Download Excel Report' }}</span>
                </button>
            </div>

        </form>

    </div>

    <!-- ========================================== -->
    <!-- MODAL: PASTE FROM EXCEL CLIPBOARD          -->
    <!-- ========================================== -->
    <div id="paste-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm hidden p-4">
        <div class="w-full max-w-2xl bg-white dark:bg-[#071F33] rounded-3xl border border-slate-200 dark:border-[#133957] p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
                <div class="flex items-center gap-2.5">
                    <i class="fa-solid fa-paste text-sky-500 text-lg"></i>
                    <h3 class="text-sm font-black text-slate-900 dark:text-white m-0">
                        {{ $isAr ? 'لصق بيانات جدول من ملف إكسل محلي' : 'Paste Tab-Delimited Data from Desktop Excel' }}
                    </h3>
                </div>
                <button type="button" onclick="closePasteModal()" class="text-slate-400 hover:text-slate-600 text-lg">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <p class="text-xs text-slate-500 dark:text-slate-400 m-0">
                {{ $isAr ? 'انسخ الخلايا من جدول الإكسل بجهازك (Ctrl + C) ثم الصقها هنا مباشرة (Ctrl + V). سيقوم النظام بتقسيم الأعمدة وإضافتها تلقائياً إلى الجدول التفاعلي:' : 'Copy cells from your Excel spreadsheet (Ctrl+C) and paste them here (Ctrl+V). Columns will be auto-mapped into the manual grid:' }}
            </p>

            <textarea id="paste-textarea" rows="8" placeholder="Name	Company	Phone	Email	City	Value	Notes" class="w-full p-3 font-mono text-xs rounded-xl bg-slate-50 dark:bg-[#0A2942] border border-slate-300 dark:border-[#1E4E73] text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-sky-500"></textarea>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" onclick="closePasteModal()" class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                    {{ $isAr ? 'إلغاء' : 'Cancel' }}
                </button>
                <button type="button" onclick="applyPastedData()" class="px-5 py-2 rounded-xl text-xs font-black bg-sky-600 hover:bg-sky-500 text-white shadow-md transition-all">
                    {{ $isAr ? 'إدراج الصفوف بالجدول' : 'Import Cells into Grid' }}
                </button>
            </div>
        </div>
    </div>

    <!-- Hidden form for manual grid export to Excel -->
    <form id="manual-grid-export-form" action="{{ route('admin.crm.excel.manual.export') }}" method="POST" target="_blank" class="hidden">
        @csrf
        <input type="hidden" name="grid_data" id="export_grid_data">
    </form>

    <!-- Client-Side JavaScript Logic Engine for Interactive Spreadsheet -->
    <script>
        // Tab Switcher
        function switchExcelTab(tabId) {
            document.querySelectorAll('.excel-tab-content').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.excel-tab-btn').forEach(btn => btn.classList.remove('active'));
            
            const target = document.getElementById(tabId);
            if (target) target.classList.remove('hidden');

            const activeBtn = Array.from(document.querySelectorAll('.excel-tab-btn')).find(b => b.getAttribute('onclick')?.includes(tabId));
            if (activeBtn) activeBtn.classList.add('active');
        }

        // Custom Date Range Inputs Toggle
        function toggleCustomDateInputs(val) {
            const startCol = document.getElementById('custom-date-start-col');
            const endCol = document.getElementById('custom-date-end-col');
            if (val === 'custom') {
                startCol?.classList.remove('hidden');
                endCol?.classList.remove('hidden');
            } else {
                startCol?.classList.add('hidden');
                endCol?.classList.add('hidden');
            }
        }

        // File Dropzone Handling
        const dropzone = document.getElementById('excel-dropzone');
        if (dropzone) {
            ['dragenter', 'dragover'].forEach(eventName => {
                dropzone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    dropzone.classList.add('dragover');
                }, false);
            });
            ['dragleave', 'drop'].forEach(eventName => {
                dropzone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    dropzone.classList.remove('dragover');
                }, false);
            });
            dropzone.addEventListener('drop', (e) => {
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    const input = document.getElementById('sheet_file_input');
                    input.files = files;
                    handleFileSelected(input);
                }
            });
        }

        let selectedFileRef = null;

        function handleFileSelected(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                selectedFileRef = file;
                document.getElementById('file-info-bar')?.classList.remove('hidden');
                document.getElementById('selected-file-name').textContent = file.name;
                document.getElementById('selected-file-size').textContent = (file.size / 1024).toFixed(1) + ' KB';
                document.getElementById('dropzone-title').textContent = 'Selected: ' + file.name;
            }
        }

        // Interactive AJAX Pre-flight Inspection
        function runFilePreview() {
            if (!selectedFileRef) return;

            const btn = document.getElementById('btn-preview-file');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Parsing...';
            btn.disabled = true;

            const formData = new FormData();
            formData.append('sheet_file', selectedFileRef);
            formData.append('_token', '{{ csrf_token() }}');

            fetch('{{ route("admin.crm.excel.preview") }}', {
                method: 'POST',
                body: formData,
                headers: { 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(data => {
                btn.innerHTML = originalText;
                btn.disabled = false;

                if (data.error) {
                    alert('Error: ' + data.error);
                    return;
                }

                document.getElementById('preview-results-card')?.classList.remove('hidden');
                document.getElementById('preview-total-badge').textContent = data.total_rows + ' Rows Detected';
                document.getElementById('preview-valid-count').textContent = data.valid_count;
                document.getElementById('preview-duplicate-count').textContent = data.duplicate_count;
                document.getElementById('preview-invalid-count').textContent = data.invalid_count;

                const tbody = document.getElementById('preview-tbody');
                tbody.innerHTML = '';

                data.preview_rows.forEach(r => {
                    const tr = document.createElement('tr');
                    tr.className = 'hover:bg-slate-50 dark:hover:bg-slate-800/40';

                    let badge = '<span class="px-2 py-0.5 rounded text-[9px] font-black uppercase bg-emerald-500/15 text-emerald-600 dark:text-emerald-400">Valid</span>';
                    if (!r.is_valid) {
                        badge = '<span class="px-2 py-0.5 rounded text-[9px] font-black uppercase bg-rose-500/15 text-rose-600 dark:text-rose-400" title="' + r.errors.join(', ') + '">Error</span>';
                    } else if (r.is_duplicate) {
                        badge = '<span class="px-2 py-0.5 rounded text-[9px] font-black uppercase bg-amber-500/15 text-amber-600 dark:text-amber-400" title="Existing Phone Found">Duplicate</span>';
                    }

                    tr.innerHTML = `
                        <td class="py-2 px-3 text-center text-slate-400 font-bold">${r.row_number}</td>
                        <td class="py-2 px-3 font-bold text-slate-900 dark:text-white">${r.name} <span class="text-slate-400 font-normal">(${r.company})</span></td>
                        <td class="py-2 px-3 text-slate-600 dark:text-slate-300 font-mono">${r.phone}</td>
                        <td class="py-2 px-3 text-end font-mono font-bold text-emerald-600 dark:text-emerald-400">${r.value}</td>
                        <td class="py-2 px-3 text-center">${badge}</td>
                    `;
                    tbody.appendChild(tr);
                });
            })
            .catch(err => {
                btn.innerHTML = originalText;
                btn.disabled = false;
                alert('Preview inspection failed: ' + err);
            });
        }

        // ==========================================
        // MANUAL INTERACTIVE SPREADSHEET ENGINE
        // ==========================================
        let manualGridRows = [];

        function renderManualGrid() {
            const tbody = document.getElementById('manual-grid-tbody');
            if (!tbody) return;
            tbody.innerHTML = '';

            let totalVal = 0;

            manualGridRows.forEach((row, idx) => {
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-slate-50/70 dark:hover:bg-slate-800/30 transition-colors';

                const valNum = parseFloat(row.value) || 0;
                totalVal += valNum;

                tr.innerHTML = `
                    <td class="py-2 px-2 text-center font-mono text-slate-400 font-bold">${idx + 1}</td>
                    <td class="p-1">
                        <input type="text" class="grid-cell-input font-bold" value="${escapeHtml(row.name || '')}" placeholder="Client Name *" onchange="updateRowField(${idx}, 'name', this.value)">
                    </td>
                    <td class="p-1">
                        <input type="text" class="grid-cell-input" value="${escapeHtml(row.company || '')}" placeholder="Clinic / Pharmacy" onchange="updateRowField(${idx}, 'company', this.value)">
                    </td>
                    <td class="p-1">
                        <input type="text" class="grid-cell-input font-mono" value="${escapeHtml(row.phone || '')}" placeholder="+966 50 000 0000" onchange="updateRowField(${idx}, 'phone', this.value)">
                    </td>
                    <td class="p-1">
                        <input type="email" class="grid-cell-input font-mono" value="${escapeHtml(row.email || '')}" placeholder="email@domain.com" onchange="updateRowField(${idx}, 'email', this.value)">
                    </td>
                    <td class="p-1">
                        <input type="text" class="grid-cell-input" value="${escapeHtml(row.city || '')}" placeholder="Riyadh / Dubai" onchange="updateRowField(${idx}, 'city', this.value)">
                    </td>
                    <td class="p-1">
                        <input type="number" step="0.01" class="grid-cell-input text-end font-mono font-bold text-emerald-600 dark:text-emerald-400" value="${row.value !== undefined ? row.value : ''}" placeholder="0.00" onchange="updateRowField(${idx}, 'value', this.value)">
                    </td>
                    <td class="p-1">
                        <select class="grid-cell-input font-semibold" onchange="updateRowField(${idx}, 'status', this.value)">
                            <option value="new" ${row.status === 'new' ? 'selected' : ''}>New (جديد)</option>
                            <option value="contacted" ${row.status === 'contacted' ? 'selected' : ''}>Contacted (تواصل)</option>
                            <option value="qualified" ${row.status === 'qualified' ? 'selected' : ''}>Qualified (مؤهل)</option>
                            <option value="won" ${row.status === 'won' ? 'selected' : ''}>Won (رابحة)</option>
                            <option value="lost" ${row.status === 'lost' ? 'selected' : ''}>Lost (خاسرة)</option>
                        </select>
                    </td>
                    <td class="p-1">
                        <select class="grid-cell-input font-semibold" onchange="updateRowField(${idx}, 'priority', this.value)">
                            <option value="low" ${row.priority === 'low' ? 'selected' : ''}>Low</option>
                            <option value="medium" ${(!row.priority || row.priority === 'medium') ? 'selected' : ''}>Medium</option>
                            <option value="high" ${row.priority === 'high' ? 'selected' : ''}>High</option>
                            <option value="urgent" ${row.priority === 'urgent' ? 'selected' : ''}>Urgent</option>
                        </select>
                    </td>
                    <td class="p-1">
                        <input type="text" class="grid-cell-input" value="${escapeHtml(row.task_subject || '')}" placeholder="e.g. Schedule Clinic Demo" onchange="updateRowField(${idx}, 'task_subject', this.value)">
                    </td>
                    <td class="p-1">
                        <input type="date" class="grid-cell-input font-mono" value="${escapeHtml(row.task_due || '')}" onchange="updateRowField(${idx}, 'task_due', this.value)">
                    </td>
                    <td class="p-1">
                        <input type="text" class="grid-cell-input" value="${escapeHtml(row.notes || '')}" placeholder="Protocol interest, notes..." onchange="updateRowField(${idx}, 'notes', this.value)">
                    </td>
                    <td class="py-2 px-2 text-center">
                        <button type="button" onclick="removeManualRow(${idx})" class="text-slate-400 hover:text-rose-500 transition-colors p-1" title="Delete Row">
                            <i class="fa-regular fa-trash-can"></i>
                        </button>
                    </td>
                `;
                tbody.appendChild(tr);
            });

            document.getElementById('grid-row-count').textContent = manualGridRows.length;
            document.getElementById('grid-total-value').textContent = totalVal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' SAR';
        }

        function updateRowField(idx, field, value) {
            if (manualGridRows[idx]) {
                manualGridRows[idx][field] = value;
                if (field === 'value') {
                    // recompute total
                    let totalVal = manualGridRows.reduce((acc, r) => acc + (parseFloat(r.value) || 0), 0);
                    document.getElementById('grid-total-value').textContent = totalVal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' SAR';
                }
            }
        }

        function addManualRow(data = {}) {
            manualGridRows.push({
                name: data.name || '',
                company: data.company || '',
                phone: data.phone || '',
                email: data.email || '',
                city: data.city || '',
                value: data.value !== undefined ? data.value : 0,
                status: data.status || 'new',
                priority: data.priority || 'medium',
                task_subject: data.task_subject || 'Initial Doctor Call & Protocol Review',
                task_due: data.task_due || new Date(Date.now() + 2*24*60*60*1000).toISOString().split('T')[0],
                notes: data.notes || '',
            });
            renderManualGrid();
        }

        function removeManualRow(idx) {
            manualGridRows.splice(idx, 1);
            renderManualGrid();
        }

        function clearManualGrid() {
            if (confirm('{{ $isAr ? "هل أنت متأكد من تفريغ كافة صفوف الجدول اليدوي؟" : "Are you sure you want to clear all rows?" }}')) {
                manualGridRows = [];
                renderManualGrid();
            }
        }

        function escapeHtml(text) {
            if (!text) return '';
            return String(text).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }

        // Load Realistic Medical Demo Rows
        function loadMedicalSamples() {
            const today = new Date();
            const fmt = (days) => new Date(today.getTime() + days*86400000).toISOString().split('T')[0];

            manualGridRows = [
                {
                    name: 'Dr. Faisal Al-Otaibi',
                    company: 'King Fahd Medical City - Longevity Dept',
                    phone: '+966 50 443 2211',
                    email: 'f.otaibi@kfmc.med.sa',
                    city: 'Riyadh',
                    value: 85000,
                    status: 'qualified',
                    priority: 'high',
                    task_subject: 'Deliver Clinical Sample Protocol Pack (60 bottles)',
                    task_due: fmt(2),
                    notes: 'Interested in clinical trial batch of Blue Cell NAD+ for 40 cardiology patients.'
                },
                {
                    name: 'Dr. Mona Al-Shehri',
                    company: 'Elegance Anti-Aging & Aesthetic Center',
                    phone: '+966 55 119 8877',
                    email: 'dr.mona@eleganceclinic.sa',
                    city: 'Jeddah',
                    value: 48000,
                    status: 'new',
                    priority: 'urgent',
                    task_subject: 'Zoom Meeting: Wholesale Terms & Prescribing Guide',
                    task_due: fmt(1),
                    notes: 'VIP clinic requiring monthly recurring wholesale supply of Blue Mind cognitive formulation.'
                },
                {
                    name: 'Pharm. Badr Al-Harbi',
                    company: 'Al-Nahdi Flagship Bio-Care Branch',
                    phone: '+966 54 998 7766',
                    email: 'badr.h@alnahdi-care.sa',
                    city: 'Dammam',
                    value: 135000,
                    status: 'contacted',
                    priority: 'high',
                    task_subject: 'Contract Signing & Cold-Chain Delivery Logistics',
                    task_due: fmt(3),
                    notes: 'Quarterly supply agreement for 6 branches in the Eastern Province.'
                },
                {
                    name: 'Eng. Mansour Al-Kabbani',
                    company: 'Executive Longevity Syndicate',
                    phone: '+966 50 771 9922',
                    email: 'm.kabbani@vip-holdings.com',
                    city: 'Riyadh',
                    value: 26000,
                    status: 'new',
                    priority: 'medium',
                    task_subject: 'Executive VIP Protocol Consultation Call',
                    task_due: fmt(4),
                    notes: 'Personal customized longevity protocol for 4 board members.'
                }
            ];
            renderManualGrid();
        }

        // Paste Modal Handling
        function openPasteModal() {
            document.getElementById('paste-modal')?.classList.remove('hidden');
            document.getElementById('paste-textarea')?.focus();
        }

        function closePasteModal() {
            document.getElementById('paste-modal')?.classList.add('hidden');
        }

        function applyPastedData() {
            const raw = document.getElementById('paste-textarea').value.trim();
            if (!raw) {
                closePasteModal();
                return;
            }

            const lines = raw.split(/\r?\n/);
            lines.forEach(line => {
                if (!line.trim()) return;
                // split by tab (Excel copy default) or comma
                const parts = line.includes('\t') ? line.split('\t') : line.split(',');
                if (parts.length > 0 && parts[0].trim()) {
                    manualGridRows.push({
                        name: parts[0]?.trim() || '',
                        company: parts[1]?.trim() || '',
                        phone: parts[2]?.trim() || '',
                        email: parts[3]?.trim() || '',
                        city: parts[4]?.trim() || '',
                        value: parseFloat(parts[5]?.replace(/[^0-9.]/g, '')) || 0,
                        status: 'new',
                        priority: 'medium',
                        task_subject: 'Follow up regarding initial inquiry',
                        task_due: new Date(Date.now() + 2*86400000).toISOString().split('T')[0],
                        notes: parts[6]?.trim() || '',
                    });
                }
            });

            document.getElementById('paste-textarea').value = '';
            closePasteModal();
            renderManualGrid();
        }

        // Save All Manual Grid to CRM via AJAX
        function saveManualGridToCrm() {
            if (manualGridRows.length === 0) {
                alert('{{ $isAr ? "الجدول فارغ! الرجاء إضافة صف واحد على الأقل." : "The grid is empty! Please add at least one row." }}');
                return;
            }

            // validate at least name on all rows
            const emptyNames = manualGridRows.filter(r => !r.name || !r.name.trim());
            if (emptyNames.length > 0) {
                alert('{{ $isAr ? "يوجد صفوف بدون اسم عميل! الرجاء تعبئة الأسماء المطلوبة." : "Some rows are missing client names! Please fill required fields." }}');
                return;
            }

            const btn = document.getElementById('btn-save-manual-grid');
            const origText = btn.innerHTML;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> {{ $isAr ? "جاري الحفظ في CRM..." : "Saving to CRM..." }}';
            btn.disabled = true;

            const payload = {
                rows: manualGridRows,
                pipeline_id: document.getElementById('manual_pipeline_id').value,
                default_owner_id: document.getElementById('manual_owner_id').value,
                create_tasks: document.getElementById('manual_create_tasks').checked,
                _token: '{{ csrf_token() }}'
            };

            fetch('{{ route("admin.crm.excel.manual.save") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(data => {
                btn.innerHTML = origText;
                btn.disabled = false;

                if (data.success) {
                    alert('🎉 ' + data.message);
                    manualGridRows = [];
                    renderManualGrid();
                } else {
                    alert('Error: ' + (data.error || 'Failed to save rows.'));
                }
            })
            .catch(err => {
                btn.innerHTML = origText;
                btn.disabled = false;
                alert('Request failed: ' + err);
            });
        }

        // Export manual grid to Excel
        function downloadManualGridXlsx() {
            if (manualGridRows.length === 0) {
                alert('{{ $isAr ? "الجدول فارغ ولا يوجد بيانات للتصدير." : "Grid is empty!" }}');
                return;
            }
            document.getElementById('export_grid_data').value = JSON.stringify(manualGridRows);
            document.getElementById('manual-grid-export-form').submit();
        }

        // Auto-load demo rows on first visit if grid is empty
        document.addEventListener('DOMContentLoaded', function() {
            loadMedicalSamples();
        });
    </script>
</x-layouts.admin>

<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'الأطباء والعيادات (قاعدة الاتصال)' : 'Doctors & Clinics (Contact Portfolio)'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'إدارة ملفات الأطباء والمراكز الطبية وتصنيفها الفئوي والتخصصي والموقع الجغرافي' : 'Manage medical professionals, clinic locations, classifications, specialties, and visit tracking.'"
    :breadcrumbs="[
        (app()->getLocale() === 'ar' ? 'نظام المندوب الطبي' : 'MR CRM') => route('admin.mr.live-map'),
        (app()->getLocale() === 'ar' ? 'الأطباء والعيادات' : 'Doctors & Clinics') => route('admin.mr.contacts.index')
    ]"
>
    <x-slot name="actions">
        <div class="flex items-center gap-2">
            <button type="button" onclick="openDoctorImportModal()" class="btn btn-secondary text-sm font-bold shadow-sm flex items-center gap-2 border-emerald-500/40 text-emerald-700 dark:text-emerald-300 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 cursor-pointer" title="{{ app()->getLocale() === 'ar' ? 'استيراد الأطباء والمراكز من ملف إكسل' : 'Batch Import Doctors from Excel' }}">
                <i class="fa-solid fa-file-arrow-up text-emerald-500"></i>
                <span>{{ app()->getLocale() === 'ar' ? 'استيراد أطباء (.xlsx)' : 'Import Doctors (.xlsx)' }}</span>
            </button>
            <a href="{{ request()->fullUrlWithQuery(['export' => 'xlsx']) }}" class="btn btn-secondary text-sm font-bold shadow-sm flex items-center gap-2" title="Export Filtered Doctors List">
                <i class="fa-solid fa-file-excel text-emerald-500"></i>
                <span>{{ app()->getLocale() === 'ar' ? 'تصدير إكسيل (.xlsx)' : 'Export Excel (.xlsx)' }}</span>
            </a>
            <a href="{{ route('admin.mr.contacts.create') }}" class="btn btn-primary font-bold shadow-sm">
                <i class="fa-solid fa-user-plus mr-1.5 ml-1.5"></i> {{ app()->getLocale() === 'ar' ? 'إضافة طبيب جديد' : 'Add New Doctor' }}
            </a>
        </div>
    </x-slot>

    <!-- Toolbar & Filter Card -->
    <div class="card p-4 mb-6">
        <form method="GET" action="{{ route('admin.mr.contacts.index') }}" class="flex flex-wrap items-center justify-between gap-4">
            <div class="search-wrapper flex-1 min-w-[260px] max-w-md">
                <svg class="search-icon" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control search-input text-sm" placeholder="{{ app()->getLocale() === 'ar' ? 'بحث بالاسم، الكود، الهاتف، العيادة...' : 'Search doctor name, code, phone, clinic...' }}">
            </div>

            <div class="flex items-center gap-3 flex-wrap">
                <select name="classification_id" onchange="this.form.submit()" class="form-select text-sm w-auto">
                    <option value="">{{ app()->getLocale() === 'ar' ? 'جميع الفئات (A+/A/B/C)' : 'All Classifications' }}</option>
                    @foreach($classifications as $c)
                        <option value="{{ $c->id }}" {{ request('classification_id') == $c->id ? 'selected' : '' }}>
                            Class {{ $c->code }} ({{ $c->points }} pts / {{ $c->required_visits }} req)
                        </option>
                    @endforeach
                </select>

                <select name="specialty_id" onchange="this.form.submit()" class="form-select text-sm w-auto">
                    <option value="">{{ app()->getLocale() === 'ar' ? 'جميع التخصصات' : 'All Specialties' }}</option>
                    @foreach($specialties as $sp)
                        <option value="{{ $sp->id }}" {{ request('specialty_id') == $sp->id ? 'selected' : '' }}>
                            {{ $sp->name }}
                        </option>
                    @endforeach
                </select>

                <select name="city_id" onchange="this.form.submit()" class="form-select text-sm w-auto">
                    <option value="">{{ app()->getLocale() === 'ar' ? 'جميع المدن' : 'All Cities' }}</option>
                    @foreach($cities as $ct)
                        <option value="{{ $ct->id }}" {{ request('city_id') == $ct->id ? 'selected' : '' }}>
                            {{ $ct->name }}
                        </option>
                    @endforeach
                </select>

                @if(request()->hasAny(['search', 'classification_id', 'specialty_id', 'city_id']))
                    <a href="{{ route('admin.mr.contacts.index') }}" class="btn btn-outline text-xs">
                        <i class="fa-solid fa-xmark mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'إلغاء الفلتر' : 'Reset' }}
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Doctors Table -->
    <div class="card p-0 overflow-hidden shadow-sm border border-gray-100 dark:border-gray-800">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm table bz-sortable-table" data-table-sortable="true">
                <thead class="bg-gray-50/75 dark:bg-gray-800/60 text-gray-600 dark:text-gray-300 font-semibold border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="px-5 py-3.5">{{ app()->getLocale() === 'ar' ? 'الكود' : 'Code' }}</th>
                        <th class="px-5 py-3.5">{{ app()->getLocale() === 'ar' ? 'اسم الطبيب / المركز' : 'Doctor & Facility' }}</th>
                        <th class="px-5 py-3.5">{{ app()->getLocale() === 'ar' ? 'التخصص' : 'Specialty' }}</th>
                        <th class="px-5 py-3.5 text-center">{{ app()->getLocale() === 'ar' ? 'التصنيف الفئوي' : 'Class' }}</th>
                        <th class="px-5 py-3.5">{{ app()->getLocale() === 'ar' ? 'المدينة / المنطقة' : 'Location' }}</th>
                        <th class="px-5 py-3.5">{{ app()->getLocale() === 'ar' ? 'الهاتف' : 'Contact' }}</th>
                        <th class="px-5 py-3.5 text-center">{{ app()->getLocale() === 'ar' ? 'إحداثيات GPS' : 'GPS Coords' }}</th>
                        <th class="px-5 py-3.5 text-center">{{ app()->getLocale() === 'ar' ? 'الزيارات' : 'Visits' }}</th>
                        <th class="px-5 py-3.5 text-right no-sort" data-no-sort>{{ app()->getLocale() === 'ar' ? 'الإجراءات' : 'Actions' }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($contacts as $doc)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/40 transition-colors">
                            <td class="px-5 py-4 font-mono text-xs font-bold text-gray-500">
                                {{ $doc->code }}
                            </td>
                            <td class="px-5 py-4">
                                <a href="{{ route('admin.mr.contacts.show', $doc->id) }}" class="font-bold text-gray-900 dark:text-white hover:text-sky-500 transition-colors">
                                    {{ $doc->name }}
                                </a>
                                @if($doc->hospital_clinic_name)
                                    <div class="text-xs text-gray-500 mt-0.5">
                                        <i class="fa-solid fa-hospital text-gray-400 mr-1 ml-1 text-[11px]"></i> {{ $doc->hospital_clinic_name }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <span class="badge badge-outline text-xs">
                                    {{ $doc->specialty?->name ?? 'General' }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                @if($doc->classification)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-black {{ $doc->classification->code === 'A+' ? 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-400 border border-amber-300 dark:border-amber-700' : ($doc->classification->code === 'A' ? 'bg-sky-100 text-sky-800 dark:bg-sky-950/60 dark:text-sky-400' : 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300') }}">
                                        {{ $doc->classification->code }}
                                    </span>
                                    <div class="text-[10px] text-gray-400 mt-0.5">{{ $doc->classification->points }} pts • {{ $doc->classification->required_visits }}/cycle</div>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-xs text-gray-600 dark:text-gray-400">
                                <div class="font-semibold">{{ $doc->city?->name ?? '—' }}</div>
                                @if($doc->area || $doc->break)
                                    <div class="text-[11px] text-sky-600 dark:text-sky-400 font-medium">
                                        {{ $doc->area?->name }} @if($doc->break) › <span class="font-bold text-indigo-500">{{ $doc->break?->name }}</span> @endif
                                    </div>
                                @elseif($doc->region)
                                    <div class="text-gray-400 text-[11px]">{{ $doc->region }}</div>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-xs">
                                @if($doc->phone)
                                    <div class="text-gray-800 dark:text-gray-200"><i class="fa-solid fa-phone text-gray-400 mr-1 ml-1"></i> {{ $doc->phone }}</div>
                                @endif
                                @if($doc->email)
                                    <div class="text-gray-400 text-[11px]"><i class="fa-solid fa-envelope text-gray-400 mr-1 ml-1"></i> {{ $doc->email }}</div>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center text-xs">
                                @if($doc->latitude && $doc->longitude)
                                    <span class="inline-flex items-center gap-1 text-emerald-600 dark:text-emerald-400 font-bold" title="{{ $doc->latitude }}, {{ $doc->longitude }}">
                                        <i class="fa-solid fa-location-crosshairs"></i> Geocoded
                                    </span>
                                @else
                                    <span class="text-amber-500 text-[11px]">
                                        <i class="fa-solid fa-triangle-exclamation"></i> No GPS
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center font-bold text-gray-700 dark:text-gray-300">
                                {{ $doc->visits_count }}
                            </td>
                            <td class="px-5 py-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.mr.contacts.show', $doc->id) }}" class="btn btn-sm btn-ghost p-1.5 text-gray-500 hover:text-sky-500" title="View Profile">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.mr.contacts.edit', $doc->id) }}" class="btn btn-sm btn-ghost p-1.5 text-gray-500 hover:text-amber-500" title="Edit Doctor">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    @if(empty($isRep) || !$isRep)
                                    <form method="POST" action="{{ route('admin.mr.contacts.destroy', $doc->id) }}" onsubmit="return confirm('Delete this doctor?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-ghost p-1.5 text-gray-500 hover:text-rose-500" title="Delete">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-5 py-12 text-center text-gray-400">
                                <i class="fa-solid fa-user-doctor text-4xl mb-2"></i>
                                <p class="text-sm font-semibold">{{ app()->getLocale() === 'ar' ? 'لم يتم العثور على أطباء مسجلين مطابقين للبحث.' : 'No doctors or clinics found matching the query.' }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($contacts->hasPages())
            <div class="p-4 border-t border-gray-100 dark:border-gray-800">
                {{ $contacts->links() }}
            </div>
        @endif
    </div>

    <!-- ===================================================================== -->
    <!-- DOCTORS EXCEL BATCH IMPORT MODAL (SENIOR EXECUTIVE UI)                -->
    <!-- ===================================================================== -->
    <div id="doctorImportModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity" onclick="closeDoctorImportModal()"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-3xl bg-white dark:bg-[#071F33] text-left rtl:text-right shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl border border-slate-200/90 dark:border-[#133957]">
                
                <!-- Modal Header -->
                <div class="p-6 bg-gradient-to-r from-slate-50 to-sky-50/50 dark:from-[#071F33] dark:to-[#0A2942] border-b border-slate-200/80 dark:border-[#133957] flex items-start justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-2xl shadow-inner">
                            <i class="fa-solid fa-file-excel"></i>
                        </div>
                        <div>
                            <h3 class="text-base sm:text-lg font-black text-slate-900 dark:text-white" id="modal-title">
                                {{ app()->getLocale() === 'ar' ? 'استيراد الأطباء والمراكز من ملف إكسل' : 'Batch Import Doctors & Clinics (Excel / CSV)' }}
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                {{ app()->getLocale() === 'ar' ? 'فحص ومطابقة جماعية لقاعدة بيانات الأطباء مع التعيين التلقائي للمناديب والدورات' : 'Intelligent doctor onboarding with pre-flight inspection, rep allocation & cycle agenda sync' }}
                            </p>
                        </div>
                    </div>
                    <button type="button" onclick="closeDoctorImportModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-[#0B2942] transition-colors cursor-pointer">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6 space-y-5 max-h-[75vh] overflow-y-auto custom-scrollbar">

                    <!-- Section 1: Download Standard Example Template -->
                    <div class="p-4 rounded-2xl bg-emerald-50/60 dark:bg-emerald-950/20 border border-emerald-500/30 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-xs sm:text-sm text-emerald-900 dark:text-emerald-200">
                                    {{ app()->getLocale() === 'ar' ? 'نموذج الإكسل المعتمد (Example Sheet)' : 'Official Example Excel Sheet & Guide' }}
                                </span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-500 text-white">XLSX</span>
                            </div>
                            <p class="text-[11px] text-emerald-700/80 dark:text-emerald-300/80 leading-relaxed">
                                {{ app()->getLocale() === 'ar' ? 'يحتوي على 7 أطباء تجريبيين بتخصصات وفئات مطابقة للنظام، وقوائم منسدلة للفئات، ودليل الحقول المرجعي.' : 'Includes 7 realistic sample HCPs, interactive dropdown data validation (A+/A/B/C), and a reference guide sheet.' }}
                            </p>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            <a href="{{ route('admin.mr.excel.template', ['type' => 'doctors', 'format' => 'xlsx']) }}" class="btn btn-save btn-xs px-3.5 py-2 rounded-xl font-bold flex items-center gap-1.5 shadow-sm">
                                <i class="fa-solid fa-download text-xs"></i>
                                <span>{{ app()->getLocale() === 'ar' ? 'تحميل النموذج (.xlsx)' : 'Download Example (.xlsx)' }}</span>
                            </a>
                            <a href="{{ route('admin.mr.excel.template', ['type' => 'doctors', 'format' => 'csv']) }}" class="px-2.5 py-2 rounded-xl text-xs font-bold bg-white dark:bg-[#031827] text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-[#1E4E73] hover:bg-slate-100 transition-colors">
                                CSV
                            </a>
                        </div>
                    </div>

                    <!-- Section 2: Drag & Drop File Upload -->
                    <div>
                        <label class="block text-xs font-black uppercase text-slate-400 dark:text-slate-400 tracking-wider mb-2">
                            <i class="fa-solid fa-cloud-arrow-up text-sky-500 mr-1 ml-1"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'اختر أو اسحب ملف الإكسل' : 'Select or Drop Excel Spreadsheet' }}</span>
                        </label>

                        <div id="modalDropzoneBox" 
                            class="border-2 border-dashed border-slate-300 dark:border-[#1E4E73] rounded-2xl p-6 text-center bg-slate-50/50 dark:bg-[#031827]/60 hover:border-emerald-500 dark:hover:border-emerald-500 transition-all cursor-pointer"
                            onclick="document.getElementById('modalDoctorFileInput').click()"
                            ondragover="event.preventDefault(); this.classList.add('border-emerald-500', 'bg-emerald-50/20');"
                            ondragleave="this.classList.remove('border-emerald-500', 'bg-emerald-50/20');"
                            ondrop="handleModalFileDrop(event)">
                            
                            <input type="file" id="modalDoctorFileInput" accept=".xlsx,.xls,.csv,.txt" class="hidden" onchange="handleModalFileSelect(this)">
                            
                            <div class="space-y-2 pointer-events-none">
                                <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-2xl mx-auto shadow-inner">
                                    <i class="fa-solid fa-file-arrow-up"></i>
                                </div>
                                <div id="modalFilePrompt">
                                    <p class="text-xs sm:text-sm font-bold text-slate-700 dark:text-slate-200">
                                        {{ app()->getLocale() === 'ar' ? 'انقر لاختيار الملف، أو اسحبه وأفلته هنا' : 'Click to browse or drag & drop file here' }}
                                    </p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">
                                        {{ app()->getLocale() === 'ar' ? 'يدعم (.xlsx, .xls, .csv) حتى 20 ميغابايت' : 'Supports (.xlsx, .xls, .csv) up to 20MB' }}
                                    </p>
                                </div>
                                <div id="modalFileSelectedInfo" class="hidden">
                                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white dark:bg-[#071F33] border border-emerald-500 text-emerald-700 dark:text-emerald-300 font-bold text-xs">
                                        <i class="fa-solid fa-file-excel text-emerald-500"></i>
                                        <span id="modalFileNameText"></span>
                                        <span id="modalFileSizeText" class="text-[10px] text-slate-400 font-normal"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pre-flight Inspect Status Badge (Hidden until checked) -->
                    <div id="modalInspectResultBox" class="hidden p-3.5 rounded-2xl bg-sky-50/60 dark:bg-sky-950/20 border border-sky-400/30 text-xs space-y-2">
                        <div class="flex items-center justify-between font-bold text-sky-900 dark:text-sky-200">
                            <span class="flex items-center gap-1.5">
                                <i class="fa-solid fa-circle-check text-sky-500"></i>
                                <span>{{ app()->getLocale() === 'ar' ? 'نتيجة الفحص المسبق للملف:' : 'Pre-flight File Inspection:' }}</span>
                            </span>
                            <span id="modalInspectRowCount" class="font-mono text-xs"></span>
                        </div>
                        <div id="modalInspectMatchedFields" class="text-[11px] text-slate-600 dark:text-slate-300 flex flex-wrap gap-1.5"></div>
                    </div>

                    <!-- Section 3: Import Rules & Duplicate Strategy -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                        <!-- Duplicate Resolution Rule -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                {{ app()->getLocale() === 'ar' ? 'معالجة الأطباء المكررين' : 'Duplicate Resolution Rule' }}
                            </label>
                            <select id="modalDuplicateAction" class="form-select text-xs w-full rounded-xl font-medium border border-slate-200 dark:border-[#1E4E73] bg-white dark:bg-[#031827] text-slate-900 dark:text-white">
                                <option value="skip" selected>{{ app()->getLocale() === 'ar' ? 'تخطي الطبيب الموجود مسبقاً (حماية البيانات)' : 'Skip Existing (Preserve DB record)' }}</option>
                                <option value="update">{{ app()->getLocale() === 'ar' ? 'تحديث البيانات الفارغة فقط' : 'Update & Complement Missing Fields' }}</option>
                                <option value="overwrite">{{ app()->getLocale() === 'ar' ? 'استبدال وتحديث السجل بالكامل' : 'Overwrite Existing Record' }}</option>
                            </select>
                        </div>

                        <!-- Default Rep Assignment -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                {{ app()->getLocale() === 'ar' ? 'المندوب الافتراضي (اختياري)' : 'Default Rep Assignment' }}
                            </label>
                            <select id="modalDefaultRepId" class="form-select text-xs w-full rounded-xl font-medium border border-slate-200 dark:border-[#1E4E73] bg-white dark:bg-[#031827] text-slate-900 dark:text-white">
                                <option value="">{{ app()->getLocale() === 'ar' ? '— بناءً على الملف أو بدون تعيين —' : '— From file or unassigned —' }}</option>
                                @foreach($medicalReps as $rep)
                                    <option value="{{ $rep->id }}">{{ $rep->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Target Visits Quota -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                {{ app()->getLocale() === 'ar' ? 'الحد الافتراضي للزيارات/شهر' : 'Default Monthly Visit Quota' }}
                            </label>
                            <input type="number" id="modalTargetVisitsDefault" value="2" min="1" max="20" class="form-input text-xs w-full rounded-xl font-bold font-mono border border-slate-200 dark:border-[#1E4E73] bg-white dark:bg-[#031827] text-slate-900 dark:text-white">
                        </div>

                        <!-- Auto-Enroll in Active Cycle -->
                        <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 dark:bg-[#031827] border border-slate-200 dark:border-[#1E4E73]">
                            <div>
                                <span class="text-xs font-bold text-slate-900 dark:text-white block">{{ app()->getLocale() === 'ar' ? 'إدراج بالدورة النشطة' : 'Enroll in Active Cycle' }}</span>
                                <span class="text-[10px] text-slate-400 block">{{ $activeCycle?->name ?? (app()->getLocale() === 'ar' ? 'الدورة الحالية' : 'Current Cycle') }}</span>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" id="modalAutoAssignCycle" checked class="sr-only peer">
                                <div class="w-9 h-5 bg-slate-200 peer-focus:outline-hidden rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-500"></div>
                            </label>
                        </div>
                    </div>

                    <!-- Progress / Status Message Box -->
                    <div id="modalImportStatusAlert" class="hidden p-4 rounded-xl text-xs font-bold"></div>
                </div>

                <!-- Modal Footer -->
                <div class="p-5 bg-slate-50 dark:bg-[#06243C] border-t border-slate-200 dark:border-[#133957] flex flex-col sm:flex-row items-center justify-between gap-3">
                    <div class="flex items-center gap-2 text-xs">
                        <a href="{{ route('admin.mr.excel.index') }}" class="text-sky-600 dark:text-sky-400 font-bold hover:underline flex items-center gap-1">
                            <i class="fa-solid fa-table-cells text-xs"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'الانتقال لمركز إكسل المتكامل والجدول التفاعلي' : 'Open Full MR Excel Hub & Manual Grid' }}</span>
                        </a>
                    </div>

                    <div class="flex items-center gap-2.5">
                        <button type="button" onclick="closeDoctorImportModal()" class="px-4 py-2 rounded-xl text-xs font-bold bg-white dark:bg-[#071F33] text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-[#1E4E73] hover:bg-slate-100 dark:hover:bg-[#0B2942] transition-colors cursor-pointer">
                            {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                        </button>

                        <button type="button" id="modalInspectBtn" onclick="inspectModalFile()" class="px-3.5 py-2 rounded-xl text-xs font-bold bg-slate-100 dark:bg-[#0B2942] text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-[#1E4E73] hover:bg-slate-200 cursor-pointer hidden flex items-center gap-1.5">
                            <i class="fa-solid fa-magnifying-glass-chart text-sky-500"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'فحص الملف' : 'Inspect' }}</span>
                        </button>

                        <button type="button" id="modalSubmitImportBtn" onclick="submitModalDoctorImport()" class="btn btn-save text-xs font-black px-6 py-2 rounded-xl flex items-center gap-2 cursor-pointer shadow-lg">
                            <i class="fa-solid fa-bolt text-white" id="modalSubmitIcon"></i>
                            <span id="modalSubmitText">{{ app()->getLocale() === 'ar' ? 'بدء استيراد الأطباء' : 'Import Doctors Now' }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Doctor Import Modal Interaction Scripts -->
    <script>
        let modalSelectedDoctorFile = null;

        function openDoctorImportModal() {
            document.getElementById('doctorImportModal')?.classList.remove('hidden');
        }

        function closeDoctorImportModal() {
            document.getElementById('doctorImportModal')?.classList.add('hidden');
        }

        function handleModalFileSelect(input) {
            if (input.files && input.files[0]) {
                setModalDoctorFile(input.files[0]);
            }
        }

        function handleModalFileDrop(e) {
            e.preventDefault();
            const box = document.getElementById('modalDropzoneBox');
            box.classList.remove('border-emerald-500', 'bg-emerald-50/20');
            if (e.dataTransfer.files && e.dataTransfer.files[0]) {
                setModalDoctorFile(e.dataTransfer.files[0]);
            }
        }

        function setModalDoctorFile(file) {
            modalSelectedDoctorFile = file;
            document.getElementById('modalFilePrompt').classList.add('hidden');
            document.getElementById('modalFileSelectedInfo').classList.remove('hidden');
            document.getElementById('modalFileNameText').innerText = file.name;
            document.getElementById('modalFileSizeText').innerText = '(' + (file.size / 1024).toFixed(1) + ' KB)';
            document.getElementById('modalInspectBtn').classList.remove('hidden');
            
            // Auto inspect upon selection
            inspectModalFile();
        }

        async function inspectModalFile() {
            if (!modalSelectedDoctorFile) return;

            const inspectBtn = document.getElementById('modalInspectBtn');
            const originalText = inspectBtn.innerHTML;
            inspectBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-sky-500"></i>';
            inspectBtn.disabled = true;

            const formData = new FormData();
            formData.append('file', modalSelectedDoctorFile);

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
                    const resultBox = document.getElementById('modalInspectResultBox');
                    resultBox.classList.remove('hidden');
                    document.getElementById('modalInspectRowCount').innerText = (data.total_rows || 0) + ' {{ app()->getLocale() === 'ar' ? 'طبيب مرصود في الملف' : 'HCP rows detected' }}';
                    
                    const fieldsContainer = document.getElementById('modalInspectMatchedFields');
                    fieldsContainer.innerHTML = '';
                    
                    const keys = Object.keys(data.matched_mapping || {});
                    keys.forEach(k => {
                        const span = document.createElement('span');
                        span.className = 'px-2 py-0.5 rounded-md bg-white dark:bg-[#071F33] border border-slate-200 dark:border-[#1E4E73] font-mono text-[10px] text-emerald-600 dark:text-emerald-400 font-bold';
                        span.innerText = '✓ ' + k;
                        fieldsContainer.appendChild(span);
                    });

                    if (data.potential_duplicates > 0) {
                        const dupSpan = document.createElement('span');
                        dupSpan.className = 'px-2 py-0.5 rounded-md bg-amber-500/10 border border-amber-500/40 text-amber-600 dark:text-amber-400 font-bold text-[10px]';
                        dupSpan.innerText = '⚠️ ' + data.potential_duplicates + ' {{ app()->getLocale() === 'ar' ? 'أطباء موجودين مسبقاً' : 'potential duplicates' }}';
                        fieldsContainer.appendChild(dupSpan);
                    }
                }
            } catch (e) {
                console.error('Inspection failed:', e);
            } finally {
                inspectBtn.innerHTML = originalText;
                inspectBtn.disabled = false;
            }
        }

        async function submitModalDoctorImport() {
            if (!modalSelectedDoctorFile) {
                alert('{{ app()->getLocale() === 'ar' ? 'يرجى اختيار ملف الإكسل أولاً.' : 'Please select an Excel spreadsheet first.' }}');
                return;
            }

            const submitBtn = document.getElementById('modalSubmitImportBtn');
            const submitText = document.getElementById('modalSubmitText');
            const submitIcon = document.getElementById('modalSubmitIcon');
            const statusAlert = document.getElementById('modalImportStatusAlert');

            submitBtn.disabled = true;
            submitIcon.className = 'fa-solid fa-spinner fa-spin text-white';
            submitText.innerText = '{{ app()->getLocale() === 'ar' ? 'جاري الاستيراد والمعالجة...' : 'Importing & Validating...' }}';
            statusAlert.classList.add('hidden');

            const formData = new FormData();
            formData.append('file', modalSelectedDoctorFile);
            formData.append('import_type', 'doctors');
            formData.append('duplicate_action', document.getElementById('modalDuplicateAction').value);
            formData.append('default_rep_id', document.getElementById('modalDefaultRepId').value);
            formData.append('auto_assign_cycle', document.getElementById('modalAutoAssignCycle').checked ? '1' : '0');
            formData.append('target_visits_default', document.getElementById('modalTargetVisitsDefault').value);

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
                statusAlert.classList.remove('hidden');

                if (data.success) {
                    statusAlert.className = 'p-4 rounded-xl text-xs font-bold bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-400 text-emerald-800 dark:text-emerald-200';
                    statusAlert.innerHTML = '<i class="fa-solid fa-circle-check text-emerald-600 mr-1 ml-1 text-sm"></i> ' + (data.message || 'Import completed successfully!');
                    
                    setTimeout(() => {
                        window.location.reload();
                    }, 1200);
                } else {
                    statusAlert.className = 'p-4 rounded-xl text-xs font-bold bg-rose-50 dark:bg-rose-950/40 border border-rose-400 text-rose-800 dark:text-rose-200';
                    statusAlert.innerHTML = '<i class="fa-solid fa-circle-exclamation text-rose-600 mr-1 ml-1 text-sm"></i> ' + (data.message || 'Failed to import doctors.');
                    submitBtn.disabled = false;
                    submitIcon.className = 'fa-solid fa-bolt text-white';
                    submitText.innerText = '{{ app()->getLocale() === 'ar' ? 'إعادة المحاولة' : 'Retry Import' }}';
                }
            } catch (err) {
                statusAlert.classList.remove('hidden');
                statusAlert.className = 'p-4 rounded-xl text-xs font-bold bg-rose-50 dark:bg-rose-950/40 border border-rose-400 text-rose-800 dark:text-rose-200';
                statusAlert.innerText = 'Network error during import: ' + err.toString();
                submitBtn.disabled = false;
                submitIcon.className = 'fa-solid fa-bolt text-white';
                submitText.innerText = '{{ app()->getLocale() === 'ar' ? 'إعادة المحاولة' : 'Retry Import' }}';
            }
        }
    </script>
</x-layouts.admin>

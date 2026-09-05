<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'الملف الشخصي وإعدادات الحساب' : 'My Profile & Account Security'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'تحكم في بياناتك الشخصية، الأمان، الصلاحيات الممنوحة، ونظام التنبيهات الصوتية.' : 'Manage your identity, security credentials, active privileges, and acoustic preferences.'"
    :breadcrumbs="[__('admin.menu.users') => route('admin.users.index'), (app()->getLocale() === 'ar' ? 'الملف الشخصي' : 'My Profile') => route('admin.profile.index')]"
>
    <!-- Profile Banner Card -->
    <div class="card shadow-md border border-gray-100 dark:border-gray-800 mb-8 overflow-hidden rounded-2xl transition-all duration-300 hover:shadow-lg">
        <!-- Hero Cover Header with Luxury Gradient & Subtle Bioceutical Pattern -->
        <div class="h-44 sm:h-52 bg-gradient-to-r from-[#031827] via-[#0A4F78] to-[#1a6b9a] relative overflow-hidden">
            <div class="absolute inset-0 opacity-15 bg-[radial-gradient(#2A8FC2_1px,transparent_1px)] [background-size:16px_16px]"></div>
            <div class="absolute -right-16 -top-16 w-64 h-64 rounded-full bg-[#2A8FC2]/20 blur-3xl pointer-events-none"></div>
            <div class="absolute left-1/4 -bottom-16 w-80 h-80 rounded-full bg-[#0A4F78]/40 blur-3xl pointer-events-none"></div>
        </div>

        <!-- Profile Bar Container -->
        <div class="px-6 sm:px-8 pb-7 pt-0 relative bg-white dark:bg-[#071d2e]">
            <div class="flex flex-col lg:flex-row items-start lg:items-end justify-between gap-6 -mt-20 sm:-mt-24">
                
                <!-- Avatar & Identity Info -->
                <div class="flex flex-col sm:flex-row items-center sm:items-end gap-6 text-center sm:text-start w-full lg:w-auto">
                    <!-- Avatar Frame -->
                    <div class="relative flex-shrink-0 group">
                        <div class="w-32 h-32 sm:w-36 sm:h-36 rounded-2xl bg-white dark:bg-[#062B49] p-1.5 shadow-2xl ring-4 ring-white dark:ring-[#0c2438] overflow-hidden flex items-center justify-center font-black text-4xl text-primary transition-all duration-300 group-hover:scale-105 group-hover:shadow-primary/30">
                            @if($user->avatar_url)
                                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-full h-full object-cover rounded-xl" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div style="display: none;" class="w-full h-full bg-gradient-to-br from-[#0A4F78] to-[#031827] text-white items-center justify-center rounded-xl text-3xl font-black">
                                    {{ strtoupper(substr(trim($user->name), 0, 2)) }}
                                </div>
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-[#0A4F78] to-[#031827] text-white flex items-center justify-center rounded-xl text-3xl font-black">
                                    {{ strtoupper(substr(trim($user->name), 0, 2)) }}
                                </div>
                            @endif
                        </div>
                        <!-- Online Status Dot -->
                        <span class="absolute bottom-2 end-2 flex h-4 w-4">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-4 w-4 bg-emerald-500 border-2 border-white dark:border-[#062B49]"></span>
                        </span>
                    </div>

                    <!-- Identity & Meta Details -->
                    <div class="space-y-2.5 pb-2">
                        <div class="flex items-center justify-center sm:justify-start gap-3 flex-wrap">
                            <h2 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">
                                <bdi>{{ $user->name }}</bdi>
                            </h2>
                            <span class="badge badge-accent text-xs font-bold uppercase tracking-wider px-3 py-1 shadow-xs border border-primary/20">
                                <i class="fa-solid fa-shield-halved mr-1 ml-1 opacity-80"></i> {{ $role?->name ?? 'Super Administrator' }}
                            </span>
                            <span class="badge badge-success text-xs font-bold px-3 py-1 shadow-xs border border-emerald-500/20">
                                <i class="fa-solid fa-circle-check mr-1 ml-1 opacity-80"></i> {{ ucfirst($user->status ?? 'Active') }}
                            </span>
                        </div>

                        <!-- Contact Meta Chips -->
                        <div class="text-sm text-slate-500 dark:text-slate-400 flex items-center justify-center sm:justify-start gap-4 sm:gap-6 flex-wrap font-medium">
                            <span class="inline-flex items-center gap-1.5 hover:text-primary transition-colors">
                                <i class="fa-solid fa-envelope text-primary/70"></i> {{ $user->email }}
                            </span>
                            @if($user->phone)
                                <span class="inline-flex items-center gap-1.5 hover:text-primary transition-colors">
                                    <i class="fa-solid fa-phone text-primary/70"></i> {{ $user->phone }}
                                </span>
                            @endif
                            <span class="inline-flex items-center gap-1.5">
                                <i class="fa-solid fa-calendar-check text-primary/70"></i> {{ __('admin.customers.member_since') }} <strong class="text-slate-700 dark:text-slate-200">{{ $user->created_at?->format('M Y') ?? 'Jan 2026' }}</strong>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Action Button in Header -->
                <div class="flex items-center justify-center sm:justify-end gap-3 pb-2 w-full lg:w-auto">
                    <a href="#security" class="btn btn-secondary btn-sm font-bold shadow-xs hover:shadow-sm transition-all hover:-translate-y-0.5">
                        <i class="fa-solid fa-key mr-1.5 ml-1.5"></i> {{ app()->getLocale() === 'ar' ? 'تغيير كلمة المرور' : 'Change Password' }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Grid Content -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Left Column: Personal Info & Security -->
        <div class="lg:col-span-8 space-y-8">
            
            <!-- 1. Personal Information Form -->
            <div class="card shadow-sm border border-gray-100 dark:border-gray-800 p-6 sm:p-8 rounded-2xl hover:shadow-md transition-shadow">
                <div class="border-b border-gray-100 dark:border-gray-800 pb-5 mb-6 flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-black text-gray-900 dark:text-white flex items-center gap-2.5">
                            <div class="w-10 h-10 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                                <i class="fa-solid fa-user-pen"></i>
                            </div>
                            {{ app()->getLocale() === 'ar' ? 'المعلومات الشخصية والبيانات العامة' : 'Personal Details & Identity' }}
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2 font-medium">
                            {{ app()->getLocale() === 'ar' ? 'تعديل الاسم المعروض، البريد الإلكتروني، ورقم الهاتف الشخصي.' : 'Update your display name, primary email address, and contact number.' }}
                        </p>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="group">
                            <x-forms.input 
                                name="name" 
                                :label="__('admin.users.name')" 
                                :value="old('name', $user->name)" 
                                required 
                            />
                        </div>

                        <div class="group">
                            <x-forms.input 
                                name="email" 
                                type="email" 
                                :label="__('admin.users.email')" 
                                :value="old('email', $user->email)" 
                                required 
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="group sm:col-span-2">
                            <x-forms.input 
                                name="phone" 
                                type="tel" 
                                :label="app()->getLocale() === 'ar' ? 'رقم الهاتف / الجوال' : 'Phone / Mobile Number'" 
                                placeholder="+966 50 000 0000" 
                                :value="old('phone', $user->phone)" 
                            />
                        </div>
                    </div>

                    <!-- FilePond Filament FileUpload Component for Avatar -->
                    <div class="group">
                        <x-file-uploader 
                            name="avatar_file" 
                            :label="app()->getLocale() === 'ar' ? 'الصورة الرمزية (رفع ملف جديد عبر FilePond)' : 'Avatar Photo (Upload via FilePond)'" 
                            :helper="app()->getLocale() === 'ar' ? 'اسحب وأفلت صورة شخصية جديدة هنا أو انقر للاستعراض (PNG, JPG, WEBP حتى 2MB)' : 'Drag & drop your new avatar image or click to browse (PNG, JPG, WEBP up to 2MB)'"
                            accept="image/png, image/jpeg, image/jpg, image/webp"
                            :maxSize="2"
                            :existingFiles="$user->avatar_url ? [$user->avatar_url] : []"
                        />
                    </div>

                    <div class="group">
                        <x-forms.textarea 
                            name="bio" 
                            :label="app()->getLocale() === 'ar' ? 'نبذة تعريفية / التخصص السريري' : 'Bio / Professional Summary'" 
                            rows="4" 
                            placeholder="{{ app()->getLocale() === 'ar' ? 'أخصائي العمليات السريرية وإدارة التركيبات الحيوية...' : 'Clinical operations lead and longevity protocol specialist...' }}" 
                            :value="old('bio', $user->bio)" 
                        />
                    </div>

                    <div class="flex justify-end pt-4 mt-6 border-t border-gray-100 dark:border-gray-800">
                        <button type="submit" class="btn btn-primary font-bold shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all px-6 py-2.5">
                            <i class="fa-solid fa-floppy-disk mr-2 ml-2"></i>
                            {{ app()->getLocale() === 'ar' ? 'حفظ التعديلات الشخصية' : 'Save Profile Changes' }}
                        </button>
                    </div>
                </form>
            </div>

            <!-- 2. Security & Password Update -->
            <div id="security" class="card shadow-sm border border-gray-100 dark:border-gray-800 p-6 sm:p-8 rounded-2xl hover:shadow-md transition-shadow">
                <div class="border-b border-gray-100 dark:border-gray-800 pb-5 mb-6 flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-black text-gray-900 dark:text-white flex items-center gap-2.5">
                            <div class="w-10 h-10 rounded-lg bg-amber-500/10 text-amber-500 flex items-center justify-center">
                                <i class="fa-solid fa-shield-halved"></i>
                            </div>
                            {{ app()->getLocale() === 'ar' ? 'أمان الحساب وكلمة المرور' : 'Security & Password Credentials' }}
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2 font-medium">
                            {{ app()->getLocale() === 'ar' ? 'يرجى استخدام كلمة مرور قوية تحتوي على أحرف وأرقام ورموز.' : 'Ensure your account is utilizing a robust, high-entropy password.' }}
                        </p>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.profile.password') }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="max-w-xl">
                        <x-forms.input 
                            name="current_password" 
                            type="password" 
                            :label="app()->getLocale() === 'ar' ? 'كلمة المرور الحالية' : 'Current Password'" 
                            placeholder="••••••••" 
                            required 
                        />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 max-w-3xl">
                        <x-forms.input 
                            name="password" 
                            type="password" 
                            :label="app()->getLocale() === 'ar' ? 'كلمة المرور الجديدة' : 'New Password'" 
                            placeholder="••••••••" 
                            required 
                        />

                        <x-forms.input 
                            name="password_confirmation" 
                            type="password" 
                            :label="app()->getLocale() === 'ar' ? 'تأكيد كلمة المرور الجديدة' : 'Confirm New Password'" 
                            placeholder="••••••••" 
                            required 
                        />
                    </div>

                    <div class="flex justify-end pt-4 mt-6 border-t border-gray-100 dark:border-gray-800">
                        <button type="submit" class="btn btn-primary font-bold shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all px-6 py-2.5">
                            <i class="fa-solid fa-lock mr-2 ml-2"></i>
                            {{ app()->getLocale() === 'ar' ? 'تحديث وتأمين كلمة المرور' : 'Update Password' }}
                        </button>
                    </div>
                </form>
            </div>

        </div>

        <!-- Right Column: Preferences, Acoustic Sound Engine & Permissions Breakdown -->
        <div class="lg:col-span-4 space-y-8">
            
            <!-- 3. Audio & System Preferences -->
            <div class="card shadow-sm border border-gray-100 dark:border-gray-800 p-6 rounded-2xl hover:shadow-md transition-shadow">
                <div class="border-b border-gray-100 dark:border-gray-800 pb-4 mb-5">
                    <h3 class="text-lg font-black text-gray-900 dark:text-white flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-600 flex items-center justify-center">
                            <i class="fa-solid fa-sliders"></i>
                        </div>
                        {{ app()->getLocale() === 'ar' ? 'تفضيلات الإشعارات والنظام' : 'System & Notification Preferences' }}
                    </h3>
                </div>

                <form method="POST" action="{{ route('admin.profile.preferences') }}" class="space-y-5">
                    @csrf
                    @method('PUT')

                    @php
                        $userPrefs = $user->preferences ?? [];
                        $soundEnabled = $userPrefs['sound_enabled'] ?? \App\Models\Setting::get('toast_sound_enabled', true);
                    @endphp

                    <div class="space-y-5">
                        <div class="p-4 bg-gray-50/50 dark:bg-gray-800/30 rounded-xl border border-gray-100 dark:border-gray-700/50 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                            <x-forms.toggle 
                                name="sound_enabled" 
                                :label="app()->getLocale() === 'ar' ? 'المؤثرات الصوتية (Toast Sounds)' : 'Toast Sound Cues'" 
                                :description="app()->getLocale() === 'ar' ? 'تشغيل نغمات هارمونية عند النجاح والتنبيهات.' : 'Synthesized audio chimes for success, errors and notices.'" 
                                :checked="$soundEnabled" 
                            />
                        </div>

                        <!-- Live Audio Sound Tester Buttons -->
                        <div class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700 shadow-inner">
                            <span class="text-xs font-bold text-gray-600 dark:text-gray-400 block mb-3 uppercase tracking-wider">
                                <i class="fa-solid fa-headphones mr-1.5 ml-1.5"></i> {{ app()->getLocale() === 'ar' ? 'اختبار النغمات الصوتية:' : 'Live Audio Tester:' }}
                            </span>
                            <div class="grid grid-cols-2 gap-2">
                                <button type="button" onclick="window.toast.testSound('success')" class="btn btn-sm bg-white dark:bg-gray-800 border border-emerald-200 dark:border-emerald-900/50 hover:border-emerald-400 dark:hover:border-emerald-500 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 text-xs font-bold transition-all shadow-sm">
                                    <i class="fa-solid fa-circle-check mr-1.5 ml-1.5"></i> {{ app()->getLocale() === 'ar' ? 'نجاح' : 'Success' }}
                                </button>
                                <button type="button" onclick="window.toast.testSound('error')" class="btn btn-sm bg-white dark:bg-gray-800 border border-red-200 dark:border-red-900/50 hover:border-red-400 dark:hover:border-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 text-red-700 dark:text-red-400 text-xs font-bold transition-all shadow-sm">
                                    <i class="fa-solid fa-circle-xmark mr-1.5 ml-1.5"></i> {{ app()->getLocale() === 'ar' ? 'خطأ' : 'Error' }}
                                </button>
                                <button type="button" onclick="window.toast.testSound('warning')" class="btn btn-sm bg-white dark:bg-gray-800 border border-amber-200 dark:border-amber-900/50 hover:border-amber-400 dark:hover:border-amber-500 hover:bg-amber-50 dark:hover:bg-amber-900/20 text-amber-700 dark:text-amber-400 text-xs font-bold transition-all shadow-sm">
                                    <i class="fa-solid fa-triangle-exclamation mr-1.5 ml-1.5"></i> {{ app()->getLocale() === 'ar' ? 'تحذير' : 'Warning' }}
                                </button>
                                <button type="button" onclick="window.toast.testSound('info')" class="btn btn-sm bg-white dark:bg-gray-800 border border-blue-200 dark:border-blue-900/50 hover:border-blue-400 dark:hover:border-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 text-blue-700 dark:text-blue-400 text-xs font-bold transition-all shadow-sm">
                                    <i class="fa-solid fa-circle-info mr-1.5 ml-1.5"></i> {{ app()->getLocale() === 'ar' ? 'معلومة' : 'Info' }}
                                </button>
                            </div>
                        </div>

                        <div class="form-group pt-2">
                            <label class="form-label font-bold text-sm mb-2 block text-gray-700 dark:text-gray-300">
                                <i class="fa-solid fa-language mr-1.5 ml-1.5 text-primary opacity-80"></i> {{ __('app.language') }}
                            </label>
                            <select name="language" class="form-select w-full rounded-xl border-gray-200 dark:border-gray-700 shadow-sm focus:border-primary focus:ring focus:ring-primary/20 transition-all">
                                <option value="ar" {{ app()->getLocale() === 'ar' ? 'selected' : '' }}>العربية (Arabic)</option>
                                <option value="en" {{ app()->getLocale() === 'en' ? 'selected' : '' }}>English (US)</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="w-full btn btn-primary font-bold shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all mt-6 py-2.5">
                        <i class="fa-solid fa-check mr-2 ml-2"></i>
                        {{ app()->getLocale() === 'ar' ? 'حفظ تفضيلات النظام' : 'Apply Preferences' }}
                    </button>
                </form>
            </div>

            <!-- 4. Security Clearance & Permissions Matrix Overview -->
            <div class="card shadow-sm border border-gray-100 dark:border-gray-800 p-6 rounded-2xl hover:shadow-md transition-shadow">
                <div class="border-b border-gray-100 dark:border-gray-800 pb-4 mb-5 flex items-center justify-between">
                    <h3 class="text-lg font-black text-gray-900 dark:text-white flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                            <i class="fa-solid fa-id-card-clip"></i>
                        </div>
                        {{ app()->getLocale() === 'ar' ? 'الصلاحيات والوصول الأمني' : 'Assigned Security Clearance' }}
                    </h3>
                </div>

                <div class="space-y-4">
                    <div class="p-4 bg-gradient-to-br from-primary/10 to-primary/5 rounded-xl border border-primary/20 shadow-inner">
                        <div class="text-sm font-black text-primary flex items-center gap-2">
                            <i class="fa-solid fa-crown text-amber-500"></i> {{ $role?->name ?? 'Super Admin' }}
                        </div>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1.5 font-medium leading-relaxed">{{ $role?->description ?? (app()->getLocale() === 'ar' ? 'صلاحيات إدارية كاملة وغير مقيدة للنظام.' : 'Full unrestricted clinical administrative authority.') }}</p>
                    </div>

                    <div class="space-y-1.5 max-h-72 overflow-y-auto pr-2 custom-scrollbar">
                        @foreach($modules as $mKey => $mLabel)
                            @php
                                $hasAccess = in_array('*', $userPermissions) 
                                    || in_array($mKey, $userPermissions)
                                    || in_array($mKey . '.view', $userPermissions)
                                    || (isset($userPermissions[$mKey]['view']) && $userPermissions[$mKey]['view']);
                            @endphp
                            <div class="flex items-center justify-between text-sm py-2.5 border-b border-gray-100/70 dark:border-gray-800/70 hover:bg-gray-50/50 dark:hover:bg-gray-800/30 px-2 rounded-lg transition-colors">
                                <span class="font-bold text-gray-700 dark:text-gray-300">{{ $mLabel }}</span>
                                @if($hasAccess)
                                    <span class="text-emerald-600 font-bold text-xs flex items-center gap-1.5 bg-emerald-50 dark:bg-emerald-900/20 px-2.5 py-1 rounded-full border border-emerald-100 dark:border-emerald-800">
                                        <i class="fa-solid fa-check"></i> {{ app()->getLocale() === 'ar' ? 'مفعل' : 'Granted' }}
                                    </span>
                                @else
                                    <span class="text-gray-400 font-bold text-xs flex items-center gap-1.5 bg-gray-50 dark:bg-gray-800 px-2.5 py-1 rounded-full border border-gray-200 dark:border-gray-700">
                                        <i class="fa-solid fa-minus"></i> {{ app()->getLocale() === 'ar' ? 'محظور' : 'Restricted' }}
                                    </span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>

    </div>
</x-layouts.admin>

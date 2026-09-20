<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ app()->getLocale() === 'ar' ? 'بوابة المندوب الطبي الميدانية' : 'MR Field Operations Portal' }} — BLUE ZONE™</title>
    
    <!-- Theme Detection (Immediate execution to avoid FOUC) -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('bz_mr_theme');
            if (savedTheme === 'light') {
                document.documentElement.classList.remove('dark');
                document.documentElement.classList.add('light');
            } else {
                document.documentElement.classList.add('dark');
                document.documentElement.classList.remove('light');
            }
        })();
    </script>

    <!-- Suppress Tailwind CDN production warning in browser console -->
    <script>
        (function() {
            const originalWarn = console.warn;
            console.warn = function(...args) {
                if (args[0] && typeof args[0] === 'string' && args[0].includes('cdn.tailwindcss.com should not be used in production')) {
                    return;
                }
                originalWarn.apply(console, args);
            };
        })();
    </script>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        bz: {
                            50: '#f0f7fb',
                            100: '#dfedf6',
                            500: '#2a8fc2',
                            600: '#0a4f78',
                            700: '#083f61',
                            800: '#062b49',
                            900: '#031827',
                            950: '#02101c',
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- Google Fonts & Font Awesome -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    <style>
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            -webkit-tap-highlight-color: transparent;
        }
        [dir="ar"] body { font-family: 'Cairo', sans-serif; }
        
        /* Glassmorphism custom styles with light/dark adaptability */
        .glass-card {
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(226, 232, 240, 0.9);
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.04);
        }
        .dark .glass-card {
            background: rgba(15, 23, 42, 0.78);
            border: 1px solid rgba(51, 65, 85, 0.6);
            box-shadow: 0 4px 24px -2px rgba(0, 0, 0, 0.35);
        }
        .glass-card-hover {
            transition: all 0.25s ease;
        }
        .glass-card-hover:hover {
            border-color: rgba(56, 189, 248, 0.4);
            transform: translateY(-2px);
            box-shadow: 0 12px 24px -10px rgba(14, 165, 233, 0.15);
        }
        
        /* Radar pulse animations for GPS */
        @keyframes radarSweep {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .animate-radar-sweep {
            animation: radarSweep 3s linear infinite;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: rgba(226, 232, 240, 0.5); }
        .dark ::-webkit-scrollbar-track { background: rgba(15, 23, 42, 0.5); }
        ::-webkit-scrollbar-thumb { background: rgba(148, 163, 184, 0.8); border-radius: 999px; }
        .dark ::-webkit-scrollbar-thumb { background: rgba(51, 65, 85, 0.8); border-radius: 999px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(100, 116, 139, 1); }
    </style>
</head>
<body class="bg-slate-100 dark:bg-slate-950 text-slate-800 dark:text-slate-100 min-h-screen selection:bg-cyan-500 selection:text-white antialiased transition-colors duration-200">

    <!-- Ambient Glow Backdrops -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
        <div class="absolute -top-40 -left-40 w-96 h-96 bg-cyan-500/10 dark:bg-cyan-600/10 rounded-full blur-[120px]"></div>
        <div class="absolute top-1/3 -right-40 w-96 h-96 bg-indigo-500/10 dark:bg-indigo-600/10 rounded-full blur-[120px]"></div>
    </div>

    <div class="relative z-10 flex flex-col min-h-screen">
        
        {{-- ================= Top Navigation Bar ================= --}}
        <header class="sticky top-0 z-40 bg-white/85 dark:bg-slate-950/85 backdrop-blur-xl border-b border-slate-200 dark:border-slate-800/80 px-4 sm:px-6 lg:px-8 py-3.5 transition-all">
            <div class="max-w-7xl mx-auto flex items-center justify-between gap-4">
                
                <!-- Logo & Brand -->
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-cyan-600 via-sky-500 to-indigo-600 flex items-center justify-center font-black text-white text-base shadow-lg shadow-cyan-500/25 ring-1 ring-white/20 flex-shrink-0">
                        BZ
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="text-sm sm:text-base font-extrabold text-slate-900 dark:text-white tracking-tight leading-none">
                                {{ app()->getLocale() === 'ar' ? 'بوابة المندوب الميدانية' : 'MR Field Portal' }}
                            </h1>
                            <span class="hidden sm:inline-flex px-2 py-0.5 rounded-md text-[10px] font-bold bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 border border-cyan-500/25">
                                BZ-OS
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 flex items-center gap-1.5 font-medium">
                            <i class="fa-regular fa-calendar-check text-sky-500 dark:text-sky-400 text-[11px]"></i>
                            <span>{{ $activeCycle->name ?? (app()->getLocale() === 'ar' ? 'الدورة النشطة' : 'Active Cycle') }}</span>
                            @if($activeCycle)
                                <span class="text-slate-400 dark:text-slate-600">•</span>
                                <span class="text-[11px] text-slate-500 hidden md:inline">{{ $activeCycle->start_date->format('d M') }} — {{ $activeCycle->end_date->format('d M Y') }}</span>
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Right Controls: GPS Status Pill, Theme Toggle, & User -->
                <div class="flex items-center gap-2 sm:gap-3">
                    
                    <!-- Interactive GPS Status & Config Button -->
                    <button type="button" onclick="openGpsConfigModal()" id="top-gps-indicator" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/25 shadow-sm hover:scale-105 active:scale-95 transition cursor-pointer" title="{{ app()->getLocale() === 'ar' ? 'إعدادات وصلاحيات الـ GPS' : 'GPS Geofence Settings & Permissions' }}">
                        <span id="top-gps-pulse" class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        <span id="top-gps-label" class="hidden xs:inline text-[11px] font-bold">
                            {{ app()->getLocale() === 'ar' ? 'GPS نشط' : 'GPS Active' }}
                        </span>
                    </button>

                    <!-- Theme Mode Toggle Button (Light/Dark) -->
                    <button type="button" onclick="toggleTheme()" id="theme-toggle-btn" class="p-2 sm:px-3 sm:py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-900 dark:hover:bg-slate-800 border border-slate-300 dark:border-slate-800 text-slate-700 dark:text-slate-200 transition text-xs font-bold flex items-center gap-1.5 shadow-sm" title="Toggle Light/Dark Theme">
                        <i id="theme-toggle-icon" class="fa-solid fa-sun text-amber-500 text-sm"></i>
                        <span id="theme-toggle-text" class="hidden md:inline text-[11px]">Light Mode</span>
                    </button>

                    <!-- User Profile Pill -->
                    <div class="hidden sm:flex items-center gap-2.5 px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-xs">
                        <div class="w-6 h-6 rounded-full bg-indigo-500/15 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-[11px]">
                            <i class="fa-solid fa-user-doctor"></i>
                        </div>
                        <span class="font-bold text-slate-800 dark:text-slate-200 max-w-[140px] truncate">{{ $user->name ?? 'Medical Rep' }}</span>
                    </div>

                    <!-- Language Switcher -->
                    <a href="{{ route('locale.switch', app()->getLocale() === 'ar' ? 'en' : 'ar') }}" class="p-2 sm:px-2.5 sm:py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-900 dark:hover:bg-slate-800 border border-slate-300 dark:border-slate-800 text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition text-xs font-bold flex items-center gap-1.5" title="Switch Language">
                        <i class="fa-solid fa-language text-sky-500 dark:text-sky-400"></i>
                        <span class="hidden sm:inline">{{ app()->getLocale() === 'ar' ? 'English' : 'العربية' }}</span>
                    </a>

                    <!-- Back to Admin Dashboard -->
                    <a href="{{ route('admin.dashboard') }}" class="p-2 sm:px-3 sm:py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-900 dark:hover:bg-slate-800 border border-slate-300 dark:border-slate-800 text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition text-xs font-bold flex items-center gap-1.5 shadow-sm" title="Admin Overview">
                        <i class="fa-solid fa-arrow-up-right-from-square text-cyan-600 dark:text-cyan-400 text-xs"></i>
                        <span class="hidden md:inline">{{ app()->getLocale() === 'ar' ? 'لوحة التحكم' : 'Admin' }}</span>
                    </a>
                </div>

            </div>
        </header>

        {{-- ================= Main Responsive Canvas ================= --}}
        <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

            {{-- 0. Dynamic Smart GPS Alert Banner (Visible if Denied or Pending) --}}
            <div id="gps-alert-banner" class="hidden transition-all duration-300 rounded-2xl p-4 sm:p-5 shadow-lg border relative overflow-hidden">
                <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                    <div class="flex items-start gap-3.5">
                        <div id="gps-banner-icon-box" class="w-12 h-12 rounded-xl flex items-center justify-center text-xl flex-shrink-0">
                            <i id="gps-banner-icon" class="fa-solid fa-location-crosshairs animate-pulse"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <span id="gps-banner-badge" class="px-2.5 py-0.5 rounded text-[10px] font-black uppercase tracking-wider">
                                    {{ app()->getLocale() === 'ar' ? 'تنبيه الموقع الجغرافي' : 'GPS Compliance' }}
                                </span>
                                <span id="gps-banner-status-text" class="text-xs font-semibold"></span>
                            </div>
                            <h3 id="gps-banner-title" class="text-sm sm:text-base font-bold text-slate-900 dark:text-white mt-1">
                                {{ app()->getLocale() === 'ar' ? 'يلزم تفعيل الـ GPS للتحقق من التواجد في العيادة' : 'High-Accuracy Geofencing Required for Visit Verification' }}
                            </h3>
                            <p id="gps-banner-desc" class="text-xs text-slate-600 dark:text-slate-300 mt-0.5 leading-relaxed max-w-3xl">
                                {{ app()->getLocale() === 'ar' 
                                    ? 'يتطلب نظام BlueZone CRM التحقق من إحداثيات موقعك الجغرافي داخل النطاق المعتمد للعيادة لاحتساب نقاط الدورة والتأكد من الزيارات الميدانية.' 
                                    : 'BlueZone CRM verifies your presence within the clinic geofence perimeter to validate visits and earn full cycle points.' }}
                            </p>
                        </div>
                    </div>

                    <!-- Action Controls -->
                    <div class="flex items-center gap-2.5 w-full md:w-auto flex-wrap sm:flex-nowrap justify-end">
                        <button type="button" onclick="handleGpsBannerAction('allow')" id="gps-banner-allow-btn" class="flex-1 md:flex-none px-4 py-2.5 rounded-xl bg-gradient-to-r from-cyan-600 to-sky-600 hover:from-cyan-500 hover:to-sky-500 text-white font-bold text-xs active:scale-95 transition shadow-md shadow-cyan-600/20 flex items-center justify-center gap-1.5 whitespace-nowrap">
                            <i class="fa-solid fa-location-arrow"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'السماح بالـ GPS الآن' : 'Allow GPS Access' }}</span>
                        </button>
                        
                        <button type="button" onclick="openGpsConfigModal()" class="px-3.5 py-2.5 rounded-xl bg-slate-200 hover:bg-slate-300 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs transition border border-slate-300 dark:border-slate-700 flex items-center justify-center gap-1.5 whitespace-nowrap">
                            <i class="fa-solid fa-sliders text-sky-500 dark:text-sky-400"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'الإعدادات' : 'Configure' }}</span>
                        </button>

                        <button type="button" onclick="dismissGpsBanner()" class="p-2.5 rounded-xl hover:bg-slate-200 dark:hover:bg-slate-800 text-slate-400 hover:text-slate-700 dark:hover:text-white transition" title="Dismiss">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- 1. Hero Performance & Milestone Banner --}}
            <div class="glass-card rounded-2xl p-5 sm:p-6 shadow-xl relative overflow-hidden">
                <!-- Background Accent Glow -->
                <div class="absolute -right-20 -bottom-20 w-64 h-64 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
                    
                    <!-- Rep Greeting & Milestone (5 Cols on Desktop/Tablet) -->
                    <div class="lg:col-span-5 space-y-3">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-black bg-cyan-500/15 text-cyan-600 dark:text-cyan-400 border border-cyan-500/30">
                                {{ app()->getLocale() === 'ar' ? 'الميدان الطبي' : 'Field Operations' }}
                            </span>
                            <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">
                                {{ now()->format('l, d F Y') }}
                            </span>
                        </div>

                        <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                            {{ app()->getLocale() === 'ar' ? 'أهلاً بك، ' : 'Welcome, ' }} {{ $user->name ?? 'Dr. Rep' }}
                        </h2>

                        <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                            {{ app()->getLocale() === 'ar' 
                                ? 'لوحة متابعة الزيارات الميدانية، التحقق من النطاق الجغرافي للعيادات، وتحقيق مستهدف النقاط للدورة الحالية.' 
                                : 'Live field execution, GPS-verified clinic check-ins, and cycle frequency compliance.' }}
                        </p>

                        <!-- Points Target Milestone Progress -->
                        <div class="pt-2">
                            <div class="flex items-center justify-between text-xs font-bold mb-1.5">
                                <span class="text-slate-600 dark:text-slate-400 flex items-center gap-1.5">
                                    <i class="fa-solid fa-trophy text-amber-500 dark:text-amber-400"></i>
                                    {{ app()->getLocale() === 'ar' ? 'التقدم في نقاط الدورة' : 'Cycle Target Points' }}
                                </span>
                                <span class="text-cyan-600 dark:text-cyan-400 font-black">
                                    {{ $snapshot->achieved_points ?? 0 }} / {{ $snapshot->target_points ?? 0 }} pts
                                    ({{ $snapshot->points_achieved_pct ?? 0 }}%)
                                </span>
                            </div>
                            <div class="w-full bg-slate-200 dark:bg-slate-800/80 rounded-full h-2.5 overflow-hidden p-0.5 border border-slate-300 dark:border-slate-700/60">
                                <div class="bg-gradient-to-r from-cyan-500 via-sky-400 to-indigo-500 h-full rounded-full transition-all duration-500" style="width: {{ min(100, $snapshot->points_achieved_pct ?? 0) }}%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- 4 High-Impact KPI Stat Cards (7 Cols on Desktop/Tablet) -->
                    <div class="lg:col-span-7 grid grid-cols-2 sm:grid-cols-4 gap-3">
                        
                        <!-- KPI 1: Coverage Rate -->
                        <div class="p-4 rounded-xl bg-white/90 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800/80 text-center flex flex-col justify-between hover:border-slate-300 dark:hover:border-slate-700 transition shadow-sm">
                            <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-sky-500/10 text-sky-600 dark:text-sky-400 mx-auto mb-2">
                                <i class="fa-solid fa-chart-pie text-sm"></i>
                            </div>
                            <div class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                                {{ $snapshot->coverage_rate_pct ?? 0 }}%
                            </div>
                            <div class="text-[11px] text-slate-600 dark:text-slate-400 font-semibold mt-1">
                                {{ app()->getLocale() === 'ar' ? 'نسبة التغطية' : 'Coverage Rate' }}
                            </div>
                            <div class="text-[10px] text-slate-500 mt-0.5">
                                {{ $snapshot->unique_contacts_visited ?? 0 }}/{{ $snapshot->total_assigned_contacts ?? 0 }} {{ app()->getLocale() === 'ar' ? 'أطباء' : 'doctors' }}
                            </div>
                        </div>

                        <!-- KPI 2: Completed Visits -->
                        <div class="p-4 rounded-xl bg-white/90 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800/80 text-center flex flex-col justify-between hover:border-slate-300 dark:hover:border-slate-700 transition shadow-sm">
                            <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 mx-auto mb-2">
                                <i class="fa-solid fa-calendar-check text-sm"></i>
                            </div>
                            <div class="text-xl sm:text-2xl font-black text-emerald-600 dark:text-emerald-400 tracking-tight">
                                {{ $snapshot->visits_done ?? 0 }} <span class="text-xs text-slate-400 dark:text-slate-500 font-normal">/ {{ $snapshot->planned_visits ?? 0 }}</span>
                            </div>
                            <div class="text-[11px] text-slate-600 dark:text-slate-400 font-semibold mt-1">
                                {{ app()->getLocale() === 'ar' ? 'الزيارات المنفذة' : 'Visits Done' }}
                            </div>
                            <div class="text-[10px] text-slate-500 mt-0.5">
                                {{ $snapshot->visit_compliance_pct ?? 0 }}% {{ app()->getLocale() === 'ar' ? 'التزام' : 'compliance' }}
                            </div>
                        </div>

                        <!-- KPI 3: Achieved Points -->
                        <div class="p-4 rounded-xl bg-white/90 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800/80 text-center flex flex-col justify-between hover:border-slate-300 dark:hover:border-slate-700 transition shadow-sm">
                            <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 mx-auto mb-2">
                                <i class="fa-solid fa-ranking-star text-sm"></i>
                            </div>
                            <div class="text-xl sm:text-2xl font-black text-indigo-600 dark:text-indigo-400 tracking-tight">
                                {{ $snapshot->achieved_points ?? 0 }}
                            </div>
                            <div class="text-[11px] text-slate-600 dark:text-slate-400 font-semibold mt-1">
                                {{ app()->getLocale() === 'ar' ? 'النقاط المكتسبة' : 'Points Earned' }}
                            </div>
                            <div class="text-[10px] text-slate-500 mt-0.5">
                                {{ app()->getLocale() === 'ar' ? 'محتسبة بنظام الفئات' : 'Capped tier points' }}
                            </div>
                        </div>

                        <!-- KPI 4: GPS Accuracy -->
                        <div class="p-4 rounded-xl bg-white/90 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800/80 text-center flex flex-col justify-between hover:border-slate-300 dark:hover:border-slate-700 transition shadow-sm">
                            <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 mx-auto mb-2">
                                <i class="fa-solid fa-location-crosshairs text-sm"></i>
                            </div>
                            <div class="text-xl sm:text-2xl font-black text-cyan-600 dark:text-cyan-400 tracking-tight">
                                {{ $snapshot->gps_accuracy_pct ?? 0 }}%
                            </div>
                            <div class="text-[11px] text-slate-600 dark:text-slate-400 font-semibold mt-1">
                                {{ app()->getLocale() === 'ar' ? 'دقة الـ GPS' : 'GPS Accuracy' }}
                            </div>
                            <div class="text-[10px] text-slate-500 mt-0.5">
                                {{ $snapshot->verified_visits ?? 0 }} {{ app()->getLocale() === 'ar' ? 'مؤكدة' : 'verified' }}
                            </div>
                        </div>

                    </div>

                </div>
            </div>

            {{-- 2. Active Ongoing Visit Floating Banner (If Check-In Active) --}}
            @if($activeOngoingVisit)
                <div class="relative overflow-hidden p-5 rounded-2xl bg-gradient-to-r from-amber-500/20 via-orange-500/15 to-slate-100 dark:to-slate-900 border border-amber-500/40 shadow-xl shadow-amber-500/5 backdrop-blur-md">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div class="flex items-start gap-3.5">
                            <div class="w-12 h-12 rounded-xl bg-amber-500/20 border border-amber-500/40 text-amber-500 dark:text-amber-400 flex items-center justify-center text-xl flex-shrink-0">
                                <span class="animate-pulse"><i class="fa-solid fa-stethoscope"></i></span>
                            </div>
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded text-[10px] font-black bg-amber-500 text-slate-950 uppercase tracking-wider">
                                        ⚡ {{ app()->getLocale() === 'ar' ? 'زيارة جارية الآن' : 'Visit In Progress' }}
                                    </span>
                                    <span class="text-xs text-amber-600 dark:text-amber-300 font-semibold">
                                        {{ app()->getLocale() === 'ar' ? 'تم تسجيل الوصول' : 'Checked in' }} {{ $activeOngoingVisit->checkin_at?->diffForHumans() }}
                                    </span>
                                </div>
                                <h3 class="text-lg font-bold text-slate-900 dark:text-white mt-1">
                                    {{ $activeOngoingVisit->contact?->name }}
                                </h3>
                                <p class="text-xs text-slate-600 dark:text-slate-300">
                                    {{ $activeOngoingVisit->contact?->hospital_clinic_name }} • {{ $activeOngoingVisit->contact?->specialty?->name }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 w-full sm:w-auto">
                            <button onclick="openCheckoutModal({{ $activeOngoingVisit->id }}, '{{ addslashes($activeOngoingVisit->contact?->name ?? 'Doctor') }}')" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-400 hover:to-orange-400 text-slate-950 font-black text-sm active:scale-95 transition shadow-lg shadow-amber-500/20 flex items-center justify-center gap-2">
                                <i class="fa-solid fa-arrow-right-from-bracket"></i>
                                <span>{{ app()->getLocale() === 'ar' ? 'تسجيل الانصراف وإنهاء الزيارة' : 'Check Out & Complete' }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            {{-- 3. Responsive Multi-Column Layout (Tablet & Desktop Split) --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                
                {{-- LEFT PRIMARY COLUMN (8 Cols): Agenda & Doctor Hub --}}
                <div class="lg:col-span-8 space-y-6">

                    {{-- Today's Agenda Section --}}
                    <div class="glass-card rounded-2xl p-5 sm:p-6 space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800/80 pb-3.5">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-sky-500/10 text-sky-600 dark:text-sky-400 flex items-center justify-center text-sm">
                                    <i class="fa-solid fa-list-check"></i>
                                </div>
                                <div>
                                    <h3 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white leading-tight">
                                        {{ app()->getLocale() === 'ar' ? 'جدول زيارات اليوم' : "Today's Field Agenda" }}
                                    </h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ count($todayVisits) }} {{ app()->getLocale() === 'ar' ? 'زيارات مجدولة' : 'scheduled appointments' }}</p>
                                </div>
                            </div>

                            <button onclick="openScheduleModal()" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold transition flex items-center gap-1.5 border border-slate-300 dark:border-slate-700/60 shadow-sm">
                                <i class="fa-solid fa-plus text-sky-500 dark:text-sky-400"></i>
                                <span>{{ app()->getLocale() === 'ar' ? 'جدولة زيارة' : 'Schedule Visit' }}</span>
                            </button>
                        </div>

                        <div class="space-y-3">
                            @forelse($todayVisits as $sv)
                                <div class="p-4 rounded-xl bg-white/90 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800/90 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 hover:border-slate-300 dark:hover:border-slate-700 transition shadow-sm">
                                    <div class="flex items-start gap-3.5">
                                        <div class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700/80 flex flex-col items-center justify-center text-sky-600 dark:text-sky-400 flex-shrink-0">
                                            <span class="text-xs font-black">{{ $sv->scheduled_at->format('H:i') }}</span>
                                            <span class="text-[9px] text-slate-500 dark:text-slate-400 uppercase font-bold">{{ $sv->scheduled_at->format('A') }}</span>
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <h4 class="text-sm font-bold text-slate-900 dark:text-white">{{ $sv->contact?->name }}</h4>
                                                @if($sv->contact?->classification)
                                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-black {{ $sv->contact->classification->code === 'A+' ? 'bg-amber-500/20 text-amber-700 dark:text-amber-300 border border-amber-500/30' : 'bg-sky-500/20 text-sky-700 dark:text-sky-300 border border-sky-500/30' }}">
                                                        Class {{ $sv->contact->classification->code }}
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                                {{ $sv->contact?->hospital_clinic_name }} • {{ $sv->contact?->specialty?->name }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                                        @if($sv->status === 'completed')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                                <i class="fa-solid fa-check-double"></i>
                                                {{ app()->getLocale() === 'ar' ? 'تمت الزيارة' : 'Completed' }}
                                            </span>
                                        @else
                                            @if($sv->contact?->latitude && $sv->contact?->longitude)
                                                <a href="https://www.google.com/maps?q={{ $sv->contact->latitude }},{{ $sv->contact->longitude }}" target="_blank" class="p-2 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition text-xs border border-slate-200 dark:border-slate-700/60" title="Directions">
                                                    <i class="fa-solid fa-diamond-turn-right text-sky-500 dark:text-sky-400"></i>
                                                </a>
                                            @endif
                                            <button onclick="triggerCheckIn({{ $sv->contact_id }}, '{{ addslashes($sv->contact?->name ?? 'Doctor') }}', {{ $sv->assignment_id }}, {{ $sv->id }})" class="px-4 py-2 rounded-xl bg-gradient-to-r from-sky-600 to-indigo-600 hover:from-sky-500 hover:to-indigo-500 text-white font-bold text-xs active:scale-95 transition shadow-md shadow-sky-600/20 flex items-center gap-1.5">
                                                <i class="fa-solid fa-location-dot"></i>
                                                <span>{{ app()->getLocale() === 'ar' ? 'تسجيل وصول' : 'Check In' }}</span>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="p-8 rounded-xl bg-slate-50 dark:bg-slate-900/40 border border-slate-200 dark:border-slate-800/80 text-center">
                                    <div class="w-12 h-12 rounded-full bg-slate-200 dark:bg-slate-800 text-slate-500 flex items-center justify-center mx-auto mb-2 text-xl">
                                        <i class="fa-regular fa-calendar-xmark"></i>
                                    </div>
                                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">
                                        {{ app()->getLocale() === 'ar' ? 'لا توجد زيارات مجدولة في جدول اليوم.' : 'No scheduled visits on your agenda for today.' }}
                                    </p>
                                    <p class="text-xs text-slate-500 mt-1">
                                        {{ app()->getLocale() === 'ar' ? 'يمكنك اختيار أي طبيب من المحفظة أدناه لتسجيل الزيارة فوراً.' : 'Select any assigned doctor below to start a field check-in immediately.' }}
                                    </p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Doctor Portfolio Hub Section (Tablet Grid) --}}
                    <div class="glass-card rounded-2xl p-5 sm:p-6 space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200 dark:border-slate-800/80 pb-4">
                            <div>
                                <h3 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                    <i class="fa-solid fa-user-doctor text-sky-500 dark:text-sky-400"></i>
                                    <span>{{ app()->getLocale() === 'ar' ? 'محفظة الأطباء المخصصة' : 'Assigned Doctor Portfolio' }}</span>
                                    <span class="px-2 py-0.5 rounded-full text-xs font-black bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-700">
                                        {{ count($allAssignments) }}
                                    </span>
                                </h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ app()->getLocale() === 'ar' ? 'قائمة الأطباء المكلف بزيارتهم خلال الدورة الحالية مع نسب الإنجاز' : 'Target frequency progress and one-tap GPS check-in.' }}</p>
                            </div>

                            <!-- Fast Search Box -->
                            <div class="relative min-w-[240px]">
                                <i class="fa-solid fa-magnifying-glass absolute left-3.5 rtl:left-auto rtl:right-3.5 top-1/2 -translate-y-1/2 text-xs text-slate-400 dark:text-slate-500"></i>
                                <input type="text" id="doctor-search-input" onkeyup="filterDoctors()" placeholder="{{ app()->getLocale() === 'ar' ? 'بحث باسم الطبيب، المركز، الفئة...' : 'Filter doctors, clinic, specialty...' }}" class="w-full pl-9 rtl:pl-3 rtl:pr-9 pr-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-800 text-xs text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 focus:outline-none focus:border-sky-500 transition shadow-sm">
                            </div>
                        </div>

                        <!-- Filter Chips -->
                        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs">
                            <button onclick="filterByClass('all')" class="chip-filter active px-3 py-1.5 rounded-lg bg-sky-600 text-white font-bold transition shadow-sm">
                                {{ app()->getLocale() === 'ar' ? 'الكل' : 'All' }} ({{ count($allAssignments) }})
                            </button>
                            <button onclick="filterByClass('A+')" class="chip-filter px-3 py-1.5 rounded-lg bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-bold transition border border-slate-300 dark:border-slate-700/60">
                                Class A+
                            </button>
                            <button onclick="filterByClass('A')" class="chip-filter px-3 py-1.5 rounded-lg bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-bold transition border border-slate-300 dark:border-slate-700/60">
                                Class A
                            </button>
                            <button onclick="filterByClass('B')" class="chip-filter px-3 py-1.5 rounded-lg bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-bold transition border border-slate-300 dark:border-slate-700/60">
                                Class B
                            </button>
                            <button onclick="filterByClass('C')" class="chip-filter px-3 py-1.5 rounded-lg bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-bold transition border border-slate-300 dark:border-slate-700/60">
                                Class C
                            </button>
                        </div>

                        <!-- Doctor Cards Grid: 1 col on mobile, 2 cols on tablet & desktop -->
                        <div id="doctors-container" class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                            @foreach($allAssignments as $item)
                                @php
                                    $c = $item->contact;
                                    $cl = $c?->classification;
                                    $req = $cl ? (int)$cl->required_visits : (int)$item->target_visits;
                                    $done = (int)$item->visits_done;
                                    $pct = $req > 0 ? min(100, round(($done / $req) * 100)) : 0;
                                @endphp
                                <div class="doctor-card p-4 rounded-xl bg-white/90 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800/80 hover:border-slate-300 dark:hover:border-slate-700 transition flex flex-col justify-between shadow-sm"
                                     data-name="{{ strtolower($c?->name ?? '') }}"
                                     data-clinic="{{ strtolower($c?->hospital_clinic_name ?? '') }}"
                                     data-specialty="{{ strtolower($c?->specialty?->name ?? '') }}"
                                     data-class="{{ $cl?->code ?? 'C' }}">
                                    
                                    <div>
                                        <div class="flex items-start justify-between gap-2">
                                            <div>
                                                <h4 class="text-sm font-bold text-slate-900 dark:text-white hover:text-sky-600 dark:hover:text-sky-400 transition">
                                                    {{ $c?->name }}
                                                </h4>
                                                <p class="text-xs text-slate-500 dark:text-slate-400 truncate mt-0.5">
                                                    {{ $c?->hospital_clinic_name }}
                                                </p>
                                            </div>

                                            @if($cl)
                                                <span class="px-2 py-0.5 rounded text-[10px] font-black {{ $cl->code === 'A+' ? 'bg-amber-500/20 text-amber-700 dark:text-amber-300 border border-amber-500/30' : ($cl->code === 'A' ? 'bg-sky-500/20 text-sky-700 dark:text-sky-300 border border-sky-500/30' : 'bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-700') }}">
                                                    {{ $cl->code }}
                                                </span>
                                            @endif
                                        </div>

                                        <div class="flex items-center gap-2 text-[11px] text-slate-500 dark:text-slate-400 mt-2">
                                            <span class="px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800/80 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700/50">
                                                {{ $c?->specialty?->name ?? 'General' }}
                                            </span>
                                            @if($c?->city)
                                                <span>•</span>
                                                <span>{{ $c->city->name }}</span>
                                            @endif
                                        </div>

                                        <!-- Progress Bar -->
                                        <div class="mt-3 pt-3 border-t border-slate-200 dark:border-slate-800/60">
                                            <div class="flex items-center justify-between text-[11px] font-semibold mb-1">
                                                <span class="text-slate-500 dark:text-slate-400">{{ app()->getLocale() === 'ar' ? 'الزيارات:' : 'Visits:' }} <strong class="text-slate-900 dark:text-white">{{ $done }}/{{ $req }}</strong></span>
                                                <span class="text-sky-600 dark:text-sky-400 font-bold">{{ $item->achieved_points }}/{{ $item->target_points }} pts</span>
                                            </div>
                                            <div class="w-full bg-slate-200 dark:bg-slate-800 rounded-full h-1.5 overflow-hidden">
                                                <div class="h-full rounded-full {{ $pct >= 100 ? 'bg-emerald-500' : ($pct > 0 ? 'bg-sky-500' : 'bg-rose-500') }}" style="width: {{ $pct }}%"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Actions Row -->
                                    <div class="flex items-center gap-2 mt-4 pt-3 border-t border-slate-200 dark:border-slate-800/80">
                                        @if($c?->latitude && $c?->longitude)
                                            <a href="https://www.google.com/maps?q={{ $c->latitude }},{{ $c->longitude }}" target="_blank" class="p-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition text-xs border border-slate-200 dark:border-slate-700/50 shadow-sm" title="Open Google Maps">
                                                <i class="fa-solid fa-location-arrow text-sky-500 dark:text-sky-400"></i>
                                            </a>
                                        @endif

                                        <button onclick="openScheduleModal({{ $item->id }}, '{{ addslashes($c?->name ?? 'Doctor') }}')" class="p-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition text-xs border border-slate-200 dark:border-slate-700/50 shadow-sm" title="Schedule appointment">
                                            <i class="fa-regular fa-calendar-plus"></i>
                                        </button>

                                        <button onclick="triggerCheckIn({{ $c->id }}, '{{ addslashes($c?->name ?? 'Doctor') }}', {{ $item->id }})" class="flex-1 py-2.5 px-3 rounded-xl bg-gradient-to-r from-cyan-600 to-sky-600 hover:from-cyan-500 hover:to-sky-500 text-white font-bold text-xs transition active:scale-95 shadow-md shadow-cyan-600/20 flex items-center justify-center gap-1.5">
                                            <i class="fa-solid fa-location-dot"></i>
                                            <span>{{ app()->getLocale() === 'ar' ? 'تسجيل زيارة' : 'Visit Now' }}</span>
                                        </button>
                                    </div>

                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>

                {{-- RIGHT SIDEBAR COLUMN (4 Cols): At Risk, GPS Card & Scorecard --}}
                <div class="lg:col-span-4 space-y-6">

                    {{-- At-Risk Urgent Visits Alert Widget --}}
                    @if(count($atRiskAssignments) > 0)
                        <div class="glass-card rounded-2xl p-5 border-rose-300 dark:border-rose-900/40 bg-gradient-to-b from-rose-50 dark:from-rose-950/25 to-white dark:to-slate-900/80 space-y-3.5">
                            <div class="flex items-center justify-between border-b border-rose-200 dark:border-rose-900/40 pb-3">
                                <h3 class="text-xs font-black text-rose-600 dark:text-rose-400 uppercase tracking-wider flex items-center gap-1.5">
                                    <i class="fa-solid fa-triangle-exclamation animate-pulse"></i>
                                    <span>{{ app()->getLocale() === 'ar' ? 'أطباء بحاجة لزيارة عاجلة' : 'Doctors Behind Frequency' }}</span>
                                </h3>
                                <span class="px-2 py-0.5 rounded text-[10px] font-black bg-rose-500/20 text-rose-700 dark:text-rose-300 border border-rose-500/30">
                                    {{ count($atRiskAssignments) }}
                                </span>
                            </div>

                            <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                                {{ app()->getLocale() === 'ar' 
                                    ? 'هؤلاء الأطباء متأخرون عن التردد الإلزامي المطلوب قبل إغلاق الدورة. يرجى إتمام زياراتهم لضمان استحقاق كامل النقاط.' 
                                    : 'These doctors risk falling short of cycle target visits. Schedule visits soon to protect point yield.' }}
                            </p>

                            <div class="space-y-2.5 max-h-72 overflow-y-auto pr-1">
                                @foreach($atRiskAssignments as $assign)
                                    <div class="p-3 rounded-xl bg-white/90 dark:bg-slate-900/90 border border-rose-200 dark:border-rose-900/30 flex items-center justify-between gap-2 shadow-sm">
                                        <div class="min-w-0">
                                            <div class="font-bold text-xs text-slate-900 dark:text-white truncate">{{ $assign->contact?->name }}</div>
                                            <div class="text-[10px] text-rose-600 dark:text-rose-400 font-semibold mt-0.5">
                                                {{ $assign->visits_done }}/{{ $assign->target_visits }} {{ app()->getLocale() === 'ar' ? 'زيارات مكتملة' : 'done' }}
                                            </div>
                                        </div>

                                        <button onclick="triggerCheckIn({{ $assign->contact_id }}, '{{ addslashes($assign->contact?->name ?? 'Doctor') }}', {{ $assign->id }})" class="px-3 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-500 active:scale-95 text-white font-bold text-xs transition flex-shrink-0">
                                            {{ app()->getLocale() === 'ar' ? 'زيارة' : 'Visit' }}
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Live GPS Geofence Sensor Widget --}}
                    <div class="glass-card rounded-2xl p-5 space-y-3.5">
                        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800/80 pb-3">
                            <h3 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider flex items-center gap-2">
                                <i class="fa-solid fa-satellite text-sky-500 dark:text-sky-400"></i>
                                <span>{{ app()->getLocale() === 'ar' ? 'حساس الـ GPS اللحظي' : 'Live GPS Geofence Sensor' }}</span>
                            </h3>
                            <div class="flex items-center gap-2">
                                <button type="button" onclick="openGpsConfigModal()" class="text-xs text-slate-500 hover:text-sky-600 dark:hover:text-sky-400 font-bold flex items-center gap-1 transition" title="{{ app()->getLocale() === 'ar' ? 'إعدادات وصلاحيات الـ GPS' : 'GPS Settings' }}">
                                    <i class="fa-solid fa-gear"></i>
                                    <span class="hidden sm:inline">{{ app()->getLocale() === 'ar' ? 'إعدادات' : 'Config' }}</span>
                                </button>
                                <button type="button" onclick="detectDeviceLocation(true)" class="text-xs text-sky-600 dark:text-sky-400 hover:text-sky-500 dark:hover:text-sky-300 font-bold transition" title="Refresh GPS">
                                    <i class="fa-solid fa-arrows-rotate"></i>
                                </button>
                            </div>
                        </div>

                        <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-900/90 border border-slate-200 dark:border-slate-800 text-xs space-y-2 font-mono">
                            <div class="flex justify-between text-slate-600 dark:text-slate-400">
                                <span>Status:</span>
                                <span id="sensor-status" class="text-slate-500 font-bold">Checking...</span>
                            </div>
                            <div class="flex justify-between text-slate-600 dark:text-slate-400">
                                <span>Latitude:</span>
                                <span id="sensor-lat" class="text-slate-900 dark:text-white font-bold">—</span>
                            </div>
                            <div class="flex justify-between text-slate-600 dark:text-slate-400">
                                <span>Longitude:</span>
                                <span id="sensor-lng" class="text-slate-900 dark:text-white font-bold">—</span>
                            </div>
                            <div class="flex justify-between text-slate-600 dark:text-slate-400">
                                <span>Accuracy:</span>
                                <span id="sensor-acc" class="text-sky-600 dark:text-sky-400 font-bold">—</span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between text-[11px] pt-1 border-t border-slate-200/60 dark:border-slate-800/60">
                            <span class="text-slate-500 dark:text-slate-400">{{ app()->getLocale() === 'ar' ? 'نصف قطر السياج:' : 'Geofence Radius:' }}</span>
                            <span class="font-bold text-sky-600 dark:text-sky-400">150 meters</span>
                        </div>

                        <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">
                            {{ app()->getLocale() === 'ar' 
                                ? 'يتم التحقق من تطابق إحداثيات موقعك مع إحداثيات العيادة في خوادم النظام قبل تأكيد الزيارة لمنع المواقع الوهمية.' 
                                : 'Coordinates are signed and verified server-side against clinic geofence parameters.' }}
                        </p>
                    </div>

                    {{-- Quick Operations Card --}}
                    <div class="glass-card rounded-2xl p-5 space-y-3">
                        <h3 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                            {{ app()->getLocale() === 'ar' ? 'روابط سريعة' : 'Quick Operations' }}
                        </h3>

                        <div class="space-y-2 text-xs">
                            <a href="{{ route('admin.mr.visits.index') }}" class="w-full p-3 rounded-xl bg-white/80 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700 flex items-center justify-between text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition font-semibold shadow-sm">
                                <span class="flex items-center gap-2">
                                    <i class="fa-solid fa-clock-rotate-left text-sky-500 dark:text-sky-400"></i>
                                    <span>{{ app()->getLocale() === 'ar' ? 'سجل زياراتي السابقة' : 'My Completed Visit Logs' }}</span>
                                </span>
                                <i class="fa-solid fa-chevron-right rtl:rotate-180 text-xs text-slate-400 dark:text-slate-600"></i>
                            </a>

                            <a href="{{ route('admin.mr.reports.performance') }}" class="w-full p-3 rounded-xl bg-white/80 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700 flex items-center justify-between text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition font-semibold shadow-sm">
                                <span class="flex items-center gap-2">
                                    <i class="fa-solid fa-chart-line text-emerald-600 dark:text-emerald-400"></i>
                                    <span>{{ app()->getLocale() === 'ar' ? 'بطاقة تقييم الأداء والمكافأة' : 'Performance Scorecard' }}</span>
                                </span>
                                <i class="fa-solid fa-chevron-right rtl:rotate-180 text-xs text-slate-400 dark:text-slate-600"></i>
                            </a>
                        </div>
                    </div>

                </div>

            </div>

        </main>

        {{-- Toast Notification Box --}}
        <div id="toast" class="fixed bottom-6 right-6 rtl:right-auto rtl:left-6 z-50 transform transition-all duration-300 translate-y-24 opacity-0 pointer-events-none p-4 rounded-xl shadow-2xl text-xs font-bold flex items-center gap-2.5 max-w-sm">
            <i id="toast-icon" class="text-base"></i>
            <span id="toast-message"></span>
        </div>

    </div>

    {{-- ================= Professional GPS Configuration & Permission Modal ================= --}}
    <div id="gps-config-modal" class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-md hidden flex items-center justify-center p-4">
        <div class="glass-card bg-white dark:bg-slate-900 rounded-3xl w-full max-w-lg p-6 sm:p-8 space-y-6 shadow-2xl border border-slate-200 dark:border-slate-700/80 relative max-h-[90vh] overflow-y-auto">
            
            <!-- Modal Header -->
            <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-satellite-dish"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-900 dark:text-white text-base sm:text-lg tracking-tight">
                            {{ app()->getLocale() === 'ar' ? 'إعدادات وصلاحيات الـ GPS الجغرافي' : 'GPS Geofence & Location Control' }}
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            {{ app()->getLocale() === 'ar' ? 'التحقق من التواجد الميداني داخل محيط العيادات المعتمد' : 'Field location verification & compliance audit' }}
                        </p>
                    </div>
                </div>
                <button type="button" onclick="closeGpsConfigModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-white transition p-2 rounded-xl">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Visual Radar Status Display -->
            <div class="p-6 rounded-2xl bg-gradient-to-b from-slate-100 to-white dark:from-slate-800/60 dark:to-slate-900/90 border border-slate-200 dark:border-slate-800 text-center relative overflow-hidden">
                <!-- Concentric Radar Rings -->
                <div class="relative w-28 h-28 mx-auto flex items-center justify-center">
                    <div class="absolute inset-0 rounded-full border border-sky-400/20 dark:border-sky-400/30"></div>
                    <div class="absolute inset-2 rounded-full border border-sky-400/30 dark:border-sky-400/40"></div>
                    <div class="absolute inset-6 rounded-full border border-sky-400/40 dark:border-sky-400/50"></div>
                    
                    <!-- Radar Sweeper -->
                    <div id="modal-radar-sweep" class="absolute inset-0 rounded-full animate-radar-sweep origin-center pointer-events-none opacity-40" style="background: conic-gradient(from 0deg, transparent 0deg, transparent 270deg, rgba(14, 165, 233, 0.4) 360deg);"></div>
                    
                    <!-- Center Icon Indicator -->
                    <div id="modal-radar-center" class="w-12 h-12 rounded-full bg-slate-900 text-white shadow-xl flex items-center justify-center text-lg z-10 border-2 border-white dark:border-slate-700">
                        <i id="modal-radar-icon" class="fa-solid fa-location-crosshairs text-sky-400"></i>
                    </div>
                </div>

                <!-- Status Badge & Telemetry Text -->
                <div class="mt-4">
                    <span id="modal-gps-badge" class="px-3 py-1 rounded-full text-xs font-black inline-flex items-center gap-1.5 bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-700">
                        <span id="modal-gps-dot" class="w-2 h-2 rounded-full bg-slate-400"></span>
                        <span id="modal-gps-text">Checking Status...</span>
                    </span>
                    <p id="modal-gps-subtext" class="text-xs text-slate-500 dark:text-slate-400 mt-2 font-medium">
                        {{ app()->getLocale() === 'ar' ? 'جاري الاستعلام عن صلاحيات المتصفح...' : 'Querying device sensor status...' }}
                    </p>
                </div>

                <!-- Coordinates Grid (If active) -->
                <div id="modal-coords-box" class="hidden mt-4 pt-3 border-t border-slate-200 dark:border-slate-800/80 grid grid-cols-3 gap-2 text-left rtl:text-right font-mono text-[11px]">
                    <div class="p-2 rounded-lg bg-slate-200/60 dark:bg-slate-950/60">
                        <div class="text-[9px] text-slate-400 uppercase">Latitude</div>
                        <div id="modal-lat-val" class="font-bold text-slate-800 dark:text-white truncate">—</div>
                    </div>
                    <div class="p-2 rounded-lg bg-slate-200/60 dark:bg-slate-950/60">
                        <div class="text-[9px] text-slate-400 uppercase">Longitude</div>
                        <div id="modal-lng-val" class="font-bold text-slate-800 dark:text-white truncate">—</div>
                    </div>
                    <div class="p-2 rounded-lg bg-slate-200/60 dark:bg-slate-950/60">
                        <div class="text-[9px] text-slate-400 uppercase">Accuracy</div>
                        <div id="modal-acc-val" class="font-bold text-emerald-600 dark:text-emerald-400 truncate">—</div>
                    </div>
                </div>
            </div>

            <!-- Why BlueZone Requires GPS (Clinical Compliance Spec) -->
            <div class="space-y-3">
                <h4 class="text-xs font-black text-slate-400 uppercase tracking-wider">
                    {{ app()->getLocale() === 'ar' ? 'لماذا يشترط النظام تفعيل الـ GPS؟' : 'Why Is GPS Location Required?' }}
                </h4>
                
                <div class="space-y-2 text-xs">
                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/70 border border-slate-200 dark:border-slate-800 flex items-start gap-3">
                        <div class="w-6 h-6 rounded-lg bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 flex items-center justify-center text-xs flex-shrink-0 mt-0.5 font-bold">1</div>
                        <div>
                            <span class="font-bold text-slate-900 dark:text-white">{{ app()->getLocale() === 'ar' ? 'التحقق التلقائي من السياج الجغرافي:' : 'Automatic Clinic Geofence Match:' }}</span>
                            <span class="text-slate-600 dark:text-slate-400">{{ app()->getLocale() === 'ar' ? 'يقيس النظام بعدك عن العيادة (ضمن نطاق 150م) لتوثيق الزيارة.' : 'Measures distance against clinic coordinates (150m radius) to authenticate presence.' }}</span>
                        </div>
                    </div>

                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/70 border border-slate-200 dark:border-slate-800 flex items-start gap-3">
                        <div class="w-6 h-6 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs flex-shrink-0 mt-0.5 font-bold">2</div>
                        <div>
                            <span class="font-bold text-slate-900 dark:text-white">{{ app()->getLocale() === 'ar' ? 'حساب كامل نقاط الدورة ومكافأة الأداء:' : 'Full Cycle Points & Reward Yield:' }}</span>
                            <span class="text-slate-600 dark:text-slate-400">{{ app()->getLocale() === 'ar' ? 'الزيارات المؤكدة بالـ GPS تمنحك 100% من نقاط الفئة (A+/A/B/C) بدون خصومات.' : 'GPS-verified visits unlock 100% tier points on your performance scorecard.' }}</span>
                        </div>
                    </div>

                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/70 border border-slate-200 dark:border-slate-800 flex items-start gap-3">
                        <div class="w-6 h-6 rounded-lg bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xs flex-shrink-0 mt-0.5 font-bold">3</div>
                        <div>
                            <span class="font-bold text-slate-900 dark:text-white">{{ app()->getLocale() === 'ar' ? 'وضع عدم التفعيل (الرفض/Deny):' : 'Fallback / Denied Mode:' }}</span>
                            <span class="text-slate-600 dark:text-slate-400">{{ app()->getLocale() === 'ar' ? 'في حال الرفض يمكنك تسجيل الزيارة يدوياً، ولكن سيتم وسمها كـ "غير مؤكدة" وتتطلب موافقة المشرف.' : 'If denied, visits are recorded as "Flagged / Unverified" and require supervisor review.' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Troubleshooting Instructions (If permission denied on tablet/browser) -->
            <div id="modal-troubleshoot-box" class="hidden p-3.5 rounded-xl bg-amber-50 dark:bg-amber-950/30 border border-amber-300 dark:border-amber-800/50 text-xs text-amber-800 dark:text-amber-300 space-y-1.5">
                <div class="font-bold flex items-center gap-1.5">
                    <i class="fa-solid fa-circle-info"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'كيفية تفعيل إذن الموقع في حال الحظر من المتصفح:' : 'How to unblock location in your browser / tablet:' }}</span>
                </div>
                <ul class="list-disc list-inside space-y-1 text-[11px] text-amber-700 dark:text-amber-400">
                    <li><strong>Chrome / Android / iPad:</strong> {{ app()->getLocale() === 'ar' ? 'اضغط على أيقونة القفل 🔒 بجانب الرابط > الأذونات > الموقع > تفعيل.' : 'Tap the padlock 🔒 in the address bar > Permissions > Location > Allow.' }}</li>
                    <li><strong>Safari / iPadOS:</strong> {{ app()->getLocale() === 'ar' ? 'اضغط على أيقونة (aA) أو الإعدادات > إعدادات موقع الويب > الموقع > سماح.' : 'Tap (aA) in the search bar > Website Settings > Location > Allow.' }}</li>
                </ul>
            </div>

            <!-- Modal Action Buttons: Allow vs Deny -->
            <div class="flex flex-col sm:flex-row items-center justify-end gap-3 pt-2 border-t border-slate-200 dark:border-slate-800">
                <button type="button" onclick="handleUserDenyGps()" class="w-full sm:w-auto px-4 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs transition border border-slate-300 dark:border-slate-700 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-ban text-rose-500"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'رفض الـ GPS ومتابعة بدون توثيق' : 'Deny & Continue Offline' }}</span>
                </button>

                <button type="button" onclick="handleUserAllowGps()" id="modal-allow-gps-btn" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-600 hover:from-emerald-500 hover:to-cyan-500 text-white font-black text-xs active:scale-95 transition shadow-lg shadow-emerald-600/30 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'السماح بالـ GPS وتفعيل السياج' : 'Allow GPS & Enable Geofence' }}</span>
                </button>
            </div>

        </div>
    </div>

    {{-- ================= Interactive Check-In Modal ================= --}}
    <div id="checkin-modal" class="fixed inset-0 z-50 bg-slate-950/60 backdrop-blur-md hidden flex items-center justify-center p-4">
        <div class="glass-card bg-white dark:bg-slate-900 rounded-2xl w-full max-w-md p-6 space-y-4 shadow-2xl border border-slate-200 dark:border-slate-700/80 relative">
            <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
                <h3 class="font-bold text-slate-900 dark:text-white text-base flex items-center gap-2">
                    <i class="fa-solid fa-location-dot text-cyan-500 dark:text-cyan-400"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'تسجيل وصول جغرافي (GPS Check-In)' : 'GPS Visit Check-In' }}</span>
                </h3>
                <button onclick="closeCheckinModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-white transition">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700/70">
                <div class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ app()->getLocale() === 'ar' ? 'العيادة المستهدفة:' : 'Target Clinic / Doctor:' }}</div>
                <div id="checkin-doctor-name" class="font-bold text-slate-900 dark:text-white text-sm mt-0.5"></div>
            </div>

            <div id="gps-status-box" class="p-3.5 rounded-xl bg-cyan-500/10 border border-cyan-500/30 text-xs text-cyan-700 dark:text-cyan-300 flex items-center gap-2.5">
                <i class="fa-solid fa-spinner fa-spin text-sm text-cyan-500 dark:text-cyan-400"></i>
                <span>{{ app()->getLocale() === 'ar' ? 'جاري تحديد إحداثيات الـ GPS بدقة عالية...' : 'Acquiring high-accuracy GPS coordinates...' }}</span>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-2">
                <button type="button" onclick="closeCheckinModal()" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs transition border border-slate-200 dark:border-transparent">
                    {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                </button>
                <button id="confirm-checkin-btn" onclick="submitCheckinForm()" disabled class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-cyan-600 to-sky-600 hover:from-cyan-500 hover:to-sky-500 text-white font-bold text-xs disabled:opacity-50 disabled:cursor-not-allowed active:scale-95 transition shadow-lg shadow-cyan-600/30 flex items-center gap-2">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'تأكيد تسجيل الوصول' : 'Confirm Check-In' }}</span>
                </button>
            </div>
        </div>
    </div>

    {{-- ================= Interactive Check-Out Modal ================= --}}
    <div id="checkout-modal" class="fixed inset-0 z-50 bg-slate-950/60 backdrop-blur-md hidden flex items-center justify-center p-4">
        <div class="glass-card bg-white dark:bg-slate-900 rounded-2xl w-full max-w-md p-6 space-y-4 shadow-2xl border border-slate-200 dark:border-slate-700/80 relative">
            <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
                <h3 class="font-bold text-slate-900 dark:text-white text-base flex items-center gap-2">
                    <i class="fa-solid fa-clipboard-check text-emerald-500 dark:text-emerald-400"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'تسجيل الانصراف ومخرجات الزيارة' : 'Visit Check-Out & Outcome' }}</span>
                </h3>
                <button onclick="closeCheckoutModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-white transition">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <input type="hidden" id="checkout-visit-id">

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                    {{ app()->getLocale() === 'ar' ? 'نتيجة الزيارة *' : 'Visit Outcome *' }}
                </label>
                <select id="checkout-outcome" class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white p-3 focus:outline-none focus:border-emerald-500">
                    <option value="completed">✅ {{ app()->getLocale() === 'ar' ? 'مقابلة ناجحة ومناقشة تفصيلية' : 'Successful Meeting & Clinical Discussion' }}</option>
                    <option value="sample_delivered">💊 {{ app()->getLocale() === 'ar' ? 'تسليم عينات ومواد ترويجية' : 'Samples & Marketing Materials Delivered' }}</option>
                    <option value="follow_up_needed">📅 {{ app()->getLocale() === 'ar' ? 'تحديد موعد متابعة قادم' : 'Follow-Up Needed / Action Assigned' }}</option>
                    <option value="doctor_busy">⏳ {{ app()->getLocale() === 'ar' ? 'الطبيب منشغل / مقابلة سريعة' : 'Doctor Busy / Brief Presentation' }}</option>
                    <option value="cancelled">❌ {{ app()->getLocale() === 'ar' ? 'الطبيب غير متاح بالعيادة' : 'Doctor Unavailable at Clinic' }}</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                    {{ app()->getLocale() === 'ar' ? 'ملاحظات الزيارة وملاحظات الطبيب' : 'Meeting Notes & Doctor Feedback' }}
                </label>
                <textarea id="checkout-notes" rows="3" placeholder="{{ app()->getLocale() === 'ar' ? 'التركيبات الطبية المناقشة، استجابة الطبيب، الاحتياجات...' : 'Key longevity formulations discussed, product feedback, requirements...' }}" class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white p-3 focus:outline-none focus:border-emerald-500"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-2">
                <button type="button" onclick="closeCheckoutModal()" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs transition border border-slate-200 dark:border-transparent">
                    {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                </button>
                <button onclick="submitCheckoutForm()" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold text-xs active:scale-95 transition shadow-lg shadow-emerald-600/30 flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'حفظ وإنهاء الزيارة' : 'Save & Log Visit' }}</span>
                </button>
            </div>
        </div>
    </div>

    {{-- ================= Schedule Appointment Modal ================= --}}
    <div id="schedule-modal" class="fixed inset-0 z-50 bg-slate-950/60 backdrop-blur-md hidden flex items-center justify-center p-4">
        <div class="glass-card bg-white dark:bg-slate-900 rounded-2xl w-full max-w-md p-6 space-y-4 shadow-2xl border border-slate-200 dark:border-slate-700/80 relative">
            <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
                <h3 class="font-bold text-slate-900 dark:text-white text-base flex items-center gap-2">
                    <i class="fa-solid fa-calendar-plus text-sky-500 dark:text-sky-400"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'جدولة زيارة في الأجندة' : 'Schedule Field Appointment' }}</span>
                </h3>
                <button onclick="closeScheduleModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-white transition">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                    {{ app()->getLocale() === 'ar' ? 'اختر الطبيب *' : 'Doctor Assignment *' }}
                </label>
                <select id="schedule-assignment-id" class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white p-3 focus:outline-none focus:border-sky-500">
                    @foreach($allAssignments as $assign)
                        <option value="{{ $assign->id }}">
                            {{ $assign->contact?->name }} (Class {{ $assign->contact?->classification?->code }}) — {{ $assign->contact?->hospital_clinic_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                    {{ app()->getLocale() === 'ar' ? 'تاريخ ووقت الزيارة *' : 'Date & Time *' }}
                </label>
                <input type="datetime-local" id="schedule-datetime" value="{{ now()->addHour()->format('Y-m-d\TH:i') }}" class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white p-3 focus:outline-none focus:border-sky-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                    {{ app()->getLocale() === 'ar' ? 'ملاحظات الموعد' : 'Appointment Notes' }}
                </label>
                <textarea id="schedule-notes" rows="2" placeholder="Specific items to present..." class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white p-3 focus:outline-none focus:border-sky-500"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-2">
                <button type="button" onclick="closeScheduleModal()" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs transition border border-slate-200 dark:border-transparent">
                    {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                </button>
                <button onclick="submitScheduleForm()" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-sky-600 to-indigo-600 hover:from-sky-500 hover:to-indigo-500 text-white font-bold text-xs active:scale-95 transition shadow-lg shadow-sky-600/30 flex items-center gap-2">
                    <i class="fa-solid fa-check"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'تأكيد الحجز' : 'Schedule Appointment' }}</span>
                </button>
            </div>
        </div>
    </div>

    {{-- ================= Script Logic ================= --}}
    <script>
        let currentCheckinData = { contactId: null, assignmentId: null, scheduledVisitId: null, lat: null, lng: null, accuracy: null };
        let deviceGpsState = {
            status: 'unknown', // 'active', 'denied', 'pending'
            lat: null,
            lng: null,
            accuracy: null
        };

        // Initialize theme and device location on DOM load
        document.addEventListener('DOMContentLoaded', function() {
            initTheme();
            initializeGpsState();
        });

        // Theme management logic
        function initTheme() {
            const saved = localStorage.getItem('bz_mr_theme');
            const isLight = saved === 'light';
            applyTheme(isLight ? 'light' : 'dark');
        }

        function toggleTheme() {
            const isDark = document.documentElement.classList.contains('dark');
            const targetTheme = isDark ? 'light' : 'dark';
            applyTheme(targetTheme);
            localStorage.setItem('bz_mr_theme', targetTheme);
        }

        function applyTheme(theme) {
            const icon = document.getElementById('theme-toggle-icon');
            const text = document.getElementById('theme-toggle-text');
            const isAr = "{{ app()->getLocale() }}" === 'ar';

            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
                document.documentElement.classList.remove('light');
                if (icon) icon.className = 'fa-solid fa-sun text-amber-400 text-sm';
                if (text) text.innerText = isAr ? 'الوضع النهاري' : 'Light Mode';
            } else {
                document.documentElement.classList.remove('dark');
                document.documentElement.classList.add('light');
                if (icon) icon.className = 'fa-solid fa-moon text-indigo-600 text-sm';
                if (text) text.innerText = isAr ? 'الوضع الليلي' : 'Dark Mode';
            }
        }

        // ================= GPS State Machine & Geofencing =================
        function initializeGpsState() {
            const userChoice = localStorage.getItem('bz_gps_user_decision'); // 'allowed', 'denied', null

            if (userChoice === 'denied') {
                updateGpsUI('denied', null, false);
                return;
            }

            // Probe Permissions API if available
            if (navigator.permissions && navigator.permissions.query) {
                navigator.permissions.query({ name: 'geolocation' }).then((permissionStatus) => {
                    if (permissionStatus.state === 'granted') {
                        detectDeviceLocation(false);
                    } else if (permissionStatus.state === 'denied') {
                        updateGpsUI('denied', null, false);
                    } else {
                        // 'prompt' state
                        updateGpsUI('pending', null, false);
                        checkBannerVisibility();
                    }

                    permissionStatus.onchange = function() {
                        if (this.state === 'granted') {
                            detectDeviceLocation(false);
                        } else if (this.state === 'denied') {
                            updateGpsUI('denied', null, false);
                        } else {
                            updateGpsUI('pending', null, false);
                        }
                    };
                }).catch(() => {
                    updateGpsUI('pending', null, false);
                    checkBannerVisibility();
                });
            } else {
                updateGpsUI('pending', null, false);
                checkBannerVisibility();
            }
        }

        function detectDeviceLocation(isManualTrigger = false) {
            if (!("geolocation" in navigator)) {
                updateGpsUI('unsupported', null, isManualTrigger);
                return;
            }

            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    deviceGpsState = {
                        status: 'active',
                        lat: pos.coords.latitude,
                        lng: pos.coords.longitude,
                        accuracy: pos.coords.accuracy
                    };
                    localStorage.setItem('bz_gps_user_decision', 'allowed');
                    sessionStorage.removeItem('bz_gps_banner_dismissed');
                    updateGpsUI('active', pos.coords, isManualTrigger);
                    if (isManualTrigger) {
                        showToast("{{ app()->getLocale() === 'ar' ? 'تم تأكيد إحداثيات الـ GPS بنجاح' : 'GPS Coordinates Locked & Verified' }}", 'success');
                    }
                },
                (err) => {
                    deviceGpsState.status = 'denied';
                    updateGpsUI('denied', null, isManualTrigger);
                    if (isManualTrigger) {
                        let reason = "{{ app()->getLocale() === 'ar' ? 'تعذر تحديد الموقع الجغرافي' : 'GPS Location Unavailable' }}";
                        if (err.code === 1) {
                            reason = "{{ app()->getLocale() === 'ar' ? 'تم حظر إذن الـ GPS من المتصفح أو الجهاز.' : 'GPS access was denied by browser/device settings.' }}";
                        } else if (err.code === 2) {
                            reason = "{{ app()->getLocale() === 'ar' ? 'إشارة الـ GPS غير متوفرة حالياً' : 'GPS signal currently unavailable.' }}";
                        } else if (err.code === 3) {
                            reason = "{{ app()->getLocale() === 'ar' ? 'انتهت مهلة انتظار إشارة الـ GPS' : 'GPS request timed out.' }}";
                        }
                        showToast(reason, 'error');
                    }
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        }

        function updateGpsUI(state, coords = null, wasUserTriggered = false) {
            const isAr = "{{ app()->getLocale() }}" === 'ar';
            const topIndicator = document.getElementById('top-gps-indicator');
            const topPulse = document.getElementById('top-gps-pulse');
            const topLabel = document.getElementById('top-gps-label');
            const sensorStatus = document.getElementById('sensor-status');
            const sensorLat = document.getElementById('sensor-lat');
            const sensorLng = document.getElementById('sensor-lng');
            const sensorAcc = document.getElementById('sensor-acc');

            const alertBanner = document.getElementById('gps-alert-banner');
            const bannerIconBox = document.getElementById('gps-banner-icon-box');
            const bannerIcon = document.getElementById('gps-banner-icon');
            const bannerBadge = document.getElementById('gps-banner-badge');
            const bannerTitle = document.getElementById('gps-banner-title');
            const bannerDesc = document.getElementById('gps-banner-desc');
            const bannerAllowBtn = document.getElementById('gps-banner-allow-btn');

            // Modal elements
            const modalBadge = document.getElementById('modal-gps-badge');
            const modalDot = document.getElementById('modal-gps-dot');
            const modalText = document.getElementById('modal-gps-text');
            const modalSubtext = document.getElementById('modal-gps-subtext');
            const modalRadarCenter = document.getElementById('modal-radar-center');
            const modalRadarIcon = document.getElementById('modal-radar-icon');
            const modalCoordsBox = document.getElementById('modal-coords-box');
            const modalLatVal = document.getElementById('modal-lat-val');
            const modalLngVal = document.getElementById('modal-lng-val');
            const modalAccVal = document.getElementById('modal-acc-val');
            const modalTroubleshoot = document.getElementById('modal-troubleshoot-box');

            if (state === 'active' && coords) {
                // Topbar
                topIndicator.className = "inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30 shadow-sm hover:scale-105 active:scale-95 transition cursor-pointer";
                topPulse.innerHTML = `
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                `;
                topLabel.innerText = isAr ? `GPS نشط (±${Math.round(coords.accuracy)}م)` : `GPS Active (±${Math.round(coords.accuracy)}m)`;

                // Sensor Card
                if (sensorStatus) {
                    sensorStatus.innerText = isAr ? 'متصل ومؤكد' : 'Locked & Verified';
                    sensorStatus.className = 'text-emerald-600 dark:text-emerald-400 font-bold';
                }
                if (sensorLat) sensorLat.innerText = coords.latitude.toFixed(6);
                if (sensorLng) sensorLng.innerText = coords.longitude.toFixed(6);
                if (sensorAcc) sensorAcc.innerText = '±' + Math.round(coords.accuracy) + 'm';

                // Modal
                if (modalBadge) {
                    modalBadge.className = "px-3 py-1 rounded-full text-xs font-black inline-flex items-center gap-1.5 bg-emerald-500/15 text-emerald-700 dark:text-emerald-300 border border-emerald-500/30";
                    modalDot.className = "w-2 h-2 rounded-full bg-emerald-500 animate-ping";
                    modalText.innerText = isAr ? 'متصل ومؤكد (Geofence Active)' : 'Active & Verified (Geofence Active)';
                    modalSubtext.innerText = isAr ? `تم تحديد الإحداثيات بدقة عالية (±${Math.round(coords.accuracy)} متر)` : `High-accuracy lock acquired (±${Math.round(coords.accuracy)}m accuracy)`;
                }
                if (modalRadarCenter) {
                    modalRadarCenter.className = "w-12 h-12 rounded-full bg-emerald-600 text-white shadow-xl flex items-center justify-center text-lg z-10 border-2 border-white dark:border-slate-700";
                    modalRadarIcon.className = "fa-solid fa-satellite text-white";
                }
                if (modalCoordsBox) {
                    modalCoordsBox.classList.remove('hidden');
                    modalLatVal.innerText = coords.latitude.toFixed(6);
                    modalLngVal.innerText = coords.longitude.toFixed(6);
                    modalAccVal.innerText = '±' + Math.round(coords.accuracy) + 'm';
                }
                if (modalTroubleshoot) modalTroubleshoot.classList.add('hidden');

                // Hide alert banner
                if (alertBanner) alertBanner.classList.add('hidden');

            } else if (state === 'denied') {
                // Topbar
                topIndicator.className = "inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/30 shadow-sm hover:scale-105 active:scale-95 transition cursor-pointer";
                topPulse.innerHTML = `<span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500"></span>`;
                topLabel.innerText = isAr ? 'GPS محظور / غير مفعل' : 'GPS Denied / Offline';

                // Sensor Card
                if (sensorStatus) {
                    sensorStatus.innerText = isAr ? 'محظور / مرفوض' : 'Permission Denied';
                    sensorStatus.className = 'text-rose-500 font-bold';
                }
                if (sensorLat) sensorLat.innerText = '—';
                if (sensorLng) sensorLng.innerText = '—';
                if (sensorAcc) sensorAcc.innerText = 'Denied';

                // Modal
                if (modalBadge) {
                    modalBadge.className = "px-3 py-1 rounded-full text-xs font-black inline-flex items-center gap-1.5 bg-rose-500/15 text-rose-700 dark:text-rose-300 border border-rose-500/30";
                    modalDot.className = "w-2 h-2 rounded-full bg-rose-500";
                    modalText.innerText = isAr ? 'محظور / غير مسموح (Access Blocked)' : 'Access Denied / Offline Mode';
                    modalSubtext.innerText = isAr ? 'تم رفض إذن الـ GPS. سيتم تسجيل الزيارات كـ "غير مؤكدة".' : 'Location denied. Check-ins will be logged as unverified without points bonus.';
                }
                if (modalRadarCenter) {
                    modalRadarCenter.className = "w-12 h-12 rounded-full bg-rose-600 text-white shadow-xl flex items-center justify-center text-lg z-10 border-2 border-white dark:border-slate-700";
                    modalRadarIcon.className = "fa-solid fa-location-pin-lock text-white";
                }
                if (modalCoordsBox) modalCoordsBox.classList.add('hidden');
                if (modalTroubleshoot) modalTroubleshoot.classList.remove('hidden');

                // Configure and show Alert Banner
                if (alertBanner && !sessionStorage.getItem('bz_gps_banner_dismissed')) {
                    alertBanner.classList.remove('hidden');
                    alertBanner.className = "transition-all duration-300 rounded-2xl p-4 sm:p-5 shadow-lg border relative overflow-hidden bg-rose-50 dark:bg-rose-950/30 border-rose-200 dark:border-rose-900/50";
                    bannerIconBox.className = "w-12 h-12 rounded-xl flex items-center justify-center text-xl flex-shrink-0 bg-rose-500/15 text-rose-600 dark:text-rose-400 border border-rose-500/25";
                    bannerIcon.className = "fa-solid fa-triangle-exclamation animate-pulse";
                    bannerBadge.className = "px-2.5 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-rose-500 text-white";
                    bannerBadge.innerText = isAr ? 'تنبيه الموقع محظور' : 'GPS Access Denied';
                    bannerTitle.innerText = isAr ? 'صلاحية الـ GPS محظورة — الزيارات ستُسجل كـ "غير مؤكدة"' : 'Location Permission Denied — Visits Logged as Unverified';
                    bannerDesc.innerText = isAr 
                        ? 'لقد تم رفض إذن تحديد الموقع الجغرافي. يمكنك متابعة العمل الميداني ولكن ستفقد نقاط التحقق الجغرافي للعيادات. يمكنك الضغط على "السماح بالـ GPS" لإعادة المحاولة.' 
                        : 'Without location access, check-ins cannot verify doctor clinic geofence proximity. Tap Allow GPS below or configure browser permissions.';
                    bannerAllowBtn.innerHTML = `<i class="fa-solid fa-rotate-right"></i><span>${isAr ? 'إعادة المحاولة وتفعيل الـ GPS' : 'Re-check & Allow GPS'}</span>`;
                }

            } else {
                // Pending / Prompt
                topIndicator.className = "inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold bg-sky-500/10 text-sky-600 dark:text-sky-400 border border-sky-500/30 shadow-sm hover:scale-105 active:scale-95 transition cursor-pointer";
                topPulse.innerHTML = `<span class="relative inline-flex rounded-full h-2 w-2 bg-sky-500"></span>`;
                topLabel.innerText = isAr ? 'تفعيل الـ GPS مطلوب' : 'GPS Required';

                if (sensorStatus) {
                    sensorStatus.innerText = isAr ? 'في انتظار الإذن' : 'Pending Authorization';
                    sensorStatus.className = 'text-sky-500 font-bold';
                }

                // Modal
                if (modalBadge) {
                    modalBadge.className = "px-3 py-1 rounded-full text-xs font-black inline-flex items-center gap-1.5 bg-sky-500/15 text-sky-700 dark:text-sky-300 border border-sky-500/30";
                    modalDot.className = "w-2 h-2 rounded-full bg-sky-500 animate-pulse";
                    modalText.innerText = isAr ? 'في انتظار منح الصلاحية' : 'Pending Authorization';
                    modalSubtext.innerText = isAr ? 'يرجى الضغط على "السماح بالـ GPS" لمنح الصلاحية وتفعيل السياج.' : 'Click Allow GPS below to grant browser location permission.';
                }
                if (modalCoordsBox) modalCoordsBox.classList.add('hidden');
                if (modalTroubleshoot) modalTroubleshoot.classList.add('hidden');

                // Configure and show Alert Banner
                if (alertBanner && !sessionStorage.getItem('bz_gps_banner_dismissed')) {
                    alertBanner.classList.remove('hidden');
                    alertBanner.className = "transition-all duration-300 rounded-2xl p-4 sm:p-5 shadow-lg border relative overflow-hidden bg-sky-50 dark:bg-slate-900/90 border-sky-200 dark:border-sky-900/50";
                    bannerIconBox.className = "w-12 h-12 rounded-xl flex items-center justify-center text-xl flex-shrink-0 bg-sky-500/15 text-sky-600 dark:text-sky-400 border border-sky-500/25";
                    bannerIcon.className = "fa-solid fa-satellite-dish animate-pulse";
                    bannerBadge.className = "px-2.5 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-sky-600 text-white";
                    bannerBadge.innerText = isAr ? 'مطلوب للميدان' : 'Action Required';
                    bannerTitle.innerText = isAr ? 'تفعيل نظام الـ GPS لتأكيد الزيارات الميدانية' : 'Enable High-Accuracy Geofencing for Doctor Visits';
                    bannerDesc.innerText = isAr 
                        ? 'يتطلب نظام المندوب الطبي التحقق التلقائي من تواجدك داخل العيادة لاحتساب النقاط والتأكد من إتمام خطة الدورة.' 
                        : 'BlueZone CRM requires device GPS access to verify clinic presence, prevent spoofing, and award 100% cycle points.';
                    bannerAllowBtn.innerHTML = `<i class="fa-solid fa-location-arrow"></i><span>${isAr ? 'السماح بالـ GPS وتفعيل السياج' : 'Allow GPS & Enable'}</span>`;
                }
            }
        }

        function checkBannerVisibility() {
            if (!sessionStorage.getItem('bz_gps_banner_dismissed')) {
                const banner = document.getElementById('gps-alert-banner');
                if (banner && deviceGpsState.status !== 'active') {
                    banner.classList.remove('hidden');
                }
            }
        }

        function dismissGpsBanner() {
            const banner = document.getElementById('gps-alert-banner');
            if (banner) banner.classList.add('hidden');
            sessionStorage.setItem('bz_gps_banner_dismissed', 'true');
        }

        function handleGpsBannerAction(action) {
            if (action === 'allow') {
                detectDeviceLocation(true);
            }
        }

        // Modal Controls
        function openGpsConfigModal() {
            document.getElementById('gps-config-modal').classList.remove('hidden');
            if (deviceGpsState.status === 'active' && deviceGpsState.lat) {
                updateGpsUI('active', { latitude: deviceGpsState.lat, longitude: deviceGpsState.lng, accuracy: deviceGpsState.accuracy }, false);
            } else if (deviceGpsState.status === 'denied') {
                updateGpsUI('denied', null, false);
            } else {
                updateGpsUI('pending', null, false);
            }
        }

        function closeGpsConfigModal() {
            document.getElementById('gps-config-modal').classList.add('hidden');
        }

        function handleUserAllowGps() {
            const btn = document.getElementById('modal-allow-gps-btn');
            btn.disabled = true;
            btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i><span>{{ app()->getLocale() === 'ar' ? 'جاري الاتصال بالأقمار الصناعية...' : 'Connecting to GPS...' }}</span>`;
            
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    deviceGpsState = {
                        status: 'active',
                        lat: pos.coords.latitude,
                        lng: pos.coords.longitude,
                        accuracy: pos.coords.accuracy
                    };
                    localStorage.setItem('bz_gps_user_decision', 'allowed');
                    sessionStorage.removeItem('bz_gps_banner_dismissed');
                    updateGpsUI('active', pos.coords, true);
                    btn.disabled = false;
                    btn.innerHTML = `<i class="fa-solid fa-circle-check"></i><span>{{ app()->getLocale() === 'ar' ? 'تم التفعيل بنجاح' : 'GPS Activated' }}</span>`;
                    showToast("{{ app()->getLocale() === 'ar' ? 'تم تفعيل الـ GPS والتحقق من النطاق الجغرافي بنجاح!' : 'GPS Geofence Successfully Enabled!' }}", 'success');
                    setTimeout(() => closeGpsConfigModal(), 1000);
                },
                (err) => {
                    deviceGpsState.status = 'denied';
                    localStorage.setItem('bz_gps_user_decision', 'denied');
                    updateGpsUI('denied', null, true);
                    btn.disabled = false;
                    btn.innerHTML = `<i class="fa-solid fa-circle-check"></i><span>{{ app()->getLocale() === 'ar' ? 'السماح بالـ GPS وتفعيل السياج' : 'Allow GPS & Enable Geofence' }}</span>`;
                    showToast("{{ app()->getLocale() === 'ar' ? 'تم حظر الـ GPS من إعدادات المتصفح أو الجهاز.' : 'GPS blocked in browser/device settings. See instructions below.' }}", 'error');
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        }

        function handleUserDenyGps() {
            localStorage.setItem('bz_gps_user_decision', 'denied');
            deviceGpsState.status = 'denied';
            updateGpsUI('denied', null, false);
            closeGpsConfigModal();
            showToast("{{ app()->getLocale() === 'ar' ? 'تم اختيار وضع عدم التفعيل. ستسجل الزيارات كـ غير مؤكدة.' : 'Offline mode active. Visits will be logged as unverified.' }}", 'error');
        }

        // Search & Filter Doctors
        function filterDoctors() {
            const query = document.getElementById('doctor-search-input').value.toLowerCase();
            const cards = document.querySelectorAll('.doctor-card');
            cards.forEach(card => {
                const name = card.getAttribute('data-name') || '';
                const clinic = card.getAttribute('data-clinic') || '';
                const spec = card.getAttribute('data-specialty') || '';
                const cls = card.getAttribute('data-class') || '';
                
                const matches = name.includes(query) || clinic.includes(query) || spec.includes(query) || cls.toLowerCase().includes(query);
                card.style.display = matches ? 'flex' : 'none';
            });
        }

        function filterByClass(targetClass) {
            document.querySelectorAll('.chip-filter').forEach(btn => {
                btn.classList.remove('active', 'bg-sky-600', 'text-white');
                btn.classList.add('bg-slate-200', 'dark:bg-slate-800', 'text-slate-700', 'dark:text-slate-400');
            });
            event.target.classList.add('active', 'bg-sky-600', 'text-white');
            event.target.classList.remove('bg-slate-200', 'dark:bg-slate-800', 'text-slate-700', 'dark:text-slate-400');

            const cards = document.querySelectorAll('.doctor-card');
            cards.forEach(card => {
                const cls = card.getAttribute('data-class');
                if (targetClass === 'all' || cls === targetClass) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        // Toast feedback
        function showToast(msg, type = 'success') {
            const toast = document.getElementById('toast');
            const icon = document.getElementById('toast-icon');
            const message = document.getElementById('toast-message');

            message.innerText = msg;
            if (type === 'success') {
                toast.className = 'fixed bottom-6 right-6 rtl:right-auto rtl:left-6 z-50 transform transition-all duration-300 translate-y-0 opacity-100 bg-emerald-950/90 text-emerald-200 border border-emerald-500/40 p-4 rounded-xl shadow-2xl text-xs font-bold flex items-center gap-2.5 max-w-sm backdrop-blur-md';
                icon.className = 'fa-solid fa-circle-check text-emerald-400 text-base';
            } else {
                toast.className = 'fixed bottom-6 right-6 rtl:right-auto rtl:left-6 z-50 transform transition-all duration-300 translate-y-0 opacity-100 bg-rose-950/90 text-rose-200 border border-rose-500/40 p-4 rounded-xl shadow-2xl text-xs font-bold flex items-center gap-2.5 max-w-sm backdrop-blur-md';
                icon.className = 'fa-solid fa-triangle-exclamation text-rose-400 text-base';
            }

            setTimeout(() => {
                toast.classList.add('translate-y-24', 'opacity-0');
            }, 4000);
        }

        // Check-In flow
        function triggerCheckIn(contactId, doctorName, assignmentId = null, scheduledVisitId = null) {
            currentCheckinData = { contactId, assignmentId, scheduledVisitId, lat: null, lng: null, accuracy: null };
            document.getElementById('checkin-doctor-name').innerText = doctorName;
            document.getElementById('checkin-modal').classList.remove('hidden');

            const statusBox = document.getElementById('gps-status-box');
            const confirmBtn = document.getElementById('confirm-checkin-btn');
            confirmBtn.disabled = true;

            statusBox.className = "p-3.5 rounded-xl bg-cyan-500/10 border border-cyan-500/30 text-xs text-cyan-700 dark:text-cyan-300 flex items-center gap-2.5";
            statusBox.innerHTML = `
                <i class="fa-solid fa-spinner fa-spin text-sm text-cyan-500 dark:text-cyan-400"></i>
                <span>{{ app()->getLocale() === 'ar' ? 'جاري تحديد إحداثيات الـ GPS بدقة عالية...' : 'Acquiring high-accuracy GPS coordinates...' }}</span>
            `;

            // If we already have locked device GPS coords, use them
            if (deviceGpsState.status === 'active' && deviceGpsState.lat) {
                currentCheckinData.lat = deviceGpsState.lat;
                currentCheckinData.lng = deviceGpsState.lng;
                currentCheckinData.accuracy = deviceGpsState.accuracy;

                statusBox.className = "p-3.5 rounded-xl bg-emerald-500/15 border border-emerald-500/40 text-xs text-emerald-700 dark:text-emerald-300 flex items-center gap-2.5";
                statusBox.innerHTML = `<i class="fa-solid fa-circle-check text-emerald-500 dark:text-emerald-400"></i><span>📍 Coordinates Acquired (Accuracy: ±${Math.round(deviceGpsState.accuracy)}m)</span>`;
                confirmBtn.disabled = false;
                return;
            }

            if ("geolocation" in navigator) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        currentCheckinData.lat = position.coords.latitude;
                        currentCheckinData.lng = position.coords.longitude;
                        currentCheckinData.accuracy = position.coords.accuracy;
                        deviceGpsState = {
                            status: 'active',
                            lat: position.coords.latitude,
                            lng: position.coords.longitude,
                            accuracy: position.coords.accuracy
                        };
                        updateGpsUI('active', position.coords, false);

                        statusBox.className = "p-3.5 rounded-xl bg-emerald-500/15 border border-emerald-500/40 text-xs text-emerald-700 dark:text-emerald-300 flex items-center gap-2.5";
                        statusBox.innerHTML = `<i class="fa-solid fa-circle-check text-emerald-500 dark:text-emerald-400"></i><span>📍 Coordinates Acquired (Accuracy: ±${Math.round(position.coords.accuracy)}m)</span>`;
                        confirmBtn.disabled = false;
                    },
                    (error) => {
                        statusBox.className = "p-3.5 rounded-xl bg-amber-500/15 border border-amber-500/40 text-xs text-amber-700 dark:text-amber-300 flex items-center gap-2.5";
                        statusBox.innerHTML = `<i class="fa-solid fa-triangle-exclamation text-amber-500 dark:text-amber-400"></i><span>GPS unavailable. Fallback visit will be recorded with flagged coordinates.</span>`;
                        confirmBtn.disabled = false;
                    },
                    { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                );
            } else {
                statusBox.innerText = "Geolocation not supported on this device.";
                confirmBtn.disabled = false;
            }
        }

        function closeCheckinModal() {
            document.getElementById('checkin-modal').classList.add('hidden');
        }

        async function submitCheckinForm() {
            const btn = document.getElementById('confirm-checkin-btn');
            btn.disabled = true;
            btn.innerText = "Verifying with Geofence...";

            try {
                const response = await fetch("{{ route('mr.checkin') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        contact_id: currentCheckinData.contactId,
                        assignment_id: currentCheckinData.assignmentId,
                        scheduled_visit_id: currentCheckinData.scheduledVisitId,
                        lat: currentCheckinData.lat,
                        lng: currentCheckinData.lng,
                        accuracy_m: currentCheckinData.accuracy
                    })
                });

                const res = await response.json();
                if (res.success) {
                    showToast(res.message, 'success');
                    closeCheckinModal();
                    setTimeout(() => window.location.reload(), 1200);
                } else {
                    showToast('Error: ' + res.message, 'error');
                    btn.disabled = false;
                    btn.innerText = "Confirm Check-In";
                }
            } catch (err) {
                showToast('Network error: ' + err.message, 'error');
                btn.disabled = false;
                btn.innerText = "Confirm Check-In";
            }
        }

        // Check-Out flow
        function openCheckoutModal(visitId, doctorName) {
            document.getElementById('checkout-visit-id').value = visitId;
            document.getElementById('checkout-modal').classList.remove('hidden');
        }

        function closeCheckoutModal() {
            document.getElementById('checkout-modal').classList.add('hidden');
        }

        async function submitCheckoutForm() {
            const visitId = document.getElementById('checkout-visit-id').value;
            const outcome = document.getElementById('checkout-outcome').value;
            const notes = document.getElementById('checkout-notes').value;

            try {
                const response = await fetch("{{ route('mr.checkout') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        visit_id: visitId,
                        outcome: outcome,
                        notes: notes
                    })
                });

                const res = await response.json();
                if (res.success) {
                    showToast(res.message, 'success');
                    closeCheckoutModal();
                    setTimeout(() => window.location.reload(), 1200);
                } else {
                    showToast('Error: ' + res.message, 'error');
                }
            } catch (err) {
                showToast('Network error: ' + err.message, 'error');
            }
        }

        // Schedule Modal flow
        function openScheduleModal(assignmentId = null, doctorName = null) {
            if (assignmentId) {
                document.getElementById('schedule-assignment-id').value = assignmentId;
            }
            document.getElementById('schedule-modal').classList.remove('hidden');
        }

        function closeScheduleModal() {
            document.getElementById('schedule-modal').classList.add('hidden');
        }

        async function submitScheduleForm() {
            const assignmentId = document.getElementById('schedule-assignment-id').value;
            const datetime = document.getElementById('schedule-datetime').value;
            const notes = document.getElementById('schedule-notes').value;

            if (!assignmentId || !datetime) {
                showToast('Please select doctor and date/time slot', 'error');
                return;
            }

            try {
                const response = await fetch("{{ route('mr.schedule') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        assignment_id: assignmentId,
                        scheduled_at: datetime,
                        notes: notes
                    })
                });

                const res = await response.json();
                if (res.success) {
                    showToast(res.message, 'success');
                    closeScheduleModal();
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    showToast('Error: ' + res.message, 'error');
                }
            } catch (err) {
                showToast('Network error: ' + err.message, 'error');
            }
        }
    </script>
</body>
</html>

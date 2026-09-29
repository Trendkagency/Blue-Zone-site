<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, viewport-fit=cover">
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
        html, body {
            overflow-x: hidden;
            width: 100%;
            -webkit-tap-highlight-color: transparent;
        }
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
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

        /* Custom scrollbar & no-scrollbar */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: rgba(226, 232, 240, 0.5); }
        .dark ::-webkit-scrollbar-track { background: rgba(15, 23, 42, 0.5); }
        ::-webkit-scrollbar-thumb { background: rgba(148, 163, 184, 0.8); border-radius: 999px; }
        .dark ::-webkit-scrollbar-thumb { background: rgba(51, 65, 85, 0.8); border-radius: 999px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(100, 116, 139, 1); }
    </style>
</head>
<body class="bg-slate-100 dark:bg-slate-950 text-slate-800 dark:text-slate-100 min-h-screen selection:bg-cyan-500 selection:text-white antialiased transition-colors duration-200 overflow-x-hidden">

    <!-- Ambient Glow Backdrops -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
        <div class="absolute -top-40 -left-40 w-96 h-96 bg-cyan-500/10 dark:bg-cyan-600/10 rounded-full blur-[120px]"></div>
        <div class="absolute top-1/3 -right-40 w-96 h-96 bg-indigo-500/10 dark:bg-indigo-600/10 rounded-full blur-[120px]"></div>
    </div>

    <div class="relative z-10 flex flex-col min-h-screen">
        
        {{-- ================= Top Navigation Bar ================= --}}
        <header class="sticky top-0 z-40 bg-white/90 dark:bg-slate-950/90 backdrop-blur-xl border-b border-slate-200 dark:border-slate-800/80 px-3 sm:px-6 lg:px-8 py-2.5 sm:py-3.5 transition-all">
            <div class="max-w-7xl mx-auto flex items-center justify-between gap-2 sm:gap-4">
                
                <!-- Logo & Brand -->
                <div class="flex items-center gap-2 sm:gap-3.5 min-w-0">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-gradient-to-tr from-cyan-600 via-sky-500 to-indigo-600 flex items-center justify-center font-black text-white text-sm sm:text-base shadow-lg shadow-cyan-500/25 ring-1 ring-white/20 flex-shrink-0">
                        BZ
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-1.5 sm:gap-2">
                            <h1 class="text-xs sm:text-base font-extrabold text-slate-900 dark:text-white tracking-tight leading-none truncate">
                                {{ app()->getLocale() === 'ar' ? 'بوابة المندوب' : 'MR Field Portal' }}
                            </h1>
                            <span class="hidden sm:inline-flex px-1.5 py-0.5 rounded text-[10px] font-bold bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 border border-cyan-500/25">
                                BZ-OS
                            </span>
                        </div>
                        <p class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 mt-0.5 flex items-center gap-1 font-medium truncate">
                            <i class="fa-regular fa-calendar-check text-sky-500 text-[10px]"></i>
                            <span class="truncate">{{ $activeCycle->name ?? (app()->getLocale() === 'ar' ? 'الدورة النشطة' : 'Active Cycle') }}</span>
                            @if($activeCycle)
                                <span class="text-slate-400 dark:text-slate-600 hidden md:inline">•</span>
                                <span class="text-[11px] text-slate-500 hidden md:inline">{{ $activeCycle->start_date->format('d M') }} — {{ $activeCycle->end_date->format('d M Y') }}</span>
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Right Controls: GPS Status, Theme Toggle, Profile & Admin -->
                <div class="flex items-center gap-1.5 sm:gap-2 flex-shrink-0">
                    
                    <!-- Interactive GPS Status & Config Button -->
                    <button type="button" onclick="openGpsConfigModal()" id="top-gps-indicator" class="inline-flex items-center gap-1.5 px-2 sm:px-3 py-1.5 rounded-xl text-xs font-semibold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/25 shadow-sm hover:scale-105 active:scale-95 transition cursor-pointer" title="{{ app()->getLocale() === 'ar' ? 'إعدادات وصلاحيات الـ GPS' : 'GPS Geofence Settings & Permissions' }}">
                        <span id="top-gps-pulse" class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        <span id="top-gps-label" class="hidden sm:inline text-[11px] font-bold">
                            {{ app()->getLocale() === 'ar' ? 'GPS نشط' : 'GPS Active' }}
                        </span>
                    </button>

                    <!-- Theme Mode Toggle Button (Light/Dark) -->
                    <button type="button" onclick="toggleTheme()" id="theme-toggle-btn" class="w-8 h-8 sm:w-auto p-1.5 sm:px-3 sm:py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-900 dark:hover:bg-slate-800 border border-slate-300 dark:border-slate-800 text-slate-700 dark:text-slate-200 transition text-xs font-bold flex items-center justify-center gap-1.5 shadow-sm" title="Toggle Light/Dark Theme">
                        <i id="theme-toggle-icon" class="fa-solid fa-sun text-amber-500 text-xs sm:text-sm"></i>
                        <span id="theme-toggle-text" class="hidden md:inline text-[11px]">Light Mode</span>
                    </button>

                    <!-- User Profile Pill -->
                    <div class="hidden md:flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-xs">
                        <div class="w-6 h-6 rounded-full bg-indigo-500/15 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-[11px]">
                            <i class="fa-solid fa-user-doctor"></i>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-bold text-slate-800 dark:text-slate-200 max-w-[120px] truncate leading-tight">{{ $user->name ?? 'Medical Rep' }}</span>
                            @if(!empty($user->area))
                                <span class="text-[10px] text-sky-600 dark:text-sky-400 font-semibold leading-tight flex items-center gap-0.5">
                                    <i class="fa-solid fa-location-dot text-[9px]"></i> {{ $user->area->name }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Language Switcher -->
                    <a href="{{ route('locale.switch', app()->getLocale() === 'ar' ? 'en' : 'ar') }}" class="w-8 h-8 sm:w-auto p-1.5 sm:px-2.5 sm:py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-900 dark:hover:bg-slate-800 border border-slate-300 dark:border-slate-800 text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition text-xs font-bold flex items-center justify-center gap-1.5" title="Switch Language">
                        <i class="fa-solid fa-language text-sky-500 dark:text-sky-400"></i>
                        <span class="hidden sm:inline">{{ app()->getLocale() === 'ar' ? 'EN' : 'عربي' }}</span>
                    </a>

                    <!-- Back to Admin Dashboard -->
                    <a href="{{ route('admin.dashboard') }}" class="w-8 h-8 sm:w-auto p-1.5 sm:px-3 sm:py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-900 dark:hover:bg-slate-800 border border-slate-300 dark:border-slate-800 text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition text-xs font-bold flex items-center justify-center gap-1.5 shadow-sm" title="Admin Overview">
                        <i class="fa-solid fa-arrow-up-right-from-square text-cyan-600 dark:text-cyan-400 text-xs"></i>
                        <span class="hidden lg:inline">{{ app()->getLocale() === 'ar' ? 'لوحة التحكم' : 'Admin' }}</span>
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
            <div class="glass-card rounded-2xl sm:rounded-3xl p-4 sm:p-6 shadow-xl relative overflow-hidden">
                <!-- Background Accent Glow -->
                <div class="absolute -right-20 -bottom-20 w-64 h-64 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 sm:gap-6 items-center">
                    
                    <!-- Rep Greeting & Milestone (5 Cols on Desktop/Tablet) -->
                    <div class="lg:col-span-5 space-y-2.5 sm:space-y-3">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-black bg-cyan-500/15 text-cyan-600 dark:text-cyan-400 border border-cyan-500/30">
                                {{ app()->getLocale() === 'ar' ? 'الميدان الطبي' : 'Field Operations' }}
                            </span>
                            <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">
                                {{ now()->format('l, d F Y') }}
                            </span>
                        </div>

                        <h2 class="text-lg sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                            {{ app()->getLocale() === 'ar' ? 'أهلاً بك، ' : 'Welcome, ' }} {{ $user->name ?? 'Dr. Rep' }}
                        </h2>

                        <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                            {{ app()->getLocale() === 'ar' 
                                ? 'لوحة متابعة الزيارات الميدانية، التحقق من النطاق الجغرافي للعيادات، وتحقيق مستهدف النقاط للدورة الحالية.' 
                                : 'Live field execution, GPS-verified clinic check-ins, and cycle frequency compliance.' }}
                        </p>

                        <!-- Points Target Milestone Progress -->
                        <div class="pt-1 sm:pt-2">
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
                    <div class="lg:col-span-7 grid grid-cols-2 sm:grid-cols-4 gap-2.5 sm:gap-3">
                        
                        <!-- KPI 1: Coverage Rate -->
                        <div class="p-3 sm:p-4 rounded-xl sm:rounded-2xl bg-white/90 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800/80 text-center flex flex-col justify-between hover:border-slate-300 dark:hover:border-slate-700 transition shadow-sm h-full">
                            <div class="flex items-center justify-center w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-sky-500/10 text-sky-600 dark:text-sky-400 mx-auto mb-1.5 sm:mb-2">
                                <i class="fa-solid fa-chart-pie text-xs sm:text-sm"></i>
                            </div>
                            <div class="text-lg sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                                {{ $snapshot->coverage_rate_pct ?? 0 }}%
                            </div>
                            <div class="text-[11px] text-slate-600 dark:text-slate-400 font-semibold mt-1">
                                {{ app()->getLocale() === 'ar' ? 'نسبة التغطية' : 'Coverage Rate' }}
                            </div>
                            <div class="text-[10px] text-slate-500 mt-0.5">
                                {{ $snapshot->unique_contacts_visited ?? 0 }}/{{ $snapshot->total_assigned_contacts ?? 0 }} {{ app()->getLocale() === 'ar' ? 'أطباء' : 'doctors' }}
                            </div>
                        </div>

                        <!-- KPI 2: Completed Visits (Clickable -> Opens Doctors Done Modal) -->
                        <div onclick="openVisitsDoneModal()" class="cursor-pointer group p-3 sm:p-4 rounded-xl sm:rounded-2xl bg-white/90 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800/80 text-center flex flex-col justify-between hover:border-emerald-500 dark:hover:border-emerald-500 hover:shadow-lg hover:shadow-emerald-500/10 hover:-translate-y-0.5 active:scale-95 transition-all shadow-sm relative overflow-hidden h-full" title="{{ app()->getLocale() === 'ar' ? 'اضغط لعرض تفاصيل الأطباء الذين تمت زيارتهم' : 'Click to view visited doctors' }}">
                            <div class="absolute top-2 right-2 rtl:right-auto rtl:left-2 opacity-60 group-hover:opacity-100 transition-opacity">
                                <span class="p-1 rounded bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 text-[9px]">
                                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                </span>
                            </div>
                            <div class="flex items-center justify-center w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 mx-auto mb-1.5 sm:mb-2 group-hover:bg-emerald-500 group-hover:text-white transition-colors">
                                <i class="fa-solid fa-calendar-check text-xs sm:text-sm"></i>
                            </div>
                            <div class="text-lg sm:text-2xl font-black text-emerald-600 dark:text-emerald-400 tracking-tight">
                                {{ $snapshot->visits_done ?? 0 }} <span class="text-xs text-slate-400 dark:text-slate-500 font-normal">/ {{ $snapshot->planned_visits ?? 0 }}</span>
                            </div>
                            <div class="text-[11px] text-slate-600 dark:text-slate-400 font-semibold mt-1">
                                {{ app()->getLocale() === 'ar' ? 'الزيارات المنفذة' : 'Visits Done' }}
                            </div>
                            <div class="text-[10px] text-emerald-600 dark:text-emerald-400 font-bold mt-0.5 group-hover:underline">
                                {{ app()->getLocale() === 'ar' ? 'عرض المكتملين' : 'View Visited' }} &rarr;
                            </div>
                        </div>

                        <!-- KPI 3: Achieved Points -->
                        <div class="p-3 sm:p-4 rounded-xl sm:rounded-2xl bg-white/90 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800/80 text-center flex flex-col justify-between hover:border-slate-300 dark:hover:border-slate-700 transition shadow-sm h-full">
                            <div class="flex items-center justify-center w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 mx-auto mb-1.5 sm:mb-2">
                                <i class="fa-solid fa-ranking-star text-xs sm:text-sm"></i>
                            </div>
                            <div class="text-lg sm:text-2xl font-black text-indigo-600 dark:text-indigo-400 tracking-tight">
                                {{ $snapshot->achieved_points ?? 0 }}
                            </div>
                            <div class="text-[11px] text-slate-600 dark:text-slate-400 font-semibold mt-1">
                                {{ app()->getLocale() === 'ar' ? 'النقاط المكتسبة' : 'Points Earned' }}
                            </div>
                            <div class="text-[10px] text-slate-500 mt-0.5">
                                {{ app()->getLocale() === 'ar' ? 'محتسبة بنظام الفئات' : 'Capped tier pts' }}
                            </div>
                        </div>

                        <!-- KPI 4: GPS Accuracy -->
                        <div class="p-3 sm:p-4 rounded-xl sm:rounded-2xl bg-white/90 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800/80 text-center flex flex-col justify-between hover:border-slate-300 dark:hover:border-slate-700 transition shadow-sm h-full">
                            <div class="flex items-center justify-center w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 mx-auto mb-1.5 sm:mb-2">
                                <i class="fa-solid fa-location-crosshairs text-xs sm:text-sm"></i>
                            </div>
                            <div class="text-lg sm:text-2xl font-black text-cyan-600 dark:text-cyan-400 tracking-tight">
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

            {{-- 3. Responsive Multi-Column Layout (Tablet & Desktop Split) --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                
                {{-- LEFT PRIMARY COLUMN (8 Cols): Agenda & Doctor Hub --}}
                <div class="lg:col-span-8 space-y-6">

                    {{-- Today's Agenda Section --}}
                    <div class="glass-card rounded-2xl sm:rounded-3xl p-4 sm:p-6 space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800/80 pb-3.5 flex-wrap gap-2">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-sky-500/10 text-sky-600 dark:text-sky-400 flex items-center justify-center text-sm flex-shrink-0">
                                    <i class="fa-solid fa-list-check"></i>
                                </div>
                                <div>
                                    <h3 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white leading-tight">
                                        {{ app()->getLocale() === 'ar' ? 'جدول زيارات اليوم' : "Today's Field Agenda" }}
                                    </h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ count($todayVisits) }} {{ app()->getLocale() === 'ar' ? 'زيارات مجدولة' : 'scheduled appointments' }}</p>
                                </div>
                            </div>

                            <button onclick="openScheduleModal()" class="px-3 py-1.5 rounded-xl bg-[#0A4F78] hover:bg-[#062B49] text-white text-xs font-bold transition flex items-center gap-1.5 shadow-sm">
                                <i class="fa-solid fa-plus text-xs"></i>
                                <span>{{ app()->getLocale() === 'ar' ? 'جدولة زيارة' : 'Schedule Visit' }}</span>
                            </button>
                        </div>

                        <div class="space-y-3">
                            @forelse($todayVisits as $sv)
                                @php
                                    $c = $sv->contact;
                                    $cl = $c?->classification;
                                    $quota = $c?->quota_info;
                                    $isCompleted = ($sv->status === 'completed');
                                @endphp
                                <div class="p-3.5 sm:p-4 rounded-2xl bg-white/90 dark:bg-slate-900/80 border {{ $isCompleted ? 'border-emerald-200 dark:border-emerald-900/40 bg-emerald-50/20' : 'border-slate-200 dark:border-slate-800/90' }} hover:border-slate-300 dark:hover:border-slate-700 transition shadow-sm space-y-3">
                                    
                                    <!-- Top Row: Time, Identity & Badges -->
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="flex items-start gap-3 min-w-0">
                                            <!-- Time Badge -->
                                            <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl {{ $isCompleted ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-slate-100 dark:bg-slate-800 text-sky-600 dark:text-sky-400' }} border border-slate-200 dark:border-slate-700/80 flex flex-col items-center justify-center flex-shrink-0">
                                                <span class="text-xs font-black">{{ $sv->scheduled_at->format('H:i') }}</span>
                                                <span class="text-[9px] text-slate-500 dark:text-slate-400 uppercase font-bold">{{ $sv->scheduled_at->format('A') }}</span>
                                            </div>

                                            <div class="min-w-0 flex-1">
                                                <div class="flex items-center gap-1.5 sm:gap-2 flex-wrap">
                                                    <h4 class="text-sm font-bold text-slate-900 dark:text-white truncate">
                                                        {{ $c?->name }}
                                                    </h4>
                                                    @if($cl)
                                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-black {{ $cl->code === 'A+' ? 'bg-amber-500/20 text-amber-700 dark:text-amber-300 border border-amber-500/30' : ($cl->code === 'A' ? 'bg-sky-500/20 text-sky-700 dark:text-sky-300 border border-sky-500/30' : 'bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300') }}">
                                                            Class {{ $cl->code }}
                                                        </span>
                                                    @endif
                                                    @if($quota)
                                                        @php
                                                            $qCur = min((int)$quota['current_count'], (int)$quota['max_visits']);
                                                            $qMax = (int)$quota['max_visits'];
                                                            $qMet = ($quota['current_count'] >= $qMax);
                                                        @endphp
                                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold {{ $qMet ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300' }}" title="{{ $qCur }}/{{ $qMax }} visits in cycle">
                                                            <i class="fa-solid fa-bullseye text-[9px]"></i>
                                                            {{ $qCur }}/{{ $qMax }}
                                                        </span>
                                                    @endif
                                                </div>

                                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 truncate">
                                                    {{ $c?->hospital_clinic_name }} • {{ $c?->specialty?->name }}
                                                </p>

                                                @if($c?->phone)
                                                    <a href="tel:{{ $c->phone }}" class="inline-flex items-center gap-1 text-[11px] text-sky-600 dark:text-sky-400 font-medium hover:underline mt-0.5">
                                                        <i class="fa-solid fa-phone text-[9px]"></i>
                                                        <span>{{ $c->phone }}</span>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Status Badge (Desktop) -->
                                        <div class="hidden sm:block flex-shrink-0">
                                            @if($isCompleted)
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                                    <i class="fa-solid fa-check-double text-[10px]"></i>
                                                    {{ app()->getLocale() === 'ar' ? 'تمت الزيارة' : 'Completed' }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                                    <i class="fa-regular fa-clock text-[10px]"></i>
                                                    {{ app()->getLocale() === 'ar' ? 'مجدولة' : 'Planned' }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    @if(!empty($sv->notes))
                                        <div class="text-[11px] text-slate-600 dark:text-slate-400 italic bg-slate-50 dark:bg-slate-800/50 p-2 rounded-lg border border-slate-100 dark:border-slate-800">
                                            "{{ Str::limit($sv->notes, 80) }}"
                                        </div>
                                    @endif

                                    <!-- Bottom Action Controls -->
                                    <div class="flex items-center justify-between sm:justify-end gap-2 pt-2 border-t border-slate-100 dark:border-slate-800/80">
                                        <!-- Status indicator on Mobile -->
                                        <div class="sm:hidden">
                                            @if($isCompleted)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[10px] font-bold bg-emerald-500/15 text-emerald-600 dark:text-emerald-400">
                                                    <i class="fa-solid fa-check text-[9px]"></i> {{ app()->getLocale() === 'ar' ? 'تمت' : 'Done' }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-500">
                                                    {{ app()->getLocale() === 'ar' ? 'مجدولة' : 'Planned' }}
                                                </span>
                                            @endif
                                        </div>

                                        <div class="flex items-center gap-1.5 flex-1 sm:flex-none justify-end">
                                            @if($c?->latitude && $c?->longitude)
                                                <a href="https://www.google.com/maps?q={{ $c->latitude }},{{ $c->longitude }}" target="_blank" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-400 flex items-center justify-center text-xs border border-slate-200 dark:border-slate-700/60 transition" title="Directions">
                                                    <i class="fa-solid fa-diamond-turn-right text-sky-500"></i>
                                                </a>
                                            @endif

                                            @if($isCompleted)
                                                <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 px-3 py-1.5 flex items-center gap-1.5 bg-emerald-500/10 rounded-xl border border-emerald-500/20">
                                                    <i class="fa-solid fa-circle-check"></i> {{ app()->getLocale() === 'ar' ? 'تمت الزيارة بنجاح' : 'Visit Completed' }}
                                                </span>
                                            @else
                                                <button type="button" onclick="openDirectVisitModal({{ $sv->contact_id }}, '{{ addslashes($c?->name ?? 'Doctor') }}', {{ $sv->assignment_id }}, {{ $sv->id }}, 'not_visited')" class="px-2.5 sm:px-3 py-2 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 border border-rose-500/30 text-xs font-bold transition active:scale-95 flex items-center justify-center gap-1.5" title="{{ app()->getLocale() === 'ar' ? 'تسجيل عدم الزيارة مع توضيح السبب' : 'Record non-visit with reason' }}">
                                                    <i class="fa-solid fa-circle-xmark text-xs"></i>
                                                    <span class="whitespace-nowrap">{{ app()->getLocale() === 'ar' ? 'عدم الزيارة' : 'Don\'t Visit' }}</span>
                                                </button>
                                                <button type="button" onclick="openDirectVisitModal({{ $sv->contact_id }}, '{{ addslashes($c?->name ?? 'Doctor') }}', {{ $sv->assignment_id }}, {{ $sv->id }}, 'visited')" class="flex-1 sm:flex-none px-3.5 sm:px-4 py-2 rounded-xl bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold text-xs active:scale-95 transition shadow-md shadow-emerald-600/20 flex items-center justify-center gap-1.5 whitespace-nowrap">
                                                    <i class="fa-solid fa-clipboard-check text-xs"></i>
                                                    <span>{{ app()->getLocale() === 'ar' ? 'تسجيل زيارة (تمت)' : 'Visit Done' }}</span>
                                                </button>
                                            @endif
                                        </div>
                                    </div>rounded-xl bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold text-xs active:scale-95 transition shadow-md shadow-emerald-600/20 flex items-center justify-center gap-1.5 whitespace-nowrap">
                                                <i class="fa-solid fa-clipboard-check text-xs"></i>
                                                <span>{{ app()->getLocale() === 'ar' ? 'تسجيل زيارة (تمت)' : 'Visit Done' }}</span>
                                            </button>
                                        </div>
                                    </div>

                                </div>
                            @empty
                                <div class="p-8 rounded-2xl bg-slate-50 dark:bg-slate-900/40 border border-slate-200 dark:border-slate-800/80 text-center">
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

                    {{-- Doctor Portfolio Hub Section (Responsive Mobile-First Cards) --}}
                    <div class="glass-card rounded-2xl sm:rounded-3xl p-4 sm:p-6 space-y-4">
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
                            <div class="relative w-full sm:w-72">
                                <i class="fa-solid fa-magnifying-glass absolute left-3.5 rtl:left-auto rtl:right-3.5 top-1/2 -translate-y-1/2 text-xs text-slate-400 dark:text-slate-500 pointer-events-none"></i>
                                <input type="text" id="doctor-search-input" onkeyup="filterDoctors()" placeholder="{{ app()->getLocale() === 'ar' ? 'بحث باسم الطبيب، المركز، الفئة...' : 'Filter doctors, clinic, specialty...' }}" class="w-full pl-9 rtl:pl-3 rtl:pr-9 pr-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-800 text-xs text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-slate-500 focus:outline-none focus:border-sky-500 transition shadow-sm">
                            </div>
                        </div>

                        <!-- Filter Chips (Touch Scrollable) -->
                        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs no-scrollbar flex-nowrap -mx-1 px-1">
                            <button onclick="filterByClass('all')" class="chip-filter active px-3 py-1.5 rounded-xl bg-sky-600 text-white font-bold transition shadow-sm whitespace-nowrap flex-shrink-0">
                                {{ app()->getLocale() === 'ar' ? 'الكل' : 'All' }} ({{ count($allAssignments) }})
                            </button>
                            <button onclick="filterByClass('A+')" class="chip-filter px-3 py-1.5 rounded-xl bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-bold transition border border-slate-300 dark:border-slate-700/60 whitespace-nowrap flex-shrink-0">
                                Class A+
                            </button>
                            <button onclick="filterByClass('A')" class="chip-filter px-3 py-1.5 rounded-xl bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-bold transition border border-slate-300 dark:border-slate-700/60 whitespace-nowrap flex-shrink-0">
                                Class A
                            </button>
                            <button onclick="filterByClass('B')" class="chip-filter px-3 py-1.5 rounded-xl bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-bold transition border border-slate-300 dark:border-slate-700/60 whitespace-nowrap flex-shrink-0">
                                Class B
                            </button>
                            <button onclick="filterByClass('C')" class="chip-filter px-3 py-1.5 rounded-xl bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-bold transition border border-slate-300 dark:border-slate-700/60 whitespace-nowrap flex-shrink-0">
                                Class C
                            </button>
                            <button onclick="filterByStatus('done')" class="chip-filter px-3 py-1.5 rounded-xl bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-bold transition border border-slate-300 dark:border-slate-700/60 flex items-center gap-1 whitespace-nowrap flex-shrink-0">
                                <i class="fa-solid fa-circle-check text-emerald-500"></i>
                                <span>{{ app()->getLocale() === 'ar' ? 'تمت زيارتهم' : 'Visited' }}</span>
                            </button>
                        </div>

                        <!-- Doctor Cards: Vertical stacked cards with responsive actions -->
                        <div id="doctors-container" class="space-y-3.5 pt-1">
                            @foreach($allAssignments as $item)
                                @php
                                    $c = $item->contact;
                                    $cl = $c?->classification;
                                    $req = $cl ? (int)$cl->required_visits : (int)$item->target_visits;
                                    $done = (int)$item->visits_done;
                                    $pct = $req > 0 ? min(100, round(($done / $req) * 100)) : 0;
                                    $quota = $c?->quota_info;
                                @endphp
                                <div class="doctor-card p-4 sm:p-5 rounded-2xl bg-white/90 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800/80 hover:border-sky-500/40 dark:hover:border-sky-500/40 transition-all flex flex-col md:flex-row md:items-center justify-between gap-3 sm:gap-4 shadow-sm"
                                     data-name="{{ strtolower($c?->name ?? '') }}"
                                     data-clinic="{{ strtolower($c?->hospital_clinic_name ?? '') }}"
                                     data-specialty="{{ strtolower($c?->specialty?->name ?? '') }}"
                                     data-class="{{ $cl?->code ?? 'C' }}"
                                     data-status="{{ $done > 0 ? 'done' : 'pending' }}">
                                    
                                    <!-- Left Section: Doctor Identity & Meta -->
                                    <div class="flex items-start sm:items-center gap-3.5 min-w-0 flex-1">
                                        <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl {{ $done >= $req ? 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' : 'bg-sky-500/10 text-sky-600 dark:text-sky-400 border border-sky-500/20' }} flex items-center justify-center font-black text-base flex-shrink-0">
                                            <i class="fa-solid fa-user-doctor"></i>
                                        </div>

                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center gap-1.5 sm:gap-2 flex-wrap">
                                                <h4 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white hover:text-sky-600 dark:hover:text-sky-400 transition truncate">
                                                    {{ $c?->name }}
                                                </h4>
                                                @if($cl)
                                                    <span class="px-2 py-0.5 rounded text-[10px] font-black {{ $cl->code === 'A+' ? 'bg-amber-500/20 text-amber-700 dark:text-amber-300 border border-amber-500/30' : ($cl->code === 'A' ? 'bg-sky-500/20 text-sky-700 dark:text-sky-300 border border-sky-500/30' : 'bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-700') }}">
                                                        Class {{ $cl->code }}
                                                    </span>
                                                @endif
                                                @if($quota)
                                                    @php
                                                        $qCur = min((int)$quota['current_count'], (int)$quota['max_visits']);
                                                        $qMax = (int)$quota['max_visits'];
                                                        $qMet = ($quota['current_count'] >= $qMax);
                                                    @endphp
                                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold {{ $qMet ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300' }}" title="{{ $qCur }}/{{ $qMax }} visits in cycle">
                                                        {{ $qCur }}/{{ $qMax }}
                                                    </span>
                                                @endif
                                            </div>

                                            <p class="text-xs text-slate-500 dark:text-slate-400 truncate mt-0.5">
                                                {{ $c?->hospital_clinic_name }} • {{ $c?->specialty?->name ?? 'General' }}
                                            </p>

                                            @if($c?->city)
                                                <span class="text-[11px] text-slate-400 truncate block">
                                                    {{ $c->city->name }} {{ $c->address ? '• ' . $c->address : '' }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Center Section: Progress & Frequency Bar -->
                                    <div class="w-full md:w-52 flex-shrink-0 pt-2.5 md:pt-0 border-t md:border-t-0 border-slate-200/60 dark:border-slate-800/60">
                                        <div class="flex items-center justify-between text-xs font-semibold mb-1">
                                            <span class="text-slate-500 dark:text-slate-400">
                                                {{ app()->getLocale() === 'ar' ? 'الزيارات:' : 'Visits:' }} 
                                                <strong class="text-slate-900 dark:text-white font-bold">{{ min($done, $req) }}/{{ $req }}</strong>
                                            </span>
                                            <span class="text-sky-600 dark:text-sky-400 font-bold text-xs">
                                                {{ $item->achieved_points }}/{{ $item->target_points }} pts
                                            </span>
                                        </div>
                                        <div class="w-full bg-slate-200 dark:bg-slate-800 rounded-full h-2 overflow-hidden p-0.5 border border-slate-300/60 dark:border-slate-700/60">
                                            <div class="h-full rounded-full transition-all duration-500 {{ $pct >= 100 ? 'bg-emerald-500' : ($pct > 0 ? 'bg-sky-500' : 'bg-slate-400 dark:bg-slate-600') }}" style="width: {{ $pct }}%"></div>
                                        </div>
                                        <div class="flex items-center justify-between text-[10px] text-slate-400 dark:text-slate-500 mt-1">
                                            <span>{{ $pct }}% {{ app()->getLocale() === 'ar' ? 'مكتمل' : 'completed' }}</span>
                                            @if($done >= $req)
                                                <span class="text-emerald-500 font-bold">✓ {{ app()->getLocale() === 'ar' ? 'اكتملت الخطة' : 'Goal met' }}</span>
                                            @elseif($done > 0)
                                                <span class="text-sky-500 font-semibold">{{ $done }} {{ app()->getLocale() === 'ar' ? 'زيارة' : 'done' }}</span>
                                            @else
                                                <span class="text-slate-400">{{ app()->getLocale() === 'ar' ? 'لم تبدأ' : 'Not started' }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Right Section: Actions Row (Touch Friendly) -->
                                    <div class="flex items-center gap-1.5 sm:gap-2 w-full md:w-auto justify-end flex-shrink-0 pt-2.5 md:pt-0 border-t md:border-t-0 border-slate-200/60 dark:border-slate-800/60">
                                        @if($c?->phone)
                                            <a href="tel:{{ $c->phone }}" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 flex items-center justify-center text-xs border border-slate-200 dark:border-slate-700/50 shadow-sm transition" title="Call">
                                                <i class="fa-solid fa-phone"></i>
                                            </a>
                                        @endif

                                        @if($c?->latitude && $c?->longitude)
                                            <a href="https://www.google.com/maps?q={{ $c->latitude }},{{ $c->longitude }}" target="_blank" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 flex items-center justify-center text-xs border border-slate-200 dark:border-slate-700/50 shadow-sm transition" title="Open Google Maps">
                                                <i class="fa-solid fa-location-arrow text-sky-500 dark:text-sky-400"></i>
                                            </a>
                                        @endif

                                        <button onclick="openScheduleModal({{ $item->id }}, '{{ addslashes($c?->name ?? 'Doctor') }}')" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 flex items-center justify-center text-xs border border-slate-200 dark:border-slate-700/50 shadow-sm transition flex-shrink-0" title="Schedule appointment">
                                            <i class="fa-regular fa-calendar-plus"></i>
                                        </button>

                                        @if($done >= $req)
                                            <span class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 shadow-sm whitespace-nowrap">
                                                <i class="fa-solid fa-circle-check text-xs"></i>
                                                <span>{{ app()->getLocale() === 'ar' ? 'اكتملت الخطة' : 'Goal Met' }}</span>
                                            </span>
                                        @else
                                            <button type="button" onclick="openDirectVisitModal({{ $c->id }}, '{{ addslashes($c?->name ?? 'Doctor') }}', {{ $item->id }}, null, 'not_visited')" class="px-2.5 sm:px-3 py-2 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 border border-rose-500/30 text-xs font-bold transition active:scale-95 flex items-center justify-center gap-1.5 whitespace-nowrap" title="{{ app()->getLocale() === 'ar' ? 'تسجيل عدم الزيارة مع توضيح السبب' : 'Record non-visit with details' }}">
                                                <i class="fa-solid fa-circle-xmark text-xs"></i>
                                                <span>{{ app()->getLocale() === 'ar' ? 'عدم الزيارة' : 'Don\'t Visit' }}</span>
                                            </button>

                                            <button type="button" onclick="openDirectVisitModal({{ $c->id }}, '{{ addslashes($c?->name ?? 'Doctor') }}', {{ $item->id }}, null, 'visited')" class="flex-1 md:flex-initial py-2 px-3.5 sm:px-4 rounded-xl bg-gradient-to-r from-cyan-600 via-sky-600 to-indigo-600 hover:from-cyan-500 hover:to-indigo-500 text-white font-bold text-xs transition active:scale-95 shadow-md shadow-cyan-600/20 flex items-center justify-center gap-1.5 whitespace-nowrap">
                                                <i class="fa-solid fa-clipboard-check text-xs"></i>
                                                <span>{{ app()->getLocale() === 'ar' ? 'تسجيل زيارة' : 'Visit Done' }}</span>
                                            </button>
                                        @endif
                                    </div>

                                </div>
                            @endforeach
                        </div>

                        <!-- Empty State when no results match search or filters -->
                        <div id="portfolio-empty-state" class="hidden p-8 rounded-2xl bg-slate-50 dark:bg-slate-900/40 border border-slate-200 dark:border-slate-800/80 text-center">
                            <div class="w-12 h-12 rounded-full bg-slate-200 dark:bg-slate-800 text-slate-400 flex items-center justify-center mx-auto mb-2 text-xl">
                                <i class="fa-solid fa-user-slash"></i>
                            </div>
                            <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">
                                {{ app()->getLocale() === 'ar' ? 'لا يوجد أطباء يطابقون خيارات البحث أو التصفية الحالية.' : 'No doctors match the selected search or filter criteria.' }}
                            </p>
                            <button type="button" onclick="resetPortfolioFilters()" class="mt-3 px-4 py-1.5 rounded-xl bg-sky-600 hover:bg-sky-500 text-white text-xs font-bold transition shadow-sm inline-flex items-center gap-1.5">
                                <i class="fa-solid fa-arrows-rotate text-xs"></i>
                                <span>{{ app()->getLocale() === 'ar' ? 'إعادة تعيين الفلاتر' : 'Reset Filters' }}</span>
                            </button>
                        </div>

                        <!-- Real-time Responsive Pagination Toolbar -->
                        <div id="portfolio-pagination-bar" class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-3 border-t border-slate-200 dark:border-slate-800/80">
                            <!-- Left: Range & Per Page Selector -->
                            <div class="flex items-center gap-3 text-xs text-slate-500 dark:text-slate-400 w-full sm:w-auto justify-between sm:justify-start">
                                <span id="portfolio-range-text" class="font-medium">
                                    {{ app()->getLocale() === 'ar' ? 'عرض 1 إلى 5 من 8 أطباء' : 'Showing 1 to 5 of 8 doctors' }}
                                </span>
                                <div class="flex items-center gap-1.5">
                                    <label for="portfolio-per-page" class="text-[11px] font-semibold text-slate-600 dark:text-slate-400">
                                        {{ app()->getLocale() === 'ar' ? 'لكل صفحة:' : 'Per page:' }}
                                    </label>
                                    <select id="portfolio-per-page" onchange="changePortfolioPerPage(this.value)" class="px-2 py-1 rounded-lg bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-xs font-bold text-slate-800 dark:text-slate-200 focus:outline-none focus:border-sky-500 cursor-pointer shadow-sm">
                                        <option value="5" selected>5</option>
                                        <option value="10">10</option>
                                        <option value="20">20</option>
                                        <option value="50">50</option>
                                        <option value="999">{{ app()->getLocale() === 'ar' ? 'الكل' : 'All' }}</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Right: Dynamic Page Buttons -->
                            <div id="portfolio-pagination-controls" class="flex items-center gap-1 w-full sm:w-auto justify-center sm:justify-end">
                                <!-- Populated dynamically by PortfolioPaginationEngine -->
                            </div>
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

                                        <div class="flex items-center gap-1.5 flex-shrink-0">
                                            <button type="button" onclick="openDirectVisitModal({{ $assign->contact_id }}, '{{ addslashes($assign->contact?->name ?? 'Doctor') }}', {{ $assign->id }}, null, 'not_visited')" class="px-2 py-1.5 rounded-lg bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 hover:bg-rose-100 text-[11px] font-bold transition border border-rose-200 dark:border-rose-900/50" title="{{ app()->getLocale() === 'ar' ? 'عدم الزيارة' : 'Don\'t Visit' }}">
                                                <i class="fa-solid fa-circle-xmark"></i>
                                            </button>
                                            <button type="button" onclick="openDirectVisitModal({{ $assign->contact_id }}, '{{ addslashes($assign->contact?->name ?? 'Doctor') }}', {{ $assign->id }}, null, 'visited')" class="px-3 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-500 active:scale-95 text-white font-bold text-xs transition flex items-center gap-1">
                                                <i class="fa-solid fa-clipboard-check text-[11px]"></i>
                                                <span>{{ app()->getLocale() === 'ar' ? 'زيارة' : 'Visit' }}</span>
                                            </button>
                                        </div>
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
    <div id="gps-config-modal" class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-md hidden flex items-center justify-center p-3 sm:p-4">
        <div class="glass-card bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl w-full max-w-lg p-4 sm:p-8 space-y-5 sm:space-y-6 shadow-2xl border border-slate-200 dark:border-slate-700/80 relative max-h-[90vh] overflow-y-auto">
            
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

    {{-- ================= Unified Direct Visit & Don't Visit Execution Modal ================= --}}
    <div id="direct-visit-modal" class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-md hidden flex items-center justify-center p-3 sm:p-4 overflow-y-auto">
        <div class="glass-card bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl w-full max-w-lg p-4 sm:p-6 space-y-4 shadow-2xl border border-slate-200 dark:border-slate-700/80 relative my-auto max-h-[92vh] flex flex-col">
            
            <!-- Modal Header -->
            <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3 flex-shrink-0">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div id="direct-modal-header-icon" class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-500 text-white flex items-center justify-center text-lg flex-shrink-0 shadow-md shadow-emerald-600/20">
                        <i class="fa-solid fa-clipboard-check"></i>
                    </div>
                    <div class="min-w-0">
                        <h3 class="font-black text-slate-900 dark:text-white text-base truncate">
                            {{ app()->getLocale() === 'ar' ? 'تسجيل وتوثيق الزيارة الميدانية' : 'Field Visit Execution & Log' }}
                        </h3>
                        <p id="direct-visit-doc-banner" class="text-xs text-slate-500 dark:text-slate-400 font-medium truncate mt-0.5"></p>
                    </div>
                </div>
                <button type="button" onclick="closeDirectVisitModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-white transition p-1.5 rounded-lg">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Hidden Inputs for Form State -->
            <input type="hidden" id="direct-visit-contact-id">
            <input type="hidden" id="direct-visit-assignment-id">
            <input type="hidden" id="direct-visit-scheduled-id">
            <input type="hidden" id="direct-visit-type" value="visited">

            <!-- Mode Selector Tabs: [Visited / Done] vs [Don't Visit / Unvisited] -->
            <div class="grid grid-cols-2 gap-2 p-1 rounded-xl bg-slate-100 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700/60 flex-shrink-0">
                <button type="button" id="direct-tab-visited" onclick="switchDirectVisitTab('visited')" class="py-2 px-3 rounded-lg font-bold text-xs transition flex items-center justify-center gap-1.5 bg-white dark:bg-slate-900 text-emerald-600 dark:text-emerald-400 shadow-sm border border-emerald-500/20">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'تمت الزيارة بنجاح' : 'Visited & Completed' }}</span>
                </button>
                <button type="button" id="direct-tab-not-visited" onclick="switchDirectVisitTab('not_visited')" class="py-2 px-3 rounded-lg font-bold text-xs transition flex items-center justify-center gap-1.5 text-slate-600 dark:text-slate-400 hover:text-rose-600 dark:hover:text-rose-400">
                    <i class="fa-solid fa-circle-xmark"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'لم تتم الزيارة (عدم زيارة)' : 'Don\'t Visit / Unvisited' }}</span>
                </button>
            </div>

            <!-- Modal Scrollable Body -->
            <div class="flex-1 overflow-y-auto space-y-4 pr-1">

                {{-- ================= SECTION 1: VISITED (DONE) ================= --}}
                <div id="direct-visited-section" class="space-y-4">
                    
                    <!-- Outcome Dropdown -->
                    <div class="relative" id="direct-outcome-wrapper">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase">
                            {{ app()->getLocale() === 'ar' ? 'مخرجات ونتيجة المقابلة *' : 'Meeting Outcome *' }}
                        </label>

                        <input type="hidden" id="direct-visit-outcome" value="completed">

                        <button type="button" id="direct-outcome-btn" onclick="toggleDirectOutcomeDropdown()" class="w-full flex items-center justify-between gap-3 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white p-3 focus:outline-none focus:border-emerald-500 hover:border-slate-400 dark:hover:border-slate-600 transition shadow-sm select-none">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <span id="direct-outcome-selected-icon" class="w-6 h-6 rounded-lg bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs flex-shrink-0">
                                    <i class="fa-solid fa-circle-check"></i>
                                </span>
                                <span id="direct-outcome-selected-text" class="font-semibold truncate text-slate-800 dark:text-slate-200">
                                    {{ app()->getLocale() === 'ar' ? 'مقابلة ناجحة ومناقشة علمية تفصيلية' : 'Successful Meeting & Clinical Discussion' }}
                                </span>
                            </div>
                            <i id="direct-outcome-chevron" class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-200"></i>
                        </button>

                        <div id="direct-outcome-menu" class="hidden absolute top-full left-0 right-0 z-50 mt-1.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700/80 rounded-xl shadow-2xl overflow-hidden divide-y divide-slate-100 dark:divide-slate-800/60 transition-all">
                            <div onclick="selectDirectOutcome('completed', 'fa-circle-check', 'emerald', '{{ addslashes(app()->getLocale() === 'ar' ? 'مقابلة ناجحة ومناقشة علمية تفصيلية' : 'Successful Meeting & Clinical Discussion') }}')" 
                                 class="outcome-option flex items-center justify-between gap-3 p-3 hover:bg-slate-50 dark:hover:bg-slate-800/80 cursor-pointer transition select-none group"
                                 data-value="completed">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <span class="w-7 h-7 rounded-lg bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-sm flex-shrink-0">
                                        <i class="fa-solid fa-circle-check"></i>
                                    </span>
                                    <span class="text-xs font-semibold text-slate-800 dark:text-slate-200">
                                        {{ app()->getLocale() === 'ar' ? 'مقابلة ناجحة ومناقشة علمية تفصيلية' : 'Successful Meeting & Clinical Discussion' }}
                                    </span>
                                </div>
                                <i class="outcome-check fa-solid fa-check text-emerald-500 text-xs"></i>
                            </div>

                            <div onclick="selectDirectOutcome('sample_delivered', 'fa-capsules', 'sky', '{{ addslashes(app()->getLocale() === 'ar' ? 'تسليم عينات ومواد ترويجية' : 'Samples & Marketing Materials Delivered') }}')" 
                                 class="outcome-option flex items-center justify-between gap-3 p-3 hover:bg-slate-50 dark:hover:bg-slate-800/80 cursor-pointer transition select-none group"
                                 data-value="sample_delivered">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <span class="w-7 h-7 rounded-lg bg-sky-500/15 text-sky-600 dark:text-sky-400 flex items-center justify-center text-sm flex-shrink-0">
                                        <i class="fa-solid fa-capsules"></i>
                                    </span>
                                    <span class="text-xs font-semibold text-slate-800 dark:text-slate-200">
                                        {{ app()->getLocale() === 'ar' ? 'تسليم عينات ومواد ترويجية' : 'Samples & Marketing Materials Delivered' }}
                                    </span>
                                </div>
                                <i class="outcome-check hidden fa-solid fa-check text-sky-500 text-xs"></i>
                            </div>

                            <div onclick="selectDirectOutcome('follow_up_needed', 'fa-calendar-check', 'indigo', '{{ addslashes(app()->getLocale() === 'ar' ? 'تحديد موعد متابعة قادم' : 'Follow-Up Needed / Action Assigned') }}')" 
                                 class="outcome-option flex items-center justify-between gap-3 p-3 hover:bg-slate-50 dark:hover:bg-slate-800/80 cursor-pointer transition select-none group"
                                 data-value="follow_up_needed">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <span class="w-7 h-7 rounded-lg bg-indigo-500/15 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-sm flex-shrink-0">
                                        <i class="fa-solid fa-calendar-check"></i>
                                    </span>
                                    <span class="text-xs font-semibold text-slate-800 dark:text-slate-200">
                                        {{ app()->getLocale() === 'ar' ? 'تحديد موعد متابعة قادم' : 'Follow-Up Needed / Action Assigned' }}
                                    </span>
                                </div>
                                <i class="outcome-check hidden fa-solid fa-check text-indigo-500 text-xs"></i>
                            </div>

                            <div onclick="selectDirectOutcome('order_placed', 'fa-cart-shopping', 'violet', '{{ addslashes(app()->getLocale() === 'ar' ? 'طلب شراء / اهتمام عالٍ بالطلب' : 'Order Placed / High Purchasing Interest') }}')" 
                                 class="outcome-option flex items-center justify-between gap-3 p-3 hover:bg-slate-50 dark:hover:bg-slate-800/80 cursor-pointer transition select-none group"
                                 data-value="order_placed">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <span class="w-7 h-7 rounded-lg bg-violet-500/15 text-violet-600 dark:text-violet-400 flex items-center justify-center text-sm flex-shrink-0">
                                        <i class="fa-solid fa-cart-shopping"></i>
                                    </span>
                                    <span class="text-xs font-semibold text-slate-800 dark:text-slate-200">
                                        {{ app()->getLocale() === 'ar' ? 'طلب شراء / اهتمام عالٍ بالطلب' : 'Order Placed / High Purchasing Interest' }}
                                    </span>
                                </div>
                                <i class="outcome-check hidden fa-solid fa-check text-violet-500 text-xs"></i>
                            </div>

                            <div onclick="selectDirectOutcome('doctor_busy', 'fa-hourglass-half', 'amber', '{{ addslashes(app()->getLocale() === 'ar' ? 'الطبيب منشغل / مقابلة سريعة' : 'Doctor Busy / Brief Presentation') }}')" 
                                 class="outcome-option flex items-center justify-between gap-3 p-3 hover:bg-slate-50 dark:hover:bg-slate-800/80 cursor-pointer transition select-none group"
                                 data-value="doctor_busy">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <span class="w-7 h-7 rounded-lg bg-amber-500/15 text-amber-600 dark:text-amber-400 flex items-center justify-center text-sm flex-shrink-0">
                                        <i class="fa-solid fa-hourglass-half"></i>
                                    </span>
                                    <span class="text-xs font-semibold text-slate-800 dark:text-slate-200">
                                        {{ app()->getLocale() === 'ar' ? 'الطبيب منشغل / مقابلة سريعة' : 'Doctor Busy / Brief Presentation' }}
                                    </span>
                                </div>
                                <i class="outcome-check hidden fa-solid fa-check text-amber-500 text-xs"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Products Discussed (Required for Visited) -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase">
                                {{ app()->getLocale() === 'ar' ? 'المنتجات التي تمت مناقشتها مع الطبيب *' : 'Products Discussed *' }}
                            </label>
                            <span id="direct-products-count-badge" class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                <span id="direct-products-selected-count">0</span> {{ app()->getLocale() === 'ar' ? 'محدد' : 'selected' }}
                            </span>
                        </div>

                        <!-- Product Search -->
                        <div class="relative mb-2">
                            <i class="fa-solid fa-magnifying-glass absolute left-3 rtl:left-auto rtl:right-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                            <input type="text" id="direct-product-search" 
                                placeholder="{{ app()->getLocale() === 'ar' ? 'بحث باسم المنتج أو الكود...' : 'Search product name or SKU...' }}" 
                                class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white pl-8 pr-8 rtl:pl-8 rtl:pr-8 py-2 focus:outline-none focus:border-emerald-500 transition">
                        </div>

                        <!-- Product List Checkbox Rows -->
                        <div id="direct-products-list" class="max-h-40 overflow-y-auto space-y-1.5 p-1.5 rounded-xl bg-slate-50/60 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700">
                            @forelse($availableProducts as $prod)
                                <div class="direct-product-row group flex items-center justify-between gap-2.5 p-2 rounded-lg cursor-pointer transition border border-slate-200/80 dark:border-slate-700/80 bg-white dark:bg-slate-900 hover:border-emerald-500 select-none"
                                     data-id="{{ $prod->id }}"
                                     data-name="{{ app()->getLocale() === 'ar' && !empty($prod->name_ar) ? $prod->name_ar : $prod->name_en }}"
                                     data-search="{{ strtolower($prod->name_en . ' ' . $prod->name_ar . ' ' . $prod->sku) }}">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <input type="checkbox" name="direct_product_ids[]" value="{{ $prod->id }}" class="direct-product-cb h-4 w-4 rounded border-slate-300 dark:border-slate-600 text-emerald-600 focus:ring-emerald-500 pointer-events-none">
                                        <div class="min-w-0">
                                            <div class="text-xs font-bold text-slate-900 dark:text-white truncate group-hover:text-emerald-500 transition">
                                                {{ app()->getLocale() === 'ar' && !empty($prod->name_ar) ? $prod->name_ar : $prod->name_en }}
                                            </div>
                                            @if($prod->sku)
                                                <div class="text-[10px] text-slate-400 font-mono">
                                                    SKU: {{ $prod->sku }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="product-check-indicator hidden text-emerald-500 text-xs flex-shrink-0">
                                        <i class="fa-solid fa-circle-check"></i>
                                    </div>
                                </div>
                            @empty
                                <div class="p-4 text-center text-xs text-slate-400">
                                    {{ app()->getLocale() === 'ar' ? 'لا توجد منتجات مسجلة.' : 'No products available.' }}
                                </div>
                            @endforelse

                            <div id="no-direct-products-found" class="hidden p-4 text-center text-xs text-slate-400">
                                {{ app()->getLocale() === 'ar' ? 'لا توجد منتجات مطابقة لبحثك.' : 'No products matched your search.' }}
                            </div>
                        </div>

                        <div id="direct-products-error" class="hidden text-xs text-rose-500 font-bold mt-1.5 flex items-center gap-1.5">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'يرجى اختيار منتج واحد على الأقل تمت مناقشته مع الطبيب.' : 'Please select at least one product discussed.' }}</span>
                        </div>
                    </div>

                    <!-- Clinical Discussion & Doctor Feedback Notes -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase">
                            {{ app()->getLocale() === 'ar' ? 'ملاحظات الزيارة وملاحظات الطبيب (مطلوب) *' : 'Meeting Notes & Doctor Feedback (Required) *' }}
                        </label>
                        <textarea id="direct-visit-notes" rows="3" placeholder="{{ app()->getLocale() === 'ar' ? 'اكتب بالتفصيل ما تم نقاشه، استجابة الطبيب للمنتجات، العينات، أو خطط المتابعة (إجباري)...' : 'Detail clinical topics discussed, doctor feedback on formulations, sample requests, or follow-up commitments (required)...' }}" class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white p-3 focus:outline-none focus:border-emerald-500 leading-relaxed"></textarea>
                        
                        <div id="direct-notes-error" class="hidden text-xs text-rose-500 font-bold mt-1.5 flex items-center gap-1.5">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'ملاحظات الزيارة وملاحظات الطبيب مطلوبة لاعتماد الزيارة.' : 'Meeting notes and feedback are required.' }}</span>
                        </div>
                    </div>

                </div>

                {{-- ================= SECTION 2: DON'T VISIT (NOT VISITED) ================= --}}
                <div id="direct-not-visited-section" class="hidden space-y-4">
                    
                    <div class="p-3.5 rounded-xl bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-900/40 text-xs text-rose-800 dark:text-rose-300 flex items-start gap-2.5">
                        <i class="fa-solid fa-circle-info text-rose-500 text-sm mt-0.5 flex-shrink-0"></i>
                        <div>
                            <span class="font-bold block">{{ app()->getLocale() === 'ar' ? 'توثيق حالة عدم الزيارة:' : 'Non-Visit Documentation:' }}</span>
                            <span class="text-rose-700 dark:text-rose-400">{{ app()->getLocale() === 'ar' ? 'سيتم تسجيل السجل الميداني مع بيان السبب وحفظه في سجل نشاط المندوب لإحاطة الإدارة والمشرفين.' : 'This will log the reason and details why the visit could not be executed for supervisor auditing.' }}</span>
                        </div>
                    </div>

                    <!-- Non-Visit Reason Dropdown -->
                    <div class="relative" id="direct-reason-wrapper">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase">
                            {{ app()->getLocale() === 'ar' ? 'سبب عدم إتمام الزيارة *' : 'Reason for Non-Visit *' }}
                        </label>

                        <input type="hidden" id="direct-not-visit-reason" value="doctor_unavailable">

                        <button type="button" id="direct-reason-btn" onclick="toggleDirectReasonDropdown()" class="w-full flex items-center justify-between gap-3 text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white p-3 focus:outline-none focus:border-rose-500 hover:border-slate-400 dark:hover:border-slate-600 transition shadow-sm select-none">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <span id="direct-reason-selected-icon" class="w-6 h-6 rounded-lg bg-rose-500/15 text-rose-600 dark:text-rose-400 flex items-center justify-center text-xs flex-shrink-0">
                                    <i class="fa-solid fa-user-slash"></i>
                                </span>
                                <span id="direct-reason-selected-text" class="font-semibold truncate text-slate-800 dark:text-slate-200">
                                    {{ app()->getLocale() === 'ar' ? 'الطبيب غير متاح بالعيادة / مسافر' : 'Doctor Unavailable at Clinic / Away' }}
                                </span>
                            </div>
                            <i id="direct-reason-chevron" class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-200"></i>
                        </button>

                        <div id="direct-reason-menu" class="hidden absolute top-full left-0 right-0 z-50 mt-1.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700/80 rounded-xl shadow-2xl overflow-hidden divide-y divide-slate-100 dark:divide-slate-800/60 transition-all">
                            
                            <div onclick="selectDirectReason('doctor_unavailable', 'fa-user-slash', 'rose', '{{ addslashes(app()->getLocale() === 'ar' ? 'الطبيب غير متاح بالعيادة / مسافر' : 'Doctor Unavailable at Clinic / Away') }}')" 
                                 class="reason-option flex items-center justify-between gap-3 p-3 hover:bg-slate-50 dark:hover:bg-slate-800/80 cursor-pointer transition select-none group"
                                 data-value="doctor_unavailable">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <span class="w-7 h-7 rounded-lg bg-rose-500/15 text-rose-600 dark:text-rose-400 flex items-center justify-center text-sm flex-shrink-0">
                                        <i class="fa-solid fa-user-slash"></i>
                                    </span>
                                    <span class="text-xs font-semibold text-slate-800 dark:text-slate-200">
                                        {{ app()->getLocale() === 'ar' ? 'الطبيب غير متاح بالعيادة / مسافر' : 'Doctor Unavailable at Clinic / Away' }}
                                    </span>
                                </div>
                                <i class="reason-check fa-solid fa-check text-rose-500 text-xs"></i>
                            </div>

                            <div onclick="selectDirectReason('clinic_closed', 'fa-door-closed', 'amber', '{{ addslashes(app()->getLocale() === 'ar' ? 'العيادة أو المركز الطبي مغلق' : 'Clinic / Center Closed') }}')" 
                                 class="reason-option flex items-center justify-between gap-3 p-3 hover:bg-slate-50 dark:hover:bg-slate-800/80 cursor-pointer transition select-none group"
                                 data-value="clinic_closed">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <span class="w-7 h-7 rounded-lg bg-amber-500/15 text-amber-600 dark:text-amber-400 flex items-center justify-center text-sm flex-shrink-0">
                                        <i class="fa-solid fa-door-closed"></i>
                                    </span>
                                    <span class="text-xs font-semibold text-slate-800 dark:text-slate-200">
                                        {{ app()->getLocale() === 'ar' ? 'العيادة أو المركز الطبي مغلق' : 'Clinic / Center Closed' }}
                                    </span>
                                </div>
                                <i class="reason-check hidden fa-solid fa-check text-amber-500 text-xs"></i>
                            </div>

                            <div onclick="selectDirectReason('doctor_busy', 'fa-stethoscope', 'orange', '{{ addslashes(app()->getLocale() === 'ar' ? 'الطبيب منشغل جداً بحالات طارئة' : 'Doctor Extremely Busy / Emergency in Progress') }}')" 
                                 class="reason-option flex items-center justify-between gap-3 p-3 hover:bg-slate-50 dark:hover:bg-slate-800/80 cursor-pointer transition select-none group"
                                 data-value="doctor_busy">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <span class="w-7 h-7 rounded-lg bg-orange-500/15 text-orange-600 dark:text-orange-400 flex items-center justify-center text-sm flex-shrink-0">
                                        <i class="fa-solid fa-stethoscope"></i>
                                    </span>
                                    <span class="text-xs font-semibold text-slate-800 dark:text-slate-200">
                                        {{ app()->getLocale() === 'ar' ? 'الطبيب منشغل جداً بحالات طارئة' : 'Doctor Extremely Busy / Emergency in Progress' }}
                                    </span>
                                </div>
                                <i class="reason-check hidden fa-solid fa-check text-orange-500 text-xs"></i>
                            </div>

                            <div onclick="selectDirectReason('doctor_refused', 'fa-ban', 'red', '{{ addslashes(app()->getLocale() === 'ar' ? 'اعتذار الطبيب عن استقبال الزيارة' : 'Doctor Refused / Postponed Visit') }}')" 
                                 class="reason-option flex items-center justify-between gap-3 p-3 hover:bg-slate-50 dark:hover:bg-slate-800/80 cursor-pointer transition select-none group"
                                 data-value="doctor_refused">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <span class="w-7 h-7 rounded-lg bg-red-500/15 text-red-600 dark:text-red-400 flex items-center justify-center text-sm flex-shrink-0">
                                        <i class="fa-solid fa-ban"></i>
                                    </span>
                                    <span class="text-xs font-semibold text-slate-800 dark:text-slate-200">
                                        {{ app()->getLocale() === 'ar' ? 'اعتذار الطبيب عن استقبال الزيارة' : 'Doctor Refused / Postponed Visit' }}
                                    </span>
                                </div>
                                <i class="reason-check hidden fa-solid fa-check text-red-500 text-xs"></i>
                            </div>

                            <div onclick="selectDirectReason('cancelled', 'fa-calendar-xmark', 'slate', '{{ addslashes(app()->getLocale() === 'ar' ? 'إلغاء الموعد لظرف طارئ' : 'Appointment Cancelled Due to Circumstances') }}')" 
                                 class="reason-option flex items-center justify-between gap-3 p-3 hover:bg-slate-50 dark:hover:bg-slate-800/80 cursor-pointer transition select-none group"
                                 data-value="cancelled">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <span class="w-7 h-7 rounded-lg bg-slate-500/15 text-slate-600 dark:text-slate-400 flex items-center justify-center text-sm flex-shrink-0">
                                        <i class="fa-solid fa-calendar-xmark"></i>
                                    </span>
                                    <span class="text-xs font-semibold text-slate-800 dark:text-slate-200">
                                        {{ app()->getLocale() === 'ar' ? 'إلغاء الموعد لظرف طارئ' : 'Appointment Cancelled Due to Circumstances' }}
                                    </span>
                                </div>
                                <i class="reason-check hidden fa-solid fa-check text-slate-500 text-xs"></i>
                            </div>

                            <div onclick="selectDirectReason('not_visited', 'fa-triangle-exclamation', 'indigo', '{{ addslashes(app()->getLocale() === 'ar' ? 'سبب تشغيلي أو ميداني آخر' : 'Other Operational / Field Reason') }}')" 
                                 class="reason-option flex items-center justify-between gap-3 p-3 hover:bg-slate-50 dark:hover:bg-slate-800/80 cursor-pointer transition select-none group"
                                 data-value="not_visited">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <span class="w-7 h-7 rounded-lg bg-indigo-500/15 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-sm flex-shrink-0">
                                        <i class="fa-solid fa-triangle-exclamation"></i>
                                    </span>
                                    <span class="text-xs font-semibold text-slate-800 dark:text-slate-200">
                                        {{ app()->getLocale() === 'ar' ? 'سبب تشغيلي أو ميداني آخر' : 'Other Operational / Field Reason' }}
                                    </span>
                                </div>
                                <i class="reason-check hidden fa-solid fa-check text-indigo-500 text-xs"></i>
                            </div>

                        </div>
                    </div>

                    <!-- Non-Visit Details / Explanation Notes (Required) -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase">
                            {{ app()->getLocale() === 'ar' ? 'تفاصيل وتوضيح سبب عدم الزيارة (مطلوب) *' : 'Explanation & Field Context (Required) *' }}
                        </label>
                        <textarea id="direct-not-visit-notes" rows="3" placeholder="{{ app()->getLocale() === 'ar' ? 'اكتب بالتفصيل سبب عدم إتمام الزيارة، الموعد البديل المقترح، أو أي ملاحظات هامة...' : 'Detail why the visit could not take place, rescheduled timing, or field context...' }}" class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white p-3 focus:outline-none focus:border-rose-500 leading-relaxed"></textarea>
                        
                        <div id="direct-not-visit-notes-error" class="hidden text-xs text-rose-500 font-bold mt-1.5 flex items-center gap-1.5">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'يرجى كتابة سبب وتفاصيل عدم الزيارة بحد أدنى 3 أحرف.' : 'Please provide non-visit details (at least 3 characters).' }}</span>
                        </div>
                    </div>

                </div>

                {{-- ================= GPS Real-Time Sensor Telemetry Bar ================= --}}
                <div id="direct-gps-sensor-bar" class="p-3 rounded-xl bg-cyan-500/10 border border-cyan-500/30 text-xs text-cyan-700 dark:text-cyan-300 flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2 min-w-0">
                        <i id="direct-gps-icon" class="fa-solid fa-satellite-dish animate-pulse text-cyan-500 dark:text-cyan-400"></i>
                        <span id="direct-gps-status-text" class="truncate font-semibold">
                            {{ app()->getLocale() === 'ar' ? 'جاري جلب إحداثيات الـ GPS الميدانية...' : 'Acquiring high-accuracy GPS coordinates...' }}
                        </span>
                    </div>
                    <span id="direct-gps-accuracy-badge" class="px-2 py-0.5 rounded text-[10px] font-bold bg-cyan-500/20 text-cyan-700 dark:text-cyan-300 flex-shrink-0">
                        GPS Active
                    </span>
                </div>

            </div>

            <!-- Modal Action Buttons -->
            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-200 dark:border-slate-800 flex-shrink-0">
                <button type="button" onclick="closeDirectVisitModal()" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs transition border border-slate-200 dark:border-transparent">
                    {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                </button>
                <button type="button" id="submit-direct-visit-btn" onclick="submitDirectVisitForm()" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-600 hover:from-emerald-500 hover:to-cyan-500 text-white font-bold text-xs active:scale-95 transition shadow-lg shadow-emerald-600/30 flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span id="submit-direct-visit-btn-text">{{ app()->getLocale() === 'ar' ? 'حفظ واعتماد الزيارة' : 'Confirm & Save Visit' }}</span>
                </button>
            </div>

        </div>
    </div>

    {{-- ================= Schedule Appointment Modal (With Live Quota Logic) ================= --}}
    <div id="schedule-modal" class="fixed inset-0 z-50 bg-slate-950/60 backdrop-blur-md hidden flex items-center justify-center p-3 sm:p-4">
        <div class="glass-card bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl w-full max-w-md p-5 sm:p-6 space-y-4 shadow-2xl border border-slate-200 dark:border-slate-700/80 relative">
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
                <select id="schedule-assignment-id" onchange="onMrScheduleDoctorSelected(this.value)" class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white p-3 focus:outline-none focus:border-sky-500">
                    @foreach($allAssignments as $assign)
                        @php
                            $c = $assign->contact;
                            $q = $c?->quota_info;
                            $code = $c?->classification?->code ?? 'C';
                        @endphp
                        <option value="{{ $assign->id }}" 
                                data-contact-id="{{ $c?->id }}" 
                                data-name="{{ $c?->name }}"
                                data-class="{{ $code }}"
                                data-can-schedule="{{ $q && $q['can_schedule'] ? '1' : '0' }}"
                                data-count="{{ $q['current_count'] ?? 0 }}"
                                data-max="{{ $q['max_visits'] ?? 1 }}"
                                data-rem="{{ $q['remaining_visits'] ?? 0 }}">
                            {{ $c?->name }} (Class {{ $code }}) — {{ $c?->hospital_clinic_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- LIVE QUOTA EVALUATION BOX -->
            <div id="mr-sch-quota-box" class="p-3.5 rounded-xl border transition-all duration-200 bg-slate-50 dark:bg-slate-800/80 border-slate-300 dark:border-slate-700">
                <div class="flex items-center justify-between mb-1.5">
                    <span id="mr-sch-quota-badge" class="px-2 py-0.5 rounded text-[10px] font-black bg-sky-500/20 text-sky-700 dark:text-sky-300">Class -</span>
                    <span id="mr-sch-quota-stat" class="text-xs font-black text-slate-800 dark:text-white">0 / 0</span>
                </div>
                <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-1.5 overflow-hidden mb-1.5">
                    <div id="mr-sch-quota-bar" class="h-1.5 rounded-full bg-sky-500 transition-all duration-300" style="width: 0%"></div>
                </div>
                <p id="mr-sch-quota-feedback" class="text-[11px] text-slate-500 dark:text-slate-400 mb-0"></p>
                <div id="mr-sch-quota-warning" class="mt-2 p-2 rounded-lg text-xs font-bold hidden"></div>
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
                <textarea id="schedule-notes" rows="2" placeholder="{{ app()->getLocale() === 'ar' ? 'أدخل أهداف الزيارة أو المنتجات المستهدفة...' : 'Specific items to present...' }}" class="w-full text-xs rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white p-3 focus:outline-none focus:border-sky-500"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-2">
                <button type="button" onclick="closeScheduleModal()" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs transition border border-slate-200 dark:border-transparent">
                    {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                </button>
                <button id="mr-sch-submit-btn" onclick="submitScheduleForm()" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-sky-600 to-indigo-600 hover:from-sky-500 hover:to-indigo-500 text-white font-bold text-xs active:scale-95 transition shadow-lg shadow-sky-600/30 flex items-center gap-2">
                    <i class="fa-solid fa-check"></i>
                    <span id="mr-sch-submit-label">{{ app()->getLocale() === 'ar' ? 'تأكيد الحجز' : 'Schedule Appointment' }}</span>
                </button>
            </div>
        </div>
    </div>

    {{-- ================= Visits Done / Completed Doctors Modal ================= --}}
    <div id="visits-done-modal" class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-md hidden flex items-center justify-center p-3 sm:p-4">
        <div class="glass-card bg-white dark:bg-slate-900 rounded-2xl w-full max-w-2xl max-h-[90vh] flex flex-col p-4 sm:p-6 shadow-2xl border border-slate-200 dark:border-slate-800 relative">
            
            <!-- Modal Header -->
            <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-4 flex-shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-lg border border-emerald-500/25 flex-shrink-0">
                        <i class="fa-solid fa-clipboard-check"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="font-black text-slate-900 dark:text-white text-base sm:text-lg">
                                {{ app()->getLocale() === 'ar' ? 'الأطباء الذين تمت زيارتهم' : 'Doctors Visited & Done' }}
                            </h3>
                            <span id="visits-done-badge-count" class="px-2 py-0.5 rounded-full text-xs font-black bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 border border-emerald-500/30">
                                {{ count($completedVisits) }} {{ app()->getLocale() === 'ar' ? 'مكتملة' : 'completed' }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            {{ app()->getLocale() === 'ar' ? 'سجل الزيارات المنفذة مع المنتجات المناقشة وملاحظات العيادة' : 'Log of completed visits, discussed products, and doctor feedback.' }}
                        </p>
                    </div>
                </div>
                <button type="button" onclick="closeVisitsDoneModal()" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-700 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            <!-- Search & Filter Bar Inside Modal -->
            <div class="pt-3.5 pb-2 flex-shrink-0">
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 rtl:left-auto rtl:right-3.5 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                    <input type="text" id="completed-visits-search" onkeyup="filterCompletedVisits()" placeholder="{{ app()->getLocale() === 'ar' ? 'بحث في الأطباء، المركز، التخصص، أو المنتج...' : 'Search doctor name, clinic, specialty, product...' }}" class="w-full pl-9 rtl:pl-3 rtl:pr-9 pr-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800/90 border border-slate-300 dark:border-slate-700 text-xs text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:border-emerald-500 transition">
                </div>
            </div>

            <!-- Scrollable Completed Visits List -->
            <div id="completed-visits-container" class="overflow-y-auto max-h-[60vh] space-y-3 pt-2 pb-2 pr-1">
                @forelse($completedVisits as $cv)
                    @php
                        $doc = $cv->contact;
                        $spec = $doc?->specialty?->name ?? 'Specialist';
                        $classCode = $doc?->classification?->code;
                        $prods = $cv->products;
                        if ($prods->isEmpty() && $cv->product) {
                            $prods = collect([$cv->product]);
                        }
                        $prodNames = $prods->map(fn($p) => ($p->name_en . ' ' . $p->name_ar . ' ' . $p->sku))->implode(' ');
                        $durationMin = $cv->duration_minutes ?? ($cv->checkin_at && $cv->checkout_at ? $cv->checkin_at->diffInMinutes($cv->checkout_at) : 0);
                    @endphp
                    <div class="completed-visit-card p-4 rounded-xl bg-slate-50/80 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-700/60 hover:border-emerald-500/40 transition space-y-3"
                         data-doctor="{{ strtolower($doc?->name ?? '') }}"
                         data-clinic="{{ strtolower($doc?->hospital_clinic_name ?? '') }}"
                         data-specialty="{{ strtolower($spec) }}"
                         data-products="{{ strtolower($prodNames) }}"
                         data-notes="{{ strtolower($cv->notes ?? '') }}">
                        
                        <!-- Doctor & Visit Status Header -->
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold text-sm border border-emerald-500/20 flex-shrink-0">
                                    <i class="fa-solid fa-user-doctor"></i>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h4 class="text-sm font-bold text-slate-900 dark:text-white">
                                            {{ $doc?->name ?? 'Doctor' }}
                                        </h4>
                                        @if($doc?->code)
                                            <span class="text-[10px] text-slate-400 font-mono">({{ $doc->code }})</span>
                                        @endif
                                        @if($classCode)
                                            <span class="px-1.5 py-0.5 rounded text-[10px] font-black {{ $classCode === 'A+' ? 'bg-amber-500/20 text-amber-700 dark:text-amber-300 border border-amber-500/30' : 'bg-sky-500/20 text-sky-700 dark:text-sky-300 border border-sky-500/30' }}">
                                                Class {{ $classCode }}
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                        {{ $doc?->hospital_clinic_name }} • {{ $spec }}
                                        @if($doc?->city)
                                            • {{ $doc->city->name }}
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <!-- Duration & GPS Pill -->
                            <div class="text-right rtl:text-left flex flex-col items-end rtl:items-start gap-1 flex-shrink-0">
                                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-slate-700 dark:text-slate-300">
                                    <i class="fa-regular fa-clock text-slate-400"></i>
                                    {{ $durationMin }} {{ app()->getLocale() === 'ar' ? 'دقيقة' : 'min' }}
                                </span>
                                @if($cv->gps_verified)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/15 text-emerald-700 dark:text-emerald-300 border border-emerald-500/30">
                                        <i class="fa-solid fa-satellite"></i> {{ app()->getLocale() === 'ar' ? 'مؤكد بالـ GPS' : 'GPS Verified' }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/15 text-amber-700 dark:text-amber-300 border border-amber-500/30">
                                        <i class="fa-solid fa-triangle-exclamation"></i> {{ app()->getLocale() === 'ar' ? 'غير مؤكد' : 'Unverified' }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Date, Time & Outcome -->
                        <div class="flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400 border-t border-b border-slate-200/60 dark:border-slate-700/50 py-2">
                            <span class="flex items-center gap-1.5">
                                <i class="fa-regular fa-calendar-check text-sky-500"></i>
                                <span>{{ $cv->checkout_at ? $cv->checkout_at->format('d M Y, h:i A') : $cv->created_at->format('d M Y, h:i A') }}</span>
                            </span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300">
                                {{ ucfirst(str_replace('_', ' ', $cv->outcome ?? 'completed')) }}
                            </span>
                        </div>

                        <!-- Discussed Products Section -->
                        <div>
                            <div class="text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1.5 flex items-center gap-1.5">
                                <i class="fa-solid fa-capsules text-emerald-500"></i>
                                <span>{{ app()->getLocale() === 'ar' ? 'المنتجات التي نوقشت:' : 'Products Discussed:' }}</span>
                            </div>
                            <div class="flex flex-wrap gap-1.5">
                                @forelse($prods as $p)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 border border-emerald-500/20">
                                        <i class="fa-solid fa-check text-[9px] text-emerald-500"></i>
                                        <span>{{ app()->getLocale() === 'ar' && !empty($p->name_ar) ? $p->name_ar : $p->name_en }}</span>
                                    </span>
                                @empty
                                    <span class="text-xs text-slate-400 italic">
                                        {{ app()->getLocale() === 'ar' ? 'لم تُحدد منتجات' : 'No products specified' }}
                                    </span>
                                @endforelse
                            </div>
                        </div>

                        <!-- Meeting Notes & Feedback -->
                        @if($cv->notes)
                            <div class="p-3 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-xs text-slate-700 dark:text-slate-300 leading-relaxed">
                                <div class="flex items-start gap-2">
                                    <i class="fa-solid fa-quote-left text-slate-400 text-[11px] mt-0.5 flex-shrink-0"></i>
                                    <p class="whitespace-pre-line">{{ $cv->notes }}</p>
                                </div>
                            </div>
                        @endif

                    </div>
                @empty
                    <div id="completed-visits-empty-state" class="p-8 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-800 text-center">
                        <div class="w-12 h-12 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mx-auto mb-2 text-xl">
                            <i class="fa-regular fa-clipboard"></i>
                        </div>
                        <h4 class="text-sm font-bold text-slate-800 dark:text-slate-200">
                            {{ app()->getLocale() === 'ar' ? 'لا توجد زيارات مكتملة بعد' : 'No Completed Visits Yet' }}
                        </h4>
                        <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                            {{ app()->getLocale() === 'ar' ? 'عند تسجيل انصراف من زيارة طبيب وكتابة الملاحظات والمنتجات، ستظهر جميع بياناتها هنا.' : 'When you check out from a doctor visit with notes and products, the record will appear here.' }}
                        </p>
                    </div>
                @endforelse

                <div id="no-filtered-completed-visits" class="hidden p-8 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-800 text-center">
                    <div class="w-10 h-10 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-500 flex items-center justify-center mx-auto mb-2 text-base">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </div>
                    <p class="text-xs text-slate-500">
                        {{ app()->getLocale() === 'ar' ? 'لا توجد زيارات مطابقة للبحث.' : 'No completed visits matched your search.' }}
                    </p>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-between pt-3 border-t border-slate-200 dark:border-slate-800 flex-shrink-0">
                <span class="text-xs text-slate-500">
                    {{ app()->getLocale() === 'ar' ? 'إجمالي الزيارات المنفذة: ' : 'Total visits: ' }}
                    <strong class="text-emerald-600 dark:text-emerald-400 font-black">{{ count($completedVisits) }}</strong>
                </span>
                <button type="button" onclick="closeVisitsDoneModal()" class="px-5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs transition border border-slate-200 dark:border-transparent">
                    {{ app()->getLocale() === 'ar' ? 'إغلاق' : 'Close' }}
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

        // -------------------------------------------------------------
        // Portfolio Real-Time Dynamic Pagination & Multi-Filter Engine
        // -------------------------------------------------------------
        const portfolioState = {
            currentPage: 1,
            perPage: parseInt(localStorage.getItem('bz_portfolio_per_page') || '5', 10),
            searchQuery: '',
            selectedClass: 'all',
            selectedStatus: 'all'
        };

        function initPortfolioPagination() {
            const perPageSelect = document.getElementById('portfolio-per-page');
            if (perPageSelect) {
                perPageSelect.value = String(portfolioState.perPage);
            }
            renderPortfolio();
        }

        function filterDoctors() {
            const input = document.getElementById('doctor-search-input');
            portfolioState.searchQuery = (input ? input.value : '').trim().toLowerCase();
            portfolioState.currentPage = 1;
            renderPortfolio();
        }

        function filterByClass(targetClass, btn = null) {
            portfolioState.selectedClass = targetClass;
            portfolioState.selectedStatus = 'all';
            portfolioState.currentPage = 1;

            document.querySelectorAll('.chip-filter').forEach(el => {
                el.classList.remove('active', 'bg-sky-600', 'text-white', 'bg-emerald-600');
                el.classList.add('bg-slate-200', 'dark:bg-slate-800', 'text-slate-700', 'dark:text-slate-400');
            });

            const activeBtn = btn || (event && event.currentTarget ? event.currentTarget : null);
            if (activeBtn) {
                activeBtn.classList.add('active', 'bg-sky-600', 'text-white');
                activeBtn.classList.remove('bg-slate-200', 'dark:bg-slate-800', 'text-slate-700', 'dark:text-slate-400');
            }

            renderPortfolio();
        }

        function filterByStatus(targetStatus, btn = null) {
            portfolioState.selectedStatus = targetStatus;
            portfolioState.selectedClass = 'all';
            portfolioState.currentPage = 1;

            document.querySelectorAll('.chip-filter').forEach(el => {
                el.classList.remove('active', 'bg-sky-600', 'text-white', 'bg-emerald-600');
                el.classList.add('bg-slate-200', 'dark:bg-slate-800', 'text-slate-700', 'dark:text-slate-400');
            });

            const activeBtn = btn || (event && event.currentTarget ? event.currentTarget : null);
            if (activeBtn) {
                activeBtn.classList.add('active', 'bg-emerald-600', 'text-white');
                activeBtn.classList.remove('bg-slate-200', 'dark:bg-slate-800', 'text-slate-700', 'dark:text-slate-400');
            }

            renderPortfolio();
        }

        function changePortfolioPerPage(val) {
            portfolioState.perPage = parseInt(val, 10) || 5;
            localStorage.setItem('bz_portfolio_per_page', portfolioState.perPage);
            portfolioState.currentPage = 1;
            renderPortfolio();
        }

        function goToPortfolioPage(page) {
            portfolioState.currentPage = page;
            renderPortfolio();
            const section = document.getElementById('doctors-container');
            if (section) {
                section.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        }

        function resetPortfolioFilters() {
            portfolioState.searchQuery = '';
            portfolioState.selectedClass = 'all';
            portfolioState.selectedStatus = 'all';
            portfolioState.currentPage = 1;

            const searchInput = document.getElementById('doctor-search-input');
            if (searchInput) searchInput.value = '';

            const firstChip = document.querySelector('.chip-filter');
            if (firstChip) filterByClass('all', firstChip);
            else renderPortfolio();
        }

        function renderPortfolio() {
            const cards = Array.from(document.querySelectorAll('#doctors-container .doctor-card'));
            const emptyState = document.getElementById('portfolio-empty-state');
            const paginationBar = document.getElementById('portfolio-pagination-bar');
            const rangeText = document.getElementById('portfolio-range-text');
            const controls = document.getElementById('portfolio-pagination-controls');

            // 1. Filter matching cards
            const matchingCards = cards.filter(card => {
                const name = (card.getAttribute('data-name') || '').toLowerCase();
                const clinic = (card.getAttribute('data-clinic') || '').toLowerCase();
                const spec = (card.getAttribute('data-specialty') || '').toLowerCase();
                const cls = card.getAttribute('data-class') || '';
                const status = card.getAttribute('data-status') || '';

                const matchesQuery = !portfolioState.searchQuery || 
                    name.includes(portfolioState.searchQuery) ||
                    clinic.includes(portfolioState.searchQuery) ||
                    spec.includes(portfolioState.searchQuery) ||
                    cls.toLowerCase().includes(portfolioState.searchQuery);

                const matchesClass = portfolioState.selectedClass === 'all' || cls === portfolioState.selectedClass;
                const matchesStatus = portfolioState.selectedStatus === 'all' || status === portfolioState.selectedStatus;

                return matchesQuery && matchesClass && matchesStatus;
            });

            const totalCount = matchingCards.length;
            const perPage = portfolioState.perPage;
            const totalPages = Math.max(1, Math.ceil(totalCount / perPage));

            if (portfolioState.currentPage > totalPages) {
                portfolioState.currentPage = totalPages;
            }
            if (portfolioState.currentPage < 1) {
                portfolioState.currentPage = 1;
            }

            const startIndex = (portfolioState.currentPage - 1) * perPage;
            const endIndex = Math.min(startIndex + perPage, totalCount);

            // 2. Hide all cards first
            cards.forEach(c => c.style.display = 'none');

            // 3. Show only current page items
            matchingCards.slice(startIndex, endIndex).forEach(c => {
                c.style.display = 'flex';
            });

            // 4. Handle Empty State vs Results
            if (totalCount === 0) {
                if (emptyState) emptyState.classList.remove('hidden');
                if (paginationBar) paginationBar.classList.add('hidden');
            } else {
                if (emptyState) emptyState.classList.add('hidden');
                if (paginationBar) paginationBar.classList.remove('hidden');

                // 5. Update Range Summary Text
                const isAr = "{{ app()->getLocale() }}" === 'ar';
                const startDisplay = startIndex + 1;
                const endDisplay = endIndex;
                if (rangeText) {
                    rangeText.innerHTML = isAr 
                        ? `عرض <strong class="text-slate-900 dark:text-white font-bold">${startDisplay} - ${endDisplay}</strong> من أصل <strong class="text-slate-900 dark:text-white font-bold">${totalCount}</strong> طبيب`
                        : `Showing <strong class="text-slate-900 dark:text-white font-bold">${startDisplay}-${endDisplay}</strong> of <strong class="text-slate-900 dark:text-white font-bold">${totalCount}</strong> doctors`;
                }

                // 6. Build Rich Pagination Controls
                if (controls) {
                    let html = '';
                    const currentPage = portfolioState.currentPage;

                    // Previous Button
                    const prevDisabled = currentPage <= 1;
                    const prevIcon = isAr ? 'fa-chevron-right' : 'fa-chevron-left';
                    const prevText = isAr ? 'السابق' : 'Prev';
                    html += `
                        <button type="button" onclick="goToPortfolioPage(${currentPage - 1})" ${prevDisabled ? 'disabled' : ''} class="px-2.5 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700/80 text-xs font-bold ${prevDisabled ? 'opacity-40 cursor-not-allowed bg-slate-100 dark:bg-slate-800/40 text-slate-400' : 'bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 hover:border-sky-500 hover:text-sky-600 dark:hover:text-sky-400 transition shadow-sm active:scale-95'} flex items-center gap-1">
                            <i class="fa-solid ${prevIcon} text-[10px]"></i>
                            <span class="hidden sm:inline">${prevText}</span>
                        </button>
                    `;

                    // Numbered Pages Logic
                    const maxVisible = 5;
                    let startPage = Math.max(1, currentPage - 2);
                    let endPage = Math.min(totalPages, startPage + maxVisible - 1);
                    if (endPage - startPage + 1 < maxVisible) {
                        startPage = Math.max(1, endPage - maxVisible + 1);
                    }

                    if (startPage > 1) {
                        html += `
                            <button type="button" onclick="goToPortfolioPage(1)" class="w-8 h-8 rounded-xl border border-slate-200 dark:border-slate-700/80 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 text-xs font-bold hover:border-sky-500 hover:text-sky-600 transition shadow-sm active:scale-95">1</button>
                        `;
                        if (startPage > 2) {
                            html += `<span class="px-1 text-slate-400 text-xs font-bold">...</span>`;
                        }
                    }

                    for (let p = startPage; p <= endPage; p++) {
                        const isActive = p === currentPage;
                        if (isActive) {
                            html += `
                                <button type="button" class="w-8 h-8 rounded-xl bg-sky-600 text-white text-xs font-black shadow-md shadow-sky-600/30 border border-sky-500">${p}</button>
                            `;
                        } else {
                            html += `
                                <button type="button" onclick="goToPortfolioPage(${p})" class="w-8 h-8 rounded-xl border border-slate-200 dark:border-slate-700/80 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 text-xs font-bold hover:border-sky-500 hover:text-sky-600 dark:hover:text-sky-400 transition shadow-sm active:scale-95">${p}</button>
                            `;
                        }
                    }

                    if (endPage < totalPages) {
                        if (endPage < totalPages - 1) {
                            html += `<span class="px-1 text-slate-400 text-xs font-bold">...</span>`;
                        }
                        html += `
                            <button type="button" onclick="goToPortfolioPage(${totalPages})" class="w-8 h-8 rounded-xl border border-slate-200 dark:border-slate-700/80 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 text-xs font-bold hover:border-sky-500 hover:text-sky-600 transition shadow-sm active:scale-95">${totalPages}</button>
                        `;
                    }

                    // Next Button
                    const nextDisabled = currentPage >= totalPages;
                    const nextIcon = isAr ? 'fa-chevron-left' : 'fa-chevron-right';
                    const nextText = isAr ? 'التالي' : 'Next';
                    html += `
                        <button type="button" onclick="goToPortfolioPage(${currentPage + 1})" ${nextDisabled ? 'disabled' : ''} class="px-2.5 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700/80 text-xs font-bold ${nextDisabled ? 'opacity-40 cursor-not-allowed bg-slate-100 dark:bg-slate-800/40 text-slate-400' : 'bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 hover:border-sky-500 hover:text-sky-600 dark:hover:text-sky-400 transition shadow-sm active:scale-95'} flex items-center gap-1">
                            <span class="hidden sm:inline">${nextText}</span>
                            <i class="fa-solid ${nextIcon} text-[10px]"></i>
                        </button>
                    `;

                    controls.innerHTML = html;
                }
            }
        }

        // Visits Done / Completed Doctors Modal Controls
        function openVisitsDoneModal() {
            const modal = document.getElementById('visits-done-modal');
            if (modal) {
                modal.classList.remove('hidden');
                const searchInput = document.getElementById('completed-visits-search');
                if (searchInput) {
                    searchInput.value = '';
                    filterCompletedVisits();
                    setTimeout(() => searchInput.focus(), 150);
                }
            }
        }

        function closeVisitsDoneModal() {
            const modal = document.getElementById('visits-done-modal');
            if (modal) {
                modal.classList.add('hidden');
            }
        }

        function filterCompletedVisits() {
            const query = (document.getElementById('completed-visits-search').value || '').trim().toLowerCase();
            const items = document.querySelectorAll('#completed-visits-container .completed-visit-card');
            let visibleCount = 0;

            items.forEach(card => {
                const doc = card.getAttribute('data-doctor') || '';
                const clinic = card.getAttribute('data-clinic') || '';
                const spec = card.getAttribute('data-specialty') || '';
                const prods = card.getAttribute('data-products') || '';
                const notes = card.getAttribute('data-notes') || '';

                const matches = !query || doc.includes(query) || clinic.includes(query) || spec.includes(query) || prods.includes(query) || notes.includes(query);
                if (matches) {
                    card.style.display = 'block';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            const noResults = document.getElementById('no-filtered-completed-visits');
            if (noResults) {
                noResults.classList.toggle('hidden', visibleCount > 0 || items.length === 0);
            }
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

        // Direct Field Visit Execution State & Controls
        let currentDirectVisitData = {
            contactId: null,
            doctorName: '',
            assignmentId: null,
            scheduledVisitId: null,
            lat: null,
            lng: null,
            accuracy: null,
            type: 'visited'
        };

        function openDirectVisitModal(contactId, doctorName, assignmentId = null, scheduledVisitId = null, initialType = 'visited') {
            currentDirectVisitData = {
                contactId: contactId,
                doctorName: doctorName,
                assignmentId: assignmentId,
                scheduledVisitId: scheduledVisitId,
                lat: deviceGpsState.lat || null,
                lng: deviceGpsState.lng || null,
                accuracy: deviceGpsState.accuracy || null,
                type: initialType
            };

            document.getElementById('direct-visit-contact-id').value = contactId;
            document.getElementById('direct-visit-assignment-id').value = assignmentId || '';
            document.getElementById('direct-visit-scheduled-id').value = scheduledVisitId || '';

            const docBanner = document.getElementById('direct-visit-doc-banner');
            if (docBanner) {
                docBanner.innerText = doctorName ? ('{{ app()->getLocale() === 'ar' ? 'الطبيب المستهدف: ' : 'Target Doctor: ' }}' + doctorName) : '';
            }

            // Reset validation errors
            const notesErr = document.getElementById('direct-notes-error');
            const prodsErr = document.getElementById('direct-products-error');
            const notVisitNotesErr = document.getElementById('direct-not-visit-notes-error');
            if (notesErr) notesErr.classList.add('hidden');
            if (prodsErr) prodsErr.classList.add('hidden');
            if (notVisitNotesErr) notVisitNotesErr.classList.add('hidden');

            // Switch to initial tab
            switchDirectVisitTab(initialType);

            // Default selections
            selectDirectOutcome('completed', 'fa-circle-check', 'emerald', "{{ addslashes(app()->getLocale() === 'ar' ? 'مقابلة ناجحة ومناقشة علمية تفصيلية' : 'Successful Meeting & Clinical Discussion') }}");
            selectDirectReason('doctor_unavailable', 'fa-user-slash', 'rose', "{{ addslashes(app()->getLocale() === 'ar' ? 'الطبيب غير متاح بالعيادة / مسافر' : 'Doctor Unavailable at Clinic / Away') }}");

            // GPS Telemetry Acquisition
            const gpsStatusText = document.getElementById('direct-gps-status-text');
            const gpsAccBadge = document.getElementById('direct-gps-accuracy-badge');
            const gpsIcon = document.getElementById('direct-gps-icon');

            if (deviceGpsState.status === 'active' && deviceGpsState.lat) {
                currentDirectVisitData.lat = deviceGpsState.lat;
                currentDirectVisitData.lng = deviceGpsState.lng;
                currentDirectVisitData.accuracy = deviceGpsState.accuracy;
                if (gpsStatusText) gpsStatusText.innerHTML = `📍 Coordinates Locked (±${Math.round(deviceGpsState.accuracy)}m)`;
                if (gpsAccBadge) gpsAccBadge.innerText = 'GPS Verified';
            } else if ("geolocation" in navigator) {
                navigator.geolocation.getCurrentPosition(
                    (pos) => {
                        currentDirectVisitData.lat = pos.coords.latitude;
                        currentDirectVisitData.lng = pos.coords.longitude;
                        currentDirectVisitData.accuracy = pos.coords.accuracy;
                        deviceGpsState = {
                            status: 'active',
                            lat: pos.coords.latitude,
                            lng: pos.coords.longitude,
                            accuracy: pos.coords.accuracy
                        };
                        updateGpsUI('active', pos.coords, false);
                        if (gpsStatusText) gpsStatusText.innerHTML = `📍 Coordinates Locked (±${Math.round(pos.coords.accuracy)}m)`;
                        if (gpsAccBadge) gpsAccBadge.innerText = 'GPS Verified';
                    },
                    (err) => {
                        if (gpsStatusText) gpsStatusText.innerText = 'GPS Signal Weak / Offline';
                        if (gpsAccBadge) gpsAccBadge.innerText = 'Fallback Mode';
                    },
                    { enableHighAccuracy: true, timeout: 8000, maximumAge: 0 }
                );
            }

            document.getElementById('direct-visit-modal').classList.remove('hidden');
        }

        function closeDirectVisitModal() {
            document.getElementById('direct-visit-modal').classList.add('hidden');
        }

        function switchDirectVisitTab(type) {
            currentDirectVisitData.type = type;
            const inputType = document.getElementById('direct-visit-type');
            if (inputType) inputType.value = type;

            const tabVisited = document.getElementById('direct-tab-visited');
            const tabNotVisited = document.getElementById('direct-tab-not-visited');
            const sectionVisited = document.getElementById('direct-visited-section');
            const sectionNotVisited = document.getElementById('direct-not-visited-section');
            const headerIcon = document.getElementById('direct-modal-header-icon');
            const submitBtn = document.getElementById('submit-direct-visit-btn');
            const submitBtnText = document.getElementById('submit-direct-visit-btn-text');

            if (type === 'visited') {
                // Highlight Visited Tab
                tabVisited.className = "py-2 px-3 rounded-lg font-bold text-xs transition flex items-center justify-center gap-1.5 bg-white dark:bg-slate-900 text-emerald-600 dark:text-emerald-400 shadow-sm border border-emerald-500/20";
                tabNotVisited.className = "py-2 px-3 rounded-lg font-bold text-xs transition flex items-center justify-center gap-1.5 text-slate-600 dark:text-slate-400 hover:text-rose-600 dark:hover:text-rose-400";
                
                sectionVisited.classList.remove('hidden');
                sectionNotVisited.classList.add('hidden');

                if (headerIcon) {
                    headerIcon.className = "w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-500 text-white flex items-center justify-center text-lg flex-shrink-0 shadow-md shadow-emerald-600/20";
                    headerIcon.innerHTML = '<i class="fa-solid fa-clipboard-check"></i>';
                }

                if (submitBtn) {
                    submitBtn.className = "px-5 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-600 hover:from-emerald-500 hover:to-cyan-500 text-white font-bold text-xs active:scale-95 transition shadow-lg shadow-emerald-600/30 flex items-center gap-2";
                }
                if (submitBtnText) {
                    submitBtnText.innerText = "{{ app()->getLocale() === 'ar' ? 'حفظ واعتماد الزيارة' : 'Confirm & Save Visit' }}";
                }
            } else {
                // Highlight Don't Visit Tab
                tabVisited.className = "py-2 px-3 rounded-lg font-bold text-xs transition flex items-center justify-center gap-1.5 text-slate-600 dark:text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400";
                tabNotVisited.className = "py-2 px-3 rounded-lg font-bold text-xs transition flex items-center justify-center gap-1.5 bg-white dark:bg-slate-900 text-rose-600 dark:text-rose-400 shadow-sm border border-rose-500/20";
                
                sectionVisited.classList.add('hidden');
                sectionNotVisited.classList.remove('hidden');

                if (headerIcon) {
                    headerIcon.className = "w-10 h-10 rounded-xl bg-gradient-to-tr from-rose-600 to-amber-600 text-white flex items-center justify-center text-lg flex-shrink-0 shadow-md shadow-rose-600/20";
                    headerIcon.innerHTML = '<i class="fa-solid fa-circle-xmark"></i>';
                }

                if (submitBtn) {
                    submitBtn.className = "px-5 py-2.5 rounded-xl bg-gradient-to-r from-rose-600 via-pink-600 to-amber-600 hover:from-rose-500 hover:to-amber-500 text-white font-bold text-xs active:scale-95 transition shadow-lg shadow-rose-600/30 flex items-center gap-2";
                }
                if (submitBtnText) {
                    submitBtnText.innerText = "{{ app()->getLocale() === 'ar' ? 'حفظ تقرير عدم الزيارة' : 'Save Non-Visit Record' }}";
                }
            }
        }

        // Custom Outcome Dropdown Controls
        function toggleDirectOutcomeDropdown() {
            const menu = document.getElementById('direct-outcome-menu');
            const chevron = document.getElementById('direct-outcome-chevron');
            if (menu) {
                const isHidden = menu.classList.contains('hidden');
                menu.classList.toggle('hidden', !isHidden);
                if (chevron) chevron.style.transform = isHidden ? 'rotate(180deg)' : 'rotate(0deg)';
            }
        }

        function selectDirectOutcome(val, iconName, colorName, textLabel) {
            const input = document.getElementById('direct-visit-outcome');
            if (input) input.value = val;

            const iconSpan = document.getElementById('direct-outcome-selected-icon');
            if (iconSpan) {
                iconSpan.className = `w-6 h-6 rounded-lg bg-${colorName}-500/15 text-${colorName}-600 dark:text-${colorName}-400 flex items-center justify-center text-xs flex-shrink-0`;
                iconSpan.innerHTML = `<i class="fa-solid ${iconName}"></i>`;
            }

            const textSpan = document.getElementById('direct-outcome-selected-text');
            if (textSpan) textSpan.innerText = textLabel;

            document.querySelectorAll('#direct-outcome-menu .outcome-option').forEach(opt => {
                const check = opt.querySelector('.outcome-check');
                if (opt.getAttribute('data-value') === val) {
                    if (check) check.classList.remove('hidden');
                    opt.classList.add('bg-slate-100/70', 'dark:bg-slate-800');
                } else {
                    if (check) check.classList.add('hidden');
                    opt.classList.remove('bg-slate-100/70', 'dark:bg-slate-800');
                }
            });

            const menu = document.getElementById('direct-outcome-menu');
            if (menu) menu.classList.add('hidden');
            const chevron = document.getElementById('direct-outcome-chevron');
            if (chevron) chevron.style.transform = 'rotate(0deg)';
        }

        // Custom Non-Visit Reason Dropdown Controls
        function toggleDirectReasonDropdown() {
            const menu = document.getElementById('direct-reason-menu');
            const chevron = document.getElementById('direct-reason-chevron');
            if (menu) {
                const isHidden = menu.classList.contains('hidden');
                menu.classList.toggle('hidden', !isHidden);
                if (chevron) chevron.style.transform = isHidden ? 'rotate(180deg)' : 'rotate(0deg)';
            }
        }

        function selectDirectReason(val, iconName, colorName, textLabel) {
            const input = document.getElementById('direct-not-visit-reason');
            if (input) input.value = val;

            const iconSpan = document.getElementById('direct-reason-selected-icon');
            if (iconSpan) {
                iconSpan.className = `w-6 h-6 rounded-lg bg-${colorName}-500/15 text-${colorName}-600 dark:text-${colorName}-400 flex items-center justify-center text-xs flex-shrink-0`;
                iconSpan.innerHTML = `<i class="fa-solid ${iconName}"></i>`;
            }

            const textSpan = document.getElementById('direct-reason-selected-text');
            if (textSpan) textSpan.innerText = textLabel;

            document.querySelectorAll('#direct-reason-menu .reason-option').forEach(opt => {
                const check = opt.querySelector('.reason-check');
                if (opt.getAttribute('data-value') === val) {
                    if (check) check.classList.remove('hidden');
                    opt.classList.add('bg-slate-100/70', 'dark:bg-slate-800');
                } else {
                    if (check) check.classList.add('hidden');
                    opt.classList.remove('bg-slate-100/70', 'dark:bg-slate-800');
                }
            });

            const menu = document.getElementById('direct-reason-menu');
            if (menu) menu.classList.add('hidden');
            const chevron = document.getElementById('direct-reason-chevron');
            if (chevron) chevron.style.transform = 'rotate(0deg)';
        }

        // Close dropdowns on outside click
        document.addEventListener('click', function(e) {
            const outWrapper = document.getElementById('direct-outcome-wrapper');
            const outMenu = document.getElementById('direct-outcome-menu');
            if (outWrapper && outMenu && !outWrapper.contains(e.target)) {
                outMenu.classList.add('hidden');
                const chevron = document.getElementById('direct-outcome-chevron');
                if (chevron) chevron.style.transform = 'rotate(0deg)';
            }

            const reasonWrapper = document.getElementById('direct-reason-wrapper');
            const reasonMenu = document.getElementById('direct-reason-menu');
            if (reasonWrapper && reasonMenu && !reasonWrapper.contains(e.target)) {
                reasonMenu.classList.add('hidden');
                const chevron = document.getElementById('direct-reason-chevron');
                if (chevron) chevron.style.transform = 'rotate(0deg)';
            }
        });

        // Setup Direct Visit Product Selection and Search
        document.addEventListener('DOMContentLoaded', function() {
            const prodRows = document.querySelectorAll('#direct-products-list .direct-product-row');
            prodRows.forEach(row => {
                row.addEventListener('click', function(e) {
                    const cb = row.querySelector('.direct-product-cb');
                    if (!cb) return;
                    cb.checked = !cb.checked;
                    
                    const indicator = row.querySelector('.product-check-indicator');
                    if (cb.checked) {
                        row.classList.add('bg-emerald-500/10', 'border-emerald-500', 'ring-1', 'ring-emerald-500/30');
                        row.classList.remove('bg-white', 'dark:bg-slate-900', 'border-slate-200/80', 'dark:border-slate-700/80');
                        if (indicator) indicator.classList.remove('hidden');
                    } else {
                        row.classList.remove('bg-emerald-500/10', 'border-emerald-500', 'ring-1', 'ring-emerald-500/30');
                        row.classList.add('bg-white', 'dark:bg-slate-900', 'border-slate-200/80', 'dark:border-slate-700/80');
                        if (indicator) indicator.classList.add('hidden');
                    }

                    const checkedCount = document.querySelectorAll('#direct-products-list .direct-product-cb:checked').length;
                    const badgeCount = document.getElementById('direct-products-selected-count');
                    if (badgeCount) badgeCount.innerText = checkedCount;
                    if (checkedCount > 0) {
                        const err = document.getElementById('direct-products-error');
                        if (err) err.classList.add('hidden');
                    }
                });
            });

            // Product search filter
            const prodSearchInput = document.getElementById('direct-product-search');
            if (prodSearchInput) {
                prodSearchInput.addEventListener('input', function() {
                    const q = (this.value || '').trim().toLowerCase();
                    let visible = 0;
                    prodRows.forEach(r => {
                        const search = r.getAttribute('data-search') || '';
                        if (!q || search.includes(q)) {
                            r.style.display = 'flex';
                            visible++;
                        } else {
                            r.style.display = 'none';
                        }
                    });
                    const noResults = document.getElementById('no-direct-products-found');
                    if (noResults) {
                        noResults.classList.toggle('hidden', visible > 0);
                    }
                });
            }

            // Real-time input validation listeners
            const visitNotesInput = document.getElementById('direct-visit-notes');
            if (visitNotesInput) {
                visitNotesInput.addEventListener('input', function() {
                    if (this.value.trim().length >= 3) {
                        const err = document.getElementById('direct-notes-error');
                        if (err) err.classList.add('hidden');
                    }
                });
            }

            const notVisitNotesInput = document.getElementById('direct-not-visit-notes');
            if (notVisitNotesInput) {
                notVisitNotesInput.addEventListener('input', function() {
                    if (this.value.trim().length >= 3) {
                        const err = document.getElementById('direct-not-visit-notes-error');
                        if (err) err.classList.add('hidden');
                    }
                });
            }

            // Initialize Portfolio Real-Time Pagination
            initPortfolioPagination();
        });

        // Submit Direct Visit (Single Step Execution)
        async function submitDirectVisitForm() {
            const isVisited = (currentDirectVisitData.type === 'visited');
            let outcome = isVisited 
                ? (document.getElementById('direct-visit-outcome').value || 'completed')
                : (document.getElementById('direct-not-visit-reason').value || 'doctor_unavailable');

            let notes = isVisited 
                ? (document.getElementById('direct-visit-notes').value || '').trim()
                : (document.getElementById('direct-not-visit-notes').value || '').trim();

            let productIds = [];

            if (isVisited) {
                // Validate notes
                if (!notes || notes.length < 3) {
                    const notesErr = document.getElementById('direct-notes-error');
                    if (notesErr) notesErr.classList.remove('hidden');
                    document.getElementById('direct-visit-notes').focus();
                    return;
                }

                // Validate products
                const selectedCbs = document.querySelectorAll('#direct-products-list .direct-product-cb:checked');
                productIds = Array.from(selectedCbs).map(cb => parseInt(cb.value)).filter(id => !isNaN(id) && id > 0);

                if (productIds.length === 0) {
                    const prodsErr = document.getElementById('direct-products-error');
                    if (prodsErr) {
                        prodsErr.classList.remove('hidden');
                        document.getElementById('direct-products-list').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }
                    return;
                }
            } else {
                // Validate non-visit notes
                if (!notes || notes.length < 3) {
                    const notVisitNotesErr = document.getElementById('direct-not-visit-notes-error');
                    if (notVisitNotesErr) notVisitNotesErr.classList.remove('hidden');
                    document.getElementById('direct-not-visit-notes').focus();
                    return;
                }
            }

            const btn = document.getElementById('submit-direct-visit-btn');
            const btnText = document.getElementById('submit-direct-visit-btn-text');
            if (btn) btn.disabled = true;
            if (btnText) btnText.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'جاري الحفظ والاعتماد...' : 'Saving...' }}';

            try {
                const response = await fetch("{{ route('mr.record_visit') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        contact_id: currentDirectVisitData.contactId,
                        assignment_id: currentDirectVisitData.assignmentId,
                        scheduled_visit_id: currentDirectVisitData.scheduledVisitId,
                        visit_type: currentDirectVisitData.type,
                        outcome: outcome,
                        notes: notes,
                        product_ids: productIds,
                        product_id: productIds[0] || null,
                        lat: currentDirectVisitData.lat,
                        lng: currentDirectVisitData.lng,
                        accuracy_m: currentDirectVisitData.accuracy
                    })
                });

                const res = await response.json();
                if (res.success) {
                    showToast(res.message, 'success');
                    closeDirectVisitModal();
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    showToast('Error: ' + res.message, 'error');
                    if (btn) btn.disabled = false;
                    if (btnText) btnText.innerText = isVisited ? "{{ app()->getLocale() === 'ar' ? 'حفظ واعتماد الزيارة' : 'Confirm & Save Visit' }}" : "{{ app()->getLocale() === 'ar' ? 'حفظ تقرير عدم الزيارة' : 'Save Non-Visit Record' }}";
                }
            } catch (err) {
                showToast('Network error: ' + err.message, 'error');
                if (btn) btn.disabled = false;
                if (btnText) btnText.innerText = isVisited ? "{{ app()->getLocale() === 'ar' ? 'حفظ واعتماد الزيارة' : 'Confirm & Save Visit' }}" : "{{ app()->getLocale() === 'ar' ? 'حفظ تقرير عدم الزيارة' : 'Save Non-Visit Record' }}";
            }
        }

        // Schedule Modal flow with strict Class Quota validation
        function openScheduleModal(assignmentId = null, doctorName = null) {
            const selectEl = document.getElementById('schedule-assignment-id');
            if (assignmentId && selectEl) {
                selectEl.value = assignmentId;
            }
            if (selectEl && selectEl.value) {
                onMrScheduleDoctorSelected(selectEl.value);
            }
            document.getElementById('schedule-modal').classList.remove('hidden');
        }

        function closeScheduleModal() {
            document.getElementById('schedule-modal').classList.add('hidden');
        }

        function onMrScheduleDoctorSelected(assignmentId) {
            const selectEl = document.getElementById('schedule-assignment-id');
            if (!selectEl) return;
            const opt = selectEl.selectedOptions[0];
            if (!opt) return;

            const canSchedule = opt.getAttribute('data-can-schedule') === '1';
            const cur = parseInt(opt.getAttribute('data-count') || '0', 10);
            const max = parseInt(opt.getAttribute('data-max') || '1', 10);
            const rem = parseInt(opt.getAttribute('data-rem') || '0', 10);
            const code = opt.getAttribute('data-class') || 'C';
            const docName = opt.getAttribute('data-name') || 'Doctor';

            const box = document.getElementById('mr-sch-quota-box');
            const badge = document.getElementById('mr-sch-quota-badge');
            const stat = document.getElementById('mr-sch-quota-stat');
            const bar = document.getElementById('mr-sch-quota-bar');
            const feedback = document.getElementById('mr-sch-quota-feedback');
            const warning = document.getElementById('mr-sch-quota-warning');
            const submitBtn = document.getElementById('mr-sch-submit-btn');
            const submitLabel = document.getElementById('mr-sch-submit-label');

            badge.textContent = 'Class ' + code;
            stat.textContent = cur + ' / ' + max + ' {{ app()->getLocale() === "ar" ? "زيارات" : "visits" }}';
            const pct = Math.min(100, Math.round((cur / max) * 100));
            bar.style.width = pct + '%';

            if (!canSchedule) {
                box.className = 'p-3.5 rounded-xl border transition-all duration-200 bg-rose-50 dark:bg-rose-950/20 border-rose-300 dark:border-rose-900';
                bar.className = 'h-1.5 rounded-full bg-rose-500 transition-all duration-300';
                feedback.textContent = '{{ app()->getLocale() === "ar" ? "تم الوصول للحد الأقصى للزيارات لهذا الطبيب." : "Quota reached for this doctor in this cycle." }}';
                warning.className = 'mt-2 p-2 rounded-lg text-xs font-bold bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300 border border-rose-300 dark:border-rose-900 block';
                warning.innerHTML = '<i class="fa-solid fa-ban mr-1"></i> {{ app()->getLocale() === "ar" ? "لا يمكن الجدولة: تم استنفاد الحد الأقصى لتصنيف Class " : "Notice: Quota reached for Class " }}' + code + ' (' + cur + '/' + max + ')';
                
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                submitLabel.textContent = '{{ app()->getLocale() === "ar" ? "الحصة مكتملة" : "Quota Reached" }}';
            } else {
                box.className = 'p-3.5 rounded-xl border transition-all duration-200 bg-emerald-50/60 dark:bg-emerald-950/20 border-emerald-300 dark:border-emerald-900';
                bar.className = 'h-1.5 rounded-full bg-emerald-500 transition-all duration-300';
                feedback.textContent = rem + ' {{ app()->getLocale() === "ar" ? "زيارات متبقية مسموحة في هذه الدورة." : "visits remaining in this cycle." }}';
                warning.className = 'mt-2 p-2 rounded-lg text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-900 block';
                warning.innerHTML = '<i class="fa-solid fa-circle-check mr-1"></i> {{ app()->getLocale() === "ar" ? "الحصة متاحة: يمكنك جدولة الزيارة." : "Quota available: You can schedule this visit." }}';

                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                submitLabel.textContent = '{{ app()->getLocale() === "ar" ? "تأكيد الحجز" : "Schedule Appointment" }}';
            }
        }

        async function submitScheduleForm() {
            const selectEl = document.getElementById('schedule-assignment-id');
            const assignmentId = selectEl ? selectEl.value : null;
            const datetime = document.getElementById('schedule-datetime').value;
            const notes = document.getElementById('schedule-notes').value;

            if (!assignmentId || !datetime) {
                showToast('{{ app()->getLocale() === "ar" ? "يرجى اختيار الطبيب وتاريخ/وقت الزيارة" : "Please select doctor and date/time slot" }}', 'error');
                return;
            }

            const opt = selectEl.selectedOptions[0];
            if (opt && opt.getAttribute('data-can-schedule') === '0') {
                showToast('{{ app()->getLocale() === "ar" ? "لا يمكن جدولة الزيارة لأن الطبيب استنفد حده الأقصى للزيارات في هذه الدورة" : "Cannot schedule: Doctor has reached maximum visit quota for this cycle" }}', 'error');
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

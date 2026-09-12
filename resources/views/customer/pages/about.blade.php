<x-layouts.customer :title="'OUR STORY & ABOUT US — ' . __('app.brand_name')" :description="'Discover the origins, clinical research standards (RS), and cellular biology behind BLUE ZONE Longevity & Cellular Health.'">
    <!-- Who We Are   Section -->
    <section id="who-we-are" class="py-24 bg-[#F6F5EF] dark:bg-[#031827] border-b border-[#0A4F78]/10 transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">

                <!-- Image Column with Dynamic Nodes -->
                <div class="lg:col-span-6 relative">
                    <div class="relative rounded-3xl overflow-hidden shadow-2xl border border-[#0A4F78]/20 group">
                        <img src="{{ asset('assets/images/story-lifestyle.webp') }}" alt="Blue Zone Centenarian Lifestyle"
                            onerror="this.onerror=null; this.src='{{ asset('assets/images/story-lifestyle.jpg') }}';"
                            width="800" height="460" loading="lazy" decoding="async"
                            class="w-full h-[460px] object-cover group-hover:scale-105 transition-transform duration-700" />
                        <div class="absolute inset-0 bg-gradient-to-t from-[#031827]/80 via-transparent to-transparent">
                        </div>

                        <!-- Floating Overlay Stats Card -->
                        <div
                            class="absolute bottom-6 left-6 right-6 p-6 rounded-2xl bg-[#031827]/85 backdrop-blur-md border border-[#2A8FC2]/40 text-white space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-[10px] font-black uppercase tracking-[0.25em] text-[#2A8FC2]">
                                    {{ __('app.research_dossier') }}
                                </span>

                                <span class="w-2 h-2 rounded-full bg-[#67B34A] animate-pulse"></span>
                            </div>

                            <p class="text-sm font-bold text-[#E8DCC4] leading-relaxed">
                                "{{ __('app.research_dossier_quote') }}"
                            </p>

                            <div class="grid grid-cols-3 gap-2 pt-2 border-t border-[#0A4F78]/40 text-center">
                                <div>
                                    <span class="block text-lg font-black text-[#2A8FC2]">100+</span>
                                    <span class="text-[9px] font-bold uppercase tracking-wider text-white/70">
                                        {{ __('app.cententarians') }}
                                    </span>
                                </div>

                                <div>
                                    <span class="block text-lg font-black text-[#2A8FC2]">5</span>
                                    <span class="text-[9px] font-bold uppercase tracking-wider text-white/70">
                                        {{ __('app.blue_zones') }}
                                    </span>
                                </div>

                                <div>
                                    <span class="block text-lg font-black text-[#67B34A]">100%</span>
                                    <span class="text-[9px] font-bold uppercase tracking-wider text-white/70">
                                        {{ __('app.bio_identical') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Editorial Content Column -->
                <div class="lg:col-span-6 space-y-6">
                    <span
                        class="inline-block text-xs font-black uppercase tracking-[0.3em] text-[#0A4F78] dark:text-[#2A8FC2]">
                        {{ __('app.who_is_blue_zone') }}
                    </span>

                    <h2
                        class="text-3xl sm:text-5xl font-black text-[#031827] dark:text-[#F6F5EF] tracking-tight leading-tight">
                        {{ __('app.inspired_by_the_worlds_longest_lived') }}
                    </h2>

                    <div class="w-16 h-1.5 bg-[#0A4F78] dark:bg-[#2A8FC2] rounded-full"></div>

                    <p class="text-base text-[#031827]/80 dark:text-[#F6F5EF]/80 font-medium leading-relaxed">
                        {{ __('app.blue_zone_intro') }}
                    </p>

                    <div class="space-y-3 pt-2">

                        <div class="flex items-start gap-3">
                            <span
                                class="w-6 h-6 rounded-full bg-[#67B34A]/20 text-[#67B34A] flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">
                                <i class="fa-solid fa-check"></i>
                            </span>

                            <p class="text-xs text-[#031827]/75 dark:text-[#F6F5EF]/75 font-semibold">
                                {{ __('app.blue_zone_benefit_1') }}
                            </p>
                        </div>

                        <div class="flex items-start gap-3">
                            <span
                                class="w-6 h-6 rounded-full bg-[#67B34A]/20 text-[#67B34A] flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">
                                <i class="fa-solid fa-check"></i>
                            </span>

                            <p class="text-xs text-[#031827]/75 dark:text-[#F6F5EF]/75 font-semibold">
                                {{ __('app.blue_zone_benefit_2') }}
                            </p>
                        </div>

                        <div class="flex items-start gap-3">
                            <span
                                class="w-6 h-6 rounded-full bg-[#67B34A]/20 text-[#67B34A] flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">
                                <i class="fa-solid fa-check"></i>
                            </span>

                            <p class="text-xs text-[#031827]/75 dark:text-[#F6F5EF]/75 font-semibold">
                                {{ __('app.blue_zone_benefit_3') }}
                            </p>
                        </div>

                    </div>

                    <div class="pt-4 flex flex-wrap gap-4">

                        <a href="#philosophy"
                            class="px-7 py-3.5 bg-[#0A4F78] hover:bg-[#062B49] text-white text-xs font-black uppercase tracking-widest rounded-xl transition-all shadow-md btn-sheen">
                            {{ __('app.our_philosophy') }}
                            <i class="fa-solid fa-arrow-right rtl:rotate-180 ml-1.5"></i>
                        </a>

                        <a href="{{ route('customer.pages.science') }}"
                            class="px-7 py-3.5 border border-[#0A4F78]/30 hover:border-[#0A4F78] text-[#031827] dark:text-[#F6F5EF] text-xs font-extrabold uppercase tracking-widest rounded-xl transition-colors">
                            {{ __('app.our_science') }}
                        </a>

                    </div>
                </div>


            </div>
        </div>
    </section>
    <!-- PHILOSOPHY SECTION -->
    <!-- 04. WHAT WE BELIEVE (THE 6 PILLARS) -->
    @php
        $bluezonePillars = [
            [
                'num' => '01',
                'title' => __('app.pillars.movement.title'),
                'desc' => __('app.pillars.movement.desc'),
                'tag' => __('app.pillars.movement.tag'),
                'img' => asset('assets/images/hero/hero-03.jpg'),
                'svgIcon' =>
                    '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>',
            ],
            [
                'num' => '02',
                'title' => __('app.pillars.nutrition.title'),
                'desc' => __('app.pillars.nutrition.desc'),
                'tag' => __('app.pillars.nutrition.tag'),
                'img' => asset('assets/images/hero/hero-02.jpg'),
                'svgIcon' =>
                    '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>',
            ],
            [
                'num' => '03',
                'title' => __('app.pillars.purpose.title'),
                'desc' => __('app.pillars.purpose.desc'),
                'tag' => __('app.pillars.purpose.tag'),
                'img' => asset('assets/images/hero/hero-01.jpg'),
                'svgIcon' =>
                    '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 01-2 2h-0a2 2 0 01-2-2v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>',
            ],
            [
                'num' => '04',
                'title' => __('app.pillars.community.title'),
                'desc' => __('app.pillars.community.desc'),
                'tag' => __('app.pillars.community.tag'),
                'img' => asset('assets/images/okinawa.jpg'),
                'svgIcon' =>
                    '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>',
            ],
            [
                'num' => '05',
                'title' => __('app.pillars.rest.title'),
                'desc' => __('app.pillars.rest.desc'),
                'tag' => __('app.pillars.rest.tag'),
                'img' => asset('assets/images/hero/hero-05.jpg'),
                'svgIcon' =>
                    '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>',
            ],
            [
                'num' => '06',
                'title' => __('app.pillars.wellness.title'),
                'desc' => __('app.pillars.wellness.desc'),
                'tag' => __('app.pillars.wellness.tag'),
                'img' => asset('assets/images/hero_longevity.jpg'),
                'svgIcon' =>
                    '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>',
            ],
        ];
    @endphp

    <section id="philosophy"
        class="py-14 sm:py-20 bg-white dark:bg-[#062B49] border-b border-[#0A4F78]/10 transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8 sm:space-y-12">
            <div class="text-center max-w-2xl mx-auto space-y-3">
                <span
                    class="text-[10px] sm:text-[11px] font-extrabold uppercase tracking-[0.25em] sm:tracking-[0.3em] text-[#0A4F78] dark:text-[#2A8FC2]">
                    {{ __('app.philosophy.eyebrow') }}
                </span>
                <h2
                    class="text-2xl sm:text-4xl lg:text-5xl font-light text-[#031827] dark:text-[#F6F5EF] tracking-tight">
                    {{ __('app.philosophy.heading_prefix') }} <span
                        class="font-bold text-[#67B34A]">{{ __('app.philosophy.heading_highlight') }}</span>
                </h2>
                <p
                    class="text-xs sm:text-sm text-[#031827]/70 dark:text-[#F6F5EF]/70 font-medium max-w-lg mx-auto leading-relaxed">
                    {{ __('app.philosophy.description') }}
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-8 lg:gap-12 items-center lg:min-h-[520px]">
                <!-- Column 1: Pillar Selectors -->
                <div class="lg:col-span-4 space-y-2">
                    <div class="lg:hidden flex overflow-x-auto gap-2 pb-2 scrollbar-none border-b border-[#0A4F78]/15">
                        @foreach ($bluezonePillars as $i => $pillar)
                            <button onclick="BLUEZONE_PILLARS.select({{ $i }})"
                                class="pillar-nav-btn shrink-0 px-3 sm:px-4 py-2 rounded-full text-[11px] sm:text-xs font-bold transition-all {{ $i === 0 ? 'bg-[#67B34A] text-white' : 'bg-[#0A4F78]/10 text-[#031827] dark:text-[#F6F5EF]' }}"
                                data-index="{{ $i }}">{{ $pillar['num'] }} {{ $pillar['title'] }}</button>
                        @endforeach
                    </div>

                    <div
                        class="hidden lg:block divide-y divide-[#0A4F78]/15 dark:divide-[#0A4F78]/30 border-y border-[#0A4F78]/15 dark:border-[#0A4F78]/30">
                        @foreach ($bluezonePillars as $i => $pillar)
                            <button onclick="BLUEZONE_PILLARS.select({{ $i }})"
                                onmouseenter="BLUEZONE_PILLARS.select({{ $i }})"
                                class="pillar-desktop-btn w-full py-4 px-3 flex items-center gap-4 text-left transition-all duration-300 group cursor-pointer border-l-4 {{ $i === 0 ? 'border-[#67B34A] bg-[#67B34A]/5' : 'border-transparent hover:border-[#2A8FC2]/50 hover:bg-[#0A4F78]/5' }}"
                                data-index="{{ $i }}">
                                <span
                                    class="text-xs font-extrabold {{ $i === 0 ? 'text-[#67B34A]' : 'text-[#031827]/40 dark:text-[#F6F5EF]/40' }} font-mono">{{ $pillar['num'] }}</span>
                                <span
                                    class="text-sm {{ $i === 0 ? 'font-bold text-[#67B34A]' : 'font-medium text-[#031827]/80 dark:text-[#F6F5EF]/80 group-hover:text-[#2A8FC2]' }} tracking-wider uppercase">{{ $pillar['title'] }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Column 2: Orbital SVG Animation -->
                <div class="hidden lg:flex lg:col-span-4 items-center justify-center relative py-4">
                    <div class="w-56 h-56 xl:w-72 xl:h-72 relative flex items-center justify-center">
                        <svg class="w-full h-full" viewBox="0 0 300 300" fill="none">
                            <circle cx="150" cy="150" r="110" stroke="#0A4F78" stroke-width="1.5"
                                stroke-opacity="0.25" stroke-dasharray="4 4" />
                            <circle cx="150" cy="150" r="70" stroke="#2A8FC2" stroke-width="1"
                                stroke-opacity="0.2" />

                            <line id="spoke-0" x1="150" y1="150" x2="150" y2="40"
                                stroke="#67B34A" stroke-width="2" opacity="0.9" />
                            <line id="spoke-1" x1="150" y1="150" x2="245" y2="95"
                                stroke="#2A8FC2" stroke-width="1" opacity="0.3" />
                            <line id="spoke-2" x1="150" y1="150" x2="245" y2="205"
                                stroke="#2A8FC2" stroke-width="1" opacity="0.3" />
                            <line id="spoke-3" x1="150" y1="150" x2="150" y2="260"
                                stroke="#2A8FC2" stroke-width="1" opacity="0.3" />
                            <line id="spoke-4" x1="150" y1="150" x2="55" y2="205"
                                stroke="#2A8FC2" stroke-width="1" opacity="0.3" />
                            <line id="spoke-5" x1="150" y1="150" x2="55" y2="95"
                                stroke="#2A8FC2" stroke-width="1" opacity="0.3" />

                            <circle id="node-0" cx="150" cy="40" r="10" fill="#67B34A"
                                stroke="#FFFFFF" stroke-width="2" class="transition-all duration-300" />
                            <circle id="node-1" cx="245" cy="95" r="7" fill="#0A4F78"
                                stroke="#2A8FC2" stroke-width="1.5" class="transition-all duration-300" />
                            <circle id="node-2" cx="245" cy="205" r="7" fill="#0A4F78"
                                stroke="#2A8FC2" stroke-width="1.5" class="transition-all duration-300" />
                            <circle id="node-3" cx="150" cy="260" r="7" fill="#0A4F78"
                                stroke="#2A8FC2" stroke-width="1.5" class="transition-all duration-300" />
                            <circle id="node-4" cx="55" cy="205" r="7" fill="#0A4F78"
                                stroke="#2A8FC2" stroke-width="1.5" class="transition-all duration-300" />
                            <circle id="node-5" cx="55" cy="95" r="7" fill="#0A4F78"
                                stroke="#2A8FC2" stroke-width="1.5" class="transition-all duration-300" />
                        </svg>

                        <div class="absolute inset-0 flex flex-col items-center justify-center text-center">
                            <div
                                class="w-20 h-20 xl:w-24 xl:h-24 rounded-full bg-[#031827] border-2 border-[#2A8FC2] flex flex-col items-center justify-center p-2 shadow-xl">
                                <span
                                    class="text-[8px] xl:text-[9px] font-black tracking-widest text-[#2A8FC2] uppercase">{{ __('app.brand_name') }}</span>
                                <span
                                    class="text-[7px] xl:text-[8px] font-bold text-[#E8DCC4] uppercase tracking-wider mt-0.5">{{ __('app.philosophy.core_label') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Column 3: Active Pillar Content Display Panel -->
                <div class="lg:col-span-4 p-5 sm:p-6 lg:p-8 rounded-2xl sm:rounded-3xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/20 shadow-xl transition-all duration-500 relative min-h-0 lg:min-h-[420px] flex flex-col justify-between"
                    id="pillar-content-panel">
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span id="pillar-active-num"
                                class="text-3xl sm:text-4xl font-light text-[#67B34A] font-mono">{{ $bluezonePillars[0]['num'] }}</span>
                            <span id="pillar-active-icon-box"
                                class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-[#67B34A]/15 text-[#67B34A] flex items-center justify-center">
                                {!! $bluezonePillars[0]['svgIcon'] !!}
                            </span>
                        </div>

                        <div
                            class="w-full h-28 sm:h-32 lg:h-36 rounded-xl sm:rounded-2xl overflow-hidden relative border border-[#0A4F78]/20 shadow-md group">
                            <img id="pillar-active-img" src="{{ $bluezonePillars[0]['img'] }}"
                                alt="{{ $bluezonePillars[0]['title'] }}" width="600" height="200"
                                loading="lazy" decoding="async"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-[#031827]/60 via-transparent to-transparent">
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <h3 id="pillar-active-title"
                                class="text-xl sm:text-2xl font-bold text-[#031827] dark:text-[#F6F5EF] tracking-tight">
                                {{ $bluezonePillars[0]['title'] }}
                            </h3>
                            <p id="pillar-active-desc"
                                class="text-xs text-[#031827]/75 dark:text-[#F6F5EF]/75 font-medium leading-relaxed">
                                {{ $bluezonePillars[0]['desc'] }}
                            </p>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-[#0A4F78]/15 dark:border-[#0A4F78]/30 space-y-1.5">
                        <span
                            class="block text-[10px] font-extrabold uppercase tracking-widest text-[#0A4F78] dark:text-[#2A8FC2]">{{ __('app.philosophy.impact_label') }}</span>
                        <div id="pillar-active-tag"
                            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-[#67B34A]/15 text-[#67B34A] text-xs font-bold">
                            <span>{{ $bluezonePillars[0]['tag'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Inline Script for Pillar Selection Controller -->
    <script>
        (function() {
            const PILLARS_DATA = @json($bluezonePillars);

            function selectPillar(idx) {
                if (idx < 0 || idx >= PILLARS_DATA.length) return;
                const p = PILLARS_DATA[idx];

                // 1. Update Desktop Left Nav
                const desktopBtns = document.querySelectorAll('.pillar-desktop-btn');
                desktopBtns.forEach((btn, i) => {
                    const numSpan = btn.querySelector('span:first-child');
                    const titleSpan = btn.querySelector('span:last-child');
                    if (i === idx) {
                        btn.className =
                            'pillar-desktop-btn w-full py-4 px-3 flex items-center gap-4 text-left transition-all duration-300 group cursor-pointer border-l-4 border-[#67B34A] bg-[#67B34A]/10';
                        if (numSpan) numSpan.className = 'text-xs font-extrabold text-[#67B34A] font-mono';
                        if (titleSpan) titleSpan.className =
                            'text-sm font-bold text-[#67B34A] tracking-wider uppercase';
                    } else {
                        btn.className =
                            'pillar-desktop-btn w-full py-4 px-3 flex items-center gap-4 text-left transition-all duration-300 group cursor-pointer border-l-4 border-transparent hover:border-[#2A8FC2]/50 hover:bg-[#0A4F78]/5';
                        if (numSpan) numSpan.className =
                            'text-xs font-extrabold text-[#031827]/40 dark:text-[#F6F5EF]/40 font-mono';
                        if (titleSpan) titleSpan.className =
                            'text-sm font-medium text-[#031827]/80 dark:text-[#F6F5EF]/80 tracking-wider uppercase group-hover:text-[#2A8FC2]';
                    }
                });

                // 2. Update Mobile Horizontal Nav
                const mobileBtns = document.querySelectorAll('.pillar-nav-btn');
                mobileBtns.forEach((btn, i) => {
                    if (i === idx) {
                        btn.className =
                            'pillar-nav-btn shrink-0 px-3 sm:px-4 py-2 rounded-full text-[11px] sm:text-xs font-bold transition-all bg-[#67B34A] text-white';
                    } else {
                        btn.className =
                            'pillar-nav-btn shrink-0 px-3 sm:px-4 py-2 rounded-full text-[11px] sm:text-xs font-bold transition-all bg-[#0A4F78]/10 text-[#031827] dark:text-[#F6F5EF]';
                    }
                });

                // 3. Update Orbital Center SVG Nodes & Spokes
                for (let i = 0; i < 6; i++) {
                    const node = document.getElementById(`node-${i}`);
                    const spoke = document.getElementById(`spoke-${i}`);
                    if (i === idx) {
                        if (node) {
                            node.setAttribute('r', '11');
                            node.setAttribute('fill', '#67B34A');
                            node.setAttribute('stroke', '#FFFFFF');
                            node.setAttribute('stroke-width', '2.5');
                        }
                        if (spoke) {
                            spoke.setAttribute('stroke', '#67B34A');
                            spoke.setAttribute('stroke-width', '2');
                            spoke.setAttribute('opacity', '0.9');
                        }
                    } else {
                        if (node) {
                            node.setAttribute('r', '7');
                            node.setAttribute('fill', '#0A4F78');
                            node.setAttribute('stroke', '#2A8FC2');
                            node.setAttribute('stroke-width', '1.5');
                        }
                        if (spoke) {
                            spoke.setAttribute('stroke', '#2A8FC2');
                            spoke.setAttribute('stroke-width', '1');
                            spoke.setAttribute('opacity', '0.25');
                        }
                    }
                }

                // 4. Update Right Display Panel with smooth fade
                const panel = document.getElementById('pillar-content-panel');
                if (panel) {
                    panel.style.opacity = '0.4';
                    setTimeout(() => {
                        const numEl = document.getElementById('pillar-active-num');
                        const titleEl = document.getElementById('pillar-active-title');
                        const descEl = document.getElementById('pillar-active-desc');
                        const tagEl = document.getElementById('pillar-active-tag');
                        const iconBox = document.getElementById('pillar-active-icon-box');
                        const imgEl = document.getElementById('pillar-active-img');

                        if (numEl) numEl.textContent = p.num;
                        if (titleEl) titleEl.textContent = p.title;
                        if (descEl) descEl.textContent = p.desc;
                        if (tagEl) tagEl.innerHTML = `<span>${p.tag}</span>`;
                        if (iconBox) iconBox.innerHTML = p.svgIcon;
                        if (imgEl) {
                            imgEl.src = p.img;
                            imgEl.alt = p.title;
                        }

                        panel.style.opacity = '1';
                    }, 180);
                }
            }

            window.BLUEZONE_PILLARS = {
                select: selectPillar
            };
        })();
    </script>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-20 space-y-20 sm:space-y-28">

        <!-- 01. EDITORIAL BRAND HERO -->




        {{-- =========================================================
    2. THE 4-STAGE SCIENCE JOURNEY
    ========================================================== --}}
        <section class="space-y-10" id="bz-science-journey-section" aria-labelledby="bz-science-process-title">

            {{-- Section Heading --}}
            <div class="text-center max-w-xl mx-auto space-y-2">

                <span
                    class="text-[10px] font-extrabold uppercase tracking-[0.25em]
                       text-[#0A4F78] dark:text-[#2A8FC2]">
                    {{ __('app.the_process') }}
                </span>

                <h2 id="bz-science-process-title"
                    class="text-2xl sm:text-3xl font-bold tracking-tight
                       text-[#031827] dark:text-[#F6F5EF]">
                    {{ __('app.the_four_stages') }}
                </h2>

            </div>


            {{-- =====================================================
        Desktop Horizontal Timeline
        ====================================================== --}}
            <div class="hidden md:block relative py-6 max-w-3xl mx-auto" id="bz-science-desktop-timeline">

                {{-- Base Line --}}
                <div class="absolute top-1/2 left-[12%] right-[12%]
                       h-0.5 bg-[#0A4F78]/15 dark:bg-[#0A4F78]/30
                       -translate-y-1/2 z-0"
                    aria-hidden="true"></div>

                {{-- Progress --}}
                <div id="bz-timeline-progress"
                    class="absolute top-1/2 left-[12%]
                       h-0.5 bg-[#67B34A]
                       -translate-y-1/2 z-0
                       transition-all duration-500"
                    style="width: 0%;" aria-hidden="true"></div>

                <div class="grid grid-cols-4 gap-4 relative z-10 text-center">

                    {{-- Stage 01 --}}
                    <button type="button" onclick="BLUEZONE_SCIENCE.select(0)"
                        onmouseenter="BLUEZONE_SCIENCE.select(0)"
                        class="bz-timeline-node group cursor-pointer
                           flex flex-col items-center gap-2
                           transition-transform duration-300
                           focus:outline-none focus-visible:ring-2
                           focus-visible:ring-[#67B34A] focus-visible:ring-offset-2
                           rounded-xl"
                        data-index="0" aria-label="{{ __('app.source_from_nature') }}">

                        <div
                            class="node-circle w-11 h-11 rounded-full
                               bg-[#67B34A] text-white
                               flex items-center justify-center
                               font-mono text-xs font-black
                               shadow-[0_0_15px_rgba(103,179,74,0.4)]
                               scale-110 border-2 border-[#67B34A]
                               transition-all">
                            01
                        </div>

                        <span
                            class="node-title text-xs font-extrabold
                               uppercase tracking-widest text-[#67B34A]">
                            {{ __('app.source') }}
                        </span>

                    </button>


                    {{-- Stage 02 --}}
                    <button type="button" onclick="BLUEZONE_SCIENCE.select(1)"
                        onmouseenter="BLUEZONE_SCIENCE.select(1)"
                        class="bz-timeline-node group cursor-pointer
                           flex flex-col items-center gap-2
                           transition-transform duration-300
                           focus:outline-none focus-visible:ring-2
                           focus-visible:ring-[#67B34A] focus-visible:ring-offset-2
                           rounded-xl"
                        data-index="1" aria-label="{{ __('app.formulation_precision') }}">

                        <div
                            class="node-circle w-9 h-9 rounded-full
                               bg-[#F6F5EF] dark:bg-[#031827]
                               border-2 border-[#0A4F78]/30
                               flex items-center justify-center
                               font-mono text-xs font-bold
                               text-[#031827]/50 dark:text-[#F6F5EF]/50
                               transition-all
                               group-hover:border-[#67B34A]">
                            02
                        </div>

                        <span
                            class="node-title text-xs font-semibold
                               uppercase tracking-widest
                               text-[#031827]/50 dark:text-[#F6F5EF]/50
                               group-hover:text-[#67B34A]
                               transition-colors">
                            {{ __('app.formulation') }}
                        </span>

                    </button>


                    {{-- Stage 03 --}}
                    <button type="button" onclick="BLUEZONE_SCIENCE.select(2)"
                        onmouseenter="BLUEZONE_SCIENCE.select(2)"
                        class="bz-timeline-node group cursor-pointer
                           flex flex-col items-center gap-2
                           transition-transform duration-300
                           focus:outline-none focus-visible:ring-2
                           focus-visible:ring-[#67B34A] focus-visible:ring-offset-2
                           rounded-xl"
                        data-index="2" aria-label="{{ __('app.validation_quality_focus') }}">

                        <div
                            class="node-circle w-9 h-9 rounded-full
                               bg-[#F6F5EF] dark:bg-[#031827]
                               border-2 border-[#0A4F78]/30
                               flex items-center justify-center
                               font-mono text-xs font-bold
                               text-[#031827]/50 dark:text-[#F6F5EF]/50
                               transition-all
                               group-hover:border-[#67B34A]">
                            03
                        </div>

                        <span
                            class="node-title text-xs font-semibold
                               uppercase tracking-widest
                               text-[#031827]/50 dark:text-[#F6F5EF]/50
                               group-hover:text-[#67B34A]
                               transition-colors">
                            {{ __('app.validation') }}
                        </span>

                    </button>


                    {{-- Stage 04 --}}
                    <button type="button" onclick="BLUEZONE_SCIENCE.select(3)"
                        onmouseenter="BLUEZONE_SCIENCE.select(3)"
                        class="bz-timeline-node group cursor-pointer
                           flex flex-col items-center gap-2
                           transition-transform duration-300
                           focus:outline-none focus-visible:ring-2
                           focus-visible:ring-[#67B34A] focus-visible:ring-offset-2
                           rounded-xl"
                        data-index="3" aria-label="{{ __('app.wellness_daily_life') }}">

                        <div
                            class="node-circle w-9 h-9 rounded-full
                               bg-[#F6F5EF] dark:bg-[#031827]
                               border-2 border-[#0A4F78]/30
                               flex items-center justify-center
                               font-mono text-xs font-bold
                               text-[#031827]/50 dark:text-[#F6F5EF]/50
                               transition-all
                               group-hover:border-[#67B34A]">
                            04
                        </div>

                        <span
                            class="node-title text-xs font-semibold
                               uppercase tracking-widest
                               text-[#031827]/50 dark:text-[#F6F5EF]/50
                               group-hover:text-[#67B34A]
                               transition-colors">
                            {{ __('app.wellness') }}
                        </span>

                    </button>

                </div>
            </div>


            {{-- =====================================================
        Mobile Vertical Timeline
        ====================================================== --}}
            <div class="md:hidden space-y-2
                   border-l-2 border-[#0A4F78]/20
                   pl-4 py-2"
                id="bz-science-mobile-timeline">

                <button type="button" onclick="BLUEZONE_SCIENCE.select(0)"
                    class="bz-mobile-node flex items-center gap-3
                       py-2 text-xs font-bold text-[#67B34A]"
                    data-index="0">

                    <span
                        class="w-6 h-6 rounded-full
                           bg-[#67B34A] text-white
                           flex items-center justify-center
                           text-[10px] shrink-0">
                        01
                    </span>

                    <span>
                        {{ __('app.source_from_nature') }}
                    </span>

                </button>


                <button type="button" onclick="BLUEZONE_SCIENCE.select(1)"
                    class="bz-mobile-node flex items-center gap-3
                       py-2 text-xs font-medium
                       text-[#031827]/60 dark:text-[#F6F5EF]/60"
                    data-index="1">

                    <span
                        class="w-6 h-6 rounded-full
                           bg-[#0A4F78]/20
                           text-[#031827] dark:text-[#F6F5EF]
                           flex items-center justify-center
                           text-[10px] shrink-0">
                        02
                    </span>

                    <span>
                        {{ __('app.formulation_precision') }}
                    </span>

                </button>


                <button type="button" onclick="BLUEZONE_SCIENCE.select(2)"
                    class="bz-mobile-node flex items-center gap-3
                       py-2 text-xs font-medium
                       text-[#031827]/60 dark:text-[#F6F5EF]/60"
                    data-index="2">

                    <span
                        class="w-6 h-6 rounded-full
                           bg-[#0A4F78]/20
                           text-[#031827] dark:text-[#F6F5EF]
                           flex items-center justify-center
                           text-[10px] shrink-0">
                        03
                    </span>

                    <span>
                        {{ __('app.validation_quality_focus') }}
                    </span>

                </button>


                <button type="button" onclick="BLUEZONE_SCIENCE.select(3)"
                    class="bz-mobile-node flex items-center gap-3
                       py-2 text-xs font-medium
                       text-[#031827]/60 dark:text-[#F6F5EF]/60"
                    data-index="3">

                    <span
                        class="w-6 h-6 rounded-full
                           bg-[#0A4F78]/20
                           text-[#031827] dark:text-[#F6F5EF]
                           flex items-center justify-center
                           text-[10px] shrink-0">
                        04
                    </span>

                    <span>
                        {{ __('app.wellness_daily_life') }}
                    </span>

                </button>

            </div>


            {{-- =====================================================
        Science Stage Display
        ====================================================== --}}
            <div id="bz-science-panel"
                class="p-6 sm:p-10 lg:p-12
                   rounded-3xl
                   bg-white dark:bg-[#062B49]
                   border border-[#0A4F78]/20
                   shadow-xl
                   transition-opacity duration-500
                   min-h-[380px]">

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">

                    {{-- Visual --}}
                    <div class="lg:col-span-6 relative">

                        <div
                            class="w-full
                               h-72 sm:h-96
                               rounded-2xl
                               overflow-hidden
                               relative
                               shadow-md
                               border border-[#0A4F78]/20
                               bg-[#031827]
                               group">

                            <img id="bz-science-active-img" src="{{ asset('assets/images/hero_longevity.jpg') }}"
                                alt="{{ __('app.from_nature') }}" loading="eager" decoding="async"
                                fetchpriority="high"
                                onerror="this.onerror=null; this.src='{{ asset('image.jpg') }}';"
                                class="w-full h-full object-cover
                                   transition-transform duration-700
                                   group-hover:scale-105" />

                            <div class="absolute inset-0
                                   bg-gradient-to-t
                                   from-[#031827]/90
                                   via-[#031827]/30
                                   to-transparent
                                   pointer-events-none"
                                aria-hidden="true"></div>


                            {{-- Biological Flow --}}
                            <div
                                class="absolute inset-x-4 bottom-4
                                   p-4 rounded-xl
                                   bg-[#031827]/85
                                   border border-[#2A8FC2]/30
                                   backdrop-blur-md
                                   text-white space-y-2">

                                <span
                                    class="block text-[9px] font-mono font-bold
                                       tracking-widest
                                       text-[#2A8FC2] uppercase">
                                    {{ __('app.biological_system_flow') }}
                                </span>

                                <div
                                    class="flex items-center justify-between
                                       gap-2
                                       text-[10px] font-mono font-bold
                                       text-white/90">

                                    <span id="bz-flow-step-1" class="text-[#67B34A]">
                                        {{ __('app.ingredient') }}
                                    </span>

                                    <i class="fa-solid fa-arrow-right
                                           rtl:rotate-180
                                           text-xs text-[#2A8FC2]"
                                        aria-hidden="true"></i>

                                    <span id="bz-flow-step-2" class="text-white/40">
                                        {{ __('app.function') }}
                                    </span>

                                    <i class="fa-solid fa-arrow-right
                                           rtl:rotate-180
                                           text-xs text-[#2A8FC2]"
                                        aria-hidden="true"></i>

                                    <span id="bz-flow-step-3" class="text-white/40">
                                        {{ __('app.body') }}
                                    </span>

                                    <i class="fa-solid fa-arrow-right
                                           rtl:rotate-180
                                           text-xs text-[#2A8FC2]"
                                        aria-hidden="true"></i>

                                    <span id="bz-flow-step-4" class="text-white/40">
                                        {{ __('app.wellness') }}
                                    </span>

                                </div>
                            </div>


                            {{-- Stage Code --}}
                            <div
                                class="absolute top-4 left-4
                                   px-3 py-1 rounded-full
                                   bg-[#031827]/80
                                   text-[#67B34A]
                                   text-[10px] font-mono font-bold
                                   border border-[#67B34A]/40
                                   backdrop-blur-sm">

                                {{ __('app.stage') }}

                                <span id="bz-science-stage-code">
                                    01/04
                                </span>

                            </div>

                        </div>

                    </div>


                    {{-- Stage Details --}}
                    <div class="lg:col-span-6 space-y-6">

                        <div class="space-y-2">

                            <div class="flex items-center gap-3">

                                <span id="bz-science-active-num"
                                    class="text-4xl font-light font-mono
                                       text-[#67B34A]">
                                    01
                                </span>

                                <span class="text-xs font-bold text-[#67B34A]">
                                    —
                                </span>

                                <span id="bz-science-active-stage"
                                    class="text-xs font-extrabold
                                       uppercase tracking-widest
                                       text-[#0A4F78]
                                       dark:text-[#2A8FC2]">
                                    {{ __('app.source') }}
                                </span>

                            </div>

                            <h3 id="bz-science-active-title"
                                class="text-2xl sm:text-4xl
                                   font-bold
                                   text-[#031827]
                                   dark:text-[#F6F5EF]
                                   tracking-tight">
                                {{ __('app.from_nature') }}
                            </h3>

                        </div>


                        <p id="bz-science-active-desc"
                            class="text-xs sm:text-sm
                               text-[#031827]/80
                               dark:text-[#F6F5EF]/80
                               font-medium leading-relaxed">
                            {{ __('app.source_stage_description') }}
                        </p>


                        <div
                            class="pt-4
                               border-t border-[#0A4F78]/15
                               dark:border-[#0A4F78]/30
                               space-y-2">

                            <span
                                class="block text-[10px] font-extrabold
                                   uppercase tracking-widest
                                   text-[#0A4F78]
                                   dark:text-[#2A8FC2]">
                                {{ __('app.key_highlights') }}
                            </span>

                            <div id="bz-science-active-chips" class="flex flex-wrap gap-2">

                                <span
                                    class="px-3 py-1.5 rounded-lg
                                       bg-[#67B34A]/15
                                       text-[#67B34A]
                                       text-xs font-bold">
                                    {{ __('app.standardized_botanical_extraction') }}
                                </span>

                                <span
                                    class="px-3 py-1.5 rounded-lg
                                       bg-[#0A4F78]/10
                                       dark:bg-[#0A4F78]/40
                                       text-[#031827]
                                       dark:text-[#F6F5EF]
                                       text-xs font-bold">
                                    {{ __('app.peak_potency_sourcing') }}
                                </span>

                            </div>
                        </div>

                    </div>

                </div>

            </div>

        </section>

    </div>

    <script>
        (() => {
            'use strict';

            const SCIENCE_DATA = [{
                    num: "01",
                    code: "01/04",
                    stage: @json(__('app.source')),
                    title: @json(__('app.from_nature')),
                    desc: @json(__('app.source_stage_description')),
                    img: @json(asset('assets/images/hero_longevity.jpg')),
                    chips: [
                        @json(__('app.standardized_botanical_extraction')),
                        @json(__('app.peak_potency_sourcing'))
                    ],
                    flowStep: 1
                },
                {
                    num: "02",
                    code: "02/04",
                    stage: @json(__('app.formulation')),
                    title: @json(__('app.precision_in_every_formula')),
                    desc: @json(__('app.formulation_stage_description')),
                    img: @json(asset('assets/products/blue-mind.webp')),
                    chips: [
                        @json(__('app.bio_identical_nutrient_ratios')),
                        @json(__('app.cellular_absorption_focus'))
                    ],
                    flowStep: 2
                },
                {
                    num: "03",
                    code: "03/04",
                    stage: @json(__('app.validation')),
                    title: @json(__('app.quality_you_can_trust')),
                    desc: @json(__('app.validation_stage_description')),
                    img: @json(asset('assets/images/blog-1.jpg')),
                    chips: [
                        @json(__('app.third_party_quality_verified')),
                        @json(__('app.zero_synthetic_additives'))
                    ],
                    flowStep: 3
                },
                {
                    num: "04",
                    code: "04/04",
                    stage: @json(__('app.wellness')),
                    title: @json(__('app.designed_for_daily_life')),
                    desc: @json(__('app.wellness_stage_description')),
                    img: @json(asset('assets/images/blog-2.jpg')),
                    chips: [
                        @json(__('app.cognitive_resilience')),
                        @json(__('app.daily_vitality_support'))
                    ],
                    flowStep: 4
                }
            ];


            const SELECTORS = {
                progress: '#bz-timeline-progress',
                desktopNodes: '#bz-science-desktop-timeline .bz-timeline-node',
                mobileNodes: '#bz-science-mobile-timeline .bz-mobile-node',
                panel: '#bz-science-panel',
                image: '#bz-science-active-img',
                code: '#bz-science-stage-code',
                number: '#bz-science-active-num',
                stage: '#bz-science-active-stage',
                title: '#bz-science-active-title',
                description: '#bz-science-active-desc',
                chips: '#bz-science-active-chips'
            };


            const getElement = (selector) => {
                return document.querySelector(selector);
            };


            const getElements = (selector) => {
                return document.querySelectorAll(selector);
            };


            function updateTimeline(index) {
                const progress = getElement(SELECTORS.progress);

                if (progress) {
                    const progressValues = [0, 33.3, 66.6, 100];
                    progress.style.width = `${progressValues[index]}%`;
                }


                getElements(SELECTORS.desktopNodes).forEach((node, nodeIndex) => {
                    const circle = node.querySelector('.node-circle');
                    const title = node.querySelector('.node-title');

                    if (nodeIndex === index) {
                        if (circle) {
                            circle.className =
                                'node-circle w-11 h-11 rounded-full ' +
                                'bg-[#67B34A] text-white flex items-center justify-center ' +
                                'font-mono text-xs font-black ' +
                                'shadow-[0_0_15px_rgba(103,179,74,0.4)] ' +
                                'scale-110 border-2 border-[#67B34A] transition-all';
                        }

                        if (title) {
                            title.className =
                                'node-title text-xs font-extrabold ' +
                                'uppercase tracking-widest text-[#67B34A]';
                        }

                    } else {

                        if (circle) {
                            circle.className =
                                'node-circle w-9 h-9 rounded-full ' +
                                'bg-[#F6F5EF] dark:bg-[#031827] ' +
                                'border-2 border-[#0A4F78]/30 ' +
                                'flex items-center justify-center ' +
                                'font-mono text-xs font-bold ' +
                                'text-[#031827]/50 dark:text-[#F6F5EF]/50 ' +
                                'transition-all group-hover:border-[#67B34A]';
                        }

                        if (title) {
                            title.className =
                                'node-title text-xs font-semibold ' +
                                'uppercase tracking-widest ' +
                                'text-[#031827]/50 dark:text-[#F6F5EF]/50 ' +
                                'group-hover:text-[#67B34A] ' +
                                'transition-colors';
                        }
                    }
                });


                getElements(SELECTORS.mobileNodes).forEach((node, nodeIndex) => {

                    if (nodeIndex === index) {
                        node.className =
                            'bz-mobile-node flex items-center gap-3 ' +
                            'py-2 text-xs font-bold text-[#67B34A]';
                    } else {
                        node.className =
                            'bz-mobile-node flex items-center gap-3 ' +
                            'py-2 text-xs font-medium ' +
                            'text-[#031827]/60 dark:text-[#F6F5EF]/60';
                    }
                });
            }


            function updateFlow(flowStep) {
                for (let step = 1; step <= 4; step++) {

                    const element = document.getElementById(
                        `bz-flow-step-${step}`
                    );

                    if (!element) continue;

                    element.className =
                        step <= flowStep ?
                        'text-[#67B34A] font-bold' :
                        'text-white/40 font-normal';
                }
            }


            function updateChips(chips) {
                const chipsContainer = getElement(SELECTORS.chips);

                if (!chipsContainer) return;

                const fragment = document.createDocumentFragment();

                chips.forEach((chip, index) => {

                    const span = document.createElement('span');

                    span.className =
                        index === 0 ?
                        'px-3 py-1.5 rounded-lg bg-[#67B34A]/15 text-[#67B34A] text-xs font-bold' :
                        'px-3 py-1.5 rounded-lg bg-[#0A4F78]/10 dark:bg-[#0A4F78]/40 text-[#031827] dark:text-[#F6F5EF] text-xs font-bold';

                    span.textContent = chip;

                    fragment.appendChild(span);
                });

                chipsContainer.replaceChildren(fragment);
            }


            function updatePanel(stage) {

                const image = getElement(SELECTORS.image);
                const code = getElement(SELECTORS.code);
                const number = getElement(SELECTORS.number);
                const stageElement = getElement(SELECTORS.stage);
                const title = getElement(SELECTORS.title);
                const description = getElement(SELECTORS.description);

                if (image) {
                    image.src = stage.img;
                    image.alt = stage.title;
                }

                if (code) {
                    code.textContent = stage.code;
                }

                if (number) {
                    number.textContent = stage.num;
                }

                if (stageElement) {
                    stageElement.textContent = stage.stage;
                }

                if (title) {
                    title.textContent = stage.title;
                }

                if (description) {
                    description.textContent = stage.desc;
                }

                updateChips(stage.chips);
            }


            function selectScience(index) {

                if (
                    !Number.isInteger(index) ||
                    index < 0 ||
                    index >= SCIENCE_DATA.length
                ) {
                    return;
                }

                const stage = SCIENCE_DATA[index];
                const panel = getElement(SELECTORS.panel);

                updateTimeline(index);
                updateFlow(stage.flowStep);

                if (!panel) {
                    updatePanel(stage);
                    return;
                }

                panel.style.opacity = '0.3';

                window.setTimeout(() => {
                    updatePanel(stage);
                    panel.style.opacity = '1';
                }, 180);
            }


            window.BLUEZONE_SCIENCE = {
                select: selectScience
            };


            // Initialize first stage.
            if (document.readyState === 'loading') {
                document.addEventListener(
                    'DOMContentLoaded',
                    () => selectScience(0), {
                        once: true
                    }
                );
            } else {
                selectScience(0);
            }

        })();
    </script>
</x-layouts.customer>

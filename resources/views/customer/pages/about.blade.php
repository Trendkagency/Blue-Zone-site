<x-layouts.customer :title="'OUR STORY & ABOUT US — ' . __('app.brand_name')" :description="'Discover the origins, clinical research standards (RS), and cellular biology behind BLUE ZONE Longevity & Cellular Health.'">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-20 space-y-20 sm:space-y-28">
        
        <!-- 01. EDITORIAL BRAND HERO -->
        <section class="max-w-4xl mx-auto text-center space-y-6">
            <span class="inline-block px-4 py-1.5 rounded-full bg-[#67B34A]/15 text-[#67B34A] text-xs font-black uppercase tracking-[0.3em] border border-[#67B34A]/30">
                OUR STORY & FOUNDATIONAL ETHOS
            </span>
            <h1 class="text-4xl sm:text-6xl md:text-7xl font-black text-[#031827] dark:text-[#F6F5EF] tracking-tight leading-tight">
                BRIDGING CENTENARIAN WISDOM & <span class="text-[#0A4F78] dark:text-[#2A8FC2]">CELLULAR MEDICINE</span>.
            </h1>
            <p class="text-base sm:text-xl text-[#031827]/75 dark:text-[#F6F5EF]/75 font-medium leading-relaxed max-w-3xl mx-auto">
                BLUE ZONE™ was born from an audacious biological inquiry: <strong class="text-[#0A4F78] dark:text-[#2A8FC2]">Why do populations in 5 specific global pockets routinely thrive past 100 years without chronic cellular degeneration?</strong> We isolate their most potent wild botanical flavonoids and formulate them in clinical bio-identical dosages.
            </p>
            <div class="pt-4 flex items-center justify-center gap-4 flex-wrap">
                <a href="#research-standards" class="px-8 py-4 rounded-full bg-[#0A4F78] hover:bg-[#062B49] text-white text-xs font-black uppercase tracking-widest transition-all shadow-xl hover:scale-105 btn-sheen">
                    EXPLORE RS STANDARDS ↓
                </a>
                <a href="{{ route('customer.shop') }}" class="px-8 py-4 rounded-full border border-[#0A4F78]/30 hover:bg-[#0A4F78]/10 text-[#0A4F78] dark:text-[#2A8FC2] text-xs font-black uppercase tracking-widest transition-all">
                    VIEW FORMULATIONS →
                </a>
            </div>
        </section>

        <!-- 02. VISUAL BRAND SHOWCASE / SCREENSHOT EXPERIENCE -->
        <section class="relative rounded-3xl overflow-hidden shadow-xl dark:shadow-2xl border border-[#0A4F78]/15 dark:border-[#0A4F78]/20 bg-white dark:bg-[#031827] transition-colors">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center p-8 sm:p-14">
                <div class="lg:col-span-6 space-y-6 text-[#031827] dark:text-white">
                    <span class="text-xs font-mono font-bold text-[#589c3e] dark:text-[#67B34A] tracking-widest uppercase">
                        LABORATORY PURITY & SOURCING
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-black tracking-tight leading-tight text-[#031827] dark:text-white">
                        Formulated in Europe. Verified by Molecular Chromatography.
                    </h2>
                    <p class="text-sm text-[#031827]/80 dark:text-[#E8DCC4] leading-relaxed">
                        From the remote mountainous slopes of Ikaria to the wild polyphenol groves of Okinawa, our research team partners directly with sustainable botanists to harvest plants at peak bioactive concentration. Every batch undergoes supercritical extraction to maintain delicate molecular structures.
                    </p>
                    <div class="grid grid-cols-3 gap-4 pt-4 border-t border-[#0A4F78]/15 dark:border-white/15 text-center">
                        <div class="space-y-1">
                            <span class="text-2xl sm:text-3xl font-black text-[#589c3e] dark:text-[#67B34A]">100%</span>
                            <span class="block text-[10px] uppercase font-bold text-[#031827]/70 dark:text-white/70">Bio-Identical</span>
                        </div>
                        <div class="space-y-1">
                            <span class="text-2xl sm:text-3xl font-black text-[#0A4F78] dark:text-[#2A8FC2]">0.0%</span>
                            <span class="block text-[10px] uppercase font-bold text-[#031827]/70 dark:text-white/70">Synthetics</span>
                        </div>
                        <div class="space-y-1">
                            <span class="text-2xl sm:text-3xl font-black text-[#0A4F78] dark:text-[#E8DCC4]">cGMP</span>
                            <span class="block text-[10px] uppercase font-bold text-[#031827]/70 dark:text-white/70">Certified</span>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-6">
                    <div class="relative rounded-2xl overflow-hidden shadow-xl border border-[#0A4F78]/10 dark:border-white/10 group">
                        <img src="{{ asset('assets/images/hero/hero-01.webp') }}" 
                             alt="BLUE ZONE Botanical Science Visual" 
                             onerror="this.onerror=null; this.src='{{ asset('assets/images/hero/hero-01.jpg') }}';" 
                             class="w-full h-80 sm:h-96 object-cover group-hover:scale-105 transition-transform duration-700 filter brightness-95" />
                        <div class="absolute inset-0 bg-gradient-to-t from-[#031827]/80 via-transparent to-transparent"></div>
                        <div class="absolute bottom-6 left-6 right-6 flex items-center justify-between text-xs text-[#031827] dark:text-white font-bold bg-white/90 dark:bg-[#031827]/80 backdrop-blur-md p-3.5 rounded-xl border border-[#0A4F78]/20 dark:border-white/10 shadow-lg">
                            <span>Clinical Research Compound Verification</span>
                            <span class="text-[#589c3e] dark:text-[#67B34A] font-mono">HPLC Verified ✓</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 03. DEDICATED RS (RESEARCH & STANDARDS) SECTION -->
        <section id="research-standards" data-section="regulatory-standards" class="space-y-10">
            <div class="text-center max-w-2xl mx-auto space-y-3">
                <span class="text-xs font-black uppercase tracking-[0.3em] text-[#0A4F78] dark:text-[#2A8FC2]">
                    STANDARDS & REGULATORY COMPLIANCE
                </span>
                <h2 class="text-3xl sm:text-5xl font-black text-[#031827] dark:text-[#F6F5EF] tracking-tight">
                    THE RS <span class="text-[#67B34A]">FRAMEWORK</span>.
                </h2>
                <p class="text-xs sm:text-sm text-[#031827]/70 dark:text-[#F6F5EF]/70 font-medium leading-relaxed">
                    Our rigorous Research & Standards (RS) protocol establishes an uncompromising scientific threshold that every BLUE ZONE formulation must satisfy before release.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- RS Pillar 1 -->
                <div class="p-8 rounded-3xl bg-white dark:bg-[#062B49] border border-[#0A4F78]/15 dark:border-[#0A4F78]/30 shadow-md space-y-4 hover:-translate-y-1 transition-transform">
                    <div class="w-14 h-14 rounded-2xl bg-[#0A4F78]/10 dark:bg-[#0A4F78]/30 flex items-center justify-center text-2xl text-[#0A4F78] dark:text-[#2A8FC2]">
                        🔬
                    </div>
                    <span class="text-[10px] font-mono font-bold text-[#0A4F78] dark:text-[#2A8FC2] uppercase">RS PROTOCOL 01</span>
                    <h3 class="text-lg font-black text-[#031827] dark:text-[#F6F5EF]">Standardized Potency</h3>
                    <p class="text-xs text-[#031827]/70 dark:text-[#F6F5EF]/70 leading-relaxed">
                        Unlike crushed raw herb powders, every botanical is standardized to exact active fractions (e.g. 55% Bacosides in Bacopa, 95% Curcuminoids) using high-performance liquid chromatography.
                    </p>
                </div>

                <!-- RS Pillar 2 -->
                <div class="p-8 rounded-3xl bg-white dark:bg-[#062B49] border border-[#0A4F78]/15 dark:border-[#0A4F78]/30 shadow-md space-y-4 hover:-translate-y-1 transition-transform">
                    <div class="w-14 h-14 rounded-2xl bg-[#67B34A]/15 flex items-center justify-center text-2xl text-[#67B34A]">
                        🛡️
                    </div>
                    <span class="text-[10px] font-mono font-bold text-[#67B34A] uppercase">RS PROTOCOL 02</span>
                    <h3 class="text-lg font-black text-[#031827] dark:text-[#F6F5EF]">Heavy Metal Screening</h3>
                    <p class="text-xs text-[#031827]/70 dark:text-[#F6F5EF]/70 leading-relaxed">
                        Inductively Coupled Plasma Mass Spectrometry (ICP-MS) tests every raw harvest for lead, arsenic, cadmium, and mercury to parts-per-billion clinical cleanliness.
                    </p>
                </div>

                <!-- RS Pillar 3 -->
                <div class="p-8 rounded-3xl bg-white dark:bg-[#062B49] border border-[#0A4F78]/15 dark:border-[#0A4F78]/30 shadow-md space-y-4 hover:-translate-y-1 transition-transform">
                    <div class="w-14 h-14 rounded-2xl bg-[#2A8FC2]/15 flex items-center justify-center text-2xl text-[#2A8FC2]">
                        🧬
                    </div>
                    <span class="text-[10px] font-mono font-bold text-[#2A8FC2] uppercase">RS PROTOCOL 03</span>
                    <h3 class="text-lg font-black text-[#031827] dark:text-[#F6F5EF]">Phospholipid Delivery</h3>
                    <p class="text-xs text-[#031827]/70 dark:text-[#F6F5EF]/70 leading-relaxed">
                        Phytosome and liposomal encapsulation binds hydrophobic polyphenols to sunflower-derived phospholipids, enhancing gastrointestinal passage and cellular permeability up to 29x.
                    </p>
                </div>

                <!-- RS Pillar 4 -->
                <div class="p-8 rounded-3xl bg-white dark:bg-[#062B49] border border-[#0A4F78]/15 dark:border-[#0A4F78]/30 shadow-md space-y-4 hover:-translate-y-1 transition-transform">
                    <div class="w-14 h-14 rounded-2xl bg-amber-500/15 flex items-center justify-center text-2xl text-amber-500">
                        📜
                    </div>
                    <span class="text-[10px] font-mono font-bold text-amber-500 uppercase">RS PROTOCOL 04</span>
                    <h3 class="text-lg font-black text-[#031827] dark:text-[#F6F5EF]">Batch-Level Traceability</h3>
                    <p class="text-xs text-[#031827]/70 dark:text-[#F6F5EF]/70 leading-relaxed">
                        Every bottle possesses a dedicated Certificate of Analysis (CoA) tied to individual compounding lots, verified by independent third-party European testing laboratories.
                    </p>
                </div>
            </div>
        </section>

        <!-- 04. OUR SCIENCE INTEGRATED INSIDE ABOUT US -->
        <section id="our-science-section" class="p-8 sm:p-14 rounded-3xl bg-[#F6F5EF] dark:bg-[#062B49] border border-[#0A4F78]/20 shadow-xl space-y-12">
            <div class="max-w-3xl space-y-3">
                <span class="text-xs font-black uppercase tracking-[0.3em] text-[#0A4F78] dark:text-[#2A8FC2]">
                    OUR SCIENCE & CELLULAR MECHANICS
                </span>
                <h2 class="text-3xl sm:text-5xl font-black text-[#031827] dark:text-[#F6F5EF] tracking-tight">
                    HOW BLUE ZONE WORKS AT THE <span class="text-[#67B34A]">CELLULAR LEVEL</span>.
                </h2>
                <p class="text-xs sm:text-sm text-[#031827]/75 dark:text-[#F6F5EF]/75 font-medium leading-relaxed">
                    Longevity is not merely lifespan; it is healthspan. Our formulations target the four fundamental biological hallmarks of human aging.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="p-6 rounded-2xl bg-white dark:bg-[#031827] border border-[#0A4F78]/15 space-y-3 shadow-xs">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-[#0A4F78] text-white flex items-center justify-center font-bold text-xs">01</span>
                        <h4 class="text-base font-black text-[#031827] dark:text-[#F6F5EF]">Mitochondrial ATP & NAD+ Synthesis</h4>
                    </div>
                    <p class="text-xs text-[#031827]/70 dark:text-[#F6F5EF]/70 leading-relaxed">
                        Mitochondria produce 90% of cellular energy. As we age, mitochondrial respiration declines by ~8% per decade. BLUE ZONE supplies direct coenzymes (Co-Q10 Ubiquinol, PQQ, Acetyl L-Carnitine) to reactivate cellular Krebs cycle efficiency and ATP synthesis.
                    </p>
                </div>

                <div class="p-6 rounded-2xl bg-white dark:bg-[#031827] border border-[#0A4F78]/15 space-y-3 shadow-xs">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-[#67B34A] text-white flex items-center justify-center font-bold text-xs">02</span>
                        <h4 class="text-base font-black text-[#031827] dark:text-[#F6F5EF]">Autophagy & Senolytic Clearance</h4>
                    </div>
                    <p class="text-xs text-[#031827]/70 dark:text-[#F6F5EF]/70 leading-relaxed">
                        Senescent "zombie cells" secrete damaging inflammatory cytokines. Our fasting-mimetic plant compounds stimulate macro-autophagy, allowing lysosomal enzymes to break down and recycle dysfunctional cellular debris.
                    </p>
                </div>

                <div class="p-6 rounded-2xl bg-white dark:bg-[#031827] border border-[#0A4F78]/15 space-y-3 shadow-xs">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-[#2A8FC2] text-white flex items-center justify-center font-bold text-xs">03</span>
                        <h4 class="text-base font-black text-[#031827] dark:text-[#F6F5EF]">Neuro-Synaptic Plasticity & BBB Transport</h4>
                    </div>
                    <p class="text-xs text-[#031827]/70 dark:text-[#F6F5EF]/70 leading-relaxed">
                        The Blood-Brain Barrier strictly regulates cerebral uptake. We engineer phospholipid carriers that deliver Bacosides and Phosphatidylserine directly across the BBB to protect dendritic arborization and acetylcholine neurotransmitter stores.
                    </p>
                </div>

                <div class="p-6 rounded-2xl bg-white dark:bg-[#031827] border border-[#0A4F78]/15 space-y-3 shadow-xs">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-purple-600 text-white flex items-center justify-center font-bold text-xs">04</span>
                        <h4 class="text-base font-black text-[#031827] dark:text-[#F6F5EF]">Telomere Integrity & Genomic Defense</h4>
                    </div>
                    <p class="text-xs text-[#031827]/70 dark:text-[#F6F5EF]/70 leading-relaxed">
                        Chromosomal telomeres shorten with each cellular replication cycle. High-potency botanical antioxidants shield delicate telomeric DNA loops from reactive oxygen species, preserving replicative cellular integrity.
                    </p>
                </div>
            </div>

            <div class="text-center pt-4">
                <a href="{{ route('customer.pages.science') }}" class="inline-flex items-center gap-2 text-xs font-black uppercase tracking-wider text-[#0A4F78] dark:text-[#2A8FC2] hover:underline">
                    <span>READ THE COMPREHENSIVE CLINICAL WHITE PAPER</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </section>

        <!-- 05. THE 5 BLUE ZONES GEOGRAPHIC ORIGINS -->
        <section class="space-y-10">
            <div class="text-center max-w-2xl mx-auto space-y-3">
                <span class="text-xs font-black uppercase tracking-[0.3em] text-[#0A4F78] dark:text-[#2A8FC2]">
                    GEOGRAPHIC INSPIRATION
                </span>
                <h2 class="text-3xl sm:text-5xl font-black text-[#031827] dark:text-[#F6F5EF] tracking-tight">
                    THE FIVE LONGEVITY <span class="text-[#67B34A]">ZONES</span>.
                </h2>
                <p class="text-xs sm:text-sm text-[#031827]/70 dark:text-[#F6F5EF]/70 font-medium leading-relaxed">
                    Demographic regions verified to possess the highest concentrations of healthy nonagenarians and centenarians worldwide.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 text-center">
                <div class="p-6 rounded-2xl bg-white dark:bg-[#062B49] border border-[#0A4F78]/15 space-y-2">
                    <span class="text-2xl">🇯🇵</span>
                    <h4 class="text-sm font-black text-[#031827] dark:text-[#F6F5EF]">Okinawa, Japan</h4>
                    <p class="text-[11px] text-[#031827]/60 dark:text-[#F6F5EF]/60 font-medium">Turmeric, seaweed bio-polysaccharides, and daily *Ikigai* purpose.</p>
                </div>
                <div class="p-6 rounded-2xl bg-white dark:bg-[#062B49] border border-[#0A4F78]/15 space-y-2">
                    <span class="text-2xl">🇬🇷</span>
                    <h4 class="text-sm font-black text-[#031827] dark:text-[#F6F5EF]">Ikaria, Greece</h4>
                    <p class="text-[11px] text-[#031827]/60 dark:text-[#F6F5EF]/60 font-medium">Wild mountain tea polyphenols, rosemary, and natural midday circadian recovery.</p>
                </div>
                <div class="p-6 rounded-2xl bg-white dark:bg-[#062B49] border border-[#0A4F78]/15 space-y-2">
                    <span class="text-2xl">🇮🇹</span>
                    <h4 class="text-sm font-black text-[#031827] dark:text-[#F6F5EF]">Sardinia, Italy</h4>
                    <p class="text-[11px] text-[#031827]/60 dark:text-[#F6F5EF]/60 font-medium">Mountain altitude movement, flavonoid-dense Cannonau grapes, and familial bonds.</p>
                </div>
                <div class="p-6 rounded-2xl bg-white dark:bg-[#062B49] border border-[#0A4F78]/15 space-y-2">
                    <span class="text-2xl">🇨🇷</span>
                    <h4 class="text-sm font-black text-[#031827] dark:text-[#F6F5EF]">Nicoya, Costa Rica</h4>
                    <p class="text-[11px] text-[#031827]/60 dark:text-[#F6F5EF]/60 font-medium">Calcium and magnesium-rich aquifer waters paired with *Plan de Vida* vitality.</p>
                </div>
                <div class="p-6 rounded-2xl bg-white dark:bg-[#062B49] border border-[#0A4F78]/15 space-y-2">
                    <span class="text-2xl">🇺🇸</span>
                    <h4 class="text-sm font-black text-[#031827] dark:text-[#F6F5EF]">Loma Linda, USA</h4>
                    <p class="text-[11px] text-[#031827]/60 dark:text-[#F6F5EF]/60 font-medium">100% plant-forward whole foods diet and weekly restorative 24-hour sabbath rest.</p>
                </div>
            </div>
        </section>

        <!-- 06. CALL TO ACTION -->
        <section class="text-center p-10 sm:p-16 rounded-3xl bg-white dark:bg-[#031827] text-[#031827] dark:text-white space-y-6 border border-[#0A4F78]/20 dark:border-[#0A4F78]/30 shadow-xl transition-colors">
            <span class="text-xs font-mono font-bold text-[#589c3e] dark:text-[#67B34A] tracking-widest uppercase">
                START YOUR LONGEVITY PROTOCOL
            </span>
            <h2 class="text-3xl sm:text-5xl font-black tracking-tight text-[#031827] dark:text-white">
                Ready to Optimize Your Cellular Biology?
            </h2>
            <p class="text-sm sm:text-base text-[#031827]/75 dark:text-[#E8DCC4] max-w-xl mx-auto leading-relaxed">
                Explore our clinically validated formulations designed to elevate daily cognitive focus, protect mitochondrial stamina, and promote deep restorative sleep.
            </p>
            <div class="pt-2">
                <a href="{{ route('customer.shop') }}" class="inline-block px-10 py-5 rounded-2xl bg-[#0A4F78] hover:bg-[#062B49] text-white text-xs font-black uppercase tracking-widest shadow-2xl transition-all btn-sheen">
                    DISCOVER CLINICAL FORMULATIONS →
                </a>
            </div>
        </section>

    </div>
</x-layouts.customer>

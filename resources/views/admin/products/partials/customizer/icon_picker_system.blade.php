<!-- ========================================================== -->
<!-- BZ-OS ENTERPRISE FONTAWESOME ICON SELECTOR STUDIO MODAL     -->
<!-- ========================================================== -->
<div id="bzIconPickerModal" class="fixed inset-0 z-[99999] hidden items-center justify-center bg-slate-950/70 backdrop-blur-sm p-4 transition-all duration-200">
    <div class="relative w-full max-w-2xl max-h-[85vh] flex flex-col rounded-2xl bg-white dark:bg-[#031827] border-2 border-slate-200 dark:border-slate-700/80 shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-150">
        
        <!-- Header -->
        <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between bg-slate-50 dark:bg-[#062B49]">
            <div class="flex items-center gap-2.5">
                <span class="w-9 h-9 rounded-xl bg-teal-500/15 text-teal-600 dark:text-teal-400 flex items-center justify-center text-sm font-black shadow-inner">
                    <i class="fa-solid fa-icons"></i>
                </span>
                <div>
                    <h3 class="text-sm font-black text-slate-900 dark:text-white flex items-center gap-2">
                        <span>{{ $isAr ?? false ? 'مكتبة أيقونات المستحضر السريرية' : 'Clinical & Formulation Icon Library' }}</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-teal-500/20 text-teal-600 dark:text-teal-300">FA 6</span>
                    </h3>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">
                        {{ $isAr ?? false ? 'اختر أيقونة من القائمة المصنفة أو ابحث بالاسم للمسارات والشهادات' : 'Select a clinical visual icon or search by keyword' }}
                    </p>
                </div>
            </div>
            <button type="button" onclick="BZIconPicker.close()" class="w-8 h-8 rounded-xl bg-slate-200/60 dark:bg-slate-800 text-slate-500 hover:text-slate-900 dark:hover:text-white flex items-center justify-center transition-colors cursor-pointer">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <!-- Search Bar & Category Filters -->
        <div class="p-4 border-b border-slate-200 dark:border-slate-800 bg-white dark:bg-[#031827] space-y-3">
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute start-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" id="bzIconSearchInput" 
                       placeholder="{{ $isAr ?? false ? 'ابحث عن أيقونة (مثال: brain, leaf, dna, shield, atom, pill, flask)...' : 'Search icons (e.g. brain, leaf, dna, shield, atom, pill, flask)...' }}" 
                       oninput="BZIconPicker.filter(this.value)"
                       class="w-full ps-9 pe-9 py-2 text-xs rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-[#062B49] text-slate-900 dark:text-white font-mono focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none shadow-inner">
                <button type="button" id="bzIconSearchClear" onclick="BZIconPicker.clearSearch()" class="absolute end-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 hidden text-xs">
                    <i class="fa-solid fa-circle-xmark"></i>
                </button>
            </div>

            <!-- Categories Tabs -->
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 no-scrollbar text-[11px] font-bold" id="bzIconCategoryTabs">
                <button type="button" onclick="BZIconPicker.setCategory('all')" class="bz-cat-btn px-2.5 py-1 rounded-lg transition-all cursor-pointer bg-teal-600 text-white shadow-sm" data-category="all">
                    {{ $isAr ?? false ? 'الكل (80+)' : 'All (80+)' }}
                </button>
                <button type="button" onclick="BZIconPicker.setCategory('science')" class="bz-cat-btn px-2.5 py-1 rounded-lg transition-all cursor-pointer bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700" data-category="science">
                    🔬 {{ $isAr ?? false ? 'علمي ومسارات' : 'Science & Lab' }}
                </button>
                <button type="button" onclick="BZIconPicker.setCategory('nature')" class="bz-cat-btn px-2.5 py-1 rounded-lg transition-all cursor-pointer bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700" data-category="nature">
                    🌿 {{ $isAr ?? false ? 'نقاء وطبيعي' : 'Nature & Purity' }}
                </button>
                <button type="button" onclick="BZIconPicker.setCategory('trust')" class="bz-cat-btn px-2.5 py-1 rounded-lg transition-all cursor-pointer bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700" data-category="trust">
                    🛡️ {{ $isAr ?? false ? 'اعتمادات وضمان' : 'Trust & Badges' }}
                </button>
                <button type="button" onclick="BZIconPicker.setCategory('energy')" class="bz-cat-btn px-2.5 py-1 rounded-lg transition-all cursor-pointer bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700" data-category="energy">
                    ⚡ {{ $isAr ?? false ? 'طاقة وحيوية' : 'Vitality & Power' }}
                </button>
                <button type="button" onclick="BZIconPicker.setCategory('protocol')" class="bz-cat-btn px-2.5 py-1 rounded-lg transition-all cursor-pointer bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700" data-category="protocol">
                    ⏰ {{ $isAr ?? false ? 'بروتوكول وتوقيت' : 'Protocol & Regimen' }}
                </button>
                <button type="button" onclick="BZIconPicker.setCategory('commerce')" class="bz-cat-btn px-2.5 py-1 rounded-lg transition-all cursor-pointer bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700" data-category="commerce">
                    📦 {{ $isAr ?? false ? 'شحن وتسوق' : 'Commerce & Logistics' }}
                </button>
            </div>
        </div>

        <!-- Icons Grid Container (Scrollable) -->
        <div class="p-4 overflow-y-auto flex-1 max-h-[46vh] bg-slate-50/50 dark:bg-[#031827]/60" id="bzIconGridContainer">
            <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-2.5" id="bzIconGrid">
                <!-- Rendered Dynamically by JS for performance -->
            </div>
            
            <div id="bzIconEmptyState" class="hidden py-10 text-center space-y-2">
                <i class="fa-solid fa-magnifying-glass text-2xl text-slate-400"></i>
                <p class="text-xs font-bold text-slate-600 dark:text-slate-400">
                    {{ $isAr ?? false ? 'لا توجد أيقونة مطابقة لهذا البحث.' : 'No icons found matching your search.' }}
                </p>
                <p class="text-[11px] text-slate-400">
                    {{ $isAr ?? false ? 'يمكنك كتابة رمز الأيقونة المخصص في الحقل أدناه مباشرة.' : 'You can type any custom FontAwesome class in the field below.' }}
                </p>
            </div>
        </div>

        <!-- Footer: Custom Icon Input & Active Selection Status -->
        <div class="px-5 py-3 border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-[#062B49] flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <span class="text-[11px] font-bold text-slate-500 shrink-0">
                    {{ $isAr ?? false ? 'رمز مخصص:' : 'Custom Class:' }}
                </span>
                <div class="relative flex-1 sm:w-48">
                    <input type="text" id="bzIconCustomInput" 
                           placeholder="fa-solid fa-..." 
                           oninput="BZIconPicker.previewCustom(this.value)"
                           class="w-full px-2.5 py-1 text-xs rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-[#031827] text-slate-900 dark:text-white font-mono">
                </div>
                <button type="button" onclick="BZIconPicker.applyCustom()" 
                        class="px-2.5 py-1 rounded-lg bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold transition-all shadow-sm cursor-pointer">
                    {{ $isAr ?? false ? 'تطبيق' : 'Apply' }}
                </button>
            </div>

            <div class="flex items-center justify-between sm:justify-end gap-3">
                <div class="flex items-center gap-2">
                    <span class="text-[10px] text-slate-400">{{ $isAr ?? false ? 'المحددة حالياً:' : 'Active:' }}</span>
                    <span id="bzIconActivePreview" class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-teal-500/10 text-teal-600 dark:text-teal-400 font-mono text-[11px] font-bold border border-teal-500/20">
                        <i class="fa-solid fa-microchip" id="bzIconActivePreviewGlyph"></i>
                        <span id="bzIconActivePreviewName">fa-microchip</span>
                    </span>
                </div>
                <button type="button" onclick="BZIconPicker.close()" class="px-3 py-1.5 rounded-xl border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 text-xs font-bold hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer">
                    {{ $isAr ?? false ? 'إلغاء' : 'Close' }}
                </button>
            </div>
        </div>

    </div>
</div>

<script>
    // ==========================================================
    // BZ-OS FONTAWESOME ICON CATALOG & PICKER CONTROLLER
    // ==========================================================
    window.BZ_ICON_CATALOG = [
        // 🔬 SCIENCE & CELLULAR
        { name: 'fa-microchip', label: 'Microchip', labelAr: 'معالج حيوي', cat: 'science', tags: 'chip bio digital kinetics brain tech circuit' },
        { name: 'fa-atom', label: 'Atom', labelAr: 'ذرة / جزيء', cat: 'science', tags: 'atom molecule physics chemistry electron redox' },
        { name: 'fa-dna', label: 'DNA', labelAr: 'حمض نووي DNA', cat: 'science', tags: 'dna gene genetics helix molecular cell' },
        { name: 'fa-flask', label: 'Flask', labelAr: 'قارورة مخبرية', cat: 'science', tags: 'flask chemical science lab formula assay' },
        { name: 'fa-flask-vial', label: 'Assay Vial', labelAr: 'أنبوب معايرة', cat: 'science', tags: 'flask vial lab test experiment research' },
        { name: 'fa-microscope', label: 'Microscope', labelAr: 'مجهر مخبري', cat: 'science', tags: 'microscope optic lab bacteria cellular test' },
        { name: 'fa-brain', label: 'Brain / Neuro', labelAr: 'دماغ وأعصاب', cat: 'science', tags: 'brain cognitive mental memory focus neuro' },
        { name: 'fa-capsules', label: 'Capsules', labelAr: 'كبسولات دوائية', cat: 'science', tags: 'capsules pills medicine dose tablet pharma' },
        { name: 'fa-pills', label: 'Pills', labelAr: 'أقراص علاجية', cat: 'science', tags: 'pills tablets pharma medicine dose' },
        { name: 'fa-prescription-bottle', label: 'Rx Bottle', labelAr: 'عبوة دوائية', cat: 'science', tags: 'bottle prescription medicine pharma vitamins' },
        { name: 'fa-prescription-bottle-medical', label: 'Rx Medical', labelAr: 'عبوة طبية', cat: 'science', tags: 'bottle medical rx pharma pills' },
        { name: 'fa-vial', label: 'Clinical Vial', labelAr: 'عينة سريرية', cat: 'science', tags: 'vial tube blood assay fluid sample' },
        { name: 'fa-vials', label: 'Assay Vials', labelAr: 'أنابيب فحص', cat: 'science', tags: 'vials lab tubes clinical series' },
        { name: 'fa-mortar-pestle', label: 'Formulation', labelAr: 'هاون صيدلاني', cat: 'science', tags: 'mortar pestle herb compounding pharmacy formula' },
        { name: 'fa-syringe', label: 'Bio Injection', labelAr: 'حقن وامتصاص', cat: 'science', tags: 'syringe shot vaccine clinical needle' },
        { name: 'fa-heart-pulse', label: 'Cardio Pulse', labelAr: 'نبض ومؤشر حيوي', cat: 'science', tags: 'heart pulse cardio rate health vascular' },
        { name: 'fa-lungs', label: 'Respiratory', labelAr: 'تنفس ورئتان', cat: 'science', tags: 'lungs breath oxygen respiratory clinical' },
        { name: 'fa-eye', label: 'Vision / Focus', labelAr: 'بصر وتركيز', cat: 'science', tags: 'eye sight vision focus optical' },
        { name: 'fa-virus', label: 'Microbiology', labelAr: 'أحياء دقيقة', cat: 'science', tags: 'virus cell bacteria immunity flora' },
        { name: 'fa-shield-virus', label: 'Immunity Shield', labelAr: 'درع المناعة', cat: 'science', tags: 'immunity shield virus defense white blood' },

        // 🌿 NATURE & PURITY
        { name: 'fa-leaf', label: 'Organic Leaf', labelAr: 'ورقة نباتية', cat: 'nature', tags: 'leaf organic plant botanical green herbal pure' },
        { name: 'fa-seedling', label: 'Seedling Sprout', labelAr: 'برعم طبيعي', cat: 'nature', tags: 'seedling sprout plant grow natural bio' },
        { name: 'fa-tree', label: 'Botanical Tree', labelAr: 'مستخلص شجري', cat: 'nature', tags: 'tree forest wood bark natural herbal' },
        { name: 'fa-wheat-awn', label: 'Grain / Non-GMO', labelAr: 'قمح خالٍ من التعديل', cat: 'nature', tags: 'wheat grain non-gmo gluten oat cereal' },
        { name: 'fa-droplet', label: 'Pure Droplet', labelAr: 'قطرة نقية', cat: 'nature', tags: 'droplet drop water oil lipid purity' },
        { name: 'fa-droplet-slash', label: 'Solvent-Free', labelAr: 'خالٍ من المذيبات', cat: 'nature', tags: 'droplet slash pure clean free without non' },
        { name: 'fa-water', label: 'Bio Hydration', labelAr: 'ماء ونقاء', cat: 'nature', tags: 'water liquid hydration fluid mineral sea' },
        { name: 'fa-sun', label: 'Circadian Sun', labelAr: 'شمس وصباح', cat: 'nature', tags: 'sun morning circadian daylight vitamin d energy' },
        { name: 'fa-moon', label: 'Night Recovery', labelAr: 'قمر ومساء', cat: 'nature', tags: 'moon night sleep dream recovery dark' },
        { name: 'fa-cloud-sun', label: 'Equilibrium', labelAr: 'توازن يومي', cat: 'nature', tags: 'cloud sun balance weather daily circadian' },
        { name: 'fa-apple-whole', label: 'Whole Food', labelAr: 'غذاء طبيعي', cat: 'nature', tags: 'apple food fruit nutrition diet vitamin' },
        { name: 'fa-spa', label: 'Spa Wellness', labelAr: 'استرخاء وصحة', cat: 'nature', tags: 'spa wellness flower lotus relaxation calm' },
        { name: 'fa-clover', label: 'Herbal Clover', labelAr: 'عشبي نقي', cat: 'nature', tags: 'clover herb green nature botanical leaf' },
        { name: 'fa-feather', label: 'Gentle Purity', labelAr: 'امتصاص خفيف', cat: 'nature', tags: 'feather light gentle soft pure clean' },
        { name: 'fa-fire', label: 'Thermogenic', labelAr: 'حراري واستقلاب', cat: 'nature', tags: 'fire flame heat metabolic burn fat energy' },

        // 🛡️ ACCREDITATION & TRUST
        { name: 'fa-award', label: 'cGMP Award', labelAr: 'اعتماد ذهبي', cat: 'trust', tags: 'award ribbon medal quality standard fda gmp' },
        { name: 'fa-certificate', label: 'ISO Certificate', labelAr: 'شهادة رسمية', cat: 'trust', tags: 'certificate iso verify legal diploma standard' },
        { name: 'fa-check-double', label: 'Double Checked', labelAr: 'فحص مخبري مزدوج', cat: 'trust', tags: 'check double verified assay hplc tested' },
        { name: 'fa-circle-check', label: 'Verified Pass', labelAr: 'مطابق وموثق', cat: 'trust', tags: 'circle check ok success approve valid' },
        { name: 'fa-shield-halved', label: 'Clinical Defense', labelAr: 'درع حماية سريري', cat: 'trust', tags: 'shield protect defense security immune' },
        { name: 'fa-shield-check', label: 'Safety Assured', labelAr: 'أمان موثق', cat: 'trust', tags: 'shield check verified safe secure tested' },
        { name: 'fa-medal', label: 'Clinical Medal', labelAr: 'ميدالية التميز', cat: 'trust', tags: 'medal prize top gold clinical trial' },
        { name: 'fa-trophy', label: 'Top Tier Trophy', labelAr: 'درجة أولى', cat: 'trust', tags: 'trophy winner premium proven effective' },
        { name: 'fa-star', label: '5-Star Quality', labelAr: 'تقييم فائق', cat: 'trust', tags: 'star rank top best quality grade' },
        { name: 'fa-lock', label: 'Tamper Evident', labelAr: 'إغلاق محكم', cat: 'trust', tags: 'lock secure sealed safe tamper privacy' },
        { name: 'fa-scale-balanced', label: 'Potency Assay', labelAr: 'معايرة دقيقة', cat: 'trust', tags: 'scale balance legal fair weight assay standard' },
        { name: 'fa-file-shield', label: 'Monograph Compliant', labelAr: 'مطابق للدستور الدوائي', cat: 'trust', tags: 'file shield monograph document usp bp' },
        { name: 'fa-thumbs-up', label: 'Doctor Approved', labelAr: 'موصى به طبياً', cat: 'trust', tags: 'thumbs up like approve recommended doctor' },
        { name: 'fa-barcode', label: 'Traceability', labelAr: 'تتبع أصلي', cat: 'trust', tags: 'barcode scan track batch genuine origin' },

        // ⚡ VITALITY & POWER
        { name: 'fa-bolt', label: 'ATP Bio Energy', labelAr: 'طاقة خلوية ATP', cat: 'energy', tags: 'bolt lightning power energy fast spark electric' },
        { name: 'fa-battery-full', label: 'Full Vitality', labelAr: 'حيوية كاملة', cat: 'energy', tags: 'battery full charge vitality endurance power' },
        { name: 'fa-gauge-high', label: 'Peak Potency', labelAr: 'أقصى كفاءة', cat: 'energy', tags: 'gauge meter speed fast maximum performance' },
        { name: 'fa-dumbbell', label: 'Physical Stamina', labelAr: 'قوة بدنية', cat: 'energy', tags: 'dumbbell gym muscle fitness sport endurance' },
        { name: 'fa-person-running', label: 'Bio Movement', labelAr: 'حركة ونشاط', cat: 'energy', tags: 'person running cardio walk sport active' },
        { name: 'fa-chart-line', label: 'Proven Biomarkers', labelAr: 'نتائج مثبتة', cat: 'energy', tags: 'chart line graph growth trend clinical data' },
        { name: 'fa-arrow-trend-up', label: 'Upregulation', labelAr: 'تحفيز تصاعدي', cat: 'energy', tags: 'arrow trend up boost increase elevate synergy' },
        { name: 'fa-sparkles', label: 'Cell Longevity', labelAr: 'إشراق وتجدد', cat: 'energy', tags: 'sparkles stars shine glow skin longevity clean' },
        { name: 'fa-circle-nodes', label: 'Synaptic Network', labelAr: 'شبكة عصبية', cat: 'energy', tags: 'nodes network synapse brain connect connect' },
        { name: 'fa-network-wired', label: 'Signaling Pathway', labelAr: 'مسار إشاري', cat: 'energy', tags: 'network wired pathway kinetic signal cellular' },

        // ⏰ PROTOCOL & TIMING
        { name: 'fa-clock', label: 'Precision Timing', labelAr: 'توقيت دقيق', cat: 'protocol', tags: 'clock time hour schedule chronotherapy dose' },
        { name: 'fa-calendar-check', label: 'Daily Regimen', labelAr: 'جدول يومي', cat: 'protocol', tags: 'calendar date day month protocol schedule' },
        { name: 'fa-repeat', label: 'Daily Cycle', labelAr: 'تكرار يومي', cat: 'protocol', tags: 'repeat loop cycle routine refresh sync' },
        { name: 'fa-glass-water', label: 'Water Directive', labelAr: 'تناول مع الماء', cat: 'protocol', tags: 'glass water drink hydration take with' },
        { name: 'fa-bottle-droplet', label: 'Lipid Synergy', labelAr: 'مع الدهون الصحية', cat: 'protocol', tags: 'bottle droplet oil lipid fat absorption meal' },
        { name: 'fa-mug-hot', label: 'Warm Beverage', labelAr: 'مشروب دافئ', cat: 'protocol', tags: 'mug hot tea coffee warm drink morning' },
        { name: 'fa-utensils', label: 'With Meals', labelAr: 'مع الوجبات', cat: 'protocol', tags: 'utensils food dinner lunch eat digestive' },
        { name: 'fa-bed', label: 'Sleep Regimen', labelAr: 'قبل النوم', cat: 'protocol', tags: 'bed sleep night rest dream relax evening' },
        { name: 'fa-ban', label: 'Contraindication', labelAr: 'تحذير ومنع', cat: 'protocol', tags: 'ban stop forbid allergen caution alert no' },
        { name: 'fa-triangle-exclamation', label: 'Precaution Alert', labelAr: 'تنبيه وقائي', cat: 'protocol', tags: 'triangle exclamation warning alert caution hazard' },

        // 📦 COMMERCE & LOGISTICS
        { name: 'fa-truck-fast', label: 'Cold Express', labelAr: 'شحن مبرد سريع', cat: 'commerce', tags: 'truck shipping fast cold chain deliver post' },
        { name: 'fa-box-open', label: 'Sealed Package', labelAr: 'طرد أصلي مغلف', cat: 'commerce', tags: 'box open parcel package order item' },
        { name: 'fa-shield-heart', label: 'Refund Guarantee', labelAr: 'ضمان طبي كامل', cat: 'commerce', tags: 'shield heart care health guarantee love trust' },
        { name: 'fa-rotate-left', label: '30-Day Return', labelAr: 'استرجاع مرن', cat: 'commerce', tags: 'rotate left undo return refund guarantee' },
        { name: 'fa-credit-card', label: 'Secure Payment', labelAr: 'دفع آمن ومحمي', cat: 'commerce', tags: 'credit card payment checkout visa instant' },
        { name: 'fa-headset', label: 'Pharmacist Support', labelAr: 'استشارة صيدلانية', cat: 'commerce', tags: 'headset support call center help contact agent' },
        { name: 'fa-tag', label: 'Controlled Price', labelAr: 'سعر مدروس', cat: 'commerce', tags: 'tag price discount offer retail cost' },
        { name: 'fa-cart-shopping', label: 'Instant Protocol Cart', labelAr: 'سلة البروتوكول', cat: 'commerce', tags: 'cart shopping buy checkout order store' },
    ];

    window.BZIconPicker = {
        currentTargetInput: null,
        currentTargetTrigger: null,
        currentCategory: 'all',
        searchQuery: '',

        init() {
            this.renderGrid();
            
            // Close on Escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') this.close();
            });

            // Close when clicking modal backdrop
            const modal = document.getElementById('bzIconPickerModal');
            if (modal) {
                modal.addEventListener('click', (e) => {
                    if (e.target === modal) this.close();
                });
            }
        },

        open(triggerEl, inputSelector) {
            this.currentTargetTrigger = triggerEl;
            if (inputSelector) {
                this.currentTargetInput = document.querySelector(inputSelector);
            } else if (triggerEl) {
                // Find associated hidden input in parent wrapper or previous sibling
                const wrapper = triggerEl.closest('.bz-icon-picker-field');
                if (wrapper) {
                    this.currentTargetInput = wrapper.querySelector('.bz-icon-input');
                } else {
                    this.currentTargetInput = triggerEl.previousElementSibling || triggerEl.nextElementSibling;
                }
            }

            const activeValue = this.currentTargetInput ? this.currentTargetInput.value : 'fa-microchip';
            this.setActivePreview(activeValue);

            const modal = document.getElementById('bzIconPickerModal');
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.style.overflow = 'hidden';
            }

            // Focus search input
            setTimeout(() => {
                const search = document.getElementById('bzIconSearchInput');
                if (search) {
                    search.value = '';
                    search.focus();
                    this.filter('');
                }
            }, 50);
        },

        close() {
            const modal = document.getElementById('bzIconPickerModal');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.style.overflow = '';
            }
            this.currentTargetTrigger = null;
            this.currentTargetInput = null;
        },

        setCategory(cat) {
            this.currentCategory = cat;
            document.querySelectorAll('#bzIconCategoryTabs .bz-cat-btn').forEach(btn => {
                if (btn.dataset.category === cat) {
                    btn.className = 'bz-cat-btn px-2.5 py-1 rounded-lg transition-all cursor-pointer bg-teal-600 text-white shadow-sm';
                } else {
                    btn.className = 'bz-cat-btn px-2.5 py-1 rounded-lg transition-all cursor-pointer bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700';
                }
            });
            this.renderGrid();
        },

        filter(query) {
            this.searchQuery = (query || '').toLowerCase().trim();
            const clearBtn = document.getElementById('bzIconSearchClear');
            if (clearBtn) {
                clearBtn.style.display = this.searchQuery ? 'block' : 'none';
            }
            this.renderGrid();
        },

        clearSearch() {
            const search = document.getElementById('bzIconSearchInput');
            if (search) search.value = '';
            this.filter('');
            if (search) search.focus();
        },

        setActivePreview(iconClass) {
            const clean = this.cleanIconClass(iconClass);
            const glyph = document.getElementById('bzIconActivePreviewGlyph');
            const nameEl = document.getElementById('bzIconActivePreviewName');
            const customIn = document.getElementById('bzIconCustomInput');

            if (glyph) glyph.className = `fa-solid ${clean}`;
            if (nameEl) nameEl.textContent = clean;
            if (customIn) customIn.value = clean;
        },

        cleanIconClass(icon) {
            if (!icon) return 'fa-microchip';
            return icon.replace(/^(fa-solid|fa-regular|fa-brands|fas|far|fab)\s+/, '').trim();
        },

        renderGrid() {
            const grid = document.getElementById('bzIconGrid');
            const empty = document.getElementById('bzIconEmptyState');
            if (!grid) return;

            const activeVal = this.currentTargetInput ? this.cleanIconClass(this.currentTargetInput.value) : '';
            const isAr = {{ ($isAr ?? false) ? 'true' : 'false' }};

            let filtered = window.BZ_ICON_CATALOG.filter(item => {
                const matchCat = (this.currentCategory === 'all' || item.cat === this.currentCategory);
                if (!matchCat) return false;

                if (!this.searchQuery) return true;
                const searchStr = `${item.name} ${item.label} ${item.labelAr} ${item.tags}`.toLowerCase();
                return searchStr.includes(this.searchQuery);
            });

            if (filtered.length === 0) {
                grid.innerHTML = '';
                if (empty) empty.classList.remove('hidden');
                return;
            }

            if (empty) empty.classList.add('hidden');

            let html = '';
            filtered.forEach(item => {
                const isSelected = (item.name === activeVal);
                const displayLabel = isAr ? item.labelAr : item.label;

                html += `
                    <button type="button" 
                            onclick="BZIconPicker.selectIcon('${item.name}')"
                            title="${item.name} (${displayLabel})"
                            class="group relative flex flex-col items-center justify-center p-3 rounded-xl border-2 transition-all cursor-pointer text-center ${
                                isSelected 
                                    ? 'border-teal-500 bg-teal-500/10 dark:bg-teal-500/20 text-teal-600 dark:text-teal-400 shadow-md scale-102' 
                                    : 'border-slate-200 dark:border-slate-700/80 bg-white dark:bg-[#062B49] hover:border-teal-400 dark:hover:border-teal-500 hover:bg-teal-50/50 dark:hover:bg-teal-950/30 text-slate-700 dark:text-slate-300'
                            }">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg mb-1.5 transition-transform group-hover:scale-115 ${
                            isSelected ? 'bg-teal-500 text-white shadow-sm' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 group-hover:bg-teal-500 group-hover:text-white'
                        }">
                            <i class="fa-solid ${item.name}"></i>
                        </div>
                        <span class="text-[11px] font-bold truncate max-w-full block leading-tight">${displayLabel}</span>
                        <span class="text-[9px] font-mono text-slate-400 truncate max-w-full block mt-0.5 opacity-80">${item.name}</span>
                        ${isSelected ? '<span class="absolute top-1.5 end-1.5 w-2 h-2 rounded-full bg-teal-500 ring-2 ring-white dark:ring-[#031827]"></span>' : ''}
                    </button>
                `;
            });

            grid.innerHTML = html;
        },

        selectIcon(iconName) {
            const clean = this.cleanIconClass(iconName);
            if (this.currentTargetInput) {
                this.currentTargetInput.value = clean;
                
                // Dispatch input & change events for reactive two-way binding
                this.currentTargetInput.dispatchEvent(new Event('input', { bubbles: true }));
                this.currentTargetInput.dispatchEvent(new Event('change', { bubbles: true }));
            }

            if (this.currentTargetTrigger) {
                const previewGlyph = this.currentTargetTrigger.querySelector('.bz-icon-preview i, .bz-icon-preview');
                const nameLabel = this.currentTargetTrigger.querySelector('.bz-icon-name');
                if (previewGlyph) {
                    if (previewGlyph.tagName === 'I') {
                        previewGlyph.className = `fa-solid ${clean}`;
                    } else {
                        previewGlyph.innerHTML = `<i class="fa-solid ${clean}"></i>`;
                    }
                }
                if (nameLabel) {
                    nameLabel.textContent = clean;
                }
            }

            // Auto-trigger corresponding live preview synchronization
            this.syncContextualPreview();

            this.close();
        },

        previewCustom(val) {
            const clean = this.cleanIconClass(val);
            const glyph = document.getElementById('bzIconActivePreviewGlyph');
            const nameEl = document.getElementById('bzIconActivePreviewName');
            if (glyph) glyph.className = `fa-solid ${clean}`;
            if (nameEl) nameEl.textContent = clean;
        },

        applyCustom() {
            const input = document.getElementById('bzIconCustomInput');
            if (!input || !input.value.trim()) return;
            this.selectIcon(input.value.trim());
        },

        renderField(name, value = 'fa-microchip', label = '', onchange = '') {
            const clean = this.cleanIconClass(value) || 'fa-microchip';
            const fieldId = 'bz_icon_' + Math.random().toString(36).substring(2, 9);
            return `
                <div class="bz-icon-picker-field relative">
                    ${label ? `<label class="block text-[10px] font-bold text-slate-500 mb-0.5">${label}</label>` : ''}
                    <input type="hidden" name="${name}" id="${fieldId}" value="${clean}" class="bz-icon-input" ${onchange ? `onchange="${onchange}"` : ''}>
                    <button type="button" 
                            onclick="BZIconPicker.open(this)"
                            class="w-full flex items-center justify-between gap-1.5 px-2.5 py-1.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-[#031827] text-slate-800 dark:text-slate-200 hover:border-teal-500 hover:shadow-sm transition-all text-xs font-mono cursor-pointer group shadow-inner">
                        <span class="flex items-center gap-2 truncate">
                            <span class="w-6 h-6 rounded-lg bg-teal-500/10 text-teal-600 dark:text-teal-400 flex items-center justify-center text-xs shrink-0 group-hover:scale-110 transition-transform bz-icon-preview">
                                <i class="fa-solid ${clean}"></i>
                            </span>
                            <span class="font-bold text-[11px] truncate bz-icon-name text-slate-700 dark:text-slate-300">${clean}</span>
                        </span>
                        <i class="fa-solid fa-chevron-down text-[9px] text-slate-400 group-hover:text-teal-500 transition-colors shrink-0"></i>
                    </button>
                </div>
            `;
        },

        syncContextualPreview() {
            if (typeof syncPrecisionPathwaysPreview === 'function') syncPrecisionPathwaysPreview();
            if (typeof syncHeroOverlaysPreview === 'function') syncHeroOverlaysPreview();
            if (typeof syncBuyBoxPreview === 'function') syncBuyBoxPreview();
            if (typeof syncProtocolCardsPreview === 'function') syncProtocolCardsPreview();
        }
    };

    // Auto-initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => window.BZIconPicker.init());
    } else {
        window.BZIconPicker.init();
    }
</script>

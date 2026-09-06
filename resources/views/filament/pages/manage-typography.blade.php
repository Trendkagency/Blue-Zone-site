<x-filament-panels::page>
    <div 
        class="bz-typography-wrapper"
        x-data="{
            primaryFont: @entangle('font_family').live,
            headingFont: @entangle('font_heading_family').live,
            fontSize: @entangle('font_size_base').live,
            headingWeight: @entangle('font_weight_headings').live,
            bodyWeight: @entangle('font_weight_body').live,
            letterSpacing: @entangle('font_letter_spacing').live,
            adminLivePreview: false,
            activeCategory: 'All',
            searchQuery: '',
            loadedFonts: new Set(['Cairo']),

            init() {
                this.loadFont(this.primaryFont);
                if (this.headingFont && this.headingFont !== this.primaryFont) {
                    this.loadFont(this.headingFont);
                }

                this.$watch('primaryFont', (val) => {
                    this.loadFont(val);
                    if (this.adminLivePreview) {
                        this.applyAdminPreview();
                    }
                });

                this.$watch('headingFont', (val) => {
                    this.loadFont(val);
                    if (this.adminLivePreview) {
                        this.applyAdminPreview();
                    }
                });

                this.$watch('adminLivePreview', (val) => {
                    if (val) {
                        this.applyAdminPreview();
                    } else {
                        this.removeAdminPreview();
                    }
                });

                window.addEventListener('bz-typography-saved', (e) => {
                    this.loadFont(e.detail.font_family);
                    if (e.detail.font_heading_family) {
                        this.loadFont(e.detail.font_heading_family);
                    }
                });
            },

            loadFont(fontName) {
                if (!fontName || this.loadedFonts.has(fontName)) return;

                if (fontName === 'Mont Blanc') {
                    this.loadFont('Montserrat');
                    this.loadFont('Tajawal');
                    this.loadedFonts.add('Mont Blanc');
                    return;
                }

                const kebab = fontName.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
                const formattedGoogle = fontName.replace(/\s+/g, '+');
                const linkId = 'bz-font-link-' + kebab;

                if (!document.getElementById(linkId)) {
                    const link = document.createElement('link');
                    link.id = linkId;
                    link.rel = 'stylesheet';
                    link.href = `https://fonts.bunny.net/css?family=${kebab}:300,400,500,600,700,800,900&display=swap`;
                    link.onerror = () => {
                        link.href = `https://fonts.googleapis.com/css2?family=${formattedGoogle}:wght@300;400;500;600;700;800;900&display=swap`;
                    };
                    document.head.appendChild(link);
                    this.loadedFonts.add(fontName);
                }
            },

            applyAdminPreview() {
                let previewStyle = document.getElementById('bz-admin-live-font-override');
                if (!previewStyle) {
                    previewStyle = document.createElement('style');
                    previewStyle.id = 'bz-admin-live-font-override';
                    document.head.appendChild(previewStyle);
                }
                const primaryStack = `'${this.primaryFont}', 'Mont Blanc', 'Montserrat', 'Tajawal', 'Cairo', system-ui, sans-serif`;
                const headingStack = `'${this.headingFont}', '${this.primaryFont}', 'Mont Blanc', 'Montserrat', 'Tajawal', 'Cairo', system-ui, sans-serif`;
                previewStyle.innerHTML = `
                    body, .fi-body, .fi-main, .fi-sidebar, .fi-topbar, .fi-ta-table, .fi-fo-field-wrp {
                        font-family: ${primaryStack} !important;
                    }
                    h1, h2, h3, h4, h5, h6, .fi-header-heading, .fi-section-header-heading, .fi-modal-heading {
                        font-family: ${headingStack} !important;
                        font-weight: ${this.headingWeight} !important;
                    }
                `;
            },

            removeAdminPreview() {
                const previewStyle = document.getElementById('bz-admin-live-font-override');
                if (previewStyle) {
                    previewStyle.remove();
                }
            }
        }"
    >
        <style>
            .bz-typography-wrapper {
                display: flex;
                flex-direction: column;
                gap: 1.5rem;
            }
            .bz-hero-banner {
                background: linear-gradient(135deg, #0A4F78 0%, #062B49 50%, #031827 100%);
                color: #FFFFFF;
                border-radius: 1rem;
                padding: 1.75rem;
                box-shadow: 0 10px 25px -5px rgba(10, 79, 120, 0.3);
                border: 1px solid rgba(255, 255, 255, 0.1);
            }
            .bz-hero-header {
                display: flex;
                flex-direction: column;
                gap: 1rem;
                justify-content: space-between;
            }
            @media (min-width: 768px) {
                .bz-hero-header {
                    flex-direction: row;
                    align-items: center;
                }
            }
            .bz-brand-badge {
                display: flex;
                align-items: center;
                gap: 1rem;
            }
            .bz-brand-icon {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 3.25rem;
                height: 3.25rem;
                border-radius: 0.75rem;
                background: rgba(255, 255, 255, 0.12);
                font-size: 1.75rem;
                backdrop-filter: blur(8px);
                border: 1px solid rgba(255, 255, 255, 0.15);
            }
            .bz-brand-title {
                font-size: 1.35rem;
                font-weight: 800;
                margin: 0;
                color: #FFFFFF;
                letter-spacing: -0.02em;
            }
            .bz-brand-desc {
                font-size: 0.85rem;
                margin: 0.2rem 0 0 0;
                color: rgba(255, 255, 255, 0.85);
            }
            .bz-action-toolbar {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                gap: 0.75rem;
            }
            .bz-toggle-label {
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                background: rgba(255, 255, 255, 0.1);
                padding: 0.5rem 0.85rem;
                border-radius: 0.625rem;
                font-size: 0.775rem;
                font-weight: 600;
                cursor: pointer;
                border: 1px solid rgba(255, 255, 255, 0.15);
                transition: background 0.2s;
            }
            .bz-toggle-label:hover {
                background: rgba(255, 255, 255, 0.18);
            }
            .bz-btn-reset {
                background: rgba(255, 255, 255, 0.08);
                border: 1px solid rgba(255, 255, 255, 0.2);
                color: #FFFFFF;
                font-weight: 700;
                font-size: 0.8rem;
                padding: 0.5rem 1rem;
                border-radius: 0.625rem;
                cursor: pointer;
                transition: all 0.2s;
            }
            .bz-btn-reset:hover {
                background: rgba(255, 255, 255, 0.15);
            }
            .bz-btn-save {
                display: inline-flex;
                align-items: center;
                gap: 0.4rem;
                background: #67B34A;
                color: #FFFFFF;
                font-weight: 800;
                font-size: 0.8rem;
                padding: 0.55rem 1.25rem;
                border-radius: 0.625rem;
                border: none;
                cursor: pointer;
                box-shadow: 0 4px 12px rgba(103, 179, 74, 0.4);
                transition: all 0.2s;
                text-transform: uppercase;
                letter-spacing: 0.03em;
            }
            .bz-btn-save:hover {
                background: #589C3E;
                transform: translateY(-1px);
            }
            .bz-btn-save:active {
                transform: translateY(0);
            }
            .bz-pills-row {
                margin-top: 1rem;
                padding-top: 1rem;
                border-top: 1px solid rgba(255, 255, 255, 0.12);
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                gap: 0.5rem;
                font-size: 0.775rem;
            }
            .bz-pill {
                display: inline-flex;
                align-items: center;
                gap: 0.35rem;
                padding: 0.25rem 0.65rem;
                border-radius: 9999px;
                background: rgba(255, 255, 255, 0.12);
                font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
                font-weight: 600;
            }

            /* Main Layout Grid */
            .bz-main-grid {
                display: grid;
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
            @media (min-width: 1024px) {
                .bz-main-grid {
                    grid-template-columns: 7fr 5fr;
                }
            }

            /* Card Containers */
            .bz-panel-card {
                background: #FFFFFF;
                border: 1px solid #E2E8F0;
                border-radius: 1rem;
                padding: 1.5rem;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            }
            .dark .bz-panel-card {
                background: #0F172A;
                border-color: #1E293B;
            }
            .bz-panel-title {
                font-size: 1.05rem;
                font-weight: 800;
                margin: 0 0 1.25rem 0;
                color: #0F172A;
                display: flex;
                align-items: center;
                justify-content: space-between;
            }
            .dark .bz-panel-title {
                color: #F8FAFC;
            }

            /* Form Elements */
            .bz-form-grid {
                display: grid;
                grid-template-columns: 1fr;
                gap: 1.25rem;
            }
            @media (min-width: 640px) {
                .bz-form-grid {
                    grid-template-columns: 1fr 1fr;
                }
            }
            .bz-field-group {
                display: flex;
                flex-direction: column;
                gap: 0.4rem;
            }
            .bz-field-label {
                font-size: 0.75rem;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                color: #475569;
            }
            .dark .bz-field-label {
                color: #94A3B8;
            }
            .bz-select-input {
                width: 100%;
                border-radius: 0.625rem;
                border: 1px solid #CBD5E1;
                background-color: #FFFFFF;
                padding: 0.6rem 0.85rem;
                font-size: 0.875rem;
                font-weight: 600;
                color: #0F172A;
                outline: none;
                transition: border-color 0.2s, box-shadow 0.2s;
            }
            .dark .bz-select-input {
                background-color: #1E293B;
                border-color: #334155;
                color: #F8FAFC;
            }
            .bz-select-input:focus {
                border-color: #2A8FC2;
                box-shadow: 0 0 0 2px rgba(42, 143, 194, 0.2);
            }

            /* Option Button Groups */
            .bz-btn-group-5 {
                display: grid;
                grid-template-columns: repeat(5, 1fr);
                gap: 0.25rem;
            }
            .bz-btn-group-3 {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 0.25rem;
            }
            .bz-choice-btn {
                padding: 0.5rem 0.25rem;
                text-align: center;
                font-size: 0.75rem;
                font-weight: 700;
                border-radius: 0.5rem;
                border: 1px solid #E2E8F0;
                background: #F8FAFC;
                color: #334155;
                cursor: pointer;
                transition: all 0.15s;
            }
            .dark .bz-choice-btn {
                background: #1E293B;
                border-color: #334155;
                color: #CBD5E1;
            }
            .bz-choice-btn:hover {
                border-color: #2A8FC2;
            }
            .bz-choice-btn.active {
                background: #0A4F78;
                color: #FFFFFF;
                border-color: #0A4F78;
            }

            /* Catalog Section */
            .bz-catalog-header {
                display: flex;
                flex-direction: column;
                gap: 0.75rem;
                margin-bottom: 1rem;
            }
            @media (min-width: 640px) {
                .bz-catalog-header {
                    flex-direction: row;
                    align-items: center;
                    justify-content: space-between;
                }
            }
            .bz-category-pills {
                display: flex;
                flex-wrap: wrap;
                gap: 0.35rem;
            }
            .bz-cat-btn {
                padding: 0.3rem 0.65rem;
                font-size: 0.725rem;
                font-weight: 700;
                border-radius: 0.5rem;
                border: none;
                background: #F1F5F9;
                color: #475569;
                cursor: pointer;
                transition: all 0.15s;
            }
            .dark .bz-cat-btn {
                background: #1E293B;
                color: #94A3B8;
            }
            .bz-cat-btn.active {
                background: #2A8FC2;
                color: #FFFFFF;
            }
            .bz-font-grid {
                display: grid;
                grid-template-columns: 1fr;
                gap: 0.75rem;
                max-height: 520px;
                overflow-y: auto;
                padding-right: 0.25rem;
            }
            @media (min-width: 640px) {
                .bz-font-grid {
                    grid-template-columns: 1fr 1fr;
                }
            }
            .bz-font-card {
                padding: 0.85rem;
                border-radius: 0.75rem;
                border: 1px solid #E2E8F0;
                background: #FFFFFF;
                cursor: pointer;
                transition: all 0.15s ease-in-out;
                position: relative;
            }
            .dark .bz-font-card {
                background: rgba(30, 41, 59, 0.5);
                border-color: #1E293B;
            }
            .bz-font-card:hover {
                border-color: #2A8FC2;
                transform: translateY(-1px);
            }
            .bz-font-card.active {
                border-color: #2A8FC2;
                background: rgba(42, 143, 194, 0.06);
                box-shadow: 0 0 0 2px #2A8FC2;
            }
            .dark .bz-font-card.active {
                background: rgba(42, 143, 194, 0.15);
            }
            .bz-card-top {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 0.4rem;
            }
            .bz-card-title {
                font-size: 0.875rem;
                font-weight: 700;
                color: #0F172A;
            }
            .dark .bz-card-title {
                color: #F8FAFC;
            }
            .bz-card-tag {
                font-size: 0.65rem;
                font-weight: 700;
                padding: 0.15rem 0.4rem;
                border-radius: 0.35rem;
                background: #F1F5F9;
                color: #64748B;
            }
            .dark .bz-card-tag {
                background: #334155;
                color: #94A3B8;
            }
            .bz-card-ar-sample {
                font-size: 1rem;
                font-weight: 600;
                color: #1E293B;
                margin: 0.25rem 0;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            .dark .bz-card-ar-sample {
                color: #E2E8F0;
            }
            .bz-card-en-sample {
                font-size: 0.75rem;
                color: #64748B;
                margin: 0 0 0.4rem 0;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            .dark .bz-card-en-sample {
                color: #94A3B8;
            }
            .bz-card-footer {
                display: flex;
                align-items: center;
                justify-content: space-between;
                font-size: 0.7rem;
                color: #94A3B8;
                border-top: 1px solid #F1F5F9;
                padding-top: 0.35rem;
                margin-top: 0.35rem;
            }
            .dark .bz-card-footer {
                border-top-color: #334155;
            }

            /* Live Preview Sandbox */
            .bz-sticky-sandbox {
                position: sticky;
                top: 1.5rem;
            }
            .bz-sandbox-container {
                border-radius: 0.75rem;
                padding: 1.25rem;
                background: #F8FAFC;
                border: 1px solid #E2E8F0;
                display: flex;
                flex-direction: column;
                gap: 1rem;
                transition: all 0.2s;
            }
            .dark .bz-sandbox-container {
                background: rgba(15, 23, 42, 0.6);
                border-color: #1E293B;
            }
            .bz-preview-heading-ar {
                font-size: 1.25rem;
                line-height: 1.4;
                color: #031827;
                margin: 0 0 0.35rem 0;
            }
            .dark .bz-preview-heading-ar {
                color: #FFFFFF;
            }
            .bz-preview-heading-en {
                font-size: 1.1rem;
                line-height: 1.3;
                color: #0A4F78;
                margin: 0 0 0.5rem 0;
            }
            .dark .bz-preview-heading-en {
                color: #4FB0E6;
            }
            .bz-preview-body {
                line-height: 1.6;
                color: #475569;
                font-size: 0.85rem;
                margin: 0;
            }
            .dark .bz-preview-body {
                color: #CBD5E1;
            }
            .bz-preview-commerce-card {
                background: #FFFFFF;
                border: 1px solid #E2E8F0;
                border-radius: 0.75rem;
                padding: 0.85rem 1rem;
                display: flex;
                align-items: center;
                justify-content: space-between;
            }
            .dark .bz-preview-commerce-card {
                background: #1E293B;
                border-color: #334155;
            }
            .bz-live-pulse-badge {
                display: inline-flex;
                align-items: center;
                gap: 0.35rem;
                padding: 0.2rem 0.5rem;
                border-radius: 9999px;
                background: rgba(16, 185, 129, 0.12);
                color: #059669;
                font-size: 0.7rem;
                font-weight: 800;
            }
            .dark .bz-live-pulse-badge {
                background: rgba(16, 185, 129, 0.2);
                color: #34D399;
            }
            .bz-pulse-dot {
                width: 6px;
                height: 6px;
                border-radius: 50%;
                background: #10B981;
                animation: bz-pulse 1.5s infinite;
            }
            @keyframes bz-pulse {
                0% { opacity: 1; transform: scale(1); }
                50% { opacity: 0.4; transform: scale(1.3); }
                100% { opacity: 1; transform: scale(1); }
            }
        </style>

        <!-- Top Banner / Control Header -->
        <div class="bz-hero-banner">
            <div class="bz-hero-header">
                <div class="bz-brand-badge">
                    <div class="bz-brand-icon">
                        🔤
                    </div>
                    <div>
                        <h2 class="bz-brand-title">
                            Live Typography & System Font Engine
                        </h2>
                        <p class="bz-brand-desc">
                            Select and update fonts with instantaneous real-time preview across Filament Admin, Storefront, and Custom Admin.
                        </p>
                    </div>
                </div>

                <!-- Action Toolbar -->
                <div class="bz-action-toolbar">
                    <!-- Live Admin Preview Toggle -->
                    <label class="bz-toggle-label" title="Toggle immediate font preview across entire admin navigation">
                        <input type="checkbox" x-model="adminLivePreview" style="accent-color: #2A8FC2;">
                        <span>👁️ Live Admin Panel Preview</span>
                    </label>

                    <button 
                        type="button" 
                        wire:click="resetToDefault" 
                        class="bz-btn-reset"
                    >
                        Reset Defaults
                    </button>

                    <button 
                        type="button" 
                        wire:click="save" 
                        class="bz-btn-save"
                    >
                        <svg style="width: 14px; height: 14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Save & Apply Globally</span>
                    </button>
                </div>
            </div>

            <!-- Active Status Summary Pills -->
            <div class="bz-pills-row">
                <span style="opacity: 0.7;">Active Config:</span>
                <span class="bz-pill" style="color: #B8D98A;">
                    Primary: <span x-text="primaryFont"></span>
                </span>
                <span class="bz-pill" style="color: #9BC6E6;">
                    Headings: <span x-text="headingFont"></span>
                </span>
                <span class="bz-pill">
                    Size: <span x-text="fontSize"></span>
                </span>
                <span class="bz-pill">
                    Headings Weight: <span x-text="headingWeight"></span>
                </span>
                <span class="bz-pill">
                    Body Weight: <span x-text="bodyWeight"></span>
                </span>
            </div>
        </div>

        <div class="bz-main-grid">
            <!-- Left Column: Controls & Font Cards -->
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                <!-- Parameters Configuration Card -->
                <div class="bz-panel-card">
                    <div class="bz-panel-title">
                        <span>Typography Parameters</span>
                        <span class="bz-live-pulse-badge">
                            <span class="bz-pulse-dot"></span> Real-Time Sync
                        </span>
                    </div>

                    <div class="bz-form-grid">
                        <!-- Primary Font Family Select -->
                        <div class="bz-field-group">
                            <label class="bz-field-label">
                                Primary Body Font
                            </label>
                            <select 
                                wire:model.live="font_family" 
                                class="bz-select-input"
                            >
                                @foreach($this->availableFonts as $key => $font)
                                    <option value="{{ $key }}">{{ $font['label'] }} — {{ $font['category'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Headings Font Family Select -->
                        <div class="bz-field-group">
                            <label class="bz-field-label">
                                Headings & Brand Font
                            </label>
                            <select 
                                wire:model.live="font_heading_family" 
                                class="bz-select-input"
                            >
                                @foreach($this->availableFonts as $key => $font)
                                    <option value="{{ $key }}">{{ $font['label'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Base Font Size Selector -->
                        <div class="bz-field-group">
                            <label class="bz-field-label">
                                Base Font Size
                            </label>
                            <div class="bz-btn-group-5">
                                @foreach(['14px', '15px', '16px', '17px', '18px'] as $sz)
                                    <button 
                                        type="button" 
                                        wire:click="setBaseSize('{{ $sz }}')"
                                        :class="fontSize === '{{ $sz }}' ? 'active' : ''"
                                        class="bz-choice-btn"
                                    >
                                        {{ $sz }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <!-- Headings Weight Selector -->
                        <div class="bz-field-group">
                            <label class="bz-field-label">
                                Headings Weight
                            </label>
                            <div class="bz-btn-group-5">
                                @foreach(['500' => 'Med', '600' => 'Semi', '700' => 'Bold', '800' => 'XBold', '900' => 'Black'] as $wt => $lbl)
                                    <button 
                                        type="button" 
                                        wire:click="setHeadingWeight('{{ $wt }}')"
                                        :class="headingWeight === '{{ $wt }}' ? 'active' : ''"
                                        class="bz-choice-btn"
                                    >
                                        {{ $lbl }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <!-- Body Weight Selector -->
                        <div class="bz-field-group">
                            <label class="bz-field-label">
                                Body Text Weight
                            </label>
                            <div class="bz-btn-group-3">
                                @foreach(['300' => 'Light', '400' => 'Regular', '500' => 'Medium'] as $bw => $lbl)
                                    <button 
                                        type="button" 
                                        wire:click="setBodyWeight('{{ $bw }}')"
                                        :class="bodyWeight === '{{ $bw }}' ? 'active' : ''"
                                        class="bz-choice-btn"
                                    >
                                        {{ $lbl }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <!-- Letter Spacing Selector -->
                        <div class="bz-field-group">
                            <label class="bz-field-label">
                                Letter Spacing
                            </label>
                            <div class="bz-btn-group-3">
                                @foreach(['-0.015em' => 'Tight', 'normal' => 'Normal', '0.025em' => 'Relaxed'] as $lsp => $lbl)
                                    <button 
                                        type="button" 
                                        wire:click="setLetterSpacing('{{ $lsp }}')"
                                        :class="letterSpacing === '{{ $lsp }}' ? 'active' : ''"
                                        class="bz-choice-btn"
                                    >
                                        {{ $lbl }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Font Catalog Cards -->
                <div class="bz-panel-card">
                    <div class="bz-catalog-header">
                        <div>
                            <h3 style="font-size: 1rem; font-weight: 800; margin: 0; color: inherit;">
                                Curated Font Catalog ({{ count($this->availableFonts) }} Fonts)
                            </h3>
                            <p style="font-size: 0.75rem; color: #94A3B8; margin: 0.2rem 0 0 0;">
                                Click any card to apply immediately with instant preview.
                            </p>
                        </div>

                        <!-- Category Filters -->
                        <div class="bz-category-pills">
                            @foreach(['All', 'Arabic', 'Geometric', 'Editorial'] as $cat)
                                <button 
                                    type="button"
                                    @click="activeCategory = '{{ $cat }}'"
                                    :class="activeCategory === '{{ $cat }}' ? 'active' : ''"
                                    class="bz-cat-btn"
                                >
                                    {{ $cat }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div class="bz-font-grid">
                        @foreach($this->availableFonts as $key => $font)
                            @php
                                $catMatch = match(true) {
                                    str_contains($font['category'], 'Arabic') => 'Arabic',
                                    str_contains($font['category'], 'Editorial') || str_contains($font['category'], 'Corporate') || str_contains($font['category'], 'Luxury') => 'Editorial',
                                    default => 'Geometric',
                                };
                            @endphp
                            <div 
                                x-show="activeCategory === 'All' || activeCategory === '{{ $catMatch }}'"
                                wire:click="selectFont('{{ $key }}')"
                                @click="loadFont('{{ $key }}')"
                                :class="primaryFont === '{{ $key }}' ? 'active' : ''"
                                class="bz-font-card"
                            >
                                <div class="bz-card-top">
                                    <span class="bz-card-title">
                                        {{ $font['label'] }}
                                    </span>
                                    <span class="bz-card-tag">
                                        {{ $font['category'] }}
                                    </span>
                                </div>

                                <!-- Arabic Glyph Sample -->
                                <p 
                                    class="bz-card-ar-sample" 
                                    style="font-family: '{{ $key }}', 'Cairo', sans-serif;"
                                >
                                    {{ $font['preview_ar'] }}
                                </p>

                                <!-- English Glyph Sample -->
                                <p 
                                    class="bz-card-en-sample" 
                                    style="font-family: '{{ $key }}', 'Cairo', sans-serif;"
                                >
                                    {{ $font['preview_en'] }}
                                </p>

                                <div class="bz-card-footer">
                                    <span>Weights: {{ count($font['weights']) }} ({{ min($font['weights']) }}-{{ max($font['weights']) }})</span>
                                    <span x-show="primaryFont === '{{ $key }}'" style="font-weight: 800; color: #2A8FC2;">
                                        ● Selected
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Right Column: Interactive Real-Time Preview Sandbox -->
            <div>
                <div class="bz-sticky-sandbox">
                    <div class="bz-panel-card">
                        <div class="bz-panel-title">
                            <span>⚡ Real-Time Live Preview Sandbox</span>
                            <span class="bz-live-pulse-badge">
                                <span class="bz-pulse-dot"></span> Live
                            </span>
                        </div>

                        <!-- Dynamic Sandbox Container -->
                        <div 
                            class="bz-sandbox-container"
                            :style="`
                                font-family: '${primaryFont}', 'Mont Blanc', 'Montserrat', 'Tajawal', 'Cairo', system-ui, sans-serif;
                                font-size: ${fontSize};
                                font-weight: ${bodyWeight};
                                letter-spacing: ${letterSpacing};
                            `"
                        >
                            <!-- Arabic Headline -->
                            <div>
                                <span style="font-size: 0.65rem; font-weight: 800; text-transform: uppercase; color: #94A3B8; letter-spacing: 0.05em;">
                                    Arabic Headline (H1)
                                </span>
                                <h1 
                                    class="bz-preview-heading-ar"
                                    :style="`font-family: '${headingFont}', '${primaryFont}', 'Mont Blanc', 'Montserrat', 'Tajawal', 'Cairo', sans-serif; font-weight: ${headingWeight};`"
                                >
                                    بلوزون — هندسة الصحة الخلوية وطول العمر
                                </h1>
                            </div>

                            <!-- English Headline -->
                            <div>
                                <span style="font-size: 0.65rem; font-weight: 800; text-transform: uppercase; color: #94A3B8; letter-spacing: 0.05em;">
                                    English Headline (H2)
                                </span>
                                <h2 
                                    class="bz-preview-heading-en"
                                    :style="`font-family: '${headingFont}', '${primaryFont}', 'Mont Blanc', 'Montserrat', 'Tajawal', 'Cairo', sans-serif; font-weight: ${headingWeight};`"
                                >
                                    Cellular Optimization & Longevity Medicine
                                </h2>
                            </div>

                            <!-- Body Paragraph -->
                            <div>
                                <span style="font-size: 0.65rem; font-weight: 800; text-transform: uppercase; color: #94A3B8; letter-spacing: 0.05em;">
                                    Body Typography
                                </span>
                                <p class="bz-preview-body">
                                    نظام رقمي متكامل يربط إدارة المخزون والمبيعات وتجربة العميل بأعلى معايير الجودة والأداء.
                                </p>
                                <p class="bz-preview-body" style="margin-top: 0.25rem;">
                                    Precision engineered for omnichannel retail, multi-warehouse inventory routing, and clinical-grade formulations.
                                </p>
                            </div>

                            <!-- Interactive Buttons & Badges -->
                            <div>
                                <span style="font-size: 0.65rem; font-weight: 800; text-transform: uppercase; color: #94A3B8; letter-spacing: 0.05em;">
                                    Buttons & Badges
                                </span>
                                <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; align-items: center; margin-top: 0.4rem;">
                                    <button 
                                        type="button" 
                                        style="background: #0A4F78; color: #FFFFFF; font-weight: 700; font-size: 0.75rem; padding: 0.45rem 0.85rem; border-radius: 0.5rem; border: none; cursor: pointer;"
                                    >
                                        أضف للسلة • Add to Cart
                                    </button>
                                    <button 
                                        type="button" 
                                        style="background: #FFFFFF; color: #0A4F78; border: 1px solid #2A8FC2; font-weight: 700; font-size: 0.75rem; padding: 0.45rem 0.85rem; border-radius: 0.5rem; cursor: pointer;"
                                    >
                                        تفاصيل المنتج
                                    </button>
                                    <span style="background: rgba(16, 185, 129, 0.15); color: #059669; font-weight: 700; font-size: 0.7rem; padding: 0.25rem 0.6rem; border-radius: 9999px;">
                                        متوفر في المخزون
                                    </span>
                                </div>
                            </div>

                            <!-- E-commerce Price Card Preview -->
                            <div class="bz-preview-commerce-card">
                                <div>
                                    <div style="font-size: 0.8rem; font-weight: 800; color: inherit;">
                                        NMN Longevity Complex 500mg
                                    </div>
                                    <div style="font-size: 0.7rem; color: #94A3B8; margin-top: 0.15rem;">
                                        المخزون المتوفر: 48 عبوة
                                    </div>
                                </div>
                                <div style="text-align: right;">
                                    <div style="font-size: 0.95rem; font-weight: 900; color: #0A4F78;">
                                        350.00 ر.س
                                    </div>
                                    <div style="font-size: 0.7rem; color: #94A3B8; text-decoration: line-through;">
                                        420.00 ر.س
                                    </div>
                                </div>
                            </div>

                            <!-- Numbers & Monospace Sample -->
                            <div style="background: rgba(0, 0, 0, 0.03); border-radius: 0.5rem; padding: 0.5rem; text-align: center; font-size: 0.75rem; font-weight: 700; color: #475569;">
                                الأرقام والرموز: 0123456789 • SAR 1,299.00 • VAT 15% (31004829100003)
                            </div>
                        </div>

                        <div style="margin-top: 1rem; padding-top: 0.75rem; border-top: 1px solid #F1F5F9; font-size: 0.725rem; color: #94A3B8;">
                            💡 Changes update in real-time. Click <strong>"Save & Apply Globally"</strong> to save to the database and clear cache across the entire system.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>

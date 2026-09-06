<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('admin.reports.print_dossier') }} — {{ $referenceId }}</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
    @include('partials.fonts')

    <style>
        :root {
            --color-primary: #0A4F78;
            --color-accent: #2A8FC2;
            --color-dark: #031827;
            --color-gold: #D97706;
            --color-green: #10B981;
            --color-bg-subtle: #F8FAFC;
            --color-border: #E2E8F0;
        }

        body {
            font-family: var(--font-family-base, 'Mont Blanc', 'Montserrat', 'Tajawal', 'Cairo', sans-serif);
            background-color: #F1F5F9;
            color: #0F172A;
            margin: 0;
            padding: 2rem 1rem;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .dossier-sheet {
            max-width: 960px;
            margin: 0 auto;
            background: #FFFFFF;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            padding: 3.5rem 3rem;
            position: relative;
        }

        .dossier-watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 6.5rem;
            font-weight: 900;
            color: rgba(10, 79, 120, 0.03);
            pointer-events: none;
            white-space: nowrap;
            text-transform: uppercase;
            z-index: 1;
        }

        .table-dossier th {
            background-color: #0A4F78 !important;
            color: #FFFFFF !important;
            font-weight: 700;
            font-size: 0.8125rem;
            padding: 0.65rem 0.85rem;
            text-align: start;
        }

        .table-dossier td {
            padding: 0.65rem 0.85rem;
            border-bottom: 1px solid #E2E8F0;
            font-size: 0.8125rem;
        }

        .table-dossier tr:nth-child(even) {
            background-color: #F8FAFC;
        }

        @media print {
            body {
                background: #FFF !important;
                padding: 0 !important;
            }
            .dossier-sheet {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                max-width: 100% !important;
            }
            .no-print {
                display: none !important;
            }
            .page-break {
                page-break-before: always;
                margin-top: 2rem;
            }
        }
    </style>
</head>
<body>

    <!-- Floating Top Action Bar (Hidden in Print) -->
    <div class="no-print" style="max-width: 960px; margin: 0 auto 1.5rem auto; display: flex; justify-content: space-between; align-items: center; background: #031827; color: #FFF; padding: 1rem 1.5rem; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.15);">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <a href="{{ route('admin.reports.index') }}" style="color: #94A3B8; text-decoration: none; font-size: 0.875rem; font-weight: 700;">
                <i class="fa-solid fa-arrow-left mr-1 ml-1"></i> {{ __('admin.reports.title') }}
            </a>
            <span style="color: #475569;">|</span>
            <span style="font-size: 0.875rem; font-weight: 700; color: #E8DCC4;">
                <i class="fa-solid fa-file-contract text-primary mr-1 ml-1"></i> {{ $referenceId }}
            </span>
        </div>

        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <a href="{{ route('admin.reports.export', array_merge(request()->query(), ['type' => 'master', 'format' => 'excel'])) }}" style="background: #10B981; color: #FFF; padding: 0.5rem 1rem; border-radius: 6px; font-weight: 700; font-size: 0.8125rem; text-decoration: none; display: inline-flex; align-items: center; gap: 0.4rem;">
                <i class="fa-solid fa-file-excel"></i> {{ __('admin.reports.export_excel_all') }}
            </a>
            <button type="button" onclick="window.print()" style="background: #0A4F78; color: #FFF; border: none; padding: 0.5rem 1.25rem; border-radius: 6px; font-weight: 800; font-size: 0.875rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.4rem;">
                <i class="fa-solid fa-print"></i> {{ __('admin.reports.print_report') }}
            </button>
        </div>
    </div>

    <!-- Main Dossier Sheet -->
    <div class="dossier-sheet">
        <div class="dossier-watermark">CONFIDENTIAL</div>

        <!-- 1. Corporate Letterhead Header -->
        <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 3px solid #0A4F78; padding-bottom: 1.75rem; margin-bottom: 2rem;">
            <div style="display: flex; align-items: center; gap: 1.25rem;">
                <img src="{{ asset('assets/logo/logo-dark.webp') }}" alt="Blue Zone" style="height: 48px;" onerror="this.src='{{ asset('assets/logo/logo-dark.png') }}';">
                <div>
                    <h1 style="font-size: 1.6rem; font-weight: 900; color: #031827; margin: 0; letter-spacing: -0.5px;">BLUE ZONE BIOCEUTICALS</h1>
                    <p style="font-size: 0.875rem; font-weight: 700; color: #0A4F78; margin: 0.15rem 0 0 0;">ADVANCED CELLULAR LONGEVITY & CLINICAL NUTRACEUTICALS</p>
                    <p style="font-size: 0.75rem; color: #64748B; margin: 0.15rem 0 0 0;">Riyadh Flagship Boutique & Central Laboratory — Kingdom of Saudi Arabia</p>
                </div>
            </div>

            <div style="text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }}; font-size: 0.75rem; color: #475569; line-height: 1.4;">
                <div><strong style="color: #031827;">REF NO:</strong> <span style="font-family: monospace; font-weight: 700;">{{ $referenceId }}</span></div>
                <div><strong style="color: #031827;">DATE ISSUED:</strong> {{ now()->format('Y-m-d H:i:s') }}</div>
                <div><strong style="color: #031827;">PERIOD:</strong> {{ $filters['label'] }}</div>
                <div><strong style="color: #031827;">CLASSIFICATION:</strong> <span style="color: #DC2626; font-weight: 800;">STRICTLY CONFIDENTIAL</span></div>
            </div>
        </div>

        <!-- 2. Report Document Title & Purpose -->
        <div style="background: linear-gradient(135deg, #031827, #0A4F78); color: #FFF; padding: 1.25rem 1.75rem; border-radius: 8px; margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h2 style="font-size: 1.25rem; font-weight: 800; margin: 0; color: #F6F5EF;">{{ __('admin.reports.title') }}</h2>
                <p style="font-size: 0.8125rem; color: #B8D98A; margin: 0.25rem 0 0 0;">{{ __('admin.reports.subtitle') }}</p>
            </div>
            <div style="text-align: center; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); padding: 0.5rem 1rem; border-radius: 6px;">
                <div style="font-size: 0.7rem; text-transform: uppercase; color: #CBD5E1;">System Health</div>
                <div style="font-size: 0.875rem; font-weight: 800; color: #67B34A;">● OPERATIONAL</div>
            </div>
        </div>

        <!-- 3. Key Financial Performance Matrix -->
        <div style="margin-bottom: 2.25rem;">
            <h3 style="font-size: 1rem; font-weight: 800; color: #0A4F78; border-bottom: 1.5px solid #E2E8F0; padding-bottom: 0.5rem; margin-bottom: 1rem; text-transform: uppercase; letter-spacing: 0.5px;">
                1. Executive Financial Summary & Key Performance Indicators
            </h3>

            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1rem;">
                <div style="border: 1px solid #CBD5E1; border-radius: 8px; padding: 1rem; background: #F8FAFC;">
                    <div style="font-size: 0.75rem; font-weight: 700; color: #64748B;">GROSS REVENUE</div>
                    <div style="font-size: 1.5rem; font-weight: 900; color: #031827; margin: 0.25rem 0; font-variant-numeric: tabular-nums;">
                        ${{ number_format($kpi['total_sales'], 2) }}
                    </div>
                    <div style="font-size: 0.75rem; font-weight: 700; color: #10B981;">
                        <i class="fa-solid fa-arrow-trend-up"></i> {{ $kpi['growth_rate'] }} YoY
                    </div>
                </div>

                <div style="border: 1px solid #CBD5E1; border-radius: 8px; padding: 1rem; background: #F8FAFC;">
                    <div style="font-size: 0.75rem; font-weight: 700; color: #64748B;">NET REALIZED REVENUE</div>
                    <div style="font-size: 1.5rem; font-weight: 900; color: #10B981; margin: 0.25rem 0; font-variant-numeric: tabular-nums;">
                        ${{ number_format($kpi['net_revenue'], 2) }}
                    </div>
                    <div style="font-size: 0.75rem; color: #64748B;">Excl. discounts</div>
                </div>

                <div style="border: 1px solid #CBD5E1; border-radius: 8px; padding: 1rem; background: #F8FAFC;">
                    <div style="font-size: 0.75rem; font-weight: 700; color: #64748B;">AVERAGE ORDER VALUE</div>
                    <div style="font-size: 1.5rem; font-weight: 900; color: #0A4F78; margin: 0.25rem 0; font-variant-numeric: tabular-nums;">
                        ${{ number_format($kpi['average_order_value'], 2) }}
                    </div>
                    <div style="font-size: 0.75rem; color: #64748B;">Across all channels</div>
                </div>

                <div style="border: 1px solid #CBD5E1; border-radius: 8px; padding: 1rem; background: #F8FAFC;">
                    <div style="font-size: 0.75rem; font-weight: 700; color: #64748B;">TOTAL INVENTORY VALUATION</div>
                    <div style="font-size: 1.5rem; font-weight: 900; color: #031827; margin: 0.25rem 0; font-variant-numeric: tabular-nums;">
                        ${{ number_format($inventory['total_valuation'], 2) }}
                    </div>
                    <div style="font-size: 0.75rem; color: #64748B;">{{ $inventory['total_units'] }} Units on Hand</div>
                </div>
            </div>
        </div>

        <!-- 4. Longevity Formulations Sales Table -->
        <div style="margin-bottom: 2.25rem;">
            <h3 style="font-size: 1rem; font-weight: 800; color: #0A4F78; border-bottom: 1.5px solid #E2E8F0; padding-bottom: 0.5rem; margin-bottom: 1rem; text-transform: uppercase; letter-spacing: 0.5px;">
                2. Longevity Formulations & Systems Performance
            </h3>

            <table class="table-dossier" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="width: 100px;">SKU</th>
                        <th>{{ __('admin.products.fields.name_en') }}</th>
                        <th>{{ __('admin.menu.categories') }}</th>
                        <th style="text-align: right;">Unit Price</th>
                        <th style="text-align: center;">Units Sold</th>
                        <th style="text-align: right;">Gross Revenue</th>
                        <th style="text-align: center;">Share %</th>
                        <th style="text-align: center;">Stock Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $p)
                        <tr>
                            <td style="font-family: monospace; font-weight: 700; color: #475569;">{{ $p['sku'] }}</td>
                            <td style="font-weight: 800; color: #031827;">{{ app()->getLocale() === 'ar' ? $p['name_ar'] : $p['name_en'] }}</td>
                            <td>{{ app()->getLocale() === 'ar' ? $p['category_ar'] : $p['category_en'] }}</td>
                            <td style="text-align: right; font-variant-numeric: tabular-nums;">${{ number_format($p['price'], 2) }}</td>
                            <td style="text-align: center; font-weight: 800; color: #0A4F78;">{{ $p['units_sold'] }}</td>
                            <td style="text-align: right; font-weight: 800; font-variant-numeric: tabular-nums;">${{ number_format($p['revenue'], 2) }}</td>
                            <td style="text-align: center; font-weight: 700;">{{ $p['share_pct'] }}%</td>
                            <td style="text-align: center;">
                                <span style="display: inline-block; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.7rem; font-weight: 700; background: #DCFCE7; color: #166534;">
                                    {{ $p['total_stock'] }} In Stock
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- 5. Omnichannel Breakdown (Online Store vs Boutique POS) -->
        <div style="margin-bottom: 2.25rem;">
            <h3 style="font-size: 1rem; font-weight: 800; color: #0A4F78; border-bottom: 1.5px solid #E2E8F0; padding-bottom: 0.5rem; margin-bottom: 1rem; text-transform: uppercase; letter-spacing: 0.5px;">
                3. Omnichannel Distribution & Sales Velocity
            </h3>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div style="border: 1px solid #CBD5E1; border-radius: 8px; padding: 1.25rem; background: #F8FAFC;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                        <span style="font-weight: 800; font-size: 0.9375rem; color: #0A4F78;">ONLINE DIRECT-TO-CONSUMER</span>
                        <span style="font-size: 0.75rem; font-weight: 700; background: #E0F2FE; color: #0369A1; padding: 0.2rem 0.5rem; border-radius: 4px;">{{ $channels['online']['percentage'] }}% Gross Share</span>
                    </div>
                    <div style="font-size: 1.35rem; font-weight: 900; color: #031827; margin-bottom: 0.25rem;">
                        ${{ number_format($channels['online']['revenue'], 2) }}
                    </div>
                    <div style="font-size: 0.8125rem; color: #64748B;">
                        {{ $channels['online']['orders_count'] }} Orders | Average Basket: ${{ number_format($channels['online']['avg_ticket'], 2) }}
                    </div>
                </div>

                <div style="border: 1px solid #CBD5E1; border-radius: 8px; padding: 1.25rem; background: #F8FAFC;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                        <span style="font-weight: 800; font-size: 0.9375rem; color: #D97706;">FLAGSHIP PHYSICAL BOUTIQUE (POS)</span>
                        <span style="font-size: 0.75rem; font-weight: 700; background: #FEF3C7; color: #B45309; padding: 0.2rem 0.5rem; border-radius: 4px;">{{ $channels['pos']['percentage'] }}% Gross Share</span>
                    </div>
                    <div style="font-size: 1.35rem; font-weight: 900; color: #031827; margin-bottom: 0.25rem;">
                        ${{ number_format($channels['pos']['revenue'], 2) }}
                    </div>
                    <div style="font-size: 0.8125rem; color: #64748B;">
                        {{ $channels['pos']['orders_count'] }} Tickets | Average Basket: ${{ number_format($channels['pos']['avg_ticket'], 2) }}
                    </div>
                </div>
            </div>
        </div>

        <!-- 6. Tax & ZATCA Settlement Statement -->
        <div style="margin-bottom: 2.5rem;">
            <h3 style="font-size: 1rem; font-weight: 800; color: #0A4F78; border-bottom: 1.5px solid #E2E8F0; padding-bottom: 0.5rem; margin-bottom: 1rem; text-transform: uppercase; letter-spacing: 0.5px;">
                4. ZATCA Compliance & Tax Settlement Statement
            </h3>

            <div style="border: 1px solid #CBD5E1; border-radius: 8px; padding: 1.25rem; background: #F8FAFC; display: grid; grid-template-columns: repeat(5, 1fr); gap: 1rem; text-align: center;">
                <div>
                    <div style="font-size: 0.75rem; font-weight: 700; color: #64748B;">Gross Subtotal</div>
                    <div style="font-size: 1.1rem; font-weight: 800; color: #031827; margin-top: 0.25rem;">${{ number_format($tax['subtotal_gross'], 2) }}</div>
                </div>
                <div>
                    <div style="font-size: 0.75rem; font-weight: 700; color: #DC2626;">Discounts Given</div>
                    <div style="font-size: 1.1rem; font-weight: 800; color: #DC2626; margin-top: 0.25rem;">-${{ number_format($tax['discounts'], 2) }}</div>
                </div>
                <div>
                    <div style="font-size: 0.75rem; font-weight: 700; color: #0A4F78;">Net Taxable Base</div>
                    <div style="font-size: 1.1rem; font-weight: 800; color: #0A4F78; margin-top: 0.25rem;">${{ number_format($tax['net_taxable'], 2) }}</div>
                </div>
                <div>
                    <div style="font-size: 0.75rem; font-weight: 700; color: #0A4F78;">VAT 15% Collected</div>
                    <div style="font-size: 1.1rem; font-weight: 800; color: #0A4F78; margin-top: 0.25rem;">+${{ number_format($tax['vat_15'], 2) }}</div>
                </div>
                <div>
                    <div style="font-size: 0.75rem; font-weight: 700; color: #10B981;">Gross Cash Realized</div>
                    <div style="font-size: 1.1rem; font-weight: 900; color: #10B981; margin-top: 0.25rem;">${{ number_format($tax['realized_gross'], 2) }}</div>
                </div>
            </div>
        </div>

        <!-- 7. Executive Signatures & Board Sign-off Block -->
        <div style="border-top: 2px solid #0A4F78; padding-top: 2rem; margin-top: 3rem;">
            <div style="font-size: 0.8125rem; font-weight: 800; color: #031827; margin-bottom: 1.5rem; text-transform: uppercase;">
                5. Executive Verification & Board Sign-Off
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem;">
                <div style="border-top: 1px dashed #94A3B8; padding-top: 0.75rem;">
                    <div style="font-weight: 800; font-size: 0.875rem; color: #031827;">Chief Medical Officer &amp; Formulation Lead</div>
                    <div style="font-size: 0.75rem; color: #64748B;">Blue Zone Bioceuticals Inc.</div>
                    <div style="margin-top: 1.5rem; font-size: 0.75rem; color: #94A3B8;">Signature: __________________________ &nbsp;&nbsp; Date: _________</div>
                </div>

                <div style="border-top: 1px dashed #94A3B8; padding-top: 0.75rem;">
                    <div style="font-weight: 800; font-size: 0.875rem; color: #031827;">Chief Financial Officer &amp; Controller</div>
                    <div style="font-size: 0.75rem; color: #64748B;">Blue Zone Bioceuticals Inc.</div>
                    <div style="margin-top: 1.5rem; font-size: 0.75rem; color: #94A3B8;">Signature: __________________________ &nbsp;&nbsp; Date: _________</div>
                </div>
            </div>
        </div>

        <!-- 8. Footer -->
        <div style="text-align: center; font-size: 0.7rem; color: #94A3B8; margin-top: 3rem; border-top: 1px solid #E2E8F0; padding-top: 1rem;">
            {{ __('admin.reports.dossier_confidential') }} | Blue Zone Operating System v2.6
        </div>

    </div>

</body>
</html>

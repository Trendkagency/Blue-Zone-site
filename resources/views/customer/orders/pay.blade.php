<x-layouts.app :title="(app()->getLocale() === 'ar' ? 'سداد الطلب #' : 'Pay Order #') . $order->order_number">
    <div style="min-height: 80vh; padding: 3rem 1rem; background: var(--color-bg, #0B1118); color: var(--color-text, #f8fafc);">
        <div style="max-width: 760px; margin: 0 auto;">
            
            <!-- Header Brand -->
            <div style="text-align: center; margin-bottom: 2rem;">
                <img src="{{ asset('assets/logo/logo-main.png') }}" alt="Blue Zone" style="height: 52px; margin: 0 auto 1rem; display: block;" onerror="this.onerror=null; this.src='{{ asset('image.jpg') }}';">
                <h1 style="font-size: 1.6rem; font-weight: 900; margin: 0; color: #fff;">
                    {{ app()->getLocale() === 'ar' ? 'بوابة سداد الطلب الآمنة' : 'Secure Order Payment Portal' }}
                </h1>
                <p style="font-size: 0.85rem; color: #94a3b8; margin-top: 0.35rem;">
                    {{ app()->getLocale() === 'ar' ? 'شركة بلو زون للأدوية والمستحضرات الطبية • تشفير بنكي معتمد 256-Bit SSL' : 'Blue Zone Bioceuticals • 256-Bit End-to-End SSL Encrypted Checkout' }}
                </p>
            </div>

            @if(session('status'))
                <div style="background: rgba(16, 185, 129, 0.15); border: 1.5px solid #10b981; color: #10b981; padding: 1rem 1.25rem; border-radius: 14px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem; font-weight: 700;">
                    <i class="fa-solid fa-circle-check text-xl"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if($isPaid)
                <!-- Order Already Paid State -->
                <div style="background: rgba(15, 23, 42, 0.85); border: 1.5px solid rgba(16, 185, 129, 0.4); border-radius: 20px; padding: 2.5rem; text-align: center; box-shadow: 0 20px 40px rgba(0,0,0,0.4); backdrop-filter: blur(16px);">
                    <div style="width: 72px; height: 72px; border-radius: 50%; background: rgba(16, 185, 129, 0.2); color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 2rem; margin: 0 auto 1.25rem;">
                        <i class="fa-solid fa-check-double"></i>
                    </div>
                    <h2 style="font-size: 1.5rem; font-weight: 900; color: #fff; margin: 0 0 0.5rem 0;">
                        {{ app()->getLocale() === 'ar' ? 'تم سداد هذا الطلب بنجاح!' : 'Order Paid Successfully!' }}
                    </h2>
                    <p style="font-size: 0.9rem; color: #94a3b8; max-width: 480px; margin: 0 auto 1.5rem;">
                        {{ app()->getLocale() === 'ar' ? 'تم تسجيل وتوثيق عملية الدفع بنجاح في سجلات الشركة. طلبك قيد التجهيز والتنفيذ.' : 'Your payment has been captured and verified. The protocol formulation is now undergoing clinical preparation and dispatch.' }}
                    </p>

                    <div style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); border-radius: 14px; padding: 1.25rem; margin-bottom: 1.75rem; text-align: start; display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div>
                            <div style="font-size: 0.75rem; color: #64748b; font-weight: 700;">{{ app()->getLocale() === 'ar' ? 'رقم الطلب' : 'Order Number' }}</div>
                            <div style="font-size: 1rem; font-weight: 900; color: #38bdf8; font-family: monospace;">{{ $order->order_number }}</div>
                        </div>
                        <div>
                            <div style="font-size: 0.75rem; color: #64748b; font-weight: 700;">{{ app()->getLocale() === 'ar' ? 'إجمالي المبلغ المسدد' : 'Total Paid' }}</div>
                            <div style="font-size: 1.15rem; font-weight: 900; color: #10b981; font-family: monospace;">@currency($order->total)</div>
                        </div>
                        <div>
                            <div style="font-size: 0.75rem; color: #64748b; font-weight: 700;">{{ app()->getLocale() === 'ar' ? 'رقم العملية (Transaction ID)' : 'Transaction ID' }}</div>
                            <div style="font-size: 0.8rem; font-weight: 700; color: #cbd5e1; font-family: monospace; word-break: break-all;">{{ $order->payment_transaction_id ?: 'TX-CAPTURED' }}</div>
                        </div>
                        <div>
                            <div style="font-size: 0.75rem; color: #64748b; font-weight: 700;">{{ app()->getLocale() === 'ar' ? 'حالة السداد' : 'Payment Status' }}</div>
                            <span style="display: inline-block; padding: 0.2rem 0.6rem; border-radius: 9999px; background: rgba(16, 185, 129, 0.2); color: #10b981; font-size: 0.75rem; font-weight: 800;">PAID & VERIFIED</span>
                        </div>
                    </div>

                    <a href="{{ url('/') }}" class="btn btn-primary" style="padding: 0.75rem 2rem; border-radius: 12px; font-weight: 800; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem;">
                        <i class="fa-solid fa-house"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'العودة للصفحة الرئيسية' : 'Return to Home' }}</span>
                    </a>
                </div>

            @else
                <!-- Unpaid Order / Payment Checkout Portal -->
                <div style="display: grid; grid-template-columns: 1fr; gap: 1.5rem;">
                    
                    <!-- Order Summary Card -->
                    <div style="background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 18px; padding: 1.5rem; backdrop-filter: blur(12px);">
                        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 1rem; margin-bottom: 1rem;">
                            <div>
                                <span style="font-size: 0.75rem; font-weight: 800; text-transform: uppercase; color: #38bdf8; letter-spacing: 0.05em;">{{ app()->getLocale() === 'ar' ? 'ملخص الطلب والفاتورة' : 'Order & Invoice Dossier' }}</span>
                                <h3 style="font-size: 1.2rem; font-weight: 900; margin: 0.2rem 0 0 0; color: #fff;">#{{ $order->order_number }}</h3>
                            </div>
                            <div style="text-align: end;">
                                <span style="font-size: 0.75rem; color: #94a3b8;">{{ app()->getLocale() === 'ar' ? 'تاريخ الطلب' : 'Date' }}</span>
                                <div style="font-size: 0.85rem; font-weight: 700; color: #e2e8f0; font-family: monospace;">{{ $order->date ?? $order->created_at?->format('Y-m-d') }}</div>
                            </div>
                        </div>

                        <!-- Purchased Items Mini List -->
                        <div style="margin-bottom: 1.25rem;">
                            <div style="font-size: 0.75rem; font-weight: 800; color: #94a3b8; margin-bottom: 0.5rem;">{{ app()->getLocale() === 'ar' ? 'الأصناف والتركيبات المطلوبة:' : 'Prescribed Formulations:' }}</div>
                            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                                @foreach($order->items as $item)
                                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.6rem 0.85rem; background: rgba(255,255,255,0.03); border-radius: 10px; border: 1px solid rgba(255,255,255,0.05);">
                                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                                            <span style="font-weight: 800; font-size: 0.85rem; color: #fff;">{{ app()->getLocale() === 'ar' ? ($item->product_name_ar ?? $item->product_name_en) : $item->product_name_en }}</span>
                                            <span style="font-size: 0.75rem; color: #94a3b8; font-family: monospace;">&times; {{ $item->quantity }}</span>
                                        </div>
                                        <span style="font-weight: 800; font-size: 0.85rem; color: #38bdf8; font-family: monospace;">@currency($item->total)</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Totals Breakdown -->
                        <div style="border-top: 1px solid rgba(255,255,255,0.08); padding-top: 0.85rem; display: flex; flex-direction: column; gap: 0.35rem; font-size: 0.85rem;">
                            <div style="display: flex; justify-content: space-between; color: #94a3b8;">
                                <span>{{ app()->getLocale() === 'ar' ? 'المجموع الفرعي:' : 'Subtotal:' }}</span>
                                <span class="font-mono">@currency($order->subtotal)</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; color: #94a3b8;">
                                <span>{{ app()->getLocale() === 'ar' ? 'ضريبة القيمة المضافة (15%):' : 'VAT (15%):' }}</span>
                                <span class="font-mono">@currency($order->tax)</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; color: #fff; font-size: 1.15rem; font-weight: 900; margin-top: 0.5rem; padding-top: 0.5rem; border-top: 1px solid rgba(255,255,255,0.15);">
                                <span>{{ app()->getLocale() === 'ar' ? 'المبلغ المطلوب سداده:' : 'Amount Due:' }}</span>
                                <span style="color: #10b981;" class="font-mono">@currency($order->total)</span>
                            </div>
                        </div>
                    </div>

                    <!-- Direct Card / Mada Payment Form -->
                    <div style="background: rgba(15, 23, 42, 0.9); border: 1.5px solid rgba(56, 189, 248, 0.3); border-radius: 18px; padding: 1.75rem; backdrop-filter: blur(12px);">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem;">
                            <h3 style="font-size: 1.1rem; font-weight: 900; margin: 0; color: #fff; display: flex; align-items: center; gap: 0.5rem;">
                                <i class="fa-regular fa-credit-card text-sky-400"></i>
                                <span>{{ app()->getLocale() === 'ar' ? 'الدفع بالبطاقة الائتمانية / مدى (Stripe)' : 'Credit Card / Mada Payment (Stripe)' }}</span>
                            </h3>
                            <div style="display: flex; gap: 0.4rem; font-size: 1.2rem; color: #94a3b8;">
                                <i class="fa-brands fa-cc-visa text-sky-400"></i>
                                <i class="fa-brands fa-cc-mastercard text-amber-400"></i>
                                <i class="fa-brands fa-stripe text-indigo-400"></i>
                            </div>
                        </div>

                        <form action="{{ route('customer.order-pay.process', $order->order_number) }}" method="POST" id="paymentProcessForm">
                            @csrf
                            <div style="display: flex; flex-direction: column; gap: 1rem;">
                                <div>
                                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #cbd5e1; margin-bottom: 0.35rem;">
                                        {{ app()->getLocale() === 'ar' ? 'الاسم كما يظهر على البطاقة' : 'Cardholder Name' }}
                                    </label>
                                    <input type="text" name="cardholder_name" value="{{ old('cardholder_name', $order->customer_name) }}" required class="form-control" style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.15); color: #fff; padding: 0.65rem 1rem; border-radius: 10px; width: 100%; box-sizing: border-box;">
                                </div>

                                <div>
                                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #cbd5e1; margin-bottom: 0.35rem;">
                                        {{ app()->getLocale() === 'ar' ? 'رقم البطاقة (16 رقماً)' : 'Card Number (16 digits)' }}
                                    </label>
                                    <input type="text" name="card_number" placeholder="4000 1234 5678 9010" maxlength="19" required class="form-control font-mono" style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.15); color: #fff; padding: 0.65rem 1rem; border-radius: 10px; width: 100%; box-sizing: border-box; font-weight: 700; letter-spacing: 0.05em;">
                                </div>

                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                    <div>
                                        <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #cbd5e1; margin-bottom: 0.35rem;">
                                            {{ app()->getLocale() === 'ar' ? 'تاريخ الانتهاء (MM/YY)' : 'Expiry (MM/YY)' }}
                                        </label>
                                        <input type="text" name="card_expiry" placeholder="12/28" maxlength="5" required class="form-control font-mono" style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.15); color: #fff; padding: 0.65rem 1rem; border-radius: 10px; width: 100%; box-sizing: border-box; text-align: center;">
                                    </div>
                                    <div>
                                        <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #cbd5e1; margin-bottom: 0.35rem;">
                                            {{ app()->getLocale() === 'ar' ? 'رمز الأمان (CVC)' : 'Security Code (CVC)' }}
                                        </label>
                                        <input type="password" name="card_cvc" placeholder="&bull;&bull;&bull;" maxlength="4" required class="form-control font-mono" style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.15); color: #fff; padding: 0.65rem 1rem; border-radius: 10px; width: 100%; box-sizing: border-box; text-align: center;">
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary" style="margin-top: 0.5rem; padding: 0.85rem; font-weight: 900; font-size: 1rem; border-radius: 12px; display: flex; align-items: center; justify-content: center; gap: 0.5rem; background: linear-gradient(135deg, #0284c7, #0ea5e9); border: none; box-shadow: 0 4px 15px rgba(2, 132, 199, 0.4); cursor: pointer;">
                                    <i class="fa-solid fa-lock"></i>
                                    <span>{{ app()->getLocale() === 'ar' ? 'سداد الآن آمن' : 'Pay Now Securely' }} (@currency($order->total))</span>
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            @endif

        </div>
    </div>
</x-layouts.app>

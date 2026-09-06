<x-layouts.customer :title="__('shop.account.settings') . ' — ' . __('app.brand_name')">
    <div class="container" style="padding-top: 3rem; margin-bottom: 5rem;">
        <h1 style="font-size: 2.25rem; font-weight: 800; margin-bottom: 2rem;">
            {{ __('shop.account.settings') }}
        </h1>

                    <i class="fa-solid fa-chart-pie mr-1.5 ml-1.5"></i> {{ __('shop.account.dashboard') }}
                </a>
                <a href="{{ route('customer.account.orders') }}" class="account-nav-link">
                    <i class="fa-solid fa-box mr-1.5 ml-1.5"></i> {{ __('shop.account.orders') }}
                </a>
                <a href="{{ route('customer.account.invoices') }}" class="account-nav-link">
                    <i class="fa-solid fa-file-invoice-dollar mr-1.5 ml-1.5"></i> {{ __('shop.account.invoices') }}
                </a>
                <a href="{{ route('customer.account.addresses') }}" class="account-nav-link">
                    <i class="fa-solid fa-location-dot mr-1.5 ml-1.5"></i> {{ __('shop.account.addresses') }}
                </a>
                <a href="{{ route('customer.account.wishlist') }}" class="account-nav-link">
                    <i class="fa-solid fa-heart mr-1.5 ml-1.5"></i> {{ app()->getLocale() === 'ar' ? 'المنتجات المحفوظة' : 'Saved Formulations' }}
                </a>
                <a href="{{ route('customer.account.profile') }}" class="account-nav-link">
                    <i class="fa-solid fa-user mr-1.5 ml-1.5"></i> {{ __('shop.account.profile') }}
                </a>
                <a href="{{ route('customer.account.settings') }}" class="account-nav-link active">
                    <i class="fa-solid fa-gear mr-1.5 ml-1.5"></i> {{ __('shop.account.settings') }}
                </a>

                <form action="{{ route('customer.auth.logout') }}" method="POST" style="margin-top: 0.5rem; border-top: 1px solid var(--color-border); padding-top: 0.5rem;">
                    @csrf
                    <button type="submit" class="account-nav-link" style="width: 100%; text-align: start; background: none; border: none; cursor: pointer; color: var(--color-danger);">
                        <i class="fa-solid fa-right-from-bracket mr-1.5 ml-1.5"></i> {{ __('app.nav.logout') }}
                    </button>
                </form>
            </aside>

            <!-- Settings Sections -->
            <div style="display: flex; flex-direction: column; gap: 2rem;">
                
                <!-- Password & Security Card -->
                <div class="card" style="padding: 2.25rem;">
                    <div style="border-bottom: 1px solid var(--color-border); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                        <h3 style="font-size: 1.25rem; font-weight: 800; margin: 0;">
                            <i class="fa-solid fa-shield-halved text-primary mr-1 ml-1"></i>
                            {{ app()->getLocale() === 'ar' ? 'الأمان وكلمة المرور' : 'Security & Credentials' }}
                        </h3>
                        <div class="text-xs text-muted" style="margin-top: 0.25rem;">
                            {{ app()->getLocale() === 'ar' ? 'تحديث كلمة المرور لحماية بياناتك الطبية والشخصية.' : 'Update account password to secure personal health dossier.' }}
                        </div>
                    </div>

                    <form action="{{ route('customer.account.password.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div style="margin-bottom: 1.25rem;">
                            <label class="text-xs font-bold text-muted" style="display: block; margin-bottom: 0.35rem;">
                                {{ app()->getLocale() === 'ar' ? 'كلمة المرور الحالية' : 'Current Password' }} *
                            </label>
                            <input type="password" name="current_password" class="form-control text-sm" placeholder="••••••••" required>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                            <div>
                                <label class="text-xs font-bold text-muted" style="display: block; margin-bottom: 0.35rem;">
                                    {{ app()->getLocale() === 'ar' ? 'كلمة المرور الجديدة' : 'New Password' }} *
                                </label>
                                <input type="password" name="password" class="form-control text-sm" placeholder="••••••••" required>
                            </div>
                            <div>
                                <label class="text-xs font-bold text-muted" style="display: block; margin-bottom: 0.35rem;">
                                    {{ app()->getLocale() === 'ar' ? 'تأكيد كلمة المرور الجديدة' : 'Confirm New Password' }} *
                                </label>
                                <input type="password" name="password_confirmation" class="form-control text-sm" placeholder="••••••••" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary font-bold">
                            <i class="fa-solid fa-key mr-1.5 ml-1.5"></i> {{ app()->getLocale() === 'ar' ? 'تحديث وتأمين كلمة المرور' : 'Update Password' }}
                        </button>
                    </form>
                </div>

                <!-- Communications & Alerts Card -->
                <div class="card" style="padding: 2.25rem;">
                    <div style="border-bottom: 1px solid var(--color-border); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                        <h3 style="font-size: 1.25rem; font-weight: 800; margin: 0;">
                            <i class="fa-solid fa-bell text-primary mr-1 ml-1"></i>
                            {{ app()->getLocale() === 'ar' ? 'إشعارات الشحن والتحديثات الطبية' : 'Communications & Clinical Alerts' }}
                        </h3>
                        <div class="text-xs text-muted" style="margin-top: 0.25rem;">
                            {{ app()->getLocale() === 'ar' ? 'التحكم في قنوات الإشعارات والتنبيهات المباشرة.' : 'Manage SMS dispatch checkpoints and clinical digest subscriptions.' }}
                        </div>
                    </div>

                    @php
                        $prefs = $customer->notification_preferences ?? ['email_orders' => true, 'email_science' => true, 'sms_orders' => false];
                    @endphp

                    <form action="{{ route('customer.account.settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div style="display: flex; flex-direction: column; gap: 1.25rem; margin-bottom: 1.75rem;">
                            <label style="display: flex; align-items: flex-start; gap: 0.75rem; cursor: pointer;">
                                <input type="checkbox" name="email_orders" value="1" {{ !empty($prefs['email_orders']) ? 'checked' : '' }} style="margin-top: 0.25rem;">
                                <div>
                                    <div class="font-bold text-sm">{{ app()->getLocale() === 'ar' ? 'تحديثات تتبع الشحن المبرد بالبريد' : 'Fulfillment & Cold-Chain Tracking Updates' }}</div>
                                    <div class="text-xs text-muted">{{ app()->getLocale() === 'ar' ? 'استلام تنبيهات نقاط التفتيش ودرجة الحرارة أثناء النقل اللوجستي.' : 'Receive real-time courier checkpoints and dispatch alerts.' }}</div>
                                </div>
                            </label>

                            <label style="display: flex; align-items: flex-start; gap: 0.75rem; cursor: pointer;">
                                <input type="checkbox" name="email_science" value="1" {{ !empty($prefs['email_science']) ? 'checked' : '' }} style="margin-top: 0.25rem;">
                                <div>
                                    <div class="font-bold text-sm">{{ app()->getLocale() === 'ar' ? 'ملخصات الأبحاث الحيوية ومجلة طول العمر' : 'Biochemical Research & Longevity Journal Dispatches' }}</div>
                                    <div class="text-xs text-muted">{{ app()->getLocale() === 'ar' ? 'ملخصات علمية دورية من هيئة المستشارين العلميين.' : 'Periodic clinical summaries from our Scientific Advisory Board.' }}</div>
                                </div>
                            </label>

                            <label style="display: flex; align-items: flex-start; gap: 0.75rem; cursor: pointer;">
                                <input type="checkbox" name="sms_orders" value="1" {{ !empty($prefs['sms_orders']) ? 'checked' : '' }} style="margin-top: 0.25rem;">
                                <div>
                                    <div class="font-bold text-sm">{{ app()->getLocale() === 'ar' ? 'رسائل نصية قصيرة (SMS) عند وصول المندوب' : 'Instant SMS Dispatch Notifications' }}</div>
                                    <div class="text-xs text-muted">{{ app()->getLocale() === 'ar' ? 'رسالة نصية برمز استلام الشحنة المبردة عند وصولها لوجهتك.' : 'Delivery SMS alert with courier PIN code.' }}</div>
                                </div>
                            </label>
                        </div>

                        <button type="submit" class="btn btn-secondary btn-sm">
                            <i class="fa-solid fa-floppy-disk mr-1.5 ml-1.5"></i> {{ app()->getLocale() === 'ar' ? 'حفظ التفضيلات' : 'Save Preferences' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.customer>

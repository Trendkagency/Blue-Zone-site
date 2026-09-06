<x-layouts.customer :title="__('shop.account.profile') . ' — ' . __('app.brand_name')">
    <div class="container" style="padding-top: 3rem; margin-bottom: 5rem;">
        <h1 style="font-size: 2.25rem; font-weight: 800; margin-bottom: 2rem;">
            {{ __('shop.account.profile') }}
        </h1>

<<<<<<< HEAD
=======
        @if(session('success'))
            <div class="alert alert-success" style="margin-bottom: 1.5rem;">
                <i class="fa-solid fa-circle-check mr-1.5 ml-1.5 text-success"></i> {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger" style="margin-bottom: 1.5rem;">
                <i class="fa-solid fa-triangle-exclamation mr-1.5 ml-1.5 text-danger"></i>
                <div>
                    @foreach($errors->all() as $error)
                        <div>• {{ $error }}</div>
                    @endforeach
                </div>
            </div>
        @endif

>>>>>>> origin/main
        <div class="account-layout">
            <!-- Navigation -->
            <aside class="account-sidebar-nav">
                <a href="{{ route('customer.account.dashboard') }}" class="account-nav-link">
<<<<<<< HEAD
                    📊 {{ __('shop.account.dashboard') }}
                </a>
                <a href="{{ route('customer.account.orders') }}" class="account-nav-link">
                    📦 {{ __('shop.account.orders') }}
                </a>
                <a href="{{ route('customer.account.invoices') }}" class="account-nav-link">
                    🧾 {{ __('shop.account.invoices') }}
                </a>
                <a href="{{ route('customer.account.addresses') }}" class="account-nav-link">
                    📍 {{ __('shop.account.addresses') }}
                </a>
                <a href="{{ route('customer.account.profile') }}" class="account-nav-link active">
                    👤 {{ __('shop.account.profile') }}
                </a>
                <a href="{{ route('customer.account.settings') }}" class="account-nav-link">
                    ⚙️ {{ __('shop.account.settings') }}
                </a>
            </aside>

            <!-- Profile Form Card -->
            <div class="card" style="padding: 2rem;">
                <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid var(--color-border);">
                    Clinical Account Dossier
                </h3>

                <form action="#" method="GET">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <x-forms.input 
                            name="name" 
                            :label="__('shop.checkout.full_name')" 
                            :value="$customer['name']" 
                            required 
                        />
                        <x-forms.input 
                            name="phone" 
                            type="tel" 
                            :label="__('shop.checkout.phone')" 
                            :value="$customer['phone']" 
                            required 
                        />
                    </div>

                    <x-forms.input 
                        name="email" 
                        type="email" 
                        :label="__('shop.checkout.email')" 
                        :value="$customer['email']" 
                        required 
                    />

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <x-forms.input 
                            name="city" 
                            :label="__('shop.checkout.city')" 
                            :value="$customer['city']" 
                        />
                        <x-forms.input 
                            name="country" 
                            :label="__('shop.checkout.country')" 
                            :value="$customer['country']" 
                        />
                    </div>

                    <div style="margin-top: 1rem;">
                        <button type="button" class="btn btn-primary">
                            {{ __('app.actions.save') }}
=======
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
                <a href="{{ route('customer.account.profile') }}" class="account-nav-link active">
                    <i class="fa-solid fa-user mr-1.5 ml-1.5"></i> {{ __('shop.account.profile') }}
                </a>
                <a href="{{ route('customer.account.settings') }}" class="account-nav-link">
                    <i class="fa-solid fa-gear mr-1.5 ml-1.5"></i> {{ __('shop.account.settings') }}
                </a>

                <form action="{{ route('customer.auth.logout') }}" method="POST" style="margin-top: 0.5rem; border-top: 1px solid var(--color-border); padding-top: 0.5rem;">
                    @csrf
                    <button type="submit" class="account-nav-link" style="width: 100%; text-align: start; background: none; border: none; cursor: pointer; color: var(--color-danger);">
                        <i class="fa-solid fa-right-from-bracket mr-1.5 ml-1.5"></i> {{ __('app.nav.logout') }}
                    </button>
                </form>
            </aside>

            <!-- Profile Form Card -->
            <div class="card" style="padding: 2.25rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem; margin-bottom: 1.75rem;">
                    <div>
                        <h3 style="font-size: 1.25rem; font-weight: 800; margin: 0;">
                            <i class="fa-solid fa-id-card text-primary mr-1 ml-1"></i>
                            {{ app()->getLocale() === 'ar' ? 'الملف الطبي والشخصي' : 'Clinical Account Dossier' }}
                        </h3>
                        <div class="text-xs text-muted" style="margin-top: 0.25rem;">
                            {{ app()->getLocale() === 'ar' ? 'بيانات الهوية الطبية وعنوان التوصيل المعتمد لبروتوكولاتك الحيوية.' : 'Personal identity and default delivery destination for clinical protocols.' }}
                        </div>
                    </div>
                    <span class="badge badge-success text-xs">
                        <i class="fa-solid fa-circle-check mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'حساب موثق' : 'Verified Member' }}
                    </span>
                </div>

                <form action="{{ route('customer.account.profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
                        <div>
                            <label class="text-xs font-bold text-muted" style="display: block; margin-bottom: 0.35rem;">
                                {{ __('shop.checkout.full_name') }} *
                            </label>
                            <input type="text" name="name" value="{{ old('name', $customer->name) }}" class="form-control text-sm" required>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-muted" style="display: block; margin-bottom: 0.35rem;">
                                {{ app()->getLocale() === 'ar' ? 'رقم الجوال' : 'Mobile Number' }} *
                            </label>
                            <input type="tel" name="phone" value="{{ old('phone', $customer->phone) }}" class="form-control text-sm" required>
                        </div>
                    </div>

                    <div style="margin-bottom: 1.25rem;">
                        <label class="text-xs font-bold text-muted" style="display: block; margin-bottom: 0.35rem;">
                            {{ __('shop.checkout.email') }} *
                        </label>
                        <input type="email" name="email" value="{{ old('email', $customer->email) }}" class="form-control text-sm" required>
                    </div>

                    <div style="margin-bottom: 1.25rem;">
                        <label class="text-xs font-bold text-muted" style="display: block; margin-bottom: 0.35rem;">
                            {{ app()->getLocale() === 'ar' ? 'العنوان الوطني / الشارع والحي' : 'Street Address / District' }}
                        </label>
                        <input type="text" name="address" value="{{ old('address', $customer->address) }}" class="form-control text-sm" placeholder="e.g. 742 Longevity Way, King Fahd District">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1.75rem;">
                        <div>
                            <label class="text-xs font-bold text-muted" style="display: block; margin-bottom: 0.35rem;">
                                {{ app()->getLocale() === 'ar' ? 'المدينة' : 'City' }}
                            </label>
                            <input type="text" name="city" value="{{ old('city', $customer->city) }}" class="form-control text-sm">
                        </div>
                        <div>
                            <label class="text-xs font-bold text-muted" style="display: block; margin-bottom: 0.35rem;">
                                {{ app()->getLocale() === 'ar' ? 'الدولة' : 'Country' }}
                            </label>
                            <select name="country" class="form-select text-sm" style="width: 100%;">
                                <option value="Saudi Arabia" {{ old('country', $customer->country) === 'Saudi Arabia' ? 'selected' : '' }}>🇸🇦 Saudi Arabia</option>
                                <option value="United Arab Emirates" {{ old('country', $customer->country) === 'United Arab Emirates' ? 'selected' : '' }}>🇦🇪 United Arab Emirates</option>
                                <option value="Kuwait" {{ old('country', $customer->country) === 'Kuwait' ? 'selected' : '' }}>🇰🇼 Kuwait</option>
                                <option value="Bahrain" {{ old('country', $customer->country) === 'Bahrain' ? 'selected' : '' }}>🇧🇭 Bahrain</option>
                                <option value="Qatar" {{ old('country', $customer->country) === 'Qatar' ? 'selected' : '' }}>🇶🇦 Qatar</option>
                                <option value="Oman" {{ old('country', $customer->country) === 'Oman' ? 'selected' : '' }}>🇴🇲 Oman</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-muted" style="display: block; margin-bottom: 0.35rem;">
                                {{ app()->getLocale() === 'ar' ? 'الرمز البريدي' : 'Postal Code' }}
                            </label>
                            <input type="text" name="postal_code" value="{{ old('postal_code', $customer->postal_code) }}" class="form-control text-sm" placeholder="12271">
                        </div>
                    </div>

                    <div style="display: flex; justify-content: flex-end;">
                        <button type="submit" class="btn btn-primary font-bold">
                            <i class="fa-solid fa-floppy-disk mr-1.5 ml-1.5"></i> {{ __('app.actions.save') }}
>>>>>>> origin/main
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.customer>

<x-layouts.customer :title="__('shop.account.addresses') . ' — ' . __('app.brand_name')">
    <div class="container" style="padding-top: 3rem; margin-bottom: 5rem;">
<<<<<<< HEAD
        <h1 style="font-size: 2.25rem; font-weight: 800; margin-bottom: 2rem;">
            {{ __('shop.account.addresses') }}
        </h1>
=======
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 2rem;">
            <div>
                <h1 style="font-size: 2.25rem; font-weight: 800; margin: 0;">
                    {{ __('shop.account.addresses') }}
                </h1>
                <div class="text-xs text-muted" style="margin-top: 0.25rem;">
                    {{ app()->getLocale() === 'ar' ? 'إدارة عناوين التوصيل والاستلام المبردة لطلباتك الدورية.' : 'Manage primary residences, clinical offices, and dispatch destinations.' }}
                </div>
            </div>

            <button type="button" class="btn btn-primary btn-sm" onclick="openAddAddressModal()">
                <i class="fa-solid fa-plus mr-1.5 ml-1.5"></i> {{ app()->getLocale() === 'ar' ? 'إضافة وجهة توصيل جديدة' : 'Add New Delivery Destination' }}
            </button>
        </div>

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
                <a href="{{ route('customer.account.addresses') }}" class="account-nav-link active">
                    📍 {{ __('shop.account.addresses') }}
                </a>
                <a href="{{ route('customer.account.profile') }}" class="account-nav-link">
                    👤 {{ __('shop.account.profile') }}
                </a>
                <a href="{{ route('customer.account.settings') }}" class="account-nav-link">
                    ⚙️ {{ __('shop.account.settings') }}
                </a>
=======
                    <i class="fa-solid fa-chart-pie mr-1.5 ml-1.5"></i> {{ __('shop.account.dashboard') }}
                </a>
                <a href="{{ route('customer.account.orders') }}" class="account-nav-link">
                    <i class="fa-solid fa-box mr-1.5 ml-1.5"></i> {{ __('shop.account.orders') }}
                </a>
                <a href="{{ route('customer.account.invoices') }}" class="account-nav-link">
                    <i class="fa-solid fa-file-invoice-dollar mr-1.5 ml-1.5"></i> {{ __('shop.account.invoices') }}
                </a>
                <a href="{{ route('customer.account.addresses') }}" class="account-nav-link active">
                    <i class="fa-solid fa-location-dot mr-1.5 ml-1.5"></i> {{ __('shop.account.addresses') }}
                </a>
                <a href="{{ route('customer.account.wishlist') }}" class="account-nav-link">
                    <i class="fa-solid fa-heart mr-1.5 ml-1.5"></i> {{ app()->getLocale() === 'ar' ? 'المنتجات المحفوظة' : 'Saved Formulations' }}
                </a>
                <a href="{{ route('customer.account.profile') }}" class="account-nav-link">
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
>>>>>>> origin/main
            </aside>

            <!-- Addresses Grid -->
            <div>
<<<<<<< HEAD
                <div style="display: flex; justify-content: flex-end; margin-bottom: 1.5rem;">
                    <button type="button" class="btn btn-primary btn-sm">
                        + Add New Delivery Destination
                    </button>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
                    @foreach($addresses as $address)
                        <div class="card" style="padding: 1.5rem; position: relative;">
                            @if($address['is_default'])
                                <span class="badge badge-accent" style="position: absolute; top: 1rem; inset-inline-end: 1rem;">
                                    Default Delivery
                                </span>
                            @endif

                            <h4 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 0.5rem;">
                                {{ $address['title'] }}
                            </h4>

                            <div class="text-sm text-secondary" style="display: flex; flex-direction: column; gap: 0.25rem; margin-bottom: 1.5rem;">
                                <div class="font-bold text-primary">{{ $address['recipient'] }}</div>
                                <div>{{ $address['street'] }}</div>
                                <div>{{ $address['city'] }}, {{ $address['country'] }} ({{ $address['postal_code'] }})</div>
                                <div class="text-muted">{{ $address['phone'] }}</div>
                            </div>

                            <div style="display: flex; gap: 0.5rem; border-top: 1px solid var(--color-border); padding-top: 1rem;">
                                <button type="button" class="btn btn-secondary btn-sm">
                                    {{ __('app.actions.edit') }}
                                </button>
                                @if(!$address['is_default'])
                                    <button type="button" class="btn btn-ghost btn-sm text-danger">
                                        {{ __('app.actions.delete') }}
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
=======
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
                    @forelse($addresses as $address)
                        <div class="card" style="padding: 1.75rem; position: relative; border-inline-start: 4px solid {{ !empty($address['is_default']) ? '#10B981' : 'var(--color-border)' }};">
                            @if(!empty($address['is_default']))
                                <span class="badge badge-success text-xs" style="position: absolute; top: 1.25rem; inset-inline-end: 1.25rem;">
                                    <i class="fa-solid fa-star mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'العنوان الافتراضي' : 'Default Destination' }}
                                </span>
                            @endif

                            <h4 style="font-size: 1.15rem; font-weight: 800; margin-bottom: 0.5rem;">
                                {{ $address['title'] ?? 'Delivery Address' }}
                            </h4>

                            <div class="text-sm text-secondary" style="display: flex; flex-direction: column; gap: 0.35rem; margin-bottom: 1.5rem;">
                                <div class="font-bold text-primary">{{ $address['recipient'] }}</div>
                                <div>{{ $address['street'] }}</div>
                                <div>{{ $address['city'] }}, {{ $address['country'] }} ({{ $address['postal_code'] ?? '12271' }})</div>
                                <div class="text-muted text-xs font-mono">{{ $address['phone'] }}</div>
                            </div>

                            <div style="display: flex; gap: 0.5rem; border-top: 1px solid var(--color-border); padding-top: 1rem; align-items: center; flex-wrap: wrap;">
                                <button type="button" class="btn btn-secondary btn-xs" onclick='openEditAddressModal(@json($address))'>
                                    <i class="fa-solid fa-pen mr-1 ml-1"></i> {{ __('app.actions.edit') }}
                                </button>

                                @if(empty($address['is_default']))
                                    <form action="{{ route('customer.account.addresses.default', $address['id']) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-neutral btn-xs">
                                            {{ app()->getLocale() === 'ar' ? 'تعيين كافتراضي' : 'Set as Default' }}
                                        </button>
                                    </form>

                                    <form action="{{ route('customer.account.addresses.destroy', $address['id']) }}" method="POST" class="inline" onsubmit="return confirm('{{ app()->getLocale() === 'ar' ? 'هل أنت متأكد من حذف هذا العنوان؟' : 'Are you sure you want to delete this address?' }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-ghost btn-xs text-danger">
                                            <i class="fa-solid fa-trash-can mr-1 ml-1"></i> {{ __('app.actions.delete') }}
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="card" style="padding: 2rem; text-align: center; grid-column: 1 / -1;">
                            <p class="text-muted">{{ app()->getLocale() === 'ar' ? 'لا توجد عناوين محفوظة بعد.' : 'No saved delivery destinations yet.' }}</p>
                        </div>
                    @endforelse
>>>>>>> origin/main
                </div>
            </div>
        </div>
    </div>
<<<<<<< HEAD
=======

    <!-- Add/Edit Address Modal -->
    <div id="addressModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 9999; align-items: center; justify-content: center; padding: 1rem;">
        <div class="card" style="max-width: 520px; width: 100%; padding: 2rem; border-radius: var(--radius-lg); box-shadow: var(--shadow-xl); position: relative;">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                <h3 id="addressModalTitle" style="font-size: 1.25rem; font-weight: 800; margin: 0;">
                    {{ app()->getLocale() === 'ar' ? 'إضافة وجهة توصيل جديدة' : 'Add Delivery Destination' }}
                </h3>
                <button type="button" class="btn btn-secondary btn-xs" onclick="closeAddressModal()">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form id="addressForm" action="{{ route('customer.account.addresses.store') }}" method="POST">
                @csrf
                <input type="hidden" name="_method" id="addressFormMethod" value="POST">

                <div style="margin-bottom: 1rem;">
                    <label class="text-xs font-bold text-muted">{{ app()->getLocale() === 'ar' ? 'تسمية العنوان (مثال: المنزل الرئيسي، المكتب، العيادة)' : 'Destination Label (e.g. Primary Residence, Office, Clinic)' }} *</label>
                    <input type="text" name="title" id="addrTitle" class="form-control text-sm" placeholder="e.g. Primary Residence" required style="margin-top: 0.25rem;">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label class="text-xs font-bold text-muted">{{ app()->getLocale() === 'ar' ? 'اسم المستلم' : 'Recipient Name' }} *</label>
                        <input type="text" name="recipient" id="addrRecipient" class="form-control text-sm" value="{{ $customer->name }}" required style="margin-top: 0.25rem;">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-muted">{{ app()->getLocale() === 'ar' ? 'رقم الجوال' : 'Phone Number' }} *</label>
                        <input type="tel" name="phone" id="addrPhone" class="form-control text-sm" value="{{ $customer->phone }}" required style="margin-top: 0.25rem;">
                    </div>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label class="text-xs font-bold text-muted">{{ app()->getLocale() === 'ar' ? 'الشارع ورقم المبنى والحي' : 'Street Address & District' }} *</label>
                    <input type="text" name="street" id="addrStreet" class="form-control text-sm" placeholder="e.g. 742 Longevity Way, King Fahd District" required style="margin-top: 0.25rem;">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.75rem; margin-bottom: 1.25rem;">
                    <div>
                        <label class="text-xs font-bold text-muted">{{ app()->getLocale() === 'ar' ? 'المدينة' : 'City' }} *</label>
                        <input type="text" name="city" id="addrCity" class="form-control text-sm" value="{{ $customer->city ?? 'Riyadh' }}" required style="margin-top: 0.25rem;">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-muted">{{ app()->getLocale() === 'ar' ? 'الدولة' : 'Country' }} *</label>
                        <input type="text" name="country" id="addrCountry" class="form-control text-sm" value="{{ $customer->country ?? 'Saudi Arabia' }}" required style="margin-top: 0.25rem;">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-muted">{{ app()->getLocale() === 'ar' ? 'الرمز البريدي' : 'Postal Code' }}</label>
                        <input type="text" name="postal_code" id="addrPostal" class="form-control text-sm" placeholder="12271" style="margin-top: 0.25rem;">
                    </div>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="is_default" id="addrIsDefault" value="1">
                        <span class="text-xs font-bold">{{ app()->getLocale() === 'ar' ? 'تعيين كعنوان توصيل افتراضي' : 'Set as default delivery destination' }}</span>
                    </label>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                    <button type="button" class="btn btn-secondary" onclick="closeAddressModal()">
                        {{ __('app.actions.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary font-bold">
                        <i class="fa-solid fa-floppy-disk mr-1 ml-1"></i> {{ __('app.actions.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAddAddressModal() {
            document.getElementById('addressModalTitle').innerText = "{{ app()->getLocale() === 'ar' ? 'إضافة وجهة توصيل جديدة' : 'Add Delivery Destination' }}";
            document.getElementById('addressForm').action = "{{ route('customer.account.addresses.store') }}";
            document.getElementById('addressFormMethod').value = "POST";
            document.getElementById('addrTitle').value = "";
            document.getElementById('addrRecipient').value = "{{ $customer->name }}";
            document.getElementById('addrPhone').value = "{{ $customer->phone }}";
            document.getElementById('addrStreet').value = "";
            document.getElementById('addrCity').value = "{{ $customer->city ?? 'Riyadh' }}";
            document.getElementById('addrCountry').value = "{{ $customer->country ?? 'Saudi Arabia' }}";
            document.getElementById('addrPostal').value = "";
            document.getElementById('addrIsDefault').checked = false;
            document.getElementById('addressModal').style.display = 'flex';
        }

        function openEditAddressModal(addr) {
            document.getElementById('addressModalTitle').innerText = "{{ app()->getLocale() === 'ar' ? 'تعديل وجهة التوصيل' : 'Edit Delivery Destination' }}";
            document.getElementById('addressForm').action = "{{ url('account/addresses') }}/" + addr.id;
            document.getElementById('addressFormMethod').value = "PUT";
            document.getElementById('addrTitle').value = addr.title || "";
            document.getElementById('addrRecipient').value = addr.recipient || "";
            document.getElementById('addrPhone').value = addr.phone || "";
            document.getElementById('addrStreet').value = addr.street || "";
            document.getElementById('addrCity').value = addr.city || "";
            document.getElementById('addrCountry').value = addr.country || "";
            document.getElementById('addrPostal').value = addr.postal_code || "";
            document.getElementById('addrIsDefault').checked = !!addr.is_default;
            document.getElementById('addressModal').style.display = 'flex';
        }

        function closeAddressModal() {
            document.getElementById('addressModal').style.display = 'none';
        }
    </script>
>>>>>>> origin/main
</x-layouts.customer>

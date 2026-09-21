<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'إدارة عهد وأصول الشركة' : 'Company Assets & Custody Inventory'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'حصر أجهزة الحواسيب، الهواتف، والعهد العينية المسندة للموظفين' : 'Manage corporate hardware, field devices, mobile assets, and custody assignments'"
>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0; color: var(--text-color, #1E293B);">
                <i class="fa-solid fa-boxes-stacked text-teal-500 mr-2 ml-2"></i> 
                {{ app()->getLocale() === 'ar' ? 'مخزون الأصول والعهد' : 'Corporate Asset Inventory' }}
            </h2>
        </div>
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
            <a href="{{ route('admin.hr.assets.assignments') }}" class="btn btn-outline btn-sm">
                <i class="fa-solid fa-user-tag mr-1 ml-1 text-indigo-500"></i> {{ app()->getLocale() === 'ar' ? 'العهد المسندة' : 'Active Custody' }}
            </a>
            <a href="{{ route('admin.hr.assets.returns') }}" class="btn btn-outline btn-sm">
                <i class="fa-solid fa-rotate-left mr-1 ml-1 text-emerald-500"></i> {{ app()->getLocale() === 'ar' ? 'العهد المسترجعة' : 'Return History' }}
            </a>
            <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('newAssetModal').style.display='flex'">
                <i class="fa-solid fa-plus text-xs mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'إضافة أصل جديد' : 'Register New Asset' }}
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="padding: 0.75rem 1rem; margin-bottom: 1rem; border-radius: 0.5rem; background: #DCFCE7; color: #166534; border: 1px solid #BBF7D0;">
            <i class="fa-solid fa-circle-check mr-1 ml-1"></i> {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger" style="padding: 0.75rem 1rem; margin-bottom: 1rem; border-radius: 0.5rem; background: #FEE2E2; color: #991B1B; border: 1px solid #FECACA;">
            <ul style="margin: 0; padding-left: 1.25rem;">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
        <div class="table-responsive">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                <thead>
                    <tr style="border-bottom: 2px solid #E2E8F0; text-align: left; color: #64748B;">
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'رمز الأصل' : 'Asset Code' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'اسم الجهاز / الأصل' : 'Asset Name' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'النوع' : 'Category' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'الرقم التسلسلي' : 'Serial Number' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'المستلم الحالي' : 'Current Custodian' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assets as $asset)
                        <tr style="border-bottom: 1px solid #F1F5F9;">
                            <td style="padding: 0.75rem 0.5rem; font-family: monospace; font-weight: 700; color: #0284C7;">
                                {{ $asset->asset_code }}
                            </td>
                            <td style="padding: 0.75rem 0.5rem;">
                                <strong style="color: #0F172A;">{{ $asset->name }}</strong>
                                <span style="font-size: 0.75rem; color: #64748B; display: block;">{{ $asset->notes }}</span>
                            </td>
                            <td style="padding: 0.75rem 0.5rem;">
                                <span style="background: #F1F5F9; color: #334155; font-weight: 600; font-size: 0.75rem; padding: 0.2rem 0.5rem; border-radius: 999px;">
                                    {{ ucfirst($asset->asset_type) }}
                                </span>
                            </td>
                            <td style="padding: 0.75rem 0.5rem; font-family: monospace; color: #475569;">
                                {{ $asset->serial_number ?? '—' }}
                            </td>
                            <td style="padding: 0.75rem 0.5rem;">
                                @if($asset->currentAssignment)
                                    <a href="{{ route('admin.hr.employees.show', $asset->currentAssignment->employee_id) }}" style="color: #0284C7; font-weight: 600; text-decoration: none;">
                                        {{ $asset->currentAssignment->employee?->full_name }}
                                    </a>
                                @else
                                    <span style="color: #94A3B8;">{{ app()->getLocale() === 'ar' ? 'بالمخزن' : 'In Inventory' }}</span>
                                @endif
                            </td>
                            <td style="padding: 0.75rem 0.5rem;">
                                @php
                                    $aBg = match($asset->status) {
                                        'available' => '#DCFCE7',
                                        'assigned' => '#E0E7FF',
                                        'maintenance' => '#FEF3C7',
                                        default => '#F1F5F9',
                                    };
                                    $aCol = match($asset->status) {
                                        'available' => '#166534',
                                        'assigned' => '#3730A3',
                                        'maintenance' => '#92400E',
                                        default => '#475569',
                                    };
                                @endphp
                                <span class="badge" style="background: {{ $aBg }}; color: {{ $aCol }}; padding: 0.2rem 0.55rem; border-radius: 999px; font-weight: 700;">
                                    {{ ucfirst($asset->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 3rem; color: #94A3B8;">
                                <i class="fa-solid fa-laptop" style="font-size: 2.5rem; margin-bottom: 0.75rem; display: block; opacity: 0.4;"></i>
                                {{ app()->getLocale() === 'ar' ? 'لا توجد أصول مسجلة حتى الآن.' : 'No company assets registered.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top: 1rem;">
            {{ $assets->links() }}
        </div>
    </div>

    <!-- Register Asset Modal -->
    <div id="newAssetModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div style="background: #ffffff; border-radius: 0.75rem; padding: 1.5rem; width: 100%; max-width: 480px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="font-size: 1.1rem; font-weight: 700; margin: 0;">{{ app()->getLocale() === 'ar' ? 'إضافة أصل إلى العهد' : 'Register Corporate Asset' }}</h3>
                <button type="button" onclick="document.getElementById('newAssetModal').style.display='none'" style="background: none; border: none; font-size: 1.25rem; cursor: pointer;">&times;</button>
            </div>
            <form action="{{ route('admin.hr.assets.store') }}" method="POST">
                @csrf
                <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'اسم الجهاز / الأصل *' : 'Asset Name *' }}</label>
                        <input type="text" name="name" required placeholder="e.g. MacBook Pro M3 or iPad Pro 12.9" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                        <div>
                            <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'كود الأصل *' : 'Asset Code *' }}</label>
                            <input type="text" name="asset_code" required placeholder="e.g. AST-0014" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                        </div>
                        <div>
                            <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'تصنيف الأصل *' : 'Asset Type *' }}</label>
                            <select name="asset_type" required class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                                <option value="laptop">Laptop / PC</option>
                                <option value="phone">Smartphone</option>
                                <option value="tablet">Tablet / iPad</option>
                                <option value="sim">Company SIM Card</option>
                                <option value="vehicle">Corporate Vehicle</option>
                                <option value="keys">Access Keys / Cards</option>
                                <option value="other">Other Equipment</option>
                            </select>
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                        <div>
                            <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'الرقم التسلسلي' : 'Serial Number' }}</label>
                            <input type="text" name="serial_number" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                        </div>
                        <div>
                            <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'تكلفة الشراء' : 'Purchase Cost' }}</label>
                            <input type="number" step="0.01" name="purchase_cost" placeholder="0.00" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                        </div>
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'ملاحظات وحالة الأصل' : 'Notes / Condition' }}</label>
                        <textarea name="notes" rows="2" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;"></textarea>
                    </div>
                    <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 1rem;">
                        <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('newAssetModal').style.display='none'">{{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}</button>
                        <button type="submit" class="btn btn-primary btn-sm">{{ app()->getLocale() === 'ar' ? 'حفظ الأصل' : 'Register Asset' }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>

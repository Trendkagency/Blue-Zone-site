<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'سجل تسليم وإسناد العهد' : 'Active Asset Custody Assignments'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'إدارة تسليم العهد والأجهزة للموظفين ومتابعة استردادها' : 'Track company assets currently deployed in workforce custody and process equipment returns'"
>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0; color: var(--text-color, #1E293B);">
                <i class="fa-solid fa-user-tag text-indigo-500 mr-2 ml-2"></i> 
                {{ app()->getLocale() === 'ar' ? 'العهد المسندة للموظفين' : 'Active Custody Records' }}
            </h2>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ route('admin.hr.assets.index') }}" class="btn btn-outline btn-sm">
                <i class="fa-solid fa-boxes-stacked mr-1 ml-1 text-teal-500"></i> {{ app()->getLocale() === 'ar' ? 'مخزون الأصول' : 'Inventory' }}
            </a>
            <a href="{{ route('admin.hr.assets.returns') }}" class="btn btn-outline btn-sm">
                <i class="fa-solid fa-rotate-left mr-1 ml-1 text-emerald-500"></i> {{ app()->getLocale() === 'ar' ? 'سجل الإرجاع' : 'Returns' }}
            </a>
            <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('assignModal').style.display='flex'">
                <i class="fa-solid fa-plus text-xs mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'تسليم عهدة جديدة' : 'Assign Asset' }}
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
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'الموظف المستلم' : 'Custodian' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'الأصل المسلم' : 'Asset' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'تاريخ التسليم' : 'Assigned Date' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'الإرجاع المتوقع' : 'Expected Return' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'حالة الأصل عند التسليم' : 'Condition' }}</th>
                        <th style="padding: 0.75rem 0.5rem; text-align: right;">{{ app()->getLocale() === 'ar' ? 'استرجاع العهدة' : 'Return Action' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assignments as $item)
                        <tr style="border-bottom: 1px solid #F1F5F9;">
                            <td style="padding: 0.75rem 0.5rem;">
                                <a href="{{ route('admin.hr.employees.show', $item->employee_id) }}" style="color: #0284C7; font-weight: 700; text-decoration: none;">
                                    {{ $item->employee?->full_name ?? 'N/A' }}
                                </a>
                                <span style="font-size: 0.75rem; color: #64748B; display: block;">{{ $item->employee?->employee_number }}</span>
                            </td>
                            <td style="padding: 0.75rem 0.5rem;">
                                <strong style="color: #0F172A;">{{ $item->asset?->name }}</strong>
                                <span style="font-size: 0.75rem; color: #64748B; display: block; font-family: monospace;">{{ $item->asset?->asset_code }}</span>
                            </td>
                            <td style="padding: 0.75rem 0.5rem; color: #334155;">{{ $item->assigned_at }}</td>
                            <td style="padding: 0.75rem 0.5rem; color: #334155;">{{ $item->expected_return_date ?? 'Permanent Assignment' }}</td>
                            <td style="padding: 0.75rem 0.5rem; color: #64748B;">{{ $item->condition_on_assignment ?? 'Good / New' }}</td>
                            <td style="padding: 0.75rem 0.5rem; text-align: right;">
                                @if(!$item->returned_at)
                                    <button type="button" class="btn btn-outline btn-xs text-emerald-600" style="border-color: #A7F3D0;" onclick="openReturnModal({{ $item->id }}, '{{ addslashes($item->asset?->name) }}')">
                                        <i class="fa-solid fa-rotate-left mr-1"></i> {{ app()->getLocale() === 'ar' ? 'استلام العهدة' : 'Return' }}
                                    </button>
                                @else
                                    <span style="color: #059669; font-weight: 700; font-size: 0.75rem;">Returned</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 3rem; color: #94A3B8;">
                                <i class="fa-solid fa-user-tag" style="font-size: 2.5rem; margin-bottom: 0.75rem; display: block; opacity: 0.4;"></i>
                                {{ app()->getLocale() === 'ar' ? 'لا توجد عهد مسندة للموظفين حالياً.' : 'No active custody assignments.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top: 1rem;">
            {{ $assignments->links() }}
        </div>
    </div>

    <!-- Assign Modal -->
    <div id="assignModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div style="background: #ffffff; border-radius: 0.75rem; padding: 1.5rem; width: 100%; max-width: 480px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="font-size: 1.1rem; font-weight: 700; margin: 0;">{{ app()->getLocale() === 'ar' ? 'تسليم عهدة لموظف' : 'Assign Asset to Employee' }}</h3>
                <button type="button" onclick="document.getElementById('assignModal').style.display='none'" style="background: none; border: none; font-size: 1.25rem; cursor: pointer;">&times;</button>
            </div>
            <form action="{{ route('admin.hr.assets.assign') }}" method="POST">
                @csrf
                <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'الأصل المتاح *' : 'Available Asset *' }}</label>
                        <select name="hr_asset_id" required class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                            @foreach($availableAssets as $av)
                                <option value="{{ $av->id }}">{{ $av->name }} ({{ $av->asset_code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'الموظف المستلم *' : 'Employee *' }}</label>
                        <select name="employee_id" required class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->full_name }} ({{ $emp->employee_number }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                        <div>
                            <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'تاريخ التسليم *' : 'Assigned Date *' }}</label>
                            <input type="date" name="assigned_at" required value="{{ now()->toDateString() }}" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                        </div>
                        <div>
                            <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'تاريخ الإرجاع المتوقع' : 'Expected Return' }}</label>
                            <input type="date" name="expected_return_date" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                        </div>
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'حالة الأصل عند التسليم' : 'Condition upon assignment' }}</label>
                        <input type="text" name="condition_on_assignment" placeholder="e.g. Pristine, Brand New with Charger & Bag" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                    </div>
                    <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 1rem;">
                        <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('assignModal').style.display='none'">{{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}</button>
                        <button type="submit" class="btn btn-primary btn-sm">{{ app()->getLocale() === 'ar' ? 'تسليم العهدة' : 'Assign Asset' }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Return Modal -->
    <div id="returnAssetModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div style="background: #ffffff; border-radius: 0.75rem; padding: 1.5rem; width: 100%; max-width: 440px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="font-size: 1.1rem; font-weight: 700; margin: 0;">{{ app()->getLocale() === 'ar' ? 'استرجاع العهدة إلى المخزن' : 'Confirm Asset Return' }}</h3>
                <button type="button" onclick="document.getElementById('returnAssetModal').style.display='none'" style="background: none; border: none; font-size: 1.25rem; cursor: pointer;">&times;</button>
            </div>
            <form id="returnAssetForm" method="POST">
                @csrf
                <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                    <p id="returnAssetName" style="color: #0284C7; font-weight: 700; margin: 0;"></p>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'تاريخ الاسترجاع *' : 'Returned Date *' }}</label>
                        <input type="date" name="returned_at" required value="{{ now()->toDateString() }}" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'حالة الأصل عند الاسترجاع *' : 'Condition on Return *' }}</label>
                        <input type="text" name="condition_on_return" required placeholder="e.g. Fully functional, minor scratches" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'ملاحظات الفحص' : 'Inspection Notes' }}</label>
                        <textarea name="notes" rows="2" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;"></textarea>
                    </div>
                    <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 1rem;">
                        <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('returnAssetModal').style.display='none'">{{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}</button>
                        <button type="submit" class="btn btn-primary btn-sm">{{ app()->getLocale() === 'ar' ? 'تأكيد الاستلام' : 'Confirm Return' }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openReturnModal(assignmentId, assetName) {
            document.getElementById('returnAssetForm').action = '/admin/hr/assets/assignments/' + assignmentId + '/return';
            document.getElementById('returnAssetName').textContent = 'Asset: ' + assetName;
            document.getElementById('returnAssetModal').style.display = 'flex';
        }
    </script>
</x-layouts.admin>

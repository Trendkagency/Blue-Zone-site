<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'سجل العهد المسترجعة' : 'Asset Returns Audit History'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'أرشيف العهد والأجهزة التي تم استرجاعها وفحصها وإيداعها في المخزن' : 'Historical inventory log of reclaimed company hardware and custody clearance records'"
>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0; color: var(--text-color, #1E293B);">
                <i class="fa-solid fa-rotate-left text-emerald-500 mr-2 ml-2"></i> 
                {{ app()->getLocale() === 'ar' ? 'أرشيف العهد المسترجعة' : 'Returned Assets History' }}
            </h2>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ route('admin.hr.assets.index') }}" class="btn btn-outline btn-sm">
                <i class="fa-solid fa-boxes-stacked mr-1 ml-1 text-teal-500"></i> {{ app()->getLocale() === 'ar' ? 'المخزون' : 'Inventory' }}
            </a>
            <a href="{{ route('admin.hr.assets.assignments') }}" class="btn btn-outline btn-sm">
                <i class="fa-solid fa-user-tag mr-1 ml-1 text-indigo-500"></i> {{ app()->getLocale() === 'ar' ? 'العهد المسندة' : 'Active Custody' }}
            </a>
        </div>
    </div>

    <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
        <div class="table-responsive">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                <thead>
                    <tr style="border-bottom: 2px solid #E2E8F0; text-align: left; color: #64748B;">
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'الموظف المسلّم' : 'Former Custodian' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'الأصل المسترجع' : 'Returned Asset' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'تاريخ التسليم' : 'Assigned' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'تاريخ الاسترجاع' : 'Returned' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'حالة الأصل عند الاسترجاع' : 'Condition upon Return' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($returnedAssignments as $item)
                        <tr style="border-bottom: 1px solid #F1F5F9;">
                            <td style="padding: 0.75rem 0.5rem;">
                                <a href="{{ route('admin.hr.employees.show', $item->employee_id) }}" style="color: #0284C7; font-weight: 700; text-decoration: none;">
                                    {{ $item->employee?->full_name ?? 'N/A' }}
                                </a>
                                <span style="font-size: 0.75rem; color: #64748B; display: block;">{{ $item->employee?->department?->name }}</span>
                            </td>
                            <td style="padding: 0.75rem 0.5rem;">
                                <strong style="color: #0F172A;">{{ $item->asset?->name }}</strong>
                                <span style="font-size: 0.75rem; color: #64748B; display: block; font-family: monospace;">{{ $item->asset?->asset_code }}</span>
                            </td>
                            <td style="padding: 0.75rem 0.5rem; color: #64748B;">{{ $item->assigned_at }}</td>
                            <td style="padding: 0.75rem 0.5rem; color: #059669; font-weight: 700;">{{ $item->returned_at }}</td>
                            <td style="padding: 0.75rem 0.5rem; color: #334155;">
                                <span style="background: #F1F5F9; padding: 0.2rem 0.5rem; border-radius: 0.25rem; font-weight: 600;">
                                    {{ $item->condition_on_return ?? 'Inspected & Cleared' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 3rem; color: #94A3B8;">
                                <i class="fa-solid fa-rotate-left" style="font-size: 2.5rem; margin-bottom: 0.75rem; display: block; opacity: 0.4;"></i>
                                {{ app()->getLocale() === 'ar' ? 'لا توجد عهد مسترجعة في الأرشيف.' : 'No asset return records logged yet.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top: 1rem;">
            {{ $returnedAssignments->links() }}
        </div>
    </div>
</x-layouts.admin>

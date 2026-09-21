<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'سجل إنهاء الخدمات والتعاقدات' : 'Employee Contract Terminations'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'قرارات إنهاء الخدمات، الأسباب النظامية، وحفظ الأرشيف الكامل' : 'Formal employment terminations, statutory cause records, and exit compliance audits'"
>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0; color: var(--text-color, #1E293B);">
                <i class="fa-solid fa-user-xmark text-rose-500 mr-2 ml-2"></i> 
                {{ app()->getLocale() === 'ar' ? 'إنهاء الخدمات والتعاقد' : 'Terminations Ledger' }}
            </h2>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ route('admin.hr.offboarding.resignations') }}" class="btn btn-outline btn-sm">
                <i class="fa-solid fa-person-walking-dashed-line-arrow-right mr-1 ml-1 text-sky-500"></i> {{ app()->getLocale() === 'ar' ? 'الاستقالات' : 'Resignations' }}
            </a>
            <a href="{{ route('admin.hr.offboarding.settlements') }}" class="btn btn-outline btn-sm">
                <i class="fa-solid fa-calculator mr-1 ml-1 text-emerald-500"></i> {{ app()->getLocale() === 'ar' ? 'المخالصات' : 'Settlements' }}
            </a>
            <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('newTerminationModal').style.display='flex'">
                <i class="fa-solid fa-plus text-xs mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'معالجة إنهاء خدمة' : 'Process Termination' }}
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
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'الموظف' : 'Employee' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'تاريخ الإنهاء' : 'Termination Date' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'سبب إنهاء الخدمة' : 'Reason / Cause' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'ملاحظات إضافية' : 'Notes' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($terminations as $term)
                        <tr style="border-bottom: 1px solid #F1F5F9;">
                            <td style="padding: 0.75rem 0.5rem;">
                                <a href="{{ route('admin.hr.employees.show', $term->employee_id) }}" style="color: #0284C7; font-weight: 700; text-decoration: none;">
                                    {{ $term->employee?->full_name ?? 'N/A' }}
                                </a>
                                <span style="font-size: 0.75rem; color: #64748B; display: block;">{{ $term->employee?->department?->name }}</span>
                            </td>
                            <td style="padding: 0.75rem 0.5rem; color: #DC2626; font-weight: 700;">{{ $term->last_working_date }}</td>
                            <td style="padding: 0.75rem 0.5rem; color: #0F172A; font-weight: 600;">{{ $term->reason }}</td>
                            <td style="padding: 0.75rem 0.5rem; color: #64748B; max-width: 250px;">{{ $term->notes ?? '—' }}</td>
                            <td style="padding: 0.75rem 0.5rem;">
                                <span class="badge" style="background: #FEE2E2; color: #991B1B; padding: 0.2rem 0.5rem; border-radius: 999px; font-weight: 700;">
                                    {{ ucfirst($term->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 3rem; color: #94A3B8;">
                                <i class="fa-solid fa-user-xmark" style="font-size: 2.5rem; margin-bottom: 0.75rem; display: block; opacity: 0.4;"></i>
                                {{ app()->getLocale() === 'ar' ? 'لا توجد قرارات إنهاء خدمات مسجلة.' : 'No termination records found.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top: 1rem;">
            {{ $terminations->links() }}
        </div>
    </div>

    <!-- Process Termination Modal -->
    <div id="newTerminationModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div style="background: #ffffff; border-radius: 0.75rem; padding: 1.5rem; width: 100%; max-width: 480px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="font-size: 1.1rem; font-weight: 700; margin: 0; color: #991B1B;">{{ app()->getLocale() === 'ar' ? 'معالجة إنهاء خدمة موظف' : 'Process Employee Termination' }}</h3>
                <button type="button" onclick="document.getElementById('newTerminationModal').style.display='none'" style="background: none; border: none; font-size: 1.25rem; cursor: pointer;">&times;</button>
            </div>
            <form action="{{ route('admin.hr.offboarding.terminations.store') }}" method="POST">
                @csrf
                <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'الموظف *' : 'Employee *' }}</label>
                        <select name="employee_id" required class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->full_name }} ({{ $emp->employee_number }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'آخر يوم عمل (تاريخ إنهاء الخدمة) *' : 'Last Working Date *' }}</label>
                        <input type="date" name="last_working_date" required value="{{ now()->toDateString() }}" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'أسباب وقرار إنهاء الخدمة *' : 'Reason / Justification *' }}</label>
                        <textarea name="reason" required rows="2" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;"></textarea>
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'ملاحظات وتوجيهات التسوية' : 'Settlement & Handover Instructions' }}</label>
                        <textarea name="notes" rows="2" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;"></textarea>
                    </div>
                    <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 1rem;">
                        <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('newTerminationModal').style.display='none'">{{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}</button>
                        <button type="submit" class="btn btn-danger btn-sm">{{ app()->getLocale() === 'ar' ? 'تأكيد إنهاء الخدمة' : 'Execute Termination' }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>

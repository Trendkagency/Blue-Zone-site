<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'البرامج والدورات التدريبية' : 'Corporate Training Programs'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'كتالوج الدورات التدريبية والتطوير المهني لكوادر الشركة' : 'Workforce training curriculum, continuing education, and skill development programs'"
>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0; color: var(--text-color, #1E293B);">
                <i class="fa-solid fa-graduation-cap text-sky-500 mr-2 ml-2"></i> 
                {{ app()->getLocale() === 'ar' ? 'البرامج التدريبية' : 'Training Catalog' }}
            </h2>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ route('admin.hr.training.employee-training') }}" class="btn btn-outline btn-sm">
                <i class="fa-solid fa-users mr-1 ml-1 text-indigo-500"></i> {{ app()->getLocale() === 'ar' ? 'تدريب الموظفين' : 'Enrollments' }}
            </a>
            <a href="{{ route('admin.hr.training.certificates') }}" class="btn btn-outline btn-sm">
                <i class="fa-solid fa-certificate mr-1 ml-1 text-amber-500"></i> {{ app()->getLocale() === 'ar' ? 'الشهادات' : 'Certificates' }}
            </a>
            <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('newProgramModal').style.display='flex'">
                <i class="fa-solid fa-plus text-xs mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'إضافة برنامج تدريبي' : 'Add Program' }}
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="padding: 0.75rem 1rem; margin-bottom: 1rem; border-radius: 0.5rem; background: #DCFCE7; color: #166534; border: 1px solid #BBF7D0;">
            <i class="fa-solid fa-circle-check mr-1 ml-1"></i> {{ session('success') }}
        </div>
    @endif

    <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
        <div class="table-responsive">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                <thead>
                    <tr style="border-bottom: 2px solid #E2E8F0; text-align: left; color: #64748B;">
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'اسم البرنامج التدريبي' : 'Program Name' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'الجهة المدربة' : 'Provider / Trainer' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'المدة والفترة' : 'Duration & Period' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'التكلفة' : 'Cost' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'المتدربون' : 'Enrollees' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($programs as $prog)
                        <tr style="border-bottom: 1px solid #F1F5F9;">
                            <td style="padding: 0.75rem 0.5rem;">
                                <div style="font-weight: 700; color: #0F172A; font-size: 0.95rem;">{{ $prog->name }}</div>
                                <span style="font-size: 0.75rem; color: #64748B;">{{ $prog->description }}</span>
                            </td>
                            <td style="padding: 0.75rem 0.5rem; color: #334155;">
                                <strong>{{ $prog->provider ?? 'Blue Zone Academy' }}</strong>
                                <span style="font-size: 0.75rem; color: #64748B; display: block;">{{ $prog->trainer ? 'By ' . $prog->trainer : '' }}</span>
                            </td>
                            <td style="padding: 0.75rem 0.5rem; color: #334155;">
                                <span>{{ $prog->duration ?? '30 Hours' }}</span>
                                <span style="font-size: 0.75rem; color: #64748B; display: block;">{{ $prog->start_date }} &ndash; {{ $prog->end_date }}</span>
                            </td>
                            <td style="padding: 0.75rem 0.5rem; font-family: monospace; font-weight: 600; color: #334155;">
                                {{ $prog->cost ? number_format($prog->cost, 2) . ' ' . (function_exists('currency_code') ? currency_code() : 'SAR') : 'Sponsored' }}
                            </td>
                            <td style="padding: 0.75rem 0.5rem;">
                                <span style="background: #EEF2FF; color: #4F46E5; font-weight: 700; font-size: 0.75rem; padding: 0.2rem 0.6rem; border-radius: 999px;">
                                    {{ $prog->employee_trainings_count }} {{ app()->getLocale() === 'ar' ? 'متدرب' : 'trainees' }}
                                </span>
                            </td>
                            <td style="padding: 0.75rem 0.5rem;">
                                <span class="badge" style="background: #DCFCE7; color: #166534; padding: 0.2rem 0.5rem; border-radius: 999px; font-weight: 700;">
                                    {{ ucfirst($prog->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 3rem; color: #94A3B8;">
                                <i class="fa-solid fa-graduation-cap" style="font-size: 2.5rem; margin-bottom: 0.75rem; display: block; opacity: 0.4;"></i>
                                {{ app()->getLocale() === 'ar' ? 'لا توجد برامج تدريبية مسجلة.' : 'No training programs registered.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top: 1rem;">
            {{ $programs->links() }}
        </div>
    </div>

    <!-- Create Program Modal -->
    <div id="newProgramModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div style="background: #ffffff; border-radius: 0.75rem; padding: 1.5rem; width: 100%; max-width: 480px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="font-size: 1.1rem; font-weight: 700; margin: 0;">{{ app()->getLocale() === 'ar' ? 'إضافة برنامج تدريبي' : 'New Training Program' }}</h3>
                <button type="button" onclick="document.getElementById('newProgramModal').style.display='none'" style="background: none; border: none; font-size: 1.25rem; cursor: pointer;">&times;</button>
            </div>
            <form action="{{ route('admin.hr.training.programs.store') }}" method="POST">
                @csrf
                <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'اسم البرنامج التدريبي *' : 'Program Name *' }}</label>
                        <input type="text" name="name" required placeholder="e.g. Bioceuticals Product Mastery & Compliance" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                        <div>
                            <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'الجهة المنفذة' : 'Provider' }}</label>
                            <input type="text" name="provider" placeholder="e.g. Pharma Excellence Institute" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                        </div>
                        <div>
                            <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'المدرب المسؤول' : 'Trainer' }}</label>
                            <input type="text" name="trainer" placeholder="e.g. Dr. Ahmed Mansour" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                        <div>
                            <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'تاريخ البداية' : 'Start Date' }}</label>
                            <input type="date" name="start_date" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                        </div>
                        <div>
                            <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'تاريخ الانتهاء' : 'End Date' }}</label>
                            <input type="date" name="end_date" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                        <div>
                            <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'المدة (ساعات/أيام)' : 'Duration' }}</label>
                            <input type="text" name="duration" placeholder="e.g. 24 Hours" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                        </div>
                        <div>
                            <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'التكلفة' : 'Cost' }}</label>
                            <input type="number" step="0.01" name="cost" placeholder="0.00" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                        </div>
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'وصف البرنامج والمحاور' : 'Description / Objectives' }}</label>
                        <textarea name="description" rows="2" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;"></textarea>
                    </div>
                    <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 1rem;">
                        <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('newProgramModal').style.display='none'">{{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}</button>
                        <button type="submit" class="btn btn-primary btn-sm">{{ app()->getLocale() === 'ar' ? 'إنشاء البرنامج' : 'Create Program' }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>

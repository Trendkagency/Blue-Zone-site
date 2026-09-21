<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'ورديات ومواعيد العمل' : 'Work Schedules'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'تهيئة فترات وساعات العمل وقواعد السماحية' : 'Working Shifts, Grace Periods, and Shift Rules'"
>
    <!-- Header Actions -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0; color: var(--text-color, #1E293B);">
                <i class="fa-solid fa-business-time text-amber-500 mr-2 ml-2"></i> {{ app()->getLocale() === 'ar' ? 'ورديات العمل' : 'Work Schedules' }}
            </h2>
        </div>
        <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('addSchedModal').style.display='flex'">
            <i class="fa-solid fa-plus text-xs"></i> {{ app()->getLocale() === 'ar' ? 'إضافة وردية عمل' : 'Add Work Schedule' }}
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="padding: 0.75rem 1rem; margin-bottom: 1rem; border-radius: 0.5rem; background: #DCFCE7; color: #166534; border: 1px solid #BBF7D0;">
            {{ session('success') }}
        </div>
    @endif

    <!-- Schedules Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.25rem;">
        @forelse($schedules as $sched)
            <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0); position: relative;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem;">
                    <div>
                        <h3 style="font-size: 1rem; font-weight: 700; margin: 0; color: #0284C7;">{{ $sched->name_en }}</h3>
                        <span style="font-size: 0.8rem; color: #64748B;">{{ $sched->name_ar }}</span>
                    </div>
                    <span class="badge" style="background: #E0F2FE; color: #0369A1; padding: 0.2rem 0.5rem; border-radius: 999px; font-weight: 700; font-size: 0.75rem;">
                        {{ $sched->employees_count }} Staff
                    </span>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-top: 1rem; font-size: 0.85rem;">
                    <div style="background: #F8FAFC; padding: 0.5rem; border-radius: 0.375rem;">
                        <span style="color: #64748B; font-size: 0.75rem; display: block;">Shift Hours</span>
                        <strong>{{ substr($sched->start_time, 0, 5) }} &ndash; {{ substr($sched->end_time, 0, 5) }}</strong>
                    </div>
                    <div style="background: #F8FAFC; padding: 0.5rem; border-radius: 0.375rem;">
                        <span style="color: #64748B; font-size: 0.75rem; display: block;">Working Hours</span>
                        <strong>{{ $sched->working_hours }} hrs / day</strong>
                    </div>
                    <div style="background: #F8FAFC; padding: 0.5rem; border-radius: 0.375rem;">
                        <span style="color: #64748B; font-size: 0.75rem; display: block;">Grace Period</span>
                        <strong>{{ $sched->grace_minutes }} mins</strong>
                    </div>
                    <div style="background: #F8FAFC; padding: 0.5rem; border-radius: 0.375rem;">
                        <span style="color: #64748B; font-size: 0.75rem; display: block;">Break Time</span>
                        <strong>{{ $sched->break_minutes }} mins</strong>
                    </div>
                </div>

                <div style="margin-top: 1rem; padding-top: 0.75rem; border-top: 1px solid #F1F5F9; display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 0.75rem; color: #10B981; font-weight: 600;">
                        <i class="fa-solid fa-circle-check"></i> Standard Shift
                    </span>
                    <form action="{{ route('admin.hr.organization.work-schedules.destroy', $sched->id) }}" method="POST" onsubmit="return confirm('Delete this schedule?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-ghost btn-xs text-red-500">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 3rem; background: #ffffff; border-radius: 0.75rem; border: 1px dashed #CBD5E1;">
                <i class="fa-solid fa-calendar-xmark text-3xl text-slate-300 mb-2"></i>
                <p style="color: #64748B; margin: 0;">No work schedules established yet.</p>
            </div>
        @endforelse
    </div>

    <!-- Add Schedule Modal -->
    <div id="addSchedModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div style="background: #ffffff; border-radius: 0.75rem; padding: 1.5rem; width: 100%; max-width: 500px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="font-size: 1.1rem; font-weight: 700; margin: 0;">Add Work Schedule</h3>
                <button type="button" onclick="document.getElementById('addSchedModal').style.display='none'" style="background: none; border: none; font-size: 1.25rem; cursor: pointer;">&times;</button>
            </div>
            <form action="{{ route('admin.hr.organization.work-schedules.store') }}" method="POST">
                @csrf
                <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Schedule Name (English) *</label>
                        <input type="text" name="name_en" required class="form-control" placeholder="e.g. Standard 9-5 Shift" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Schedule Name (Arabic) *</label>
                        <input type="text" name="name_ar" required class="form-control" placeholder="e.g. الدوام الصباحي المعتاد" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                        <div>
                            <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Start Time *</label>
                            <input type="time" name="start_time" value="09:00" required class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                        </div>
                        <div>
                            <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">End Time *</label>
                            <input type="time" name="end_time" value="17:00" required class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.5rem;">
                        <div>
                            <label style="font-size: 0.75rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Working Hours *</label>
                            <input type="number" step="0.5" name="working_hours" value="8" required class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                        </div>
                        <div>
                            <label style="font-size: 0.75rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Grace (mins)</label>
                            <input type="number" name="grace_minutes" value="15" required class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                        </div>
                        <div>
                            <label style="font-size: 0.75rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Break (mins)</label>
                            <input type="number" name="break_minutes" value="60" required class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                        </div>
                    </div>
                    <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 1rem;">
                        <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('addSchedModal').style.display='none'">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm">Save Schedule</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>

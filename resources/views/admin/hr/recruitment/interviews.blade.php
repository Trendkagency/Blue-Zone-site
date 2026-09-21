<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'المقابلات الشخصية' : 'Candidate Interviews'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'جدولة ومتابعة مقابلات التوظيف والتقييمات' : 'Schedule, conduct and evaluate candidate interviews'"
>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0; color: var(--text-color, #1E293B);">
                <i class="fa-solid fa-comments text-teal-500 mr-2 ml-2"></i> {{ app()->getLocale() === 'ar' ? 'المقابلات الشخصية' : 'Candidate Interviews' }}
            </h2>
        </div>
        <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('schedInterviewModal').style.display='flex'">
            <i class="fa-solid fa-calendar-plus text-xs"></i> Schedule Interview
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="padding: 0.75rem 1rem; margin-bottom: 1rem; border-radius: 0.5rem; background: #DCFCE7; color: #166534; border: 1px solid #BBF7D0;">
            {{ session('success') }}
        </div>
    @endif

    <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
        <div class="table-responsive">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                <thead>
                    <tr style="border-bottom: 2px solid #E2E8F0; text-align: left; color: #64748B;">
                        <th style="padding: 0.75rem 0.5rem;">Candidate</th>
                        <th style="padding: 0.75rem 0.5rem;">Type</th>
                        <th style="padding: 0.75rem 0.5rem;">Interviewer</th>
                        <th style="padding: 0.75rem 0.5rem;">Scheduled At</th>
                        <th style="padding: 0.75rem 0.5rem;">Location / URL</th>
                        <th style="padding: 0.75rem 0.5rem;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($interviews as $inv)
                        <tr style="border-bottom: 1px solid #F1F5F9;">
                            <td style="padding: 0.75rem 0.5rem; font-weight: 700; color: #0284C7;">
                                {{ $inv->candidate?->first_name }} {{ $inv->candidate?->last_name }}
                            </td>
                            <td style="padding: 0.75rem 0.5rem; text-transform: uppercase; font-weight: 600; font-size: 0.75rem;">{{ $inv->interview_type }}</td>
                            <td style="padding: 0.75rem 0.5rem;">{{ $inv->interviewer?->full_name ?? 'HR Team' }}</td>
                            <td style="padding: 0.75rem 0.5rem; font-weight: 600; color: #1E293B;">{{ $inv->scheduled_at }}</td>
                            <td style="padding: 0.75rem 0.5rem; color: #64748B;">{{ $inv->location ?? $inv->meeting_url ?? 'Headquarters' }}</td>
                            <td style="padding: 0.75rem 0.5rem;">
                                <span class="badge" style="background: #E0F2FE; color: #0369A1; padding: 0.2rem 0.5rem; border-radius: 999px; font-weight: 700;">
                                    {{ ucfirst($inv->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 3rem; color: #94A3B8;">No interviews scheduled.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top: 1rem;">
            {{ $interviews->links() }}
        </div>
    </div>

    <!-- Schedule Modal -->
    <div id="schedInterviewModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div style="background: #ffffff; border-radius: 0.75rem; padding: 1.5rem; width: 100%; max-width: 500px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="font-size: 1.1rem; font-weight: 700; margin: 0;">Schedule Interview</h3>
                <button type="button" onclick="document.getElementById('schedInterviewModal').style.display='none'" style="background: none; border: none; font-size: 1.25rem; cursor: pointer;">&times;</button>
            </div>
            <form action="{{ route('admin.hr.recruitment.interviews.store') }}" method="POST">
                @csrf
                <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Candidate *</label>
                        <select name="candidate_id" required class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                            @foreach($candidates as $cand)
                                <option value="{{ $cand->id }}">{{ $cand->first_name }} {{ $cand->last_name }} ({{ $cand->candidate_number }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Interview Type *</label>
                        <select name="interview_type" required class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                            <option value="HR">HR Screen</option>
                            <option value="technical">Technical Assessment</option>
                            <option value="management">Management Interview</option>
                            <option value="final">Final Executive Interview</option>
                        </select>
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Interviewer</label>
                        <select name="interviewer_employee_id" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                            <option value="">-- HR Committee --</option>
                            @foreach($interviewers as $itr)
                                <option value="{{ $itr->id }}">{{ $itr->full_name }} ({{ $itr->position?->name_en }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Scheduled Date & Time *</label>
                        <input type="datetime-local" name="scheduled_at" required class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Meeting URL / Room</label>
                        <input type="text" name="meeting_url" placeholder="Google Meet link or Room 2A" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                    </div>
                    <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 1rem;">
                        <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('schedInterviewModal').style.display='none'">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm">Schedule</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>

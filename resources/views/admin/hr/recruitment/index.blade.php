<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'التوظيف والاستقطاب' : 'Talent Acquisition & Recruitment'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'إدارة الشواغر، المتقدمين، المقابلات والعروض الوظيفية' : 'Manage job openings, candidate pipeline, interviews and offers'"
>
    <!-- 4 Stats Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
        <div class="card stat-card" style="padding: 1.25rem; border-radius: 0.75rem; border-left: 4px solid #0284C7;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                <span style="font-size: 0.8rem; font-weight: 600; color: #64748B;">Published Vacancies</span>
                <i class="fa-solid fa-bullhorn text-sky-500 text-lg"></i>
            </div>
            <div style="font-size: 1.7rem; font-weight: 800; color: #0284C7;">{{ $vacanciesCount }}</div>
            <a href="{{ route('admin.hr.recruitment.vacancies') }}" style="font-size: 0.75rem; color: #0284C7; text-decoration: none; font-weight: 600;">View vacancies &rarr;</a>
        </div>

        <div class="card stat-card" style="padding: 1.25rem; border-radius: 0.75rem; border-left: 4px solid #6366F1;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                <span style="font-size: 0.8rem; font-weight: 600; color: #64748B;">Candidates Pool</span>
                <i class="fa-solid fa-user-graduate text-indigo-500 text-lg"></i>
            </div>
            <div style="font-size: 1.7rem; font-weight: 800; color: #4F46E5;">{{ $candidatesCount }}</div>
            <a href="{{ route('admin.hr.recruitment.candidates') }}" style="font-size: 0.75rem; color: #4F46E5; text-decoration: none; font-weight: 600;">View candidates &rarr;</a>
        </div>

        <div class="card stat-card" style="padding: 1.25rem; border-radius: 0.75rem; border-left: 4px solid #14B8A6;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                <span style="font-size: 0.8rem; font-weight: 600; color: #64748B;">Scheduled Interviews</span>
                <i class="fa-solid fa-comments text-teal-500 text-lg"></i>
            </div>
            <div style="font-size: 1.7rem; font-weight: 800; color: #0D9488;">{{ $interviewsCount }}</div>
            <a href="{{ route('admin.hr.recruitment.interviews') }}" style="font-size: 0.75rem; color: #0D9488; text-decoration: none; font-weight: 600;">Schedule & manage &rarr;</a>
        </div>

        <div class="card stat-card" style="padding: 1.25rem; border-radius: 0.75rem; border-left: 4px solid #10B981;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                <span style="font-size: 0.8rem; font-weight: 600; color: #64748B;">Sent Job Offers</span>
                <i class="fa-solid fa-file-signature text-emerald-500 text-lg"></i>
            </div>
            <div style="font-size: 1.7rem; font-weight: 800; color: #059669;">{{ $offersCount }}</div>
            <a href="{{ route('admin.hr.recruitment.offers') }}" style="font-size: 0.75rem; color: #059669; text-decoration: none; font-weight: 600;">View offers &rarr;</a>
        </div>
    </div>

    <!-- Active Openings & Candidates Pipeline -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 1.25rem;">
        <!-- Active Openings -->
        <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="font-size: 0.95rem; font-weight: 700; margin: 0; color: var(--text-color, #1E293B);">
                    <i class="fa-solid fa-briefcase text-sky-500 mr-1 ml-1"></i> Active Job Openings
                </h3>
                <a href="{{ route('admin.hr.recruitment.vacancies') }}" class="btn btn-outline btn-xs">Manage All</a>
            </div>
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                @forelse($activeVacancies as $vac)
                    <div style="padding: 0.75rem; background: #F8FAFC; border-radius: 0.5rem; border: 1px solid #E2E8F0; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <div style="font-weight: 700; font-size: 0.9rem; color: #1E293B;">{{ $vac->title_en }}</div>
                            <div style="font-size: 0.75rem; color: #64748B;">
                                {{ $vac->department?->name_en }} &bull; {{ $vac->openings }} openings
                            </div>
                        </div>
                        <span class="badge" style="background: #E0F2FE; color: #0369A1; padding: 0.2rem 0.5rem; border-radius: 999px; font-weight: 700; font-size: 0.75rem;">
                            {{ $vac->candidates_count }} Applicants
                        </span>
                    </div>
                @empty
                    <p style="color: #94A3B8; text-align: center; margin: 1.5rem 0;">No active vacancies posted.</p>
                @endforelse
            </div>
        </div>

        <!-- Recent Candidates -->
        <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="font-size: 0.95rem; font-weight: 700; margin: 0; color: var(--text-color, #1E293B);">
                    <i class="fa-solid fa-users-line text-indigo-500 mr-1 ml-1"></i> Recent Applications
                </h3>
                <a href="{{ route('admin.hr.recruitment.candidates') }}" class="btn btn-outline btn-xs">All Candidates</a>
            </div>
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                @forelse($recentCandidates as $cand)
                    <div style="padding: 0.75rem; background: #F8FAFC; border-radius: 0.5rem; border: 1px solid #E2E8F0; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <div style="font-weight: 700; font-size: 0.9rem; color: #1E293B;">{{ $cand->first_name }} {{ $cand->last_name }}</div>
                            <div style="font-size: 0.75rem; color: #64748B;">{{ $cand->vacancy?->title_en ?? 'General' }} &bull; {{ $cand->phone }}</div>
                        </div>
                        <span class="badge" style="background: #E0E7FF; color: #3730A3; padding: 0.2rem 0.5rem; border-radius: 999px; font-size: 0.7rem; font-weight: 700;">
                            {{ ucfirst(str_replace('_', ' ', $cand->status)) }}
                        </span>
                    </div>
                @empty
                    <p style="color: #94A3B8; text-align: center; margin: 1.5rem 0;">No candidate applications received.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.admin>

<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'الشواغر الوظيفية' : 'Job Vacancies'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'إدارة ونشر الشواغر والفرص الوظيفية المتاحة' : 'Manage active job openings, requirements and applications'"
>
    <!-- Header Actions -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0; color: var(--text-color, #1E293B);">
                <i class="fa-solid fa-bullhorn text-rose-500 mr-2 ml-2"></i> {{ app()->getLocale() === 'ar' ? 'الشواغر الوظيفية' : 'Job Openings' }}
            </h2>
        </div>
        <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('addVacModal').style.display='flex'">
            <i class="fa-solid fa-plus text-xs"></i> Post New Vacancy
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
                        <th style="padding: 0.75rem 0.5rem;">Job Title</th>
                        <th style="padding: 0.75rem 0.5rem;">Department</th>
                        <th style="padding: 0.75rem 0.5rem;">Location</th>
                        <th style="padding: 0.75rem 0.5rem;">Openings</th>
                        <th style="padding: 0.75rem 0.5rem;">Applicants</th>
                        <th style="padding: 0.75rem 0.5rem;">Status</th>
                        <th style="padding: 0.75rem 0.5rem;">Closing Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vacancies as $vac)
                        <tr style="border-bottom: 1px solid #F1F5F9;">
                            <td style="padding: 0.75rem 0.5rem; font-weight: 700; color: #0284C7;">
                                {{ $vac->title_en }}
                                @if($vac->title_ar)
                                    <span style="font-size: 0.75rem; color: #64748B; font-weight: normal; display: block;">{{ $vac->title_ar }}</span>
                                @endif
                            </td>
                            <td style="padding: 0.75rem 0.5rem;">{{ $vac->department?->name_en ?? '—' }}</td>
                            <td style="padding: 0.75rem 0.5rem; color: #64748B;">{{ $vac->location?->name ?? 'All Locations' }}</td>
                            <td style="padding: 0.75rem 0.5rem; font-weight: 600;">{{ $vac->openings }}</td>
                            <td style="padding: 0.75rem 0.5rem;">
                                <a href="{{ route('admin.hr.recruitment.candidates', ['job_vacancy_id' => $vac->id]) }}" class="badge" style="background: #E0F2FE; color: #0369A1; padding: 0.2rem 0.5rem; border-radius: 999px; font-weight: 700; text-decoration: none;">
                                    {{ $vac->candidates_count }} Candidates
                                </a>
                            </td>
                            <td style="padding: 0.75rem 0.5rem;">
                                <span class="badge" style="background: #DCFCE7; color: #166534; padding: 0.2rem 0.5rem; border-radius: 999px; text-transform: uppercase; font-size: 0.7rem; font-weight: 700;">
                                    {{ $vac->status }}
                                </span>
                            </td>
                            <td style="padding: 0.75rem 0.5rem; color: #64748B;">{{ $vac->closing_at ?? 'Open Ended' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 3rem; color: #94A3B8;">No job vacancies posted yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top: 1rem;">
            {{ $vacancies->links() }}
        </div>
    </div>

    <!-- Add Vacancy Modal -->
    <div id="addVacModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div style="background: #ffffff; border-radius: 0.75rem; padding: 1.5rem; width: 100%; max-width: 550px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="font-size: 1.1rem; font-weight: 700; margin: 0;">Create Job Vacancy</h3>
                <button type="button" onclick="document.getElementById('addVacModal').style.display='none'" style="background: none; border: none; font-size: 1.25rem; cursor: pointer;">&times;</button>
            </div>
            <form action="{{ route('admin.hr.recruitment.vacancies.store') }}" method="POST">
                @csrf
                <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Department *</label>
                        <select name="department_id" required class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name_en }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Target Position *</label>
                        <select name="position_id" required class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                            @foreach($positions as $pos)
                                <option value="{{ $pos->id }}">{{ $pos->name_en }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Job Title (English) *</label>
                        <input type="text" name="title_en" required class="form-control" placeholder="e.g. Senior Medical Representative" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Job Title (Arabic)</label>
                        <input type="text" name="title_ar" class="form-control" placeholder="e.g. مندوب دعاية طبية أول" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                        <div>
                            <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Openings *</label>
                            <input type="number" name="openings" value="1" min="1" required class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                        </div>
                        <div>
                            <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Employment Type</label>
                            <select name="employment_type" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                                <option value="full_time">Full Time</option>
                                <option value="part_time">Part Time</option>
                                <option value="contract">Contract</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Job Description *</label>
                        <textarea name="description" rows="3" required class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;"></textarea>
                    </div>
                    <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 1rem;">
                        <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('addVacModal').style.display='none'">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm">Publish Vacancy</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>

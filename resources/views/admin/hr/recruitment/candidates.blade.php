<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'المتقدمون للوظائف' : 'Candidates Pipeline'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'متابعة المتقدمين وسير التوظيف وتحويل المقبولين إلى موظفين' : 'Applicant tracking, stages and converting hired candidates to employees'"
>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0; color: var(--text-color, #1E293B);">
                <i class="fa-solid fa-user-graduate text-indigo-500 mr-2 ml-2"></i> {{ app()->getLocale() === 'ar' ? 'المتقدمون للوظائف' : 'Candidates Pool' }}
            </h2>
        </div>
        <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('addCandModal').style.display='flex'">
            <i class="fa-solid fa-plus text-xs"></i> Register Candidate
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="padding: 0.75rem 1rem; margin-bottom: 1rem; border-radius: 0.5rem; background: #DCFCE7; color: #166534; border: 1px solid #BBF7D0;">
            {{ session('success') }}
        </div>
    @endif

    <!-- Candidates Table -->
    <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
        <div class="table-responsive">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                <thead>
                    <tr style="border-bottom: 2px solid #E2E8F0; text-align: left; color: #64748B;">
                        <th style="padding: 0.75rem 0.5rem;">Candidate</th>
                        <th style="padding: 0.75rem 0.5rem;">Applied Position</th>
                        <th style="padding: 0.75rem 0.5rem;">Contact</th>
                        <th style="padding: 0.75rem 0.5rem;">Experience</th>
                        <th style="padding: 0.75rem 0.5rem;">Stage</th>
                        <th style="padding: 0.75rem 0.5rem; text-align: right;">Hire / Convert</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($candidates as $cand)
                        <tr style="border-bottom: 1px solid #F1F5F9;">
                            <td style="padding: 0.75rem 0.5rem;">
                                <div style="font-weight: 700; color: #1E293B;">{{ $cand->first_name }} {{ $cand->last_name }}</div>
                                <span style="font-size: 0.75rem; color: #64748B; font-family: monospace;">{{ $cand->candidate_number }}</span>
                            </td>
                            <td style="padding: 0.75rem 0.5rem;">{{ $cand->vacancy?->title_en ?? 'General Pool' }}</td>
                            <td style="padding: 0.75rem 0.5rem;">
                                <div>{{ $cand->email }}</div>
                                <div style="color: #64748B;">{{ $cand->phone }}</div>
                            </td>
                            <td style="padding: 0.75rem 0.5rem; color: #475569;">{{ $cand->experience_years ? $cand->experience_years . ' yrs' : 'Fresh' }}</td>
                            <td style="padding: 0.75rem 0.5rem;">
                                <span class="badge" style="background: #E0E7FF; color: #3730A3; padding: 0.2rem 0.5rem; border-radius: 999px; font-weight: 700; font-size: 0.7rem; text-transform: uppercase;">
                                    {{ str_replace('_', ' ', $cand->status) }}
                                </span>
                            </td>
                            <td style="padding: 0.75rem 0.5rem; text-align: right;">
                                @if($cand->status === 'hired' && $cand->convertedEmployee)
                                    <a href="{{ route('admin.hr.employees.show', $cand->convertedEmployee->id) }}" class="btn btn-ghost btn-xs text-emerald-600 font-bold">
                                        <i class="fa-solid fa-id-card mr-1"></i> Employee Profile
                                    </a>
                                @else
                                    <button type="button" onclick="openConvertModal({{ $cand->id }}, '{{ addslashes($cand->first_name . ' ' . $cand->last_name) }}')" class="btn btn-primary btn-xs">
                                        <i class="fa-solid fa-user-check mr-1"></i> Hire & Convert
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 3rem; color: #94A3B8;">No candidates in pipeline.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top: 1rem;">
            {{ $candidates->links() }}
        </div>
    </div>

    <!-- Convert to Employee Modal -->
    <div id="convertModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div style="background: #ffffff; border-radius: 0.75rem; padding: 1.5rem; width: 100%; max-width: 450px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="font-size: 1.1rem; font-weight: 700; margin: 0;">Hire & Convert Candidate</h3>
                <button type="button" onclick="document.getElementById('convertModal').style.display='none'" style="background: none; border: none; font-size: 1.25rem; cursor: pointer;">&times;</button>
            </div>
            <p id="convertCandidateName" style="font-weight: 600; color: #0284C7; margin-top: 0; font-size: 0.9rem;"></p>
            <form id="convertForm" method="POST">
                @csrf
                <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Basic Salary *</label>
                        <input type="number" step="0.01" name="basic_salary" value="5000" required class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Hire Date *</label>
                        <input type="date" name="hire_date" value="{{ now()->toDateString() }}" required class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                    </div>
                    <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 1rem;">
                        <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('convertModal').style.display='none'">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm">Confirm & Hire</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Candidate Modal -->
    <div id="addCandModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div style="background: #ffffff; border-radius: 0.75rem; padding: 1.5rem; width: 100%; max-width: 500px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="font-size: 1.1rem; font-weight: 700; margin: 0;">Register New Candidate</h3>
                <button type="button" onclick="document.getElementById('addCandModal').style.display='none'" style="background: none; border: none; font-size: 1.25rem; cursor: pointer;">&times;</button>
            </div>
            <form action="{{ route('admin.hr.recruitment.candidates.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                        <div>
                            <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">First Name *</label>
                            <input type="text" name="first_name" required class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                        </div>
                        <div>
                            <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Last Name *</label>
                            <input type="text" name="last_name" required class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                        </div>
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Email Address *</label>
                        <input type="email" name="email" required class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Phone Number *</label>
                        <input type="text" name="phone" required class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Target Vacancy</label>
                        <select name="job_vacancy_id" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                            <option value="">-- General Application --</option>
                            @foreach($vacancies as $vac)
                                <option value="{{ $vac->id }}">{{ $vac->title_en }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Upload CV (PDF/DOC)</label>
                        <input type="file" name="cv_file" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                    </div>
                    <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 1rem;">
                        <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('addCandModal').style.display='none'">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm">Register Candidate</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openConvertModal(candidateId, candidateName) {
            document.getElementById('convertCandidateName').innerText = 'Hiring: ' + candidateName;
            document.getElementById('convertForm').action = '/admin/hr/recruitment/candidates/' + candidateId + '/convert';
            document.getElementById('convertModal').style.display = 'flex';
        }
    </script>
</x-layouts.admin>

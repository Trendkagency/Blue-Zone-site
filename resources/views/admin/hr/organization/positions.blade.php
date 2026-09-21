<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'الوظائف والمهن' : 'Positions Management'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'إدارة المسميات الوظيفية والدرجات' : 'Manage Job Titles, Levels and Classifications'"
>
    <!-- Header Actions -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0; color: var(--text-color, #1E293B);">
                <i class="fa-solid fa-id-card-clip text-sky-500 mr-2 ml-2"></i> {{ app()->getLocale() === 'ar' ? 'الوظائف والمهن' : 'Job Positions' }}
            </h2>
        </div>
        <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('addPosModal').style.display='flex'">
            <i class="fa-solid fa-plus text-xs"></i> {{ app()->getLocale() === 'ar' ? 'إضافة وظيفة جديدة' : 'Add Position' }}
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="padding: 0.75rem 1rem; margin-bottom: 1rem; border-radius: 0.5rem; background: #DCFCE7; color: #166534; border: 1px solid #BBF7D0;">
            {{ session('success') }}
        </div>
    @endif

    <!-- Positions Table Card -->
    <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
        <div class="table-responsive">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                <thead>
                    <tr style="border-bottom: 2px solid #E2E8F0; text-align: left; color: #64748B;">
                        <th style="padding: 0.75rem 0.5rem;">Code</th>
                        <th style="padding: 0.75rem 0.5rem;">Title (EN)</th>
                        <th style="padding: 0.75rem 0.5rem;">Title (AR)</th>
                        <th style="padding: 0.75rem 0.5rem;">Department</th>
                        <th style="padding: 0.75rem 0.5rem;">Level</th>
                        <th style="padding: 0.75rem 0.5rem;">Staff Count</th>
                        <th style="padding: 0.75rem 0.5rem; text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($positions as $pos)
                        <tr style="border-bottom: 1px solid #F1F5F9;">
                            <td style="padding: 0.75rem 0.5rem; font-weight: 700; color: #0284C7;">{{ $pos->code }}</td>
                            <td style="padding: 0.75rem 0.5rem; font-weight: 600;">{{ $pos->name_en }}</td>
                            <td style="padding: 0.75rem 0.5rem;">{{ $pos->name_ar }}</td>
                            <td style="padding: 0.75rem 0.5rem; color: #475569;">{{ $pos->department?->name_en ?? '—' }}</td>
                            <td style="padding: 0.75rem 0.5rem;">
                                <span class="badge" style="background: #F1F5F9; color: #334155; padding: 0.2rem 0.5rem; border-radius: 0.25rem; text-transform: capitalize;">
                                    {{ $pos->level }}
                                </span>
                            </td>
                            <td style="padding: 0.75rem 0.5rem;">
                                <span class="badge" style="background: #E0F2FE; color: #0369A1; padding: 0.2rem 0.5rem; border-radius: 999px; font-weight: 700;">
                                    {{ $pos->employees_count }}
                                </span>
                            </td>
                            <td style="padding: 0.75rem 0.5rem; text-align: right;">
                                <form action="{{ route('admin.hr.organization.positions.destroy', $pos->id) }}" method="POST" onsubmit="return confirm('Delete position?');" style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-ghost btn-xs text-red-500">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 2rem; color: #94A3B8;">No positions recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top: 1rem;">
            {{ $positions->links() }}
        </div>
    </div>

    <!-- Add Position Modal -->
    <div id="addPosModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div style="background: #ffffff; border-radius: 0.75rem; padding: 1.5rem; width: 100%; max-width: 500px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="font-size: 1.1rem; font-weight: 700; margin: 0;">Add Position</h3>
                <button type="button" onclick="document.getElementById('addPosModal').style.display='none'" style="background: none; border: none; font-size: 1.25rem; cursor: pointer;">&times;</button>
            </div>
            <form action="{{ route('admin.hr.organization.positions.store') }}" method="POST">
                @csrf
                <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Department *</label>
                        <select name="department_id" required class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                            <option value="">-- Choose Department --</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name_en }} ({{ $dept->code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Position Code *</label>
                        <input type="text" name="code" required class="form-control" placeholder="e.g. HR-SPEC, MR-REP" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Title (English) *</label>
                        <input type="text" name="name_en" required class="form-control" placeholder="e.g. Medical Representative" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Title (Arabic) *</label>
                        <input type="text" name="name_ar" required class="form-control" placeholder="e.g. مندوب دعاية طبية" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Hierarchy Level *</label>
                        <select name="level" required class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                            <option value="entry">Entry Level</option>
                            <option value="mid" selected>Mid Level</option>
                            <option value="senior">Senior</option>
                            <option value="lead">Lead / Supervisor</option>
                            <option value="manager">Manager</option>
                            <option value="director">Director / Executive</option>
                        </select>
                    </div>
                    <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 1rem;">
                        <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('addPosModal').style.display='none'">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm">Save Position</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>

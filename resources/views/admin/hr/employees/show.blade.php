<x-layouts.admin 
    :pageTitle="$employee->full_name . ' (' . $employee->employee_number . ')'" 
    :pageSubtitle="'Comprehensive 360° Employee Dossier & Workforce Profile'"
>
    <!-- Employee Header Profile Card -->
    <div class="card" style="padding: 1.5rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0); margin-bottom: 1.5rem;">
        <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1.25rem;">
            <div style="display: flex; align-items: center; gap: 1.25rem;">
                <div style="width: 64px; height: 64px; border-radius: 50%; background: linear-gradient(135deg, #0284C7, #0369A1); color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: 800; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); flex-shrink: 0;">
                    {{ strtoupper(substr($employee->first_name, 0, 1) . substr($employee->last_name, 0, 1)) }}
                </div>
                <div>
                    <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                        <h1 style="font-size: 1.4rem; font-weight: 800; margin: 0; color: var(--text-color, #1E293B);">
                            {{ $employee->full_name }}
                        </h1>
                        @if($employee->full_name_ar)
                            <span style="font-size: 1.1rem; color: #64748B; font-weight: 600;">({{ $employee->full_name_ar }})</span>
                        @endif
                        @php
                            $statusBg = '#DCFCE7'; $statusColor = '#166534';
                            if ($employee->employment_status === 'probation') { $statusBg = '#FEF3C7'; $statusColor = '#92400E'; }
                            elseif ($employee->employment_status === 'on_leave') { $statusBg = '#E0F2FE'; $statusColor = '#0369A1'; }
                            elseif (in_array($employee->employment_status, ['resigned', 'terminated'])) { $statusBg = '#FEE2E2'; $statusColor = '#991B1B'; }
                        @endphp
                        <span class="badge" style="background: {{ $statusBg }}; color: {{ $statusColor }}; padding: 0.25rem 0.65rem; border-radius: 999px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">
                            {{ str_replace('_', ' ', $employee->employment_status) }}
                        </span>
                    </div>
                    <div style="display: flex; flex-wrap: wrap; gap: 1rem; margin-top: 0.4rem; font-size: 0.85rem; color: #64748B;">
                        <span><i class="fa-solid fa-id-badge text-sky-500 mr-1 ml-1"></i> <strong style="color: #0284C7;">{{ $employee->employee_number }}</strong></span>
                        <span><i class="fa-solid fa-briefcase text-slate-400 mr-1 ml-1"></i> {{ $employee->position?->name_en ?? 'No Position' }}</span>
                        <span><i class="fa-solid fa-sitemap text-slate-400 mr-1 ml-1"></i> {{ $employee->department?->name_en ?? 'No Dept' }}</span>
                        <span><i class="fa-solid fa-location-dot text-slate-400 mr-1 ml-1"></i> {{ $employee->location?->name ?? 'Main Branch' }}</span>
                    </div>
                </div>
            </div>

            <div style="display: flex; gap: 0.5rem;">
                <a href="{{ route('admin.hr.employees.edit', $employee->id) }}" class="btn btn-secondary btn-sm" style="display: inline-flex; align-items: center; gap: 0.4rem;">
                    <i class="fa-solid fa-pen-to-square text-xs"></i> Edit Profile
                </a>
                <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('uploadDocModal').style.display='flex'" style="display: inline-flex; align-items: center; gap: 0.4rem;">
                    <i class="fa-solid fa-cloud-arrow-up text-xs"></i> Upload Document
                </button>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="padding: 0.75rem 1rem; margin-bottom: 1.5rem; border-radius: 0.5rem; background: #DCFCE7; color: #166534; border: 1px solid #BBF7D0;">
            {{ session('success') }}
        </div>
    @endif

    <!-- 13 Nav Tabs -->
    <div style="display: flex; gap: 0.5rem; overflow-x: auto; border-bottom: 2px solid #E2E8F0; margin-bottom: 1.5rem; padding-bottom: 0.25rem;">
        @php
            $tabs = [
                'overview' => ['icon' => 'fa-id-card', 'label' => 'Overview'],
                'employment' => ['icon' => 'fa-briefcase', 'label' => 'Employment'],
                'attendance' => ['icon' => 'fa-clock', 'label' => 'Attendance'],
                'leave' => ['icon' => 'fa-plane-departure', 'label' => 'Leave'],
                'payroll' => ['icon' => 'fa-money-bill-wave', 'label' => 'Payroll'],
                'documents' => ['icon' => 'fa-folder-open', 'label' => 'Documents (' . $employee->documents->count() . ')'],
                'contracts' => ['icon' => 'fa-file-signature', 'label' => 'Contracts (' . $employee->contracts->count() . ')'],
                'performance' => ['icon' => 'fa-star-half-stroke', 'label' => 'Performance'],
                'training' => ['icon' => 'fa-graduation-cap', 'label' => 'Training'],
                'assets' => ['icon' => 'fa-laptop', 'label' => 'Assets (' . $employee->assetAssignments->count() . ')'],
                'requests' => ['icon' => 'fa-inbox', 'label' => 'Requests (' . $employee->requests->count() . ')'],
                'disciplinary' => ['icon' => 'fa-gavel', 'label' => 'Disciplinary'],
                'timeline' => ['icon' => 'fa-timeline', 'label' => 'Timeline (' . count($timeline) . ')'],
            ];
        @endphp

        @foreach($tabs as $tabKey => $tabData)
            <button type="button" 
                onclick="switchProfileTab('{{ $tabKey }}')" 
                id="tab-btn-{{ $tabKey }}"
                class="profile-tab-btn {{ $loop->first ? 'active' : '' }}"
                style="padding: 0.6rem 1rem; border: none; background: transparent; font-size: 0.85rem; font-weight: 600; cursor: pointer; color: {{ $loop->first ? '#0284C7' : '#64748B' }}; border-bottom: 3px solid {{ $loop->first ? '#0284C7' : 'transparent' }}; white-space: nowrap; display: inline-flex; align-items: center; gap: 0.4rem;">
                <i class="fa-solid {{ $tabData['icon'] }} text-xs"></i> {{ $tabData['label'] }}
            </button>
        @endforeach
    </div>

    <!-- TAB 1: OVERVIEW -->
    <div id="tab-content-overview" class="profile-tab-content">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.25rem;">
            <!-- Personal Information -->
            <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
                <h3 style="font-size: 0.95rem; font-weight: 700; margin-top: 0; margin-bottom: 1rem; color: #0284C7;">
                    <i class="fa-solid fa-user text-sky-500 mr-1 ml-1"></i> Personal Profile
                </h3>
                <div style="display: flex; flex-direction: column; gap: 0.6rem; font-size: 0.85rem;">
                    <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #F1F5F9; padding-bottom: 0.4rem;">
                        <span style="color: #64748B;">National ID / Iqama:</span>
                        <strong>{{ $employee->national_id ?? '—' }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #F1F5F9; padding-bottom: 0.4rem;">
                        <span style="color: #64748B;">Gender:</span>
                        <strong style="text-transform: capitalize;">{{ $employee->gender ?? '—' }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #F1F5F9; padding-bottom: 0.4rem;">
                        <span style="color: #64748B;">Date of Birth:</span>
                        <strong>{{ $employee->date_of_birth ? \Carbon\Carbon::parse($employee->date_of_birth)->format('M d, Y') : '—' }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #F1F5F9; padding-bottom: 0.4rem;">
                        <span style="color: #64748B;">Nationality:</span>
                        <strong>{{ $employee->nationality ?? '—' }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #64748B;">Marital Status:</span>
                        <strong style="text-transform: capitalize;">{{ $employee->marital_status ?? '—' }}</strong>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
                <h3 style="font-size: 0.95rem; font-weight: 700; margin-top: 0; margin-bottom: 1rem; color: #0284C7;">
                    <i class="fa-solid fa-address-book text-sky-500 mr-1 ml-1"></i> Contact & Address
                </h3>
                <div style="display: flex; flex-direction: column; gap: 0.6rem; font-size: 0.85rem;">
                    <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #F1F5F9; padding-bottom: 0.4rem;">
                        <span style="color: #64748B;">Work Email:</span>
                        <a href="mailto:{{ $employee->email }}" style="color: #0284C7; text-decoration: none; font-weight: 600;">{{ $employee->email ?? '—' }}</a>
                    </div>
                    <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #F1F5F9; padding-bottom: 0.4rem;">
                        <span style="color: #64748B;">Phone:</span>
                        <strong>{{ $employee->phone ?? '—' }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #F1F5F9; padding-bottom: 0.4rem;">
                        <span style="color: #64748B;">Alternate Phone:</span>
                        <strong>{{ $employee->alternate_phone ?? '—' }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #64748B;">Address:</span>
                        <span style="text-align: right; max-width: 60%;">{{ $employee->address ?? '—' }}</span>
                    </div>
                </div>
            </div>

            <!-- Management & Hierarchy -->
            <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
                <h3 style="font-size: 0.95rem; font-weight: 700; margin-top: 0; margin-bottom: 1rem; color: #0284C7;">
                    <i class="fa-solid fa-sitemap text-sky-500 mr-1 ml-1"></i> Hierarchy & Access
                </h3>
                <div style="display: flex; flex-direction: column; gap: 0.6rem; font-size: 0.85rem;">
                    <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #F1F5F9; padding-bottom: 0.4rem;">
                        <span style="color: #64748B;">Direct Manager:</span>
                        <strong>{{ $employee->manager ? $employee->manager->full_name : 'No Direct Manager' }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #F1F5F9; padding-bottom: 0.4rem;">
                        <span style="color: #64748B;">Work Shift:</span>
                        <strong>{{ $employee->workSchedule?->name_en ?? 'Standard' }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #64748B;">Linked User Account:</span>
                        @if($employee->user)
                            <span class="badge" style="background: #DCFCE7; color: #166534; padding: 0.2rem 0.5rem; border-radius: 0.25rem; font-weight: 700;">
                                <i class="fa-solid fa-check-circle"></i> {{ $employee->user->email }}
                            </span>
                        @else
                            <span style="color: #94A3B8;">No login access</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 2: EMPLOYMENT -->
    <div id="tab-content-employment" class="profile-tab-content" style="display: none;">
        <div class="card" style="padding: 1.5rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
            <h3 style="font-size: 1rem; font-weight: 700; color: #0284C7; margin-top: 0; margin-bottom: 1rem;">
                Employment Terms & Dates
            </h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; font-size: 0.85rem;">
                <div>
                    <span style="color: #64748B; display: block; font-size: 0.75rem;">Hire Date</span>
                    <strong style="font-size: 1rem;">{{ $employee->hire_date ? \Carbon\Carbon::parse($employee->hire_date)->format('M d, Y') : '—' }}</strong>
                </div>
                <div>
                    <span style="color: #64748B; display: block; font-size: 0.75rem;">Employment Type</span>
                    <strong style="text-transform: capitalize; font-size: 1rem;">{{ str_replace('_', ' ', $employee->employment_type) }}</strong>
                </div>
                <div>
                    <span style="color: #64748B; display: block; font-size: 0.75rem;">Tenure</span>
                    <strong style="font-size: 1rem;">{{ $employee->hire_date ? \Carbon\Carbon::parse($employee->hire_date)->diffForHumans(null, true) : '—' }}</strong>
                </div>
                <div>
                    <span style="color: #64748B; display: block; font-size: 0.75rem;">Contract Expiry</span>
                    <strong style="font-size: 1rem;">{{ $employee->contract_end_date ? \Carbon\Carbon::parse($employee->contract_end_date)->format('M d, Y') : 'Indefinite' }}</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 3: ATTENDANCE -->
    <div id="tab-content-attendance" class="profile-tab-content" style="display: none;">
        <div class="card" style="padding: 1.5rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
            <h3 style="font-size: 1rem; font-weight: 700; color: #0284C7; margin-top: 0; margin-bottom: 1rem;">
                Recent Attendance History
            </h3>
            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                <thead>
                    <tr style="border-bottom: 2px solid #E2E8F0; text-align: left; color: #64748B;">
                        <th style="padding: 0.5rem;">Date</th>
                        <th style="padding: 0.5rem;">Check In</th>
                        <th style="padding: 0.5rem;">Check Out</th>
                        <th style="padding: 0.5rem;">Late (mins)</th>
                        <th style="padding: 0.5rem;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employee->attendanceRecords as $att)
                        <tr style="border-bottom: 1px solid #F1F5F9;">
                            <td style="padding: 0.5rem; font-weight: 600;">{{ $att->attendance_date }}</td>
                            <td style="padding: 0.5rem;">{{ $att->check_in ?? '—' }}</td>
                            <td style="padding: 0.5rem;">{{ $att->check_out ?? '—' }}</td>
                            <td style="padding: 0.5rem; color: {{ $att->late_minutes > 0 ? '#EF4444' : '#64748B' }};">
                                {{ $att->late_minutes }}
                            </td>
                            <td style="padding: 0.5rem;">
                                <span class="badge" style="background: {{ $att->status === 'present' ? '#DCFCE7' : ($att->status === 'late' ? '#FEF3C7' : '#FEE2E2') }}; padding: 0.2rem 0.5rem; border-radius: 999px; font-weight: 700;">
                                    {{ ucfirst($att->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" style="text-align: center; padding: 2rem; color: #94A3B8;">No attendance logged.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- TAB 4: LEAVE -->
    <div id="tab-content-leave" class="profile-tab-content" style="display: none;">
        <div class="card" style="padding: 1.5rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
            <h3 style="font-size: 1rem; font-weight: 700; color: #0284C7; margin-top: 0; margin-bottom: 1rem;">
                Leave Balances & History
            </h3>
            <div style="display: flex; gap: 1rem; flex-wrap: wrap; margin-bottom: 1.5rem;">
                @forelse($employee->leaveBalances as $lb)
                    <div style="background: #F8FAFC; padding: 1rem; border-radius: 0.5rem; min-width: 160px; border: 1px solid #E2E8F0;">
                        <span style="font-size: 0.75rem; color: #64748B; font-weight: 600;">{{ $lb->leaveType?->name ?? 'Leave' }}</span>
                        <div style="font-size: 1.4rem; font-weight: 800; color: #0284C7;">{{ $lb->remaining_days }} <span style="font-size: 0.8rem; font-weight: 600; color: #64748B;">days left</span></div>
                        <span style="font-size: 0.7rem; color: #94A3B8;">Allocated: {{ $lb->allocated_days }} | Used: {{ $lb->used_days }}</span>
                    </div>
                @empty
                    <p style="color: #94A3B8; font-size: 0.85rem;">No leave balances initialized for current year.</p>
                @endforelse
            </div>
            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                <thead>
                    <tr style="border-bottom: 2px solid #E2E8F0; text-align: left; color: #64748B;">
                        <th style="padding: 0.5rem;">Leave Type</th>
                        <th style="padding: 0.5rem;">Start Date</th>
                        <th style="padding: 0.5rem;">End Date</th>
                        <th style="padding: 0.5rem;">Days</th>
                        <th style="padding: 0.5rem;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employee->leaveRequests as $lr)
                        <tr style="border-bottom: 1px solid #F1F5F9;">
                            <td style="padding: 0.5rem; font-weight: 600;">{{ $lr->leaveType?->name ?? 'Leave' }}</td>
                            <td style="padding: 0.5rem;">{{ $lr->start_date }}</td>
                            <td style="padding: 0.5rem;">{{ $lr->end_date }}</td>
                            <td style="padding: 0.5rem;">{{ $lr->total_days }}</td>
                            <td style="padding: 0.5rem;">
                                <span class="badge" style="background: {{ $lr->status === 'approved' ? '#DCFCE7' : ($lr->status === 'pending' ? '#FEF3C7' : '#FEE2E2') }}; padding: 0.2rem 0.5rem; border-radius: 999px;">
                                    {{ ucfirst($lr->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" style="text-align: center; padding: 2rem; color: #94A3B8;">No leave requests found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- TAB 5: PAYROLL -->
    <div id="tab-content-payroll" class="profile-tab-content" style="display: none;">
        <div class="card" style="padding: 1.5rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="font-size: 1rem; font-weight: 700; color: #0284C7; margin: 0;">
                    Salary Breakdown & Historical Payroll
                </h3>
            </div>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
                <div style="background: #F8FAFC; padding: 1rem; border-radius: 0.5rem; border: 1px solid #E2E8F0;">
                    <span style="font-size: 0.75rem; color: #64748B;">Basic Salary</span>
                    <div style="font-size: 1.3rem; font-weight: 800; color: #059669;">{{ number_format($employee->basic_salary, 2) }} {{ currency_code() }}</div>
                </div>
                <div style="background: #F8FAFC; padding: 1rem; border-radius: 0.5rem; border: 1px solid #E2E8F0;">
                    <span style="font-size: 0.75rem; color: #64748B;">Housing Allowance</span>
                    <div style="font-size: 1.3rem; font-weight: 800; color: #0284C7;">{{ number_format($employee->housing_allowance, 2) }} {{ currency_code() }}</div>
                </div>
                <div style="background: #F8FAFC; padding: 1rem; border-radius: 0.5rem; border: 1px solid #E2E8F0;">
                    <span style="font-size: 0.75rem; color: #64748B;">Transport Allowance</span>
                    <div style="font-size: 1.3rem; font-weight: 800; color: #0284C7;">{{ number_format($employee->transportation_allowance, 2) }} {{ currency_code() }}</div>
                </div>
                <div style="background: #F8FAFC; padding: 1rem; border-radius: 0.5rem; border: 1px solid #E2E8F0;">
                    <span style="font-size: 0.75rem; color: #64748B;">Total Gross Monthly</span>
                    <div style="font-size: 1.3rem; font-weight: 800; color: #7C3AED;">{{ number_format($employee->gross_salary, 2) }} {{ currency_code() }}</div>
                </div>
            </div>
            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                <thead>
                    <tr style="border-bottom: 2px solid #E2E8F0; text-align: left; color: #64748B;">
                        <th style="padding: 0.5rem;">Payroll Period</th>
                        <th style="padding: 0.5rem;">Gross</th>
                        <th style="padding: 0.5rem;">Deductions</th>
                        <th style="padding: 0.5rem;">Net Salary</th>
                        <th style="padding: 0.5rem;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employee->payrollRecords as $pr)
                        <tr style="border-bottom: 1px solid #F1F5F9;">
                            <td style="padding: 0.5rem; font-weight: 600;">{{ $pr->period?->name ?? 'Period' }}</td>
                            <td style="padding: 0.5rem;">{{ number_format($pr->gross_salary, 2) }}</td>
                            <td style="padding: 0.5rem; color: #EF4444;">{{ number_format($pr->total_deductions, 2) }}</td>
                            <td style="padding: 0.5rem; font-weight: 700; color: #059669;">{{ number_format($pr->net_salary, 2) }} {{ currency_code() }}</td>
                            <td style="padding: 0.5rem;">
                                <span class="badge" style="background: #DCFCE7; color: #166534; padding: 0.2rem 0.5rem; border-radius: 999px;">{{ ucfirst($pr->status) }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" style="text-align: center; padding: 2rem; color: #94A3B8;">No payroll records generated yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- TAB 6: DOCUMENTS -->
    <div id="tab-content-documents" class="profile-tab-content" style="display: none;">
        <div class="card" style="padding: 1.5rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="font-size: 1rem; font-weight: 700; color: #0284C7; margin: 0;">Employee Document Vault</h3>
                <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('uploadDocModal').style.display='flex'">
                    <i class="fa-solid fa-cloud-arrow-up text-xs"></i> Upload Document
                </button>
            </div>
            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                <thead>
                    <tr style="border-bottom: 2px solid #E2E8F0; text-align: left; color: #64748B;">
                        <th style="padding: 0.5rem;">Document Title</th>
                        <th style="padding: 0.5rem;">Type</th>
                        <th style="padding: 0.5rem;">Document #</th>
                        <th style="padding: 0.5rem;">Expiry Date</th>
                        <th style="padding: 0.5rem;">Size</th>
                        <th style="padding: 0.5rem; text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employee->documents as $doc)
                        <tr style="border-bottom: 1px solid #F1F5F9;">
                            <td style="padding: 0.5rem; font-weight: 600;">
                                <i class="fa-solid fa-file-pdf text-red-500 mr-1 ml-1"></i> {{ $doc->title }}
                            </td>
                            <td style="padding: 0.5rem; text-transform: capitalize;">{{ str_replace('_', ' ', $doc->document_type) }}</td>
                            <td style="padding: 0.5rem; color: #64748B;">{{ $doc->document_number ?? '—' }}</td>
                            <td style="padding: 0.5rem;">
                                @if($doc->expiry_date)
                                    <span style="color: {{ \Carbon\Carbon::parse($doc->expiry_date)->isPast() ? '#EF4444' : '#10B981' }}; font-weight: 600;">
                                        {{ $doc->expiry_date }}
                                    </span>
                                @else
                                    —
                                @endif
                            </td>
                            <td style="padding: 0.5rem; color: #94A3B8;">{{ $doc->file_size }}</td>
                            <td style="padding: 0.5rem; text-align: right;">
                                <a href="{{ route('admin.hr.employees.documents.download', $doc->id) }}" class="btn btn-ghost btn-xs text-sky-600">
                                    <i class="fa-solid fa-download"></i>
                                </a>
                                <form action="{{ route('admin.hr.employees.documents.destroy', $doc->id) }}" method="POST" onsubmit="return confirm('Delete file?');" style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-ghost btn-xs text-red-500"><i class="fa-solid fa-trash-can"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" style="text-align: center; padding: 2rem; color: #94A3B8;">No documents securely uploaded yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- TAB 7: CONTRACTS -->
    <div id="tab-content-contracts" class="profile-tab-content" style="display: none;">
        <div class="card" style="padding: 1.5rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
            <h3 style="font-size: 1rem; font-weight: 700; color: #0284C7; margin-top: 0; margin-bottom: 1rem;">Contractual History</h3>
            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                <thead>
                    <tr style="border-bottom: 2px solid #E2E8F0; text-align: left; color: #64748B;">
                        <th style="padding: 0.5rem;">Contract #</th>
                        <th style="padding: 0.5rem;">Start Date</th>
                        <th style="padding: 0.5rem;">End Date</th>
                        <th style="padding: 0.5rem;">Salary</th>
                        <th style="padding: 0.5rem;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employee->contracts as $contract)
                        <tr style="border-bottom: 1px solid #F1F5F9;">
                            <td style="padding: 0.5rem; font-weight: 700; color: #0284C7;">{{ $contract->contract_number }}</td>
                            <td style="padding: 0.5rem;">{{ $contract->start_date }}</td>
                            <td style="padding: 0.5rem;">{{ $contract->end_date ?? 'Indefinite' }}</td>
                            <td style="padding: 0.5rem; font-weight: 600;">{{ number_format($contract->salary, 2) }}</td>
                            <td style="padding: 0.5rem;"><span class="badge" style="background: #DCFCE7; color: #166534; padding: 0.2rem 0.5rem; border-radius: 999px;">{{ ucfirst($contract->status) }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" style="text-align: center; padding: 2rem; color: #94A3B8;">No contracts tracked.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- TAB 8: PERFORMANCE -->
    <div id="tab-content-performance" class="profile-tab-content" style="display: none;">
        <div class="card" style="padding: 1.5rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
            <h3 style="font-size: 1rem; font-weight: 700; color: #0284C7; margin-top: 0; margin-bottom: 1rem;">Performance Evaluations</h3>
            <p style="color: #64748B; font-size: 0.85rem;">Performance appraisals and milestone reviews conducted for this employee.</p>
        </div>
    </div>

    <!-- TAB 9: TRAINING -->
    <div id="tab-content-training" class="profile-tab-content" style="display: none;">
        <div class="card" style="padding: 1.5rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
            <h3 style="font-size: 1rem; font-weight: 700; color: #0284C7; margin-top: 0; margin-bottom: 1rem;">Training & Certifications</h3>
            <p style="color: #64748B; font-size: 0.85rem;">Programs completed and active training tracks.</p>
        </div>
    </div>

    <!-- TAB 10: ASSETS -->
    <div id="tab-content-assets" class="profile-tab-content" style="display: none;">
        <div class="card" style="padding: 1.5rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
            <h3 style="font-size: 1rem; font-weight: 700; color: #0284C7; margin-top: 0; margin-bottom: 1rem;">Company Assets Custody</h3>
            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                <thead>
                    <tr style="border-bottom: 2px solid #E2E8F0; text-align: left; color: #64748B;">
                        <th style="padding: 0.5rem;">Asset</th>
                        <th style="padding: 0.5rem;">Code</th>
                        <th style="padding: 0.5rem;">Assigned At</th>
                        <th style="padding: 0.5rem;">Condition</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employee->assetAssignments as $assign)
                        <tr style="border-bottom: 1px solid #F1F5F9;">
                            <td style="padding: 0.5rem; font-weight: 600;">{{ $assign->asset?->name }}</td>
                            <td style="padding: 0.5rem; color: #0284C7;">{{ $assign->asset?->asset_code }}</td>
                            <td style="padding: 0.5rem;">{{ $assign->assigned_at }}</td>
                            <td style="padding: 0.5rem;"><span class="badge" style="background: #F1F5F9; padding: 0.2rem 0.5rem; border-radius: 0.25rem;">{{ $assign->condition_on_assignment }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" style="text-align: center; padding: 2rem; color: #94A3B8;">No assets currently in custody.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- TAB 11: REQUESTS -->
    <div id="tab-content-requests" class="profile-tab-content" style="display: none;">
        <div class="card" style="padding: 1.5rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
            <h3 style="font-size: 1rem; font-weight: 700; color: #0284C7; margin-top: 0; margin-bottom: 1rem;">Submitted Requests</h3>
            <p style="color: #64748B; font-size: 0.85rem;">Certificates, administrative letters, and general requests.</p>
        </div>
    </div>

    <!-- TAB 12: DISCIPLINARY -->
    <div id="tab-content-disciplinary" class="profile-tab-content" style="display: none;">
        <div class="card" style="padding: 1.5rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
            <h3 style="font-size: 1rem; font-weight: 700; color: #DC2626; margin-top: 0; margin-bottom: 1rem;">Disciplinary Records & Warnings</h3>
            <p style="color: #64748B; font-size: 0.85rem;">Confidential incident investigations and notices.</p>
        </div>
    </div>

    <!-- TAB 13: TIMELINE -->
    <div id="tab-content-timeline" class="profile-tab-content" style="display: none;">
        <div class="card" style="padding: 1.5rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
            <h3 style="font-size: 1rem; font-weight: 700; color: #0284C7; margin-top: 0; margin-bottom: 1.5rem;">
                <i class="fa-solid fa-timeline mr-1 ml-1"></i> Unified Career Timeline
            </h3>
            <div style="position: relative; padding-left: 2rem; border-left: 2px solid #E2E8F0; margin-left: 1rem; display: flex; flex-direction: column; gap: 1.5rem;">
                @forelse($timeline as $event)
                    <div style="position: relative;">
                        <div style="position: absolute; left: -2.6rem; top: 0; width: 18px; height: 18px; border-radius: 50%; background: #0284C7; border: 3px solid #ffffff; box-shadow: 0 0 0 2px #E2E8F0;"></div>
                        <div style="font-size: 0.8rem; color: #64748B; font-weight: 600;">{{ $event['date'] ?? 'N/A' }}</div>
                        <div style="font-weight: 700; font-size: 0.95rem; color: #1E293B; margin-top: 0.15rem;">{{ $event['title'] ?? 'Event' }}</div>
                        <div style="font-size: 0.85rem; color: #475569; margin-top: 0.25rem;">{{ $event['description'] ?? '' }}</div>
                    </div>
                @empty
                    <p style="color: #94A3B8;">No timeline milestones registered.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Document Upload Modal -->
    <div id="uploadDocModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div style="background: #ffffff; border-radius: 0.75rem; padding: 1.5rem; width: 100%; max-width: 500px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="font-size: 1.1rem; font-weight: 700; margin: 0;">Upload Employee Document</h3>
                <button type="button" onclick="document.getElementById('uploadDocModal').style.display='none'" style="background: none; border: none; font-size: 1.25rem; cursor: pointer;">&times;</button>
            </div>
            <form action="{{ route('admin.hr.employees.documents.store', $employee->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Document Type *</label>
                        <select name="document_type" required class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                            <option value="national_id">National ID / Iqama</option>
                            <option value="passport">Passport</option>
                            <option value="contract">Signed Contract</option>
                            <option value="education_certificate">Education Certificate</option>
                            <option value="medical_certificate">Medical / Insurance</option>
                            <option value="other">Other Document</option>
                        </select>
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Title / Description *</label>
                        <input type="text" name="title" required class="form-control" placeholder="e.g. Iqama Scan 2026" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Expiry Date</label>
                        <input type="date" name="expiry_date" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Select File (PDF, Images, DOC) *</label>
                        <input type="file" name="document_file" required class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                    </div>
                    <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 1rem;">
                        <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('uploadDocModal').style.display='none'">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm">Upload File</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function switchProfileTab(tabName) {
            document.querySelectorAll('.profile-tab-content').forEach(el => el.style.display = 'none');
            document.querySelectorAll('.profile-tab-btn').forEach(btn => {
                btn.style.color = '#64748B';
                btn.style.borderBottom = '3px solid transparent';
            });
            const content = document.getElementById('tab-content-' + tabName);
            if (content) content.style.display = 'block';
            const btn = document.getElementById('tab-btn-' + tabName);
            if (btn) {
                btn.style.color = '#0284C7';
                btn.style.borderBottom = '3px solid #0284C7';
            }
        }
    </script>
</x-layouts.admin>

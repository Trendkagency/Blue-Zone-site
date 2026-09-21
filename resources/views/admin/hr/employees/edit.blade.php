<x-layouts.admin 
    :pageTitle="'Edit ' . $employee->full_name" 
    :pageSubtitle="'Update employee profile and employment details'"
>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0; color: var(--text-color, #1E293B);">
                <i class="fa-solid fa-user-pen text-amber-500 mr-2 ml-2"></i> Edit Profile: {{ $employee->full_name }}
            </h2>
            <span style="font-size: 0.85rem; color: #64748B; font-family: monospace;">{{ $employee->employee_number }}</span>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ route('admin.hr.employees.show', $employee->id) }}" class="btn btn-outline btn-sm">
                <i class="fa-solid fa-eye text-xs mr-1 ml-1"></i> View Profile
            </a>
            <a href="{{ route('admin.hr.employees.index') }}" class="btn btn-secondary btn-sm">
                Back to Directory
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger" style="padding: 0.75rem 1rem; margin-bottom: 1.5rem; border-radius: 0.5rem; background: #FEE2E2; color: #991B1B; border: 1px solid #FECACA;">
            <ul style="margin: 0; padding-left: 1.25rem;">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.hr.employees.update', $employee->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- 1. Personal Identity Information -->
        <div class="card" style="padding: 1.5rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0); margin-bottom: 1.5rem;">
            <h3 style="font-size: 1rem; font-weight: 700; color: #0284C7; margin-top: 0; margin-bottom: 1rem; border-bottom: 1px solid #F1F5F9; padding-bottom: 0.5rem;">
                <i class="fa-solid fa-id-card mr-2 ml-2"></i> 1. Personal & Identity Information
            </h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;">
                <div>
                    <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">First Name (EN) *</label>
                    <input type="text" name="first_name" value="{{ old('first_name', $employee->first_name) }}" required class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                </div>
                <div>
                    <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Last Name (EN) *</label>
                    <input type="text" name="last_name" value="{{ old('last_name', $employee->last_name) }}" required class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                </div>
                <div>
                    <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">First Name (AR)</label>
                    <input type="text" name="first_name_ar" value="{{ old('first_name_ar', $employee->first_name_ar) }}" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                </div>
                <div>
                    <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Last Name (AR)</label>
                    <input type="text" name="last_name_ar" value="{{ old('last_name_ar', $employee->last_name_ar) }}" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                </div>
                <div>
                    <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">National ID / Iqama</label>
                    <input type="text" name="national_id" value="{{ old('national_id', $employee->national_id) }}" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                </div>
                <div>
                    <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Gender</label>
                    <select name="gender" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                        <option value="male" {{ old('gender', $employee->gender) === 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender', $employee->gender) === 'female' ? 'selected' : '' }}>Female</option>
                    </select>
                </div>
                <div>
                    <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Date of Birth</label>
                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $employee->date_of_birth) }}" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                </div>
                <div>
                    <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Nationality</label>
                    <input type="text" name="nationality" value="{{ old('nationality', $employee->nationality) }}" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                </div>
            </div>
        </div>

        <!-- 2. Contact Information -->
        <div class="card" style="padding: 1.5rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0); margin-bottom: 1.5rem;">
            <h3 style="font-size: 1rem; font-weight: 700; color: #0284C7; margin-top: 0; margin-bottom: 1rem; border-bottom: 1px solid #F1F5F9; padding-bottom: 0.5rem;">
                <i class="fa-solid fa-address-book mr-2 ml-2"></i> 2. Contact & Address Details
            </h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;">
                <div>
                    <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Email Address</label>
                    <input type="email" name="email" value="{{ old('email', $employee->email) }}" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                </div>
                <div>
                    <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone', $employee->phone) }}" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                </div>
                <div>
                    <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Alternate Phone</label>
                    <input type="text" name="alternate_phone" value="{{ old('alternate_phone', $employee->alternate_phone) }}" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                </div>
                <div>
                    <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Residential Address</label>
                    <input type="text" name="address" value="{{ old('address', $employee->address) }}" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                </div>
            </div>
        </div>

        <!-- 3. Employment & Placement -->
        <div class="card" style="padding: 1.5rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0); margin-bottom: 1.5rem;">
            <h3 style="font-size: 1rem; font-weight: 700; color: #0284C7; margin-top: 0; margin-bottom: 1rem; border-bottom: 1px solid #F1F5F9; padding-bottom: 0.5rem;">
                <i class="fa-solid fa-briefcase mr-2 ml-2"></i> 3. Employment & Hierarchy Placement
            </h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;">
                <div>
                    <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Department</label>
                    <select name="department_id" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                        <option value="">-- Choose Department --</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ old('department_id', $employee->department_id) == $dept->id ? 'selected' : '' }}>{{ $dept->name_en }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Job Position</label>
                    <select name="position_id" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                        <option value="">-- Choose Position --</option>
                        @foreach($positions as $pos)
                            <option value="{{ $pos->id }}" {{ old('position_id', $employee->position_id) == $pos->id ? 'selected' : '' }}>{{ $pos->name_en }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Branch / Location</label>
                    <select name="location_id" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                        <option value="">-- Choose Location --</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ old('location_id', $employee->location_id) == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Direct Manager</label>
                    <select name="manager_employee_id" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                        <option value="">-- No Direct Manager --</option>
                        @foreach($managers as $mgr)
                            <option value="{{ $mgr->id }}" {{ old('manager_employee_id', $employee->manager_employee_id) == $mgr->id ? 'selected' : '' }}>{{ $mgr->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Employment Type *</label>
                    <select name="employment_type" required class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                        <option value="full_time" {{ old('employment_type', $employee->employment_type) === 'full_time' ? 'selected' : '' }}>Full Time</option>
                        <option value="part_time" {{ old('employment_type', $employee->employment_type) === 'part_time' ? 'selected' : '' }}>Part Time</option>
                        <option value="contract" {{ old('employment_type', $employee->employment_type) === 'contract' ? 'selected' : '' }}>Contract</option>
                        <option value="freelancer" {{ old('employment_type', $employee->employment_type) === 'freelancer' ? 'selected' : '' }}>Freelancer</option>
                        <option value="intern" {{ old('employment_type', $employee->employment_type) === 'intern' ? 'selected' : '' }}>Intern</option>
                    </select>
                </div>
                <div>
                    <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Employment Status *</label>
                    <select name="employment_status" required class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                        <option value="active" {{ old('employment_status', $employee->employment_status) === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="probation" {{ old('employment_status', $employee->employment_status) === 'probation' ? 'selected' : '' }}>Probation</option>
                        <option value="on_leave" {{ old('employment_status', $employee->employment_status) === 'on_leave' ? 'selected' : '' }}>On Leave</option>
                        <option value="resigned" {{ old('employment_status', $employee->employment_status) === 'resigned' ? 'selected' : '' }}>Resigned</option>
                        <option value="terminated" {{ old('employment_status', $employee->employment_status) === 'terminated' ? 'selected' : '' }}>Terminated</option>
                    </select>
                </div>
                <div>
                    <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Hire Date *</label>
                    <input type="date" name="hire_date" value="{{ old('hire_date', $employee->hire_date) }}" required class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                </div>
                <div>
                    <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Work Schedule</label>
                    <select name="work_schedule_id" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                        <option value="">-- Choose Shift --</option>
                        @foreach($workSchedules as $ws)
                            <option value="{{ $ws->id }}" {{ old('work_schedule_id', $employee->work_schedule_id) == $ws->id ? 'selected' : '' }}>{{ $ws->name_en }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">System User Account</label>
                    <select name="user_id" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                        <option value="">-- No User Account (HR only) --</option>
                        @foreach($availableUsers as $usr)
                            <option value="{{ $usr->id }}" {{ old('user_id', $employee->user_id) == $usr->id ? 'selected' : '' }}>{{ $usr->name }} ({{ $usr->email }})</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- 4. Compensation & Salary Configuration -->
        <div class="card" style="padding: 1.5rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0); margin-bottom: 1.5rem;">
            <h3 style="font-size: 1rem; font-weight: 700; color: #0284C7; margin-top: 0; margin-bottom: 1rem; border-bottom: 1px solid #F1F5F9; padding-bottom: 0.5rem;">
                <i class="fa-solid fa-money-bill-wave mr-2 ml-2"></i> 4. Compensation & Payroll Setup
            </h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;">
                <div>
                    <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Basic Salary (Monthly) *</label>
                    <input type="number" step="0.01" name="basic_salary" value="{{ old('basic_salary', $employee->basic_salary) }}" required class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                </div>
                <div>
                    <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Housing Allowance</label>
                    <input type="number" step="0.01" name="housing_allowance" value="{{ old('housing_allowance', $employee->housing_allowance) }}" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                </div>
                <div>
                    <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Transport Allowance</label>
                    <input type="number" step="0.01" name="transportation_allowance" value="{{ old('transportation_allowance', $employee->transportation_allowance) }}" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                </div>
                <div>
                    <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Other Allowances</label>
                    <input type="number" step="0.01" name="other_allowance" value="{{ old('other_allowance', $employee->other_allowance) }}" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                </div>
                <div>
                    <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Salary Change Reason (Audited)</label>
                    <input type="text" name="salary_change_reason" placeholder="Annual increment, role adjustment, etc." class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                </div>
                <div>
                    <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Bank Name</label>
                    <input type="text" name="bank_name" value="{{ old('bank_name', $employee->bank_name) }}" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                </div>
                <div>
                    <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">IBAN</label>
                    <input type="text" name="iban" value="{{ old('iban', $employee->iban) }}" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                </div>
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-bottom: 3rem;">
            <a href="{{ route('admin.hr.employees.show', $employee->id) }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary" style="padding: 0.6rem 2rem; font-weight: 700;">
                <i class="fa-solid fa-check mr-1 ml-1"></i> Update Employee Profile
            </button>
        </div>
    </form>
</x-layouts.admin>

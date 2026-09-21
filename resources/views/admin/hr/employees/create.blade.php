<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'إضافة موظف جديد' : 'New Employee Profile'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'تسجيل موظف جديد في نظام الموارد البشرية' : 'Register a new employee profile and employment details'"
>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0; color: var(--text-color, #1E293B);">
                <i class="fa-solid fa-user-plus text-emerald-500 mr-2 ml-2"></i> {{ app()->getLocale() === 'ar' ? 'إضافة موظف جديد' : 'Create Employee Profile' }}
            </h2>
        </div>
        <a href="{{ route('admin.hr.employees.index') }}" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-arrow-left text-xs mr-1 ml-1"></i> Back to Directory
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger" style="padding: 0.75rem 1rem; margin-bottom: 1.5rem; border-radius: 0.5rem; background: #FEE2E2; color: #991B1B; border: 1px solid #FECACA;">
            <div style="font-weight: 700; margin-bottom: 0.25rem;">Please review form errors:</div>
            <ul style="margin: 0; padding-left: 1.25rem;">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.hr.employees.store') }}" method="POST">
        @csrf

        <!-- 1. Personal Identity Information -->
        <div class="card" style="padding: 1.5rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0); margin-bottom: 1.5rem;">
            <h3 style="font-size: 1rem; font-weight: 700; color: #0284C7; margin-top: 0; margin-bottom: 1rem; border-bottom: 1px solid #F1F5F9; padding-bottom: 0.5rem;">
                <i class="fa-solid fa-id-card mr-2 ml-2"></i> 1. Personal & Identity Information
            </h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;">
                <div>
                    <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">First Name (EN) *</label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}" required class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                </div>
                <div>
                    <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Last Name (EN) *</label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}" required class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                </div>
                <div>
                    <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">First Name (AR)</label>
                    <input type="text" name="first_name_ar" value="{{ old('first_name_ar') }}" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                </div>
                <div>
                    <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Last Name (AR)</label>
                    <input type="text" name="last_name_ar" value="{{ old('last_name_ar') }}" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                </div>
                <div>
                    <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">National ID / Iqama</label>
                    <input type="text" name="national_id" value="{{ old('national_id') }}" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                </div>
                <div>
                    <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Gender</label>
                    <select name="gender" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                        <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                    </select>
                </div>
                <div>
                    <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Date of Birth</label>
                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                </div>
                <div>
                    <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Nationality</label>
                    <input type="text" name="nationality" value="{{ old('nationality', 'Saudi') }}" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
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
                    <input type="email" name="email" value="{{ old('email') }}" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                </div>
                <div>
                    <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                </div>
                <div>
                    <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Alternate Phone</label>
                    <input type="text" name="alternate_phone" value="{{ old('alternate_phone') }}" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                </div>
                <div>
                    <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Residential Address</label>
                    <input type="text" name="address" value="{{ old('address') }}" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                </div>
            </div>
        </div>

        <!-- 3. Employment & Organizational Placement -->
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
                            <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name_en }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Job Position</label>
                    <select name="position_id" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                        <option value="">-- Choose Position --</option>
                        @foreach($positions as $pos)
                            <option value="{{ $pos->id }}" {{ old('position_id') == $pos->id ? 'selected' : '' }}>{{ $pos->name_en }} ({{ $pos->department?->name_en }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Branch / Location</label>
                    <select name="location_id" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                        <option value="">-- Choose Location --</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ old('location_id') == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Direct Manager</label>
                    <select name="manager_employee_id" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                        <option value="">-- No Direct Manager --</option>
                        @foreach($managers as $mgr)
                            <option value="{{ $mgr->id }}" {{ old('manager_employee_id') == $mgr->id ? 'selected' : '' }}>{{ $mgr->full_name }} ({{ $mgr->position?->name_en }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Employment Type *</label>
                    <select name="employment_type" required class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                        <option value="full_time" {{ old('employment_type') === 'full_time' ? 'selected' : '' }}>Full Time</option>
                        <option value="part_time" {{ old('employment_type') === 'part_time' ? 'selected' : '' }}>Part Time</option>
                        <option value="contract" {{ old('employment_type') === 'contract' ? 'selected' : '' }}>Contract</option>
                        <option value="freelancer" {{ old('employment_type') === 'freelancer' ? 'selected' : '' }}>Freelancer</option>
                        <option value="intern" {{ old('employment_type') === 'intern' ? 'selected' : '' }}>Intern</option>
                    </select>
                </div>
                <div>
                    <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Employment Status *</label>
                    <select name="employment_status" required class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                        <option value="active" {{ old('employment_status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="probation" {{ old('employment_status') === 'probation' ? 'selected' : '' }}>Probation</option>
                        <option value="on_leave" {{ old('employment_status') === 'on_leave' ? 'selected' : '' }}>On Leave</option>
                    </select>
                </div>
                <div>
                    <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Hire Date *</label>
                    <input type="date" name="hire_date" value="{{ old('hire_date', now()->toDateString()) }}" required class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                </div>
                <div>
                    <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Work Schedule</label>
                    <select name="work_schedule_id" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                        <option value="">-- Choose Shift --</option>
                        @foreach($workSchedules as $ws)
                            <option value="{{ $ws->id }}" {{ old('work_schedule_id') == $ws->id ? 'selected' : '' }}>{{ $ws->name_en }} ({{ substr($ws->start_time,0,5) }}-{{ substr($ws->end_time,0,5) }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">System User Account</label>
                    <select name="user_id" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                        <option value="">-- No User Account (HR only) --</option>
                        @foreach($availableUsers as $usr)
                            <option value="{{ $usr->id }}" {{ old('user_id') == $usr->id ? 'selected' : '' }}>{{ $usr->name }} ({{ $usr->email }})</option>
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
                    <input type="number" step="0.01" name="basic_salary" value="{{ old('basic_salary', 5000) }}" required class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                </div>
                <div>
                    <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Housing Allowance</label>
                    <input type="number" step="0.01" name="housing_allowance" value="{{ old('housing_allowance', 0) }}" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                </div>
                <div>
                    <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Transport Allowance</label>
                    <input type="number" step="0.01" name="transportation_allowance" value="{{ old('transportation_allowance', 0) }}" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                </div>
                <div>
                    <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Other Allowances</label>
                    <input type="number" step="0.01" name="other_allowance" value="{{ old('other_allowance', 0) }}" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                </div>
                <div>
                    <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Payment Method</label>
                    <select name="payment_method" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                        <option value="bank_transfer" selected>Bank Transfer</option>
                        <option value="cash">Cash</option>
                        <option value="cheque">Cheque</option>
                    </select>
                </div>
                <div>
                    <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Bank Name</label>
                    <input type="text" name="bank_name" value="{{ old('bank_name') }}" placeholder="e.g. Al Rajhi, SNB" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                </div>
                <div>
                    <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">IBAN</label>
                    <input type="text" name="iban" value="{{ old('iban') }}" placeholder="SA..." class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                </div>
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-bottom: 3rem;">
            <a href="{{ route('admin.hr.employees.index') }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary" style="padding: 0.6rem 2rem; font-weight: 700;">
                <i class="fa-solid fa-check mr-1 ml-1"></i> Register Employee Profile
            </button>
        </div>
    </form>
</x-layouts.admin>

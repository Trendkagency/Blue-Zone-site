<x-layouts.admin 
    :pageTitle="__('crm.leads.import_title') ?? 'Import Leads & Tasks from Excel'" 
    :pageSubtitle="__('crm.leads.import_subtitle') ?? 'Batch upload prospective customer records with attached follow-up tasks and automatic pipeline integration'"
    :breadcrumbs="[
        __('crm.dashboard.title') ?? 'CRM' => route('admin.crm.dashboard'),
        __('crm.leads.title') => route('admin.crm.leads.index'),
        'Import' => route('admin.crm.leads.import')
    ]"
>
    <!-- Top Action Bar -->
    <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1rem; margin-bottom: 1.5rem;">
        <a href="{{ route('admin.crm.leads.index') }}" class="btn btn-ghost btn-sm" style="display: inline-flex; align-items: center; gap: 0.4rem;">
            <i class="fa-solid fa-arrow-left"></i> {{ __('app.actions.back') ?? 'Back to Leads' }}
        </a>

        <!-- Template Download Buttons -->
        <div style="display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: center;">
            <span style="font-size: 0.85rem; color: #64748B; font-weight: 600;">
                <i class="fa-solid fa-circle-question mr-1 text-sky-500"></i> Sample Template:
            </span>
            <a href="{{ route('admin.crm.leads.import.template', ['format' => 'xlsx']) }}" 
               class="btn btn-secondary btn-sm" 
               style="background: #ECFDF5; color: #047857; border: 1px solid #A7F3D0; font-weight: 700; display: inline-flex; align-items: center; gap: 0.4rem;">
                <i class="fa-solid fa-file-excel text-emerald-600 text-sm"></i> Download Sample Excel (.xlsx)
            </a>
            <a href="{{ route('admin.crm.leads.import.template', ['format' => 'csv']) }}" 
               class="btn btn-ghost btn-sm" 
               style="color: #475569; border: 1px solid #CBD5E1; font-weight: 600; display: inline-flex; align-items: center; gap: 0.4rem;">
                <i class="fa-solid fa-file-csv text-slate-500 text-sm"></i> Download CSV (.csv)
            </a>
        </div>
    </div>

    @if(session('import_results'))
        @php $res = session('import_results'); @endphp
        <!-- Detailed Import Results Banner -->
        <div class="card" style="background: #ffffff; border: 1px solid #E2E8F0; border-radius: 0.75rem; padding: 1.5rem; margin-bottom: 2rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.25rem;">
                <div style="width: 2.5rem; height: 2.5rem; border-radius: 9999px; background: #DCFCE7; display: flex; align-items: center; justify-content: center;">
                    <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
                </div>
                <div>
                    <h3 style="font-size: 1.1rem; font-weight: 800; color: #0F172A; margin: 0;">Batch Import Execution Report</h3>
                    <p style="font-size: 0.85rem; color: #64748B; margin: 0.15rem 0 0;">The uploaded spreadsheet has been parsed and processed.</p>
                </div>
            </div>

            <!-- Stats Counters -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
                <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 0.5rem; padding: 1rem; text-align: center;">
                    <span style="font-size: 0.75rem; font-weight: 700; color: #64748B; text-transform: uppercase;">Total Rows</span>
                    <strong style="display: block; font-size: 1.5rem; font-weight: 800; color: #0F172A; margin-top: 0.25rem;">{{ $res['total_rows'] }}</strong>
                </div>

                <div style="background: #F0FDF4; border: 1px solid #BBF7D0; border-radius: 0.5rem; padding: 1rem; text-align: center;">
                    <span style="font-size: 0.75rem; font-weight: 700; color: #166534; text-transform: uppercase;">Leads Created</span>
                    <strong style="display: block; font-size: 1.5rem; font-weight: 800; color: #15803D; margin-top: 0.25rem;">+{{ $res['imported_leads'] }}</strong>
                </div>

                <div style="background: #EFF6FF; border: 1px solid #BFDBFE; border-radius: 0.5rem; padding: 1rem; text-align: center;">
                    <span style="font-size: 0.75rem; font-weight: 700; color: #1E40AF; text-transform: uppercase;">Tasks Scheduled</span>
                    <strong style="display: block; font-size: 1.5rem; font-weight: 800; color: #2563EB; margin-top: 0.25rem;">+{{ $res['created_tasks'] }}</strong>
                </div>

                @if($res['updated_leads'] > 0)
                    <div style="background: #FFFBEB; border: 1px solid #FDE68A; border-radius: 0.5rem; padding: 1rem; text-align: center;">
                        <span style="font-size: 0.75rem; font-weight: 700; color: #92400E; text-transform: uppercase;">Leads Updated</span>
                        <strong style="display: block; font-size: 1.5rem; font-weight: 800; color: #D97706; margin-top: 0.25rem;">{{ $res['updated_leads'] }}</strong>
                    </div>
                @endif

                <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 0.5rem; padding: 1rem; text-align: center;">
                    <span style="font-size: 0.75rem; font-weight: 700; color: #64748B; text-transform: uppercase;">Duplicates Skipped</span>
                    <strong style="display: block; font-size: 1.5rem; font-weight: 800; color: #64748B; margin-top: 0.25rem;">{{ $res['skipped_duplicates'] }}</strong>
                </div>
            </div>

            <!-- Errors table if any -->
            @if(!empty($res['errors']))
                <div style="margin-top: 1rem; border-top: 1px solid #F1F5F9; padding-top: 1rem;">
                    <h4 style="font-size: 0.9rem; font-weight: 700; color: #DC2626; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.4rem;">
                        <i class="fa-solid fa-triangle-exclamation"></i> Row-level Warnings / Errors ({{ count($res['errors']) }})
                    </h4>
                    <div style="max-height: 200px; overflow-y: auto; background: #FEF2F2; border: 1px solid #FCA5A5; border-radius: 0.5rem; padding: 0.75rem;">
                        <table style="width: 100%; font-size: 0.8rem; border-collapse: collapse;">
                            <thead>
                                <tr style="text-align: left; border-bottom: 1px solid #F87171; color: #991B1B;">
                                    <th style="padding: 0.4rem;">Row #</th>
                                    <th style="padding: 0.4rem;">Record</th>
                                    <th style="padding: 0.4rem;">Error Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($res['errors'] as $err)
                                    <tr style="border-bottom: 1px solid #FEE2E2; color: #7F1D1D;">
                                        <td style="padding: 0.4rem; font-weight: 700;">Row {{ $err['row'] }}</td>
                                        <td style="padding: 0.4rem;">{{ $err['name'] ?? 'N/A' }}</td>
                                        <td style="padding: 0.4rem;">{{ $err['error'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <div style="margin-top: 1.25rem; display: flex; gap: 0.75rem;">
                <a href="{{ route('admin.crm.leads.index') }}" class="btn btn-primary btn-sm" style="font-weight: 700;">
                    <i class="fa-solid fa-users mr-1"></i> View All Leads
                </a>
                <a href="{{ route('admin.crm.opportunities.index') }}" class="btn btn-secondary btn-sm" style="font-weight: 700;">
                    <i class="fa-solid fa-table-columns mr-1"></i> View Kanban Pipeline
                </a>
            </div>
        </div>
    @endif

    <div style="display: grid; grid-template-columns: 1fr; lg:grid-template-columns: 3fr 2fr; gap: 2rem;">
        <!-- Left: Upload Form -->
        <div>
            <form method="POST" action="{{ route('admin.crm.leads.import.process') }}" enctype="multipart/form-data">
                @csrf

                <div class="card" style="padding: 2rem; border-radius: 0.75rem; background: #ffffff; border: 1px solid #E2E8F0; margin-bottom: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    <h3 style="font-size: 1.1rem; font-weight: 800; color: #0F172A; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fa-solid fa-file-arrow-up text-sky-600"></i> Upload Leads & Tasks Spreadsheet
                    </h3>
                    <p style="font-size: 0.85rem; color: #64748B; margin-bottom: 1.5rem;">
                        Select or drop your formatted <code>.xlsx</code> or <code>.csv</code> spreadsheet. All leads will be validated, assigned, and registered in the CRM sales pipeline.
                    </p>

                    <!-- Dropzone -->
                    <div id="dropzoneContainer" 
                         onclick="document.getElementById('sheetFileInput').click()"
                         ondragover="handleDragOver(event)" 
                         ondragleave="handleDragLeave(event)" 
                         ondrop="handleFileDrop(event)"
                         style="border: 2px dashed #CBD5E1; border-radius: 0.75rem; padding: 2.5rem 1.5rem; text-align: center; cursor: pointer; background: #F8FAFC; transition: all 0.2s ease;">
                        
                        <input type="file" id="sheetFileInput" name="sheet_file" accept=".xlsx,.xls,.csv" style="display: none;" onchange="handleFileSelected(this)">
                        
                        <div id="dropzoneDefaultState">
                            <div style="width: 3.5rem; height: 3.5rem; border-radius: 9999px; background: #E0F2FE; color: #0284C7; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 1.5rem;">
                                <i class="fa-solid fa-cloud-arrow-up"></i>
                            </div>
                            <strong style="display: block; font-size: 1rem; color: #0F172A; margin-bottom: 0.25rem;">
                                Click to choose file or drag and drop here
                            </strong>
                            <span style="font-size: 0.8rem; color: #64748B;">
                                Supported formats: Microsoft Excel (.xlsx, .xls) or Comma Separated Values (.csv) up to 20MB
                            </span>
                        </div>

                        <div id="dropzoneSelectedState" style="display: none;">
                            <div style="width: 3.5rem; height: 3.5rem; border-radius: 9999px; background: #DCFCE7; color: #16A34A; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 1.5rem;">
                                <i class="fa-solid fa-file-excel"></i>
                            </div>
                            <strong id="selectedFileName" style="display: block; font-size: 1.05rem; color: #0F172A; font-weight: 700;">
                                filename.xlsx
                            </strong>
                            <span id="selectedFileSize" style="font-size: 0.8rem; color: #64748B; display: block; margin-top: 0.25rem;">
                                0 KB
                            </span>
                            <button type="button" onclick="event.stopPropagation(); resetFileSelection();" class="btn btn-ghost btn-xs text-red-600 mt-2" style="font-size: 0.75rem;">
                                <i class="fa-solid fa-trash mr-1"></i> Remove & pick another
                            </button>
                        </div>
                    </div>
                    @error('sheet_file')
                        <span style="display: block; color: #DC2626; font-size: 0.8rem; margin-top: 0.5rem; font-weight: 600;">
                            {{ $message }}
                        </span>
                    @enderror

                    <!-- Configuration Parameters -->
                    <div style="margin-top: 2rem; border-top: 1px solid #F1F5F9; padding-top: 1.5rem;">
                        <h4 style="font-size: 0.95rem; font-weight: 700; color: #334155; margin-bottom: 1rem;">
                            <i class="fa-solid fa-sliders text-indigo-500 mr-1.5 ml-1.5"></i> Import Settings & Automations
                        </h4>

                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem; margin-bottom: 1.25rem;">
                            <!-- Default Owner -->
                            <div>
                                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #334155; margin-bottom: 0.35rem;">
                                    Default Sales Owner:
                                </label>
                                <select name="default_owner_id" class="form-select" style="width: 100%; padding: 0.5rem 0.75rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; font-size: 0.875rem;">
                                    @foreach($owners as $owner)
                                        <option value="{{ $owner->id }}" {{ $owner->id === auth()->id() ? 'selected' : '' }}>
                                            {{ $owner->name }} ({{ $owner->email }})
                                        </option>
                                    @endforeach
                                </select>
                                <span style="display: block; font-size: 0.75rem; color: #94A3B8; margin-top: 0.25rem;">
                                    Used if the sheet row has no <code>assigned_owner_email</code> specified.
                                </span>
                            </div>

                            <!-- Target Pipeline -->
                            <div>
                                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #334155; margin-bottom: 0.35rem;">
                                    Target Pipeline:
                                </label>
                                <select name="pipeline_id" class="form-select" style="width: 100%; padding: 0.5rem 0.75rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; font-size: 0.875rem;">
                                    @foreach($pipelines as $p)
                                        <option value="{{ $p->id }}" {{ $p->is_default ? 'selected' : '' }}>
                                            {{ $p->name }} {{ $p->is_default ? '(Default)' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                <span style="display: block; font-size: 0.75rem; color: #94A3B8; margin-top: 0.25rem;">
                                    New leads will automatically be placed into the "New Lead" column of this pipeline.
                                </span>
                            </div>
                        </div>

                        <!-- Duplicate Action & Task Creation -->
                        <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 0.5rem; padding: 1rem; margin-bottom: 1.25rem;">
                            <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #0F172A; margin-bottom: 0.5rem;">
                                Duplicate Handling (matches Email or Phone):
                            </label>
                            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                                <label style="display: flex; align-items: flex-start; gap: 0.5rem; cursor: pointer; font-size: 0.85rem; color: #334155;">
                                    <input type="radio" name="duplicate_action" value="skip" checked style="margin-top: 0.2rem;">
                                    <div>
                                        <strong>Skip Duplicates (Recommended)</strong>
                                        <span style="display: block; font-size: 0.75rem; color: #64748B;">Keep existing lead intact without overwriting or duplicating.</span>
                                    </div>
                                </label>

                                <label style="display: flex; align-items: flex-start; gap: 0.5rem; cursor: pointer; font-size: 0.85rem; color: #334155;">
                                    <input type="radio" name="duplicate_action" value="update" style="margin-top: 0.2rem;">
                                    <div>
                                        <strong>Update Existing Leads</strong>
                                        <span style="display: block; font-size: 0.75rem; color: #64748B;">Update company, job title, and notes for matching leads, and append any new tasks.</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <input type="checkbox" id="createTasksCheck" name="create_tasks" value="1" checked style="width: 1rem; height: 1rem; accent-color: #0284C7; cursor: pointer;">
                            <label for="createTasksCheck" style="font-size: 0.875rem; font-weight: 600; color: #0F172A; cursor: pointer;">
                                Automatically create and assign follow-up tasks from sheet (e.g. <code>task_subject</code>)
                            </label>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div style="margin-top: 2rem; display: flex; justify-content: flex-end; gap: 0.75rem;">
                        <a href="{{ route('admin.crm.leads.index') }}" class="btn btn-ghost">
                            {{ __('app.actions.cancel') ?? 'Cancel' }}
                        </a>
                        <button type="submit" id="submitBtn" class="btn btn-primary" style="padding: 0.65rem 2rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.5rem;">
                            <i class="fa-solid fa-upload"></i> Start Excel Import
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Right: Interactive Template & Columns Reference -->
        <div>
            <div class="card" style="padding: 1.75rem; border-radius: 0.75rem; background: #ffffff; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; border-bottom: 1px solid #F1F5F9; padding-bottom: 0.75rem;">
                    <h3 style="font-size: 1rem; font-weight: 800; color: #0F172A; margin: 0; display: flex; align-items: center; gap: 0.4rem;">
                        <i class="fa-solid fa-table-list text-emerald-600"></i> Supported Column Schema
                    </h3>
                    <span style="font-size: 0.75rem; background: #F1F5F9; color: #475569; padding: 0.15rem 0.5rem; border-radius: 9999px; font-weight: 700;">
                        22 Columns
                    </span>
                </div>

                <p style="font-size: 0.8rem; color: #64748B; margin-bottom: 1.25rem;">
                    Headers can be lowercase, uppercase, or Title Case. Columns can appear in any order in your sheet.
                </p>

                <!-- Lead Fields Section -->
                <div style="margin-bottom: 1.25rem;">
                    <strong style="font-size: 0.85rem; color: #0284C7; text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 0.5rem;">
                        <i class="fa-solid fa-user-tag text-xs mr-1"></i> Lead Information
                    </strong>
                    <div style="display: flex; flex-direction: column; gap: 0.35rem; font-size: 0.8rem;">
                        <div style="padding: 0.35rem 0.5rem; background: #F8FAFC; border-radius: 0.35rem; display: flex; justify-content: space-between;">
                            <code>first_name</code>
                            <span style="color: #DC2626; font-weight: 700;">Required*</span>
                        </div>
                        <div style="padding: 0.35rem 0.5rem; background: #F8FAFC; border-radius: 0.35rem; display: flex; justify-content: space-between;">
                            <code>last_name</code>
                            <span style="color: #64748B;">Optional</span>
                        </div>
                        <div style="padding: 0.35rem 0.5rem; background: #F8FAFC; border-radius: 0.35rem; display: flex; justify-content: space-between;">
                            <code>company_name</code>
                            <span style="color: #64748B;">Clinic / Organization</span>
                        </div>
                        <div style="padding: 0.35rem 0.5rem; background: #F8FAFC; border-radius: 0.35rem; display: flex; justify-content: space-between;">
                            <code>job_title</code>
                            <span style="color: #64748B;">e.g. Chief Medical Officer</span>
                        </div>
                        <div style="padding: 0.35rem 0.5rem; background: #F8FAFC; border-radius: 0.35rem; display: flex; justify-content: space-between;">
                            <code>email</code>
                            <span style="color: #64748B;">Validated & used for dedup</span>
                        </div>
                        <div style="padding: 0.35rem 0.5rem; background: #F8FAFC; border-radius: 0.35rem; display: flex; justify-content: space-between;">
                            <code>phone</code>
                            <span style="color: #64748B;">Auto-normalized (e.g. +966 / +20)</span>
                        </div>
                        <div style="padding: 0.35rem 0.5rem; background: #F8FAFC; border-radius: 0.35rem; display: flex; justify-content: space-between;">
                            <code>country</code>, <code>city</code>
                            <span style="color: #64748B;">Auto-mapped to database</span>
                        </div>
                        <div style="padding: 0.35rem 0.5rem; background: #F8FAFC; border-radius: 0.35rem; display: flex; justify-content: space-between;">
                            <code>status</code>
                            <span style="color: #64748B;">new, contacted, qualified</span>
                        </div>
                        <div style="padding: 0.35rem 0.5rem; background: #F8FAFC; border-radius: 0.35rem; display: flex; justify-content: space-between;">
                            <code>priority</code>
                            <span style="color: #64748B;">low, normal, high, urgent</span>
                        </div>
                        <div style="padding: 0.35rem 0.5rem; background: #F8FAFC; border-radius: 0.35rem; display: flex; justify-content: space-between;">
                            <code>estimated_value</code>
                            <span style="color: #64748B;">e.g. 45000.00</span>
                        </div>
                        <div style="padding: 0.35rem 0.5rem; background: #F8FAFC; border-radius: 0.35rem; display: flex; justify-content: space-between;">
                            <code>source</code>, <code>campaign</code>
                            <span style="color: #64748B;">Matched by name or slug</span>
                        </div>
                        <div style="padding: 0.35rem 0.5rem; background: #F8FAFC; border-radius: 0.35rem; display: flex; justify-content: space-between;">
                            <code>assigned_owner_email</code>
                            <span style="color: #64748B;">User email (e.g. admin@bluezone.com)</span>
                        </div>
                    </div>
                </div>

                <!-- Task Fields Section -->
                <div>
                    <strong style="font-size: 0.85rem; color: #16A34A; text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 0.5rem;">
                        <i class="fa-solid fa-list-check text-xs mr-1"></i> Attached Tasks & Follow-ups
                    </strong>
                    <div style="display: flex; flex-direction: column; gap: 0.35rem; font-size: 0.8rem;">
                        <div style="padding: 0.35rem 0.5rem; background: #F0FDF4; border-radius: 0.35rem; display: flex; justify-content: space-between;">
                            <code>task_subject</code>
                            <span style="color: #166534; font-weight: 600;">Title of initial action</span>
                        </div>
                        <div style="padding: 0.35rem 0.5rem; background: #F0FDF4; border-radius: 0.35rem; display: flex; justify-content: space-between;">
                            <code>task_type</code>
                            <span style="color: #166534;">call, meeting, task, email</span>
                        </div>
                        <div style="padding: 0.35rem 0.5rem; background: #F0FDF4; border-radius: 0.35rem; display: flex; justify-content: space-between;">
                            <code>task_due_date</code>
                            <span style="color: #166534;">e.g. 2026-09-25 14:00</span>
                        </div>
                        <div style="padding: 0.35rem 0.5rem; background: #F0FDF4; border-radius: 0.35rem; display: flex; justify-content: space-between;">
                            <code>task_priority</code>
                            <span style="color: #166534;">low, normal, high, urgent</span>
                        </div>
                        <div style="padding: 0.35rem 0.5rem; background: #F0FDF4; border-radius: 0.35rem; display: flex; justify-content: space-between;">
                            <code>task_description</code>
                            <span style="color: #166534;">Notes & objectives</span>
                        </div>
                    </div>
                </div>

                <!-- Template Download Card inside sidebar -->
                <div style="margin-top: 1.5rem; background: #ECFDF5; border: 1px solid #A7F3D0; border-radius: 0.5rem; padding: 1rem; text-align: center;">
                    <i class="fa-solid fa-file-excel text-2xl text-emerald-600 mb-1"></i>
                    <strong style="display: block; font-size: 0.875rem; color: #065F46;">Need the exact format?</strong>
                    <p style="font-size: 0.75rem; color: #047857; margin: 0.25rem 0 0.75rem;">
                        Download our pre-populated sample sheet with genuine Blue Zone examples.
                    </p>
                    <a href="{{ route('admin.crm.leads.import.template', ['format' => 'xlsx']) }}" class="btn btn-sm btn-primary" style="background: #059669; border-color: #059669; font-size: 0.8rem; font-weight: 700; width: 100%;">
                        <i class="fa-solid fa-download mr-1"></i> Download Template (.xlsx)
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Drag & Drop JavaScript handlers -->
    <script>
        function handleDragOver(ev) {
            ev.preventDefault();
            document.getElementById('dropzoneContainer').style.borderColor = '#0284C7';
            document.getElementById('dropzoneContainer').style.background = '#EFF6FF';
        }

        function handleDragLeave(ev) {
            ev.preventDefault();
            document.getElementById('dropzoneContainer').style.borderColor = '#CBD5E1';
            document.getElementById('dropzoneContainer').style.background = '#F8FAFC';
        }

        function handleFileDrop(ev) {
            ev.preventDefault();
            handleDragLeave(ev);
            if (ev.dataTransfer.files && ev.dataTransfer.files.length > 0) {
                const fileInput = document.getElementById('sheetFileInput');
                fileInput.files = ev.dataTransfer.files;
                handleFileSelected(fileInput);
            }
        }

        function handleFileSelected(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                document.getElementById('dropzoneDefaultState').style.display = 'none';
                document.getElementById('dropzoneSelectedState').style.display = 'block';
                document.getElementById('selectedFileName').innerText = file.name;
                document.getElementById('selectedFileSize').innerText = (file.size / 1024).toFixed(1) + ' KB';
            }
        }

        function resetFileSelection() {
            const input = document.getElementById('sheetFileInput');
            input.value = '';
            document.getElementById('dropzoneDefaultState').style.display = 'block';
            document.getElementById('dropzoneSelectedState').style.display = 'none';
        }
    </script>
</x-layouts.admin>

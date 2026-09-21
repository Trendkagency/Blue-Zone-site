<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'أرشيف ومستندات الموظفين' : 'Employee Documents Vault'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'إدارة المستندات الرسمية، الهويات، العقود، والشهادات وتتبع الصلاحيات' : 'Centralized employee documents, IDs, contracts, certs and expiration tracker'"
>
    <!-- Header Actions -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0; color: var(--text-color, #1E293B);">
                <i class="fa-solid fa-folder-open text-violet-500 mr-2 ml-2"></i> {{ app()->getLocale() === 'ar' ? 'أرشيف المستندات' : 'Employee Documents' }}
            </h2>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ route('admin.hr.employees.documents.index', ['expiring_soon' => 1]) }}" class="btn btn-outline btn-sm" style="color: #EA580C; border-color: #FDBA74;">
                <i class="fa-solid fa-triangle-exclamation text-xs mr-1 ml-1"></i> Expiring Soon (&le;30d)
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="card" style="padding: 1rem 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0); margin-bottom: 1.5rem;">
        <form action="{{ route('admin.hr.employees.documents.index') }}" method="GET" style="display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: flex-end;">
            <div style="flex: 1; min-width: 200px;">
                <label style="font-size: 0.75rem; font-weight: 600; color: #64748B; display: block; margin-bottom: 0.25rem;">Search Title, Doc #, or Employee</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Title, Employee name..." class="form-control" style="width: 100%; padding: 0.45rem 0.75rem; border: 1px solid #CBD5E1; border-radius: 0.375rem; font-size: 0.85rem;">
            </div>

            <div style="width: 180px;">
                <label style="font-size: 0.75rem; font-weight: 600; color: #64748B; display: block; margin-bottom: 0.25rem;">Document Type</label>
                <select name="document_type" class="form-control" style="width: 100%; padding: 0.45rem 0.75rem; border: 1px solid #CBD5E1; border-radius: 0.375rem; font-size: 0.85rem;">
                    <option value="">All Types</option>
                    <option value="national_id" {{ request('document_type') === 'national_id' ? 'selected' : '' }}>National ID / Iqama</option>
                    <option value="passport" {{ request('document_type') === 'passport' ? 'selected' : '' }}>Passport</option>
                    <option value="contract" {{ request('document_type') === 'contract' ? 'selected' : '' }}>Contract</option>
                    <option value="education_certificate" {{ request('document_type') === 'education_certificate' ? 'selected' : '' }}>Education Cert</option>
                    <option value="medical_certificate" {{ request('document_type') === 'medical_certificate' ? 'selected' : '' }}>Medical / Insurance</option>
                </select>
            </div>

            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="btn btn-secondary btn-sm" style="height: 34px;">Filter</button>
                <a href="{{ route('admin.hr.employees.documents.index') }}" class="btn btn-ghost btn-sm" style="height: 34px;">Clear</a>
            </div>
        </form>
    </div>

    <!-- Documents Table -->
    <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
        <div class="table-responsive">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                <thead>
                    <tr style="border-bottom: 2px solid #E2E8F0; text-align: left; color: #64748B;">
                        <th style="padding: 0.75rem 0.5rem;">Document</th>
                        <th style="padding: 0.75rem 0.5rem;">Employee</th>
                        <th style="padding: 0.75rem 0.5rem;">Type</th>
                        <th style="padding: 0.75rem 0.5rem;">Doc #</th>
                        <th style="padding: 0.75rem 0.5rem;">Expiry Date</th>
                        <th style="padding: 0.75rem 0.5rem;">Status</th>
                        <th style="padding: 0.75rem 0.5rem; text-align: right;">Download</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($documents as $doc)
                        @php
                            $isExpiring = $doc->expiry_date && \Carbon\Carbon::parse($doc->expiry_date)->diffInDays(now()) <= 30 && !\Carbon\Carbon::parse($doc->expiry_date)->isPast();
                            $isExpired = $doc->expiry_date && \Carbon\Carbon::parse($doc->expiry_date)->isPast();
                        @endphp
                        <tr style="border-bottom: 1px solid #F1F5F9;">
                            <td style="padding: 0.75rem 0.5rem; font-weight: 600;">
                                <i class="fa-solid fa-file-lines text-sky-500 mr-1 ml-1"></i> {{ $doc->title }}
                            </td>
                            <td style="padding: 0.75rem 0.5rem;">
                                <a href="{{ route('admin.hr.employees.show', $doc->employee_id) }}" style="color: #0284C7; font-weight: 600; text-decoration: none;">
                                    {{ $doc->employee?->full_name ?? 'N/A' }}
                                </a>
                                <span style="font-size: 0.75rem; color: #64748B; display: block; font-family: monospace;">{{ $doc->employee?->employee_number }}</span>
                            </td>
                            <td style="padding: 0.75rem 0.5rem; text-transform: capitalize;">{{ str_replace('_', ' ', $doc->document_type) }}</td>
                            <td style="padding: 0.75rem 0.5rem; color: #64748B;">{{ $doc->document_number ?? '—' }}</td>
                            <td style="padding: 0.75rem 0.5rem;">
                                @if($doc->expiry_date)
                                    <span style="font-weight: 700; color: {{ $isExpired ? '#EF4444' : ($isExpiring ? '#F97316' : '#10B981') }};">
                                        {{ $doc->expiry_date }}
                                        @if($isExpired) (Expired) @elseif($isExpiring) (Expiring) @endif
                                    </span>
                                @else
                                    —
                                @endif
                            </td>
                            <td style="padding: 0.75rem 0.5rem;">
                                <span class="badge" style="background: #DCFCE7; color: #166534; padding: 0.2rem 0.5rem; border-radius: 999px;">Valid</span>
                            </td>
                            <td style="padding: 0.75rem 0.5rem; text-align: right;">
                                <a href="{{ route('admin.hr.employees.documents.download', $doc->id) }}" class="btn btn-ghost btn-xs text-sky-600">
                                    <i class="fa-solid fa-download"></i> Download
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 3rem; color: #94A3B8;">No documents found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top: 1rem;">
            {{ $documents->links() }}
        </div>
    </div>
</x-layouts.admin>

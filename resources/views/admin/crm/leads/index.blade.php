<x-layouts.admin 
    :pageTitle="__('crm.leads.title')" 
    :pageSubtitle="__('crm.leads.subtitle')"
>
    <!-- Top Actions & Export -->
    <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1rem; margin-bottom: 1.5rem;">
        <form method="GET" action="{{ route('admin.crm.leads.index') }}" style="display: flex; flex-wrap: wrap; gap: 0.5rem; flex: 1;">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('crm.leads.full_name') }}, {{ __('crm.leads.email') }}, {{ __('crm.leads.phone') }}..." 
                class="form-input" style="max-width: 280px; padding: 0.45rem 0.75rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; font-size: 0.875rem;">
            
            <select name="status" class="form-select" style="padding: 0.45rem 0.75rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; font-size: 0.875rem;">
                <option value="">{{ __('crm.leads.status') }}: All</option>
                <option value="new" {{ request('status') === 'new' ? 'selected' : '' }}>{{ __('crm.status.new') }}</option>
                <option value="contacted" {{ request('status') === 'contacted' ? 'selected' : '' }}>{{ __('crm.status.contacted') }}</option>
                <option value="qualified" {{ request('status') === 'qualified' ? 'selected' : '' }}>{{ __('crm.status.qualified') }}</option>
                <option value="converted" {{ request('status') === 'converted' ? 'selected' : '' }}>{{ __('crm.status.converted') }}</option>
                <option value="lost" {{ request('status') === 'lost' ? 'selected' : '' }}>{{ __('crm.status.lost') }}</option>
            </select>

            <select name="priority" class="form-select" style="padding: 0.45rem 0.75rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; font-size: 0.875rem;">
                <option value="">{{ __('crm.leads.priority') }}: All</option>
                <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>{{ __('crm.priorities.low') }}</option>
                <option value="normal" {{ request('priority') === 'normal' ? 'selected' : '' }}>{{ __('crm.priorities.normal') }}</option>
                <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>{{ __('crm.priorities.high') }}</option>
                <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>{{ __('crm.priorities.urgent') }}</option>
            </select>

            <button type="submit" class="btn btn-secondary btn-sm" style="padding: 0.45rem 0.85rem;">
                <i class="fa-solid fa-magnifying-glass"></i> {{ __('app.actions.filter') ?? 'Filter' }}
            </button>
            @if(request()->hasAny(['search', 'status', 'priority', 'overdue']))
                <a href="{{ route('admin.crm.leads.index') }}" class="btn btn-ghost btn-sm">
                    <i class="fa-solid fa-xmark"></i> {{ __('app.actions.reset') ?? 'Reset' }}
                </a>
            @endif
        </form>

        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
            <a href="{{ route('admin.crm.leads.import') }}" class="btn btn-secondary btn-sm" style="display: inline-flex; align-items: center; gap: 0.4rem; font-weight: 600;">
                <i class="fa-solid fa-file-excel text-emerald-600"></i> Import Excel / CSV
            </a>
            <a href="{{ route('admin.crm.leads.export', request()->query()) }}" class="btn btn-outline btn-sm" style="display: inline-flex; align-items: center; gap: 0.4rem;">
                <i class="fa-solid fa-file-csv text-emerald-600"></i> {{ __('crm.leads.export_csv') }}
            </a>
            <a href="{{ route('admin.crm.leads.create') }}" class="btn btn-primary btn-sm" style="display: inline-flex; align-items: center; gap: 0.4rem;">
                <i class="fa-solid fa-plus text-xs"></i> {{ __('crm.dashboard.create_lead') }}
            </a>
        </div>
    </div>

    <!-- Bulk Actions Form -->
    <form id="bulkActionForm" method="POST" action="{{ route('admin.crm.leads.bulk-assign') }}">
        @csrf
        <div id="bulkActionBar" style="display: none; align-items: center; justify-content: space-between; background: #EEF2FF; padding: 0.75rem 1rem; border-radius: 0.5rem; margin-bottom: 1rem; border: 1px solid #C7D2FE;">
            <div style="font-size: 0.85rem; font-weight: 700; color: #3730A3;">
                <span id="selectedCount">0</span> leads selected
            </div>
            <div style="display: flex; gap: 0.5rem; align-items: center;">
                <select name="owner_id" class="form-select" style="font-size: 0.8rem; padding: 0.35rem 0.6rem; border-radius: 0.35rem;">
                    <option value="">-- Assign Sales Owner --</option>
                    @foreach($owners as $owner)
                        <option value="{{ $owner->id }}">{{ $owner->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary btn-xs">
                    {{ __('crm.leads.bulk_assign') }}
                </button>
            </div>
        </div>

        <!-- Leads Table Card -->
        <div class="card" style="padding: 0; border-radius: 0.75rem; overflow: hidden; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: start; font-size: 0.875rem;">
                    <thead>
                        <tr style="background: #F8FAFC; border-bottom: 1px solid #E2E8F0; color: #475569;">
                            <th style="padding: 0.85rem 1rem; width: 40px;">
                                <input type="checkbox" id="selectAllCheckbox" onclick="toggleSelectAll(this)">
                            </th>
                            <th style="padding: 0.85rem 1rem; font-weight: 700;">{{ __('crm.leads.lead_number') }}</th>
                            <th style="padding: 0.85rem 1rem; font-weight: 700;">{{ __('crm.leads.full_name') }}</th>
                            <th style="padding: 0.85rem 1rem; font-weight: 700;">{{ __('crm.leads.source') }}</th>
                            <th style="padding: 0.85rem 1rem; font-weight: 700;">{{ __('crm.leads.status') }}</th>
                            <th style="padding: 0.85rem 1rem; font-weight: 700;">{{ __('crm.leads.priority') }}</th>
                            <th style="padding: 0.85rem 1rem; font-weight: 700;">{{ __('crm.leads.owner') }}</th>
                            <th style="padding: 0.85rem 1rem; font-weight: 700;">{{ __('crm.leads.score') }}</th>
                            <th style="padding: 0.85rem 1rem; text-align: end; font-weight: 700;">{{ __('app.actions.actions') ?? 'Actions' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leads as $lead)
                            <tr style="border-bottom: 1px solid #F1F5F9; transition: background 0.15s;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='transparent'">
                                <td style="padding: 0.85rem 1rem;">
                                    <input type="checkbox" name="lead_ids[]" value="{{ $lead->id }}" class="lead-checkbox" onchange="updateSelectedCount()">
                                </td>
                                <td style="padding: 0.85rem 1rem; font-weight: 700; color: #0284C7;">
                                    <a href="{{ route('admin.crm.leads.show', $lead->id) }}" style="text-decoration: none; color: inherit;">
                                        {{ $lead->lead_number }}
                                    </a>
                                </td>
                                <td style="padding: 0.85rem 1rem;">
                                    <a href="{{ route('admin.crm.leads.show', $lead->id) }}" style="font-weight: 700; color: #0F172A; text-decoration: none; display: block;">
                                        {{ $lead->full_name }}
                                    </a>
                                    <span style="font-size: 0.75rem; color: #64748B;">
                                        {{ $lead->phone ?? $lead->email ?? 'No contact' }}
                                    </span>
                                </td>
                                <td style="padding: 0.85rem 1rem;">
                                    <span style="font-size: 0.8rem; font-weight: 600; color: #334155;">
                                        {{ $lead->source?->name ?? 'Direct' }}
                                    </span>
                                </td>
                                <td style="padding: 0.85rem 1rem;">
                                    @php
                                        $statusColors = [
                                            'new' => ['bg' => '#E0F2FE', 'text' => '#0284C7'],
                                            'contacted' => ['bg' => '#FEF3C7', 'text' => '#D97706'],
                                            'qualified' => ['bg' => '#EDE9FE', 'text' => '#7C3AED'],
                                            'converted' => ['bg' => '#DCFCE7', 'text' => '#16A34A'],
                                            'lost' => ['bg' => '#FEE2E2', 'text' => '#DC2626'],
                                        ];
                                        $c = $statusColors[$lead->status] ?? ['bg' => '#F1F5F9', 'text' => '#475569'];
                                    @endphp
                                    <span style="display: inline-block; font-size: 0.75rem; font-weight: 700; padding: 0.2rem 0.6rem; border-radius: 9999px; background: {{ $c['bg'] }}; color: {{ $c['text'] }};">
                                        {{ ucfirst($lead->status) }}
                                    </span>
                                </td>
                                <td style="padding: 0.85rem 1rem;">
                                    <span style="font-size: 0.75rem; font-weight: 700; color: {{ $lead->priority === 'urgent' ? '#DC2626' : ($lead->priority === 'high' ? '#EA580C' : '#475569') }};">
                                        <i class="fa-solid fa-flag text-xs mr-1 ml-1"></i> {{ ucfirst($lead->priority) }}
                                    </span>
                                </td>
                                <td style="padding: 0.85rem 1rem; font-size: 0.8rem; color: #334155;">
                                    {{ $lead->owner?->name ?? 'Unassigned' }}
                                </td>
                                <td style="padding: 0.85rem 1rem;">
                                    <div style="display: flex; align-items: center; gap: 0.4rem;">
                                        <span style="font-weight: 800; font-size: 0.8rem; color: {{ $lead->score >= 60 ? '#16A34A' : ($lead->score >= 30 ? '#D97706' : '#64748B') }};">
                                            {{ $lead->score }}/100
                                        </span>
                                    </div>
                                </td>
                                <td style="padding: 0.85rem 1rem; text-align: end;">
                                    <div style="display: inline-flex; gap: 0.35rem;">
                                        <a href="{{ route('admin.crm.leads.show', $lead->id) }}" class="btn btn-ghost btn-xs" title="{{ __('app.actions.view') ?? 'View' }}">
                                            <i class="fa-solid fa-eye text-sky-600"></i>
                                        </a>
                                        <a href="{{ route('admin.crm.leads.edit', $lead->id) }}" class="btn btn-ghost btn-xs" title="{{ __('app.actions.edit') ?? 'Edit' }}">
                                            <i class="fa-solid fa-pen-to-square text-amber-600"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" style="text-align: center; padding: 3rem; color: #94A3B8;">
                                    <i class="fa-solid fa-user-xmark text-4xl mb-2"></i>
                                    <p style="font-size: 0.95rem; margin: 0;">{{ __('crm.leads.duplicate_notice') ?? 'No leads found matching criteria.' }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($leads->hasPages())
                <div style="padding: 1rem; border-top: 1px solid #E2E8F0;">
                    {{ $leads->links() }}
                </div>
            @endif
        </div>
    </form>

    <script>
        function toggleSelectAll(master) {
            const boxes = document.querySelectorAll('.lead-checkbox');
            boxes.forEach(b => b.checked = master.checked);
            updateSelectedCount();
        }

        function updateSelectedCount() {
            const selected = document.querySelectorAll('.lead-checkbox:checked');
            const count = selected.length;
            const bar = document.getElementById('bulkActionBar');
            const countLabel = document.getElementById('selectedCount');

            countLabel.textContent = count;
            if (count > 0) {
                bar.style.display = 'flex';
            } else {
                bar.style.display = 'none';
            }
        }
    </script>
</x-layouts.admin>

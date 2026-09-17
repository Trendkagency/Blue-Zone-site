<x-layouts.admin 
    :pageTitle="$opportunity->name . ' (' . $opportunity->opportunity_number . ')'" 
    :pageSubtitle="__('crm.opportunities.subtitle')"
>
    <!-- Visual Pipeline Progress Bar -->
    <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0); margin-bottom: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <div style="font-weight: 700; font-size: 0.95rem; color: #0F172A;">
                <i class="fa-solid fa-route text-indigo-500 mr-2 ml-2"></i> {{ $opportunity->pipeline?->name }}
            </div>
            <div>
                <span style="font-weight: 800; font-size: 1.1rem; color: #059669;">
                    {{ number_format($opportunity->value, 2) }} {{ $opportunity->currency ?? currency_code() }}
                </span>
                <span style="font-size: 0.8rem; color: #64748B; margin-left: 0.5rem; margin-right: 0.5rem;">
                    (Weighted: {{ number_format($opportunity->weighted_value, 2) }} {{ $opportunity->currency ?? currency_code() }})
                </span>
            </div>
        </div>

        <!-- Stage Steps -->
        <div style="display: flex; gap: 0.5rem; overflow-x: auto; padding: 0.5rem 0;">
            @foreach($opportunity->pipeline->stages as $st)
                @php
                    $isCurrent = $opportunity->stage_id == $st->id;
                    $isPassed = $st->sort_order < ($opportunity->stage?->sort_order ?? 0);
                @endphp
                <button type="button" 
                        onclick="updateOppStage({{ $st->id }})"
                        style="flex: 1; min-width: 120px; padding: 0.5rem 0.75rem; border-radius: 0.5rem; border: 1px solid {{ $isCurrent ? '#0284C7' : '#E2E8F0' }}; background: {{ $isCurrent ? '#E0F2FE' : ($isPassed ? '#F0FDF4' : '#F8FAFC') }}; color: {{ $isCurrent ? '#0284C7' : ($isPassed ? '#16A34A' : '#64748B') }}; font-size: 0.8rem; font-weight: 700; cursor: pointer; text-align: center; transition: all 0.15s;">
                    {{ $st->name }} ({{ $st->probability }}%)
                </button>
            @endforeach
        </div>
    </div>

    <!-- Main Detail Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem;">
        <!-- Left: Deal Dossier -->
        <div class="card" style="padding: 1.5rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
            <h3 style="font-size: 1rem; font-weight: 700; margin-bottom: 1.25rem; border-bottom: 1px solid #F1F5F9; padding-bottom: 0.5rem;">
                Deal Overview
            </h3>

            <div style="display: flex; flex-direction: column; gap: 0.75rem; font-size: 0.875rem;">
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #64748B;">{{ __('crm.opportunities.deal_number') }}:</span>
                    <strong style="color: #0284C7;">{{ $opportunity->opportunity_number }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #64748B;">{{ __('crm.opportunities.status') }}:</span>
                    <span style="font-weight: 700; text-transform: uppercase; color: {{ $opportunity->status === 'won' ? '#16A34A' : ($opportunity->status === 'lost' ? '#DC2626' : '#0284C7') }};">
                        {{ $opportunity->status }}
                    </span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #64748B;">{{ __('crm.opportunities.customer') }}:</span>
                    <span style="font-weight: 600; color: #0F172A;">
                        @if($opportunity->customer)
                            <a href="{{ route('admin.customers.crm-360', $opportunity->customer_id) }}" style="color: #0284C7; text-decoration: none;">
                                {{ $opportunity->customer->name }}
                            </a>
                        @else
                            N/A
                        @endif
                    </span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #64748B;">{{ __('crm.opportunities.company') }}:</span>
                    <span style="font-weight: 600; color: #0F172A;">
                        {{ $opportunity->company?->name ?? 'Individual' }}
                    </span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #64748B;">{{ __('crm.opportunities.owner') }}:</span>
                    <span style="font-weight: 600; color: #0F172A;">
                        {{ $opportunity->owner?->name ?? 'Unassigned' }}
                    </span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #64748B;">{{ __('crm.opportunities.expected_close') }}:</span>
                    <span style="font-weight: 600; color: #0F172A;">
                        {{ $opportunity->expected_close_date?->format('Y-m-d') ?? 'N/A' }}
                    </span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #64748B;">{{ __('crm.leads.campaign') }}:</span>
                    <span style="font-weight: 600; color: #0F172A;">
                        {{ $opportunity->campaign?->name ?? 'Direct' }}
                    </span>
                </div>
            </div>

            @if($opportunity->description)
                <div style="margin-top: 1.25rem; padding-top: 1rem; border-top: 1px solid #F1F5F9;">
                    <span style="font-size: 0.8rem; font-weight: 700; color: #64748B;">Deal Notes / Scope:</span>
                    <p style="font-size: 0.85rem; color: #334155; margin: 0.35rem 0 0;">{{ $opportunity->description }}</p>
                </div>
            @endif
        </div>

        <!-- Right: Tasks & Activities -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <div class="card" style="padding: 1.5rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; border-bottom: 1px solid #F1F5F9; padding-bottom: 0.5rem;">
                    <h3 style="font-size: 1rem; font-weight: 700; margin: 0;">
                        <i class="fa-solid fa-list-check text-emerald-500 mr-2 ml-2"></i> Tasks & Follow-ups
                    </h3>
                    <a href="{{ route('admin.crm.activities.create') }}?opportunity_id={{ $opportunity->id }}" class="btn btn-ghost btn-xs text-sky-600">
                        <i class="fa-solid fa-plus text-xs"></i> Add Task
                    </a>
                </div>

                @forelse($opportunity->activities as $act)
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.65rem 0.75rem; border-radius: 0.5rem; background: #F8FAFC; border: 1px solid #E2E8F0; margin-bottom: 0.5rem;">
                        <div>
                            <span style="font-weight: 700; font-size: 0.85rem; color: #0F172A;">{{ $act->subject }}</span>
                            <div style="font-size: 0.75rem; color: #64748B;">
                                Due: {{ $act->due_at?->format('Y-m-d H:i') ?? 'N/A' }} &bull; {{ $act->assignee?->name ?? 'Staff' }}
                            </div>
                        </div>
                        <span style="font-size: 0.75rem; font-weight: 700; padding: 0.2rem 0.5rem; border-radius: 9999px; background: {{ $act->status === 'completed' ? '#DCFCE7' : '#FEF3C7' }}; color: {{ $act->status === 'completed' ? '#16A34A' : '#D97706' }};">
                            {{ ucfirst($act->status) }}
                        </span>
                    </div>
                @empty
                    <p style="font-size: 0.85rem; color: #94A3B8; text-align: center; margin: 1rem 0;">No scheduled tasks for this deal.</p>
                @endforelse
            </div>
        </div>
    </div>

    <script>
        function updateOppStage(stageId) {
            if (!confirm('Move this opportunity to the selected stage?')) return;

            fetch(`{{ url('admin/crm/opportunities') }}/{{ $opportunity->id }}/stage`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ stage_id: stageId })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message || 'Error updating stage.');
                }
            })
            .catch(() => alert('Network error updating stage.'));
        }
    </script>
</x-layouts.admin>

<x-layouts.admin 
    :pageTitle="__('crm.opportunities.title')" 
    :pageSubtitle="__('crm.opportunities.subtitle')"
>
    <!-- Top Filter Bar & View Switcher -->
    <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1rem; margin-bottom: 1.5rem;">
        <form method="GET" action="{{ route('admin.crm.opportunities.index') }}" style="display: flex; flex-wrap: wrap; gap: 0.5rem; align-items: center;">
            <input type="hidden" name="view" value="{{ $view }}">
            
            <label style="font-size: 0.85rem; font-weight: 700; color: #334155;">{{ __('crm.leads.pipeline') }}:</label>
            <select name="pipeline_id" onchange="this.form.submit()" class="form-select" style="padding: 0.45rem 0.75rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; font-size: 0.875rem;">
                @foreach($pipelines as $p)
                    <option value="{{ $p->id }}" {{ $selectedPipelineId == $p->id ? 'selected' : '' }}>
                        {{ $p->name }}
                    </option>
                @endforeach
            </select>
        </form>

        <div style="display: flex; gap: 0.5rem; align-items: center;">
            <!-- View Switcher -->
            <div style="display: inline-flex; border: 1px solid #CBD5E1; border-radius: 0.5rem; overflow: hidden; background: #ffffff;">
                <a href="{{ route('admin.crm.opportunities.index', ['view' => 'kanban', 'pipeline_id' => $selectedPipelineId]) }}" 
                   class="btn btn-xs {{ $view === 'kanban' ? 'btn-primary' : 'btn-ghost' }}" style="border-radius: 0; padding: 0.4rem 0.75rem;">
                    <i class="fa-solid fa-table-columns mr-1 ml-1"></i> {{ __('crm.opportunities.kanban_view') }}
                </a>
                <a href="{{ route('admin.crm.opportunities.index', ['view' => 'table', 'pipeline_id' => $selectedPipelineId]) }}" 
                   class="btn btn-xs {{ $view === 'table' ? 'btn-primary' : 'btn-ghost' }}" style="border-radius: 0; padding: 0.4rem 0.75rem;">
                    <i class="fa-solid fa-list mr-1 ml-1"></i> {{ __('crm.opportunities.table_view') }}
                </a>
            </div>

            <a href="{{ route('admin.crm.opportunities.create') }}" class="btn btn-primary btn-sm" style="display: inline-flex; align-items: center; gap: 0.4rem;">
                <i class="fa-solid fa-plus text-xs"></i> {{ __('crm.dashboard.create_opportunity') }}
            </a>
        </div>
    </div>

    @if($view === 'kanban')
        <!-- Pipeline Summary Banner -->
        <div style="display: flex; flex-wrap: wrap; justify-content: space-between; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0); padding: 0.85rem 1.25rem; border-radius: 0.75rem; margin-bottom: 1.5rem; font-size: 0.875rem;">
            <div>
                <span style="color: #64748B;">Total Pipeline Value:</span>
                <strong style="color: #0F172A; font-size: 1.05rem; margin-left: 0.35rem; margin-right: 0.35rem;">{{ number_format($board['total_value'], 2) }} {{ currency_code() }}</strong>
            </div>
            <div>
                <span style="color: #64748B;">Weighted Forecast:</span>
                <strong style="color: #059669; font-size: 1.05rem; margin-left: 0.35rem; margin-right: 0.35rem;">{{ number_format($board['total_weighted'], 2) }} {{ currency_code() }}</strong>
            </div>
            <div style="color: #94A3B8; font-size: 0.8rem; display: flex; align-items: center; gap: 0.35rem;">
                <i class="fa-solid fa-circle-info"></i> {{ __('crm.opportunities.drag_drop_notice') }}
            </div>
        </div>

        <!-- Kanban Board Horizontal Scroll Area -->
        <div style="display: flex; gap: 1rem; overflow-x: auto; padding-bottom: 1.5rem; min-height: 550px;">
            @foreach($board['stages'] as $stData)
                @php $stage = $stData['stage']; @endphp
                <div class="kanban-column" 
                     data-stage-id="{{ $stage->id }}" 
                     ondragover="allowDrop(event)" 
                     ondrop="dropOpportunity(event, {{ $stage->id }})"
                     style="flex: 0 0 300px; background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 0.75rem; display: flex; flex-direction: column; max-height: 750px;">
                    
                    <!-- Stage Header -->
                    <div style="padding: 0.85rem 1rem; border-bottom: 2px solid {{ $stage->color ?? '#0284C7' }}; background: #ffffff; border-top-left-radius: 0.75rem; border-top-right-radius: 0.75rem; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <strong style="font-size: 0.9rem; color: #0F172A;">{{ $stage->name }}</strong>
                            <div style="font-size: 0.75rem; color: #64748B;">
                                {{ number_format($stData['total_value'], 2) }} {{ currency_code() }}
                            </div>
                        </div>
                        <span style="background: #E2E8F0; color: #334155; font-size: 0.75rem; font-weight: 700; padding: 0.15rem 0.5rem; border-radius: 9999px;">
                            {{ $stData['count'] }}
                        </span>
                    </div>

                    <!-- Cards Container -->
                    <div class="kanban-cards" style="padding: 0.75rem; display: flex; flex-direction: column; gap: 0.75rem; overflow-y: auto; flex: 1;">
                        @foreach($stData['opportunities'] as $opp)
                            <div class="kanban-card" 
                                 id="opp-card-{{ $opp->id }}"
                                 draggable="true" 
                                 ondragstart="dragOpportunity(event, {{ $opp->id }})"
                                 style="background: #ffffff; border: 1px solid #CBD5E1; border-radius: 0.5rem; padding: 0.85rem; box-shadow: 0 1px 3px 0 rgba(0,0,0,0.05); cursor: grab; transition: transform 0.15s, box-shadow 0.15s;">
                                
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.35rem;">
                                    <span style="font-size: 0.75rem; font-weight: 700; color: #0284C7;">{{ $opp->opportunity_number }}</span>
                                    <span style="font-size: 0.7rem; font-weight: 700; color: #64748B; background: #F1F5F9; padding: 0.1rem 0.35rem; border-radius: 0.25rem;">
                                        {{ $opp->probability }}%
                                    </span>
                                </div>

                                <a href="{{ route('admin.crm.opportunities.show', $opp->id) }}" style="font-weight: 700; font-size: 0.875rem; color: #0F172A; text-decoration: none; display: block; margin-bottom: 0.5rem;">
                                    {{ $opp->name }}
                                </a>

                                <div style="font-size: 0.8rem; color: #64748B; margin-bottom: 0.5rem;">
                                    <i class="fa-solid fa-user text-xs mr-1 ml-1"></i> {{ $opp->customer?->name ?? $opp->company?->name ?? 'Direct Account' }}
                                </div>

                                <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #F1F5F9; padding-top: 0.5rem;">
                                    <strong style="font-size: 0.875rem; color: #059669;">
                                        {{ number_format($opp->value, 2) }} {{ currency_code() }}
                                    </strong>
                                    <span style="font-size: 0.75rem; color: #64748B;">
                                        {{ $opp->owner?->name ?? 'Staff' }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <script>
            let draggedOppId = null;

            function dragOpportunity(ev, oppId) {
                draggedOppId = oppId;
                ev.dataTransfer.setData("text/plain", oppId);
                ev.target.style.opacity = '0.5';
            }

            function allowDrop(ev) {
                ev.preventDefault();
            }

            function dropOpportunity(ev, targetStageId) {
                ev.preventDefault();
                if (!draggedOppId) return;

                const card = document.getElementById('opp-card-' + draggedOppId);
                if (card) card.style.opacity = '1';

                // Send server-side AJAX stage update
                fetch(`{{ url('admin/crm/opportunities') }}/${draggedOppId}/stage`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ stage_id: targetStageId })
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert(data.message || 'Error updating stage.');
                    }
                })
                .catch(() => {
                    alert('Network error moving opportunity.');
                });
            }
        </script>

    @else
        <!-- Table View -->
        <div class="card" style="padding: 0; border-radius: 0.75rem; overflow: hidden; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: start; font-size: 0.875rem;">
                    <thead>
                        <tr style="background: #F8FAFC; border-bottom: 1px solid #E2E8F0; color: #475569;">
                            <th style="padding: 0.85rem 1rem; font-weight: 700;">{{ __('crm.opportunities.deal_number') }}</th>
                            <th style="padding: 0.85rem 1rem; font-weight: 700;">{{ __('crm.opportunities.name') }}</th>
                            <th style="padding: 0.85rem 1rem; font-weight: 700;">{{ __('crm.opportunities.customer') }}</th>
                            <th style="padding: 0.85rem 1rem; font-weight: 700;">{{ __('crm.opportunities.stage') }}</th>
                            <th style="padding: 0.85rem 1rem; font-weight: 700;">{{ __('crm.opportunities.value') }}</th>
                            <th style="padding: 0.85rem 1rem; font-weight: 700;">{{ __('crm.opportunities.probability') }}</th>
                            <th style="padding: 0.85rem 1rem; font-weight: 700;">{{ __('crm.opportunities.owner') }}</th>
                            <th style="padding: 0.85rem 1rem; text-align: end; font-weight: 700;">{{ __('app.actions.actions') ?? 'Actions' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($opportunities as $opp)
                            <tr style="border-bottom: 1px solid #F1F5F9;">
                                <td style="padding: 0.85rem 1rem; font-weight: 700; color: #0284C7;">
                                    <a href="{{ route('admin.crm.opportunities.show', $opp->id) }}" style="text-decoration: none; color: inherit;">
                                        {{ $opp->opportunity_number }}
                                    </a>
                                </td>
                                <td style="padding: 0.85rem 1rem; font-weight: 700; color: #0F172A;">
                                    {{ $opp->name }}
                                </td>
                                <td style="padding: 0.85rem 1rem; font-size: 0.8rem; color: #334155;">
                                    {{ $opp->customer?->name ?? $opp->company?->name ?? 'Direct' }}
                                </td>
                                <td style="padding: 0.85rem 1rem;">
                                    <span style="font-size: 0.75rem; font-weight: 700; padding: 0.2rem 0.6rem; border-radius: 9999px; background: #EDE9FE; color: #7C3AED;">
                                        {{ $opp->stage?->name }}
                                    </span>
                                </td>
                                <td style="padding: 0.85rem 1rem; font-weight: 800; color: #059669;">
                                    {{ number_format($opp->value, 2) }} {{ currency_code() }}
                                </td>
                                <td style="padding: 0.85rem 1rem; font-weight: 700; color: #64748B;">
                                    {{ $opp->probability }}%
                                </td>
                                <td style="padding: 0.85rem 1rem; font-size: 0.8rem; color: #334155;">
                                    {{ $opp->owner?->name ?? 'Unassigned' }}
                                </td>
                                <td style="padding: 0.85rem 1rem; text-align: end;">
                                    <a href="{{ route('admin.crm.opportunities.show', $opp->id) }}" class="btn btn-ghost btn-xs">
                                        <i class="fa-solid fa-eye text-sky-600"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 3rem; color: #94A3B8;">
                                    No opportunities found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($opportunities->hasPages())
                <div style="padding: 1rem; border-top: 1px solid #E2E8F0;">
                    {{ $opportunities->links() }}
                </div>
            @endif
        </div>
    @endif
</x-layouts.admin>

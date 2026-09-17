<x-layouts.admin 
    :pageTitle="$lead->full_name . ' (' . $lead->lead_number . ')'" 
    :pageSubtitle="__('crm.leads.subtitle')"
>
    <!-- Header Action Bar -->
    <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1rem; margin-bottom: 1.5rem; background: var(--card-bg, #ffffff); padding: 1rem 1.25rem; border-radius: 0.75rem; border: 1px solid var(--border-color, #E2E8F0);">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
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
            <span style="font-size: 0.85rem; font-weight: 700; padding: 0.3rem 0.75rem; border-radius: 9999px; background: {{ $c['bg'] }}; color: {{ $c['text'] }};">
                {{ ucfirst($lead->status) }}
            </span>
            <span style="font-size: 0.85rem; font-weight: 700; color: {{ $lead->priority === 'urgent' ? '#DC2626' : ($lead->priority === 'high' ? '#EA580C' : '#64748B') }};">
                <i class="fa-solid fa-flag text-xs"></i> {{ ucfirst($lead->priority) }}
            </span>
            <span style="font-size: 0.85rem; font-weight: 700; color: #0284C7; background: #E0F2FE; padding: 0.2rem 0.5rem; border-radius: 0.35rem;">
                Score: {{ $lead->score }}/100
            </span>
        </div>

        <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
            @if($lead->status !== 'converted')
                <button type="button" class="btn btn-success btn-sm" onclick="document.getElementById('convertLeadModal').style.display='flex'" style="display: inline-flex; align-items: center; gap: 0.4rem;">
                    <i class="fa-solid fa-award text-xs"></i> {{ __('crm.leads.convert') }}
                </button>
            @else
                @if($lead->customer_id)
                    <a href="{{ route('admin.customers.crm-360', $lead->customer_id) }}" class="btn btn-outline btn-sm">
                        <i class="fa-solid fa-user-check text-emerald-600"></i> {{ __('crm.customer_360.title') }}
                    </a>
                @endif
            @endif

            <a href="{{ route('admin.crm.leads.edit', $lead->id) }}" class="btn btn-secondary btn-sm" style="display: inline-flex; align-items: center; gap: 0.4rem;">
                <i class="fa-solid fa-pen-to-square text-xs"></i> {{ __('app.actions.edit') ?? 'Edit' }}
            </a>

            <form action="{{ route('admin.crm.leads.destroy', $lead->id) }}" method="POST" onsubmit="return confirm('Move this lead to trash?');" style="margin: 0;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-ghost btn-sm text-red-600">
                    <i class="fa-solid fa-trash-can text-xs"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- Duplicate Warning Box (if detected) -->
    @if(!empty($duplicates['has_duplicates']))
        <div style="background: #FEF2F2; border: 1px solid #FCA5A5; border-radius: 0.75rem; padding: 1rem 1.25rem; margin-bottom: 1.5rem;">
            <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                <i class="fa-solid fa-triangle-exclamation text-red-600 text-xl mt-0.5"></i>
                <div>
                    <strong style="color: #991B1B; font-size: 0.95rem;">{{ __('crm.leads.duplicate_warning') }}</strong>
                    <p style="margin: 0.25rem 0 0.5rem; color: #7F1D1D; font-size: 0.85rem;">{{ __('crm.leads.duplicate_notice') }}</p>
                    <ul style="margin: 0; padding-inline-start: 1.25rem; font-size: 0.8rem; color: #991B1B;">
                        @foreach($duplicates['existing_customers'] as $c)
                            <li>Existing Customer: <a href="{{ route('admin.customers.crm-360', $c->id) }}" style="font-weight: 700; color: #7F1D1D; text-decoration: underline;">{{ $c->name }}</a> ({{ $c->email }} &bull; {{ $c->phone }})</li>
                        @endforeach
                        @foreach($duplicates['duplicate_leads'] as $l)
                            <li>Existing Lead: <a href="{{ route('admin.crm.leads.show', $l->id) }}" style="font-weight: 700; color: #7F1D1D; text-decoration: underline;">{{ $l->lead_number }} - {{ $l->full_name }}</a> ({{ $l->status }})</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem;">
        <!-- Left Column: Lead Dossier -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <!-- Profile Overview -->
            <div class="card" style="padding: 1.5rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
                <h3 style="font-size: 1rem; font-weight: 700; margin-bottom: 1rem; border-bottom: 1px solid #F1F5F9; padding-bottom: 0.5rem;">
                    <i class="fa-solid fa-address-card text-sky-500 mr-2 ml-2"></i> Lead Details
                </h3>

                <div style="display: flex; flex-direction: column; gap: 0.75rem; font-size: 0.875rem;">
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #64748B;">{{ __('crm.leads.email') }}:</span>
                        <span style="font-weight: 600; color: #0F172A;">{{ $lead->email ?? 'N/A' }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #64748B;">{{ __('crm.leads.phone') }}:</span>
                        <span style="font-weight: 600; color: #0F172A;">{{ $lead->phone ?? 'N/A' }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #64748B;">{{ __('crm.leads.company_name') }}:</span>
                        <span style="font-weight: 600; color: #0F172A;">{{ $lead->company_name ?? 'Individual' }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #64748B;">{{ __('crm.leads.job_title') }}:</span>
                        <span style="font-weight: 600; color: #0F172A;">{{ $lead->job_title ?? 'N/A' }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #64748B;">{{ __('app.country') ?? 'Country' }}:</span>
                        <span style="font-weight: 600; color: #0F172A;">{{ $lead->country?->name_en ?? 'N/A' }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #64748B;">{{ __('crm.leads.source') }}:</span>
                        <span style="font-weight: 600; color: #0F172A;">{{ $lead->source?->name ?? 'Direct' }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #64748B;">{{ __('crm.leads.campaign') }}:</span>
                        <span style="font-weight: 600; color: #0F172A;">{{ $lead->campaign?->name ?? 'Organic' }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #64748B;">{{ __('crm.leads.owner') }}:</span>
                        <span style="font-weight: 600; color: #0F172A;">{{ $lead->owner?->name ?? 'Unassigned' }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #64748B;">{{ __('crm.leads.estimated_value') }}:</span>
                        <span style="font-weight: 800; color: #059669;">{{ number_format($lead->estimated_value ?? 0, 2) }} {{ $lead->currency ?? currency_code() }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #64748B;">{{ __('crm.leads.next_follow_up') }}:</span>
                        <span style="font-weight: 600; color: {{ $lead->next_follow_up_at && $lead->next_follow_up_at->isPast() ? '#DC2626' : '#0F172A' }};">
                            {{ $lead->next_follow_up_at?->format('Y-m-d H:i') ?? 'None scheduled' }}
                        </span>
                    </div>
                </div>

                @if($lead->notes)
                    <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #F1F5F9;">
                        <span style="font-size: 0.8rem; font-weight: 700; color: #64748B;">Initial Notes:</span>
                        <p style="font-size: 0.85rem; color: #334155; margin: 0.35rem 0 0;">{{ $lead->notes }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Column: Timeline, Activities & Quick Notes -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <!-- Activities / Scheduled Tasks -->
            <div class="card" style="padding: 1.5rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; border-bottom: 1px solid #F1F5F9; padding-bottom: 0.5rem;">
                    <h3 style="font-size: 1rem; font-weight: 700; margin: 0;">
                        <i class="fa-solid fa-list-check text-emerald-500 mr-2 ml-2"></i> {{ __('crm.activities.title') }}
                    </h3>
                    <a href="{{ route('admin.crm.activities.create') }}?lead_id={{ $lead->id }}" class="btn btn-ghost btn-xs text-sky-600">
                        <i class="fa-solid fa-plus text-xs"></i> Add Task
                    </a>
                </div>

                @if($lead->activities->isEmpty())
                    <p style="font-size: 0.85rem; color: #94A3B8; text-align: center; margin: 1rem 0;">No activities scheduled for this lead.</p>
                @else
                    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                        @foreach($lead->activities as $act)
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.65rem 0.75rem; border-radius: 0.5rem; background: #F8FAFC; border: 1px solid #E2E8F0;">
                                <div>
                                    <span style="font-weight: 700; font-size: 0.85rem; color: #0F172A;">{{ $act->subject }}</span>
                                    <div style="font-size: 0.75rem; color: #64748B;">
                                        Due: {{ $act->due_at?->format('Y-m-d H:i') ?? 'N/A' }} &bull; Assigned: {{ $act->assignee?->name ?? 'N/A' }}
                                    </div>
                                </div>
                                <span style="font-size: 0.75rem; font-weight: 700; padding: 0.2rem 0.5rem; border-radius: 9999px; background: {{ $act->status === 'completed' ? '#DCFCE7' : '#FEF3C7' }}; color: {{ $act->status === 'completed' ? '#16A34A' : '#D97706' }};">
                                    {{ ucfirst($act->status) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Notes List -->
            <div class="card" style="padding: 1.5rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
                <h3 style="font-size: 1rem; font-weight: 700; margin-bottom: 1rem; border-bottom: 1px solid #F1F5F9; padding-bottom: 0.5rem;">
                    <i class="fa-solid fa-note-sticky text-amber-500 mr-2 ml-2"></i> Internal Notes
                </h3>

                @forelse($lead->notesList as $note)
                    <div style="padding: 0.75rem; border-radius: 0.5rem; background: #FFFBEB; border: 1px solid #FDE68A; margin-bottom: 0.75rem;">
                        <p style="font-size: 0.85rem; color: #78350F; margin: 0 0 0.35rem;">{{ $note->body }}</p>
                        <span style="font-size: 0.75rem; color: #B45309;">
                            {{ $note->user?->name ?? 'Staff' }} &bull; {{ $note->created_at->diffForHumans() }}
                        </span>
                    </div>
                @empty
                    <p style="font-size: 0.85rem; color: #94A3B8; text-align: center; margin: 1rem 0;">No internal notes recorded.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Convert Lead Modal -->
    <div id="convertLeadModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 50; padding: 1rem;">
        <div style="background: #ffffff; border-radius: 0.75rem; max-width: 500px; width: 100%; padding: 1.5rem; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; border-bottom: 1px solid #E2E8F0; padding-bottom: 0.5rem;">
                <h3 style="font-size: 1.1rem; font-weight: 700; margin: 0; color: #0F172A;">
                    <i class="fa-solid fa-award text-emerald-500 mr-1.5 ml-1.5"></i> {{ __('crm.leads.convert_modal_title') }}
                </h3>
                <button type="button" onclick="document.getElementById('convertLeadModal').style.display='none'" style="background: none; border: none; font-size: 1.25rem; cursor: pointer; color: #64748B;">&times;</button>
            </div>

            <form method="POST" action="{{ route('admin.crm.leads.convert', $lead->id) }}">
                @csrf

                <div style="margin-bottom: 1rem;">
                    <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.9rem; font-weight: 600; cursor: pointer;">
                        <input type="checkbox" name="create_company" value="1" {{ $lead->company_name ? 'checked' : '' }}>
                        {{ __('crm.leads.create_company_opt') }} ({{ $lead->company_name ?: 'B2B Account' }})
                    </label>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.9rem; font-weight: 600; cursor: pointer;">
                        <input type="checkbox" name="create_opportunity" value="1" checked onchange="document.getElementById('oppFields').style.display = this.checked ? 'block' : 'none'">
                        {{ __('crm.leads.create_opp_opt') }}
                    </label>
                </div>

                <div id="oppFields" style="background: #F8FAFC; padding: 1rem; border-radius: 0.5rem; border: 1px solid #E2E8F0; margin-bottom: 1.25rem;">
                    <div style="margin-bottom: 0.75rem;">
                        <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem;">{{ __('crm.leads.opp_name') }}</label>
                        <input type="text" name="opportunity_name" value="Deal: {{ $lead->full_name }}" class="form-input" style="width: 100%; padding: 0.45rem 0.65rem; font-size: 0.85rem; border-radius: 0.35rem; border: 1px solid #CBD5E1;">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 0.75rem;">
                        <div>
                            <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem;">{{ __('crm.leads.pipeline') }}</label>
                            <select name="pipeline_id" class="form-select" style="width: 100%; padding: 0.45rem 0.65rem; font-size: 0.85rem; border-radius: 0.35rem; border: 1px solid #CBD5E1;">
                                @foreach($pipelines as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem;">{{ __('crm.leads.opp_value') }} ({{ currency_code() }})</label>
                            <input type="number" step="0.01" name="opportunity_value" value="{{ $lead->estimated_value ?? 0 }}" class="form-input" style="width: 100%; padding: 0.45rem 0.65rem; font-size: 0.85rem; border-radius: 0.35rem; border: 1px solid #CBD5E1;">
                        </div>
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                    <button type="button" onclick="document.getElementById('convertLeadModal').style.display='none'" class="btn btn-ghost btn-sm">
                        {{ __('app.actions.cancel') ?? 'Cancel' }}
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fa-solid fa-check mr-1 ml-1"></i> Confirm Conversion
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>

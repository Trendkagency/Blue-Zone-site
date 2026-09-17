<x-layouts.admin 
    :pageTitle="__('crm.activities.title')" 
    :pageSubtitle="__('crm.activities.subtitle')"
>
    <!-- Filter Bar -->
    <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1rem; margin-bottom: 1.5rem;">
        <form method="GET" action="{{ route('admin.crm.activities.index') }}" style="display: flex; flex-wrap: wrap; gap: 0.5rem; flex: 1;">
            <select name="type" class="form-select" style="padding: 0.45rem 0.75rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; font-size: 0.875rem;">
                <option value="">{{ __('crm.activities.type') }}: All</option>
                <option value="task" {{ request('type') === 'task' ? 'selected' : '' }}>{{ __('crm.activities.types.task') }}</option>
                <option value="call" {{ request('type') === 'call' ? 'selected' : '' }}>{{ __('crm.activities.types.call') }}</option>
                <option value="meeting" {{ request('type') === 'meeting' ? 'selected' : '' }}>{{ __('crm.activities.types.meeting') }}</option>
                <option value="follow_up" {{ request('type') === 'follow_up' ? 'selected' : '' }}>{{ __('crm.activities.types.follow_up') }}</option>
            </select>

            <select name="status" class="form-select" style="padding: 0.45rem 0.75rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; font-size: 0.875rem;">
                <option value="">{{ __('crm.activities.status') }}: All</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>{{ __('crm.status.pending') }}</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>{{ __('crm.status.completed') }}</option>
                <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>{{ __('crm.status.overdue') }}</option>
            </select>

            <button type="submit" class="btn btn-secondary btn-sm" style="padding: 0.45rem 0.85rem;">
                <i class="fa-solid fa-filter"></i> {{ __('app.actions.filter') ?? 'Filter' }}
            </button>
        </form>

        <a href="{{ route('admin.crm.activities.create') }}" class="btn btn-primary btn-sm" style="display: inline-flex; align-items: center; gap: 0.4rem;">
            <i class="fa-solid fa-plus text-xs"></i> {{ __('crm.dashboard.schedule_activity') }}
        </a>
    </div>

    <!-- Activities Table Card -->
    <div class="card" style="padding: 0; border-radius: 0.75rem; overflow: hidden; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: start; font-size: 0.875rem;">
                <thead>
                    <tr style="background: #F8FAFC; border-bottom: 1px solid #E2E8F0; color: #475569;">
                        <th style="padding: 0.85rem 1rem; font-weight: 700;">{{ __('crm.activities.type') }}</th>
                        <th style="padding: 0.85rem 1rem; font-weight: 700;">{{ __('crm.activities.subject') }}</th>
                        <th style="padding: 0.85rem 1rem; font-weight: 700;">Linked To</th>
                        <th style="padding: 0.85rem 1rem; font-weight: 700;">{{ __('crm.activities.assigned_to') }}</th>
                        <th style="padding: 0.85rem 1rem; font-weight: 700;">{{ __('crm.activities.due_at') }}</th>
                        <th style="padding: 0.85rem 1rem; font-weight: 700;">{{ __('crm.activities.priority') }}</th>
                        <th style="padding: 0.85rem 1rem; font-weight: 700;">{{ __('crm.activities.status') }}</th>
                        <th style="padding: 0.85rem 1rem; text-align: end; font-weight: 700;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activities as $act)
                        <tr style="border-bottom: 1px solid #F1F5F9;">
                            <td style="padding: 0.85rem 1rem;">
                                @php
                                    $iconMap = [
                                        'task' => 'fa-list-check text-indigo-500',
                                        'call' => 'fa-phone text-emerald-500',
                                        'meeting' => 'fa-calendar-check text-sky-500',
                                        'follow_up' => 'fa-repeat text-amber-500',
                                        'email' => 'fa-envelope text-purple-500',
                                    ];
                                @endphp
                                <i class="fa-solid {{ $iconMap[$act->activity_type] ?? 'fa-circle-dot text-slate-400' }} mr-1.5 ml-1.5"></i>
                                <span style="font-weight: 600;">{{ ucfirst($act->activity_type) }}</span>
                            </td>
                            <td style="padding: 0.85rem 1rem;">
                                <strong style="color: #0F172A; display: block;">{{ $act->subject }}</strong>
                                @if($act->outcome)
                                    <span style="font-size: 0.75rem; color: #059669;">Outcome: {{ $act->outcome }}</span>
                                @endif
                            </td>
                            <td style="padding: 0.85rem 1rem; font-size: 0.8rem; color: #334155;">
                                @if($act->customer)
                                    <i class="fa-solid fa-user text-xs mr-1 ml-1 text-sky-400"></i> {{ $act->customer->name }}
                                @elseif($act->lead)
                                    <i class="fa-solid fa-bullseye text-xs mr-1 ml-1 text-indigo-400"></i> {{ $act->lead->full_name }}
                                @elseif($act->opportunity)
                                    <i class="fa-solid fa-handshake text-xs mr-1 ml-1 text-amber-400"></i> {{ $act->opportunity->name }}
                                @else
                                    Internal
                                @endif
                            </td>
                            <td style="padding: 0.85rem 1rem; font-size: 0.8rem; color: #334155;">
                                {{ $act->assignee?->name ?? 'Unassigned' }}
                            </td>
                            <td style="padding: 0.85rem 1rem; font-size: 0.8rem; color: {{ $act->is_overdue ? '#DC2626' : '#334155' }}; font-weight: {{ $act->is_overdue ? '700' : 'normal' }};">
                                {{ $act->due_at?->format('Y-m-d H:i') ?? 'N/A' }}
                                @if($act->is_overdue)
                                    <span style="font-size: 0.7rem; color: #DC2626; display: block;">(Overdue)</span>
                                @endif
                            </td>
                            <td style="padding: 0.85rem 1rem;">
                                <span style="font-size: 0.75rem; font-weight: 700; color: {{ $act->priority === 'urgent' ? '#DC2626' : ($act->priority === 'high' ? '#EA580C' : '#475569') }};">
                                    {{ ucfirst($act->priority) }}
                                </span>
                            </td>
                            <td style="padding: 0.85rem 1rem;">
                                <span style="font-size: 0.75rem; font-weight: 700; padding: 0.2rem 0.55rem; border-radius: 9999px; background: {{ $act->status === 'completed' ? '#DCFCE7' : ($act->is_overdue ? '#FEE2E2' : '#FEF3C7') }}; color: {{ $act->status === 'completed' ? '#16A34A' : ($act->is_overdue ? '#DC2626' : '#D97706') }};">
                                    {{ ucfirst($act->status) }}
                                </span>
                            </td>
                            <td style="padding: 0.85rem 1rem; text-align: end;">
                                @if($act->status !== 'completed' && $act->status !== 'cancelled')
                                    <form action="{{ route('admin.crm.activities.complete', $act->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-xs btn-outline btn-success" title="{{ __('crm.activities.complete_btn') }}">
                                            <i class="fa-solid fa-check"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 3rem; color: #94A3B8;">
                                No activities found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($activities->hasPages())
            <div style="padding: 1rem; border-top: 1px solid #E2E8F0;">
                {{ $activities->links() }}
            </div>
        @endif
    </div>
</x-layouts.admin>

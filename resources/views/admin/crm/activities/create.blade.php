<x-layouts.admin 
    :pageTitle="__('crm.dashboard.schedule_activity')" 
    :pageSubtitle="__('crm.activities.subtitle')"
>
    <form method="POST" action="{{ route('admin.crm.activities.store') }}">
        @csrf

        <div style="max-width: 650px; margin: 0 auto; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0); padding: 1.5rem; border-radius: 0.75rem;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem; color: #334155;">
                        {{ __('crm.activities.type') }} <span style="color: #DC2626;">*</span>
                    </label>
                    <select name="activity_type" required class="form-select" style="width: 100%; padding: 0.55rem 0.75rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; font-size: 0.875rem;">
                        <option value="task">{{ __('crm.activities.types.task') }}</option>
                        <option value="call">{{ __('crm.activities.types.call') }}</option>
                        <option value="meeting">{{ __('crm.activities.types.meeting') }}</option>
                        <option value="follow_up">{{ __('crm.activities.types.follow_up') }}</option>
                        <option value="email">{{ __('crm.activities.types.email') }}</option>
                        <option value="visit">{{ __('crm.activities.types.visit') }}</option>
                    </select>
                </div>

                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem; color: #334155;">
                        {{ __('crm.activities.priority') }} <span style="color: #DC2626;">*</span>
                    </label>
                    <select name="priority" required class="form-select" style="width: 100%; padding: 0.55rem 0.75rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; font-size: 0.875rem;">
                        <option value="normal">Normal</option>
                        <option value="high">High</option>
                        <option value="urgent">Urgent</option>
                        <option value="low">Low</option>
                    </select>
                </div>
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem; color: #334155;">
                    {{ __('crm.activities.subject') }} <span style="color: #DC2626;">*</span>
                </label>
                <input type="text" name="subject" required class="form-input" style="width: 100%; padding: 0.55rem 0.75rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; font-size: 0.875rem;" placeholder="e.g. Discuss Clinic Partnership Order">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem; color: #334155;">
                        {{ __('crm.activities.due_at') }}
                    </label>
                    <input type="datetime-local" name="due_at" value="{{ now()->addDays(1)->format('Y-m-d\TH:i') }}" class="form-input" style="width: 100%; padding: 0.55rem 0.75rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; font-size: 0.875rem;">
                </div>

                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem; color: #334155;">
                        {{ __('crm.activities.assigned_to') }}
                    </label>
                    <select name="assigned_to" class="form-select" style="width: 100%; padding: 0.55rem 0.75rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; font-size: 0.875rem;">
                        @foreach($assignees as $u)
                            <option value="{{ $u->id }}" {{ $u->id == auth()->id() ? 'selected' : '' }}>{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Optional Link Targets -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem; color: #334155;">
                        Link to Customer
                    </label>
                    <select name="customer_id" class="form-select" style="width: 100%; padding: 0.55rem 0.75rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; font-size: 0.875rem;">
                        <option value="">-- None --</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}" {{ request('customer_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem; color: #334155;">
                        Link to Lead
                    </label>
                    <select name="lead_id" class="form-select" style="width: 100%; padding: 0.55rem 0.75rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; font-size: 0.875rem;">
                        <option value="">-- None --</option>
                        @foreach($leads as $l)
                            <option value="{{ $l->id }}" {{ request('lead_id') == $l->id ? 'selected' : '' }}>{{ $l->lead_number }} - {{ $l->full_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem; color: #334155;">
                    {{ __('crm.activities.description') }}
                </label>
                <textarea name="description" rows="3" class="form-textarea" style="width: 100%; padding: 0.55rem 0.75rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; font-size: 0.875rem;"></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                <a href="{{ route('admin.crm.activities.index') }}" class="btn btn-ghost">
                    {{ __('app.actions.cancel') ?? 'Cancel' }}
                </a>
                <button type="submit" class="btn btn-primary" style="padding: 0.65rem 1.75rem; font-weight: 700;">
                    <i class="fa-solid fa-check mr-1 ml-1"></i> Schedule Activity
                </button>
            </div>
        </div>
    </form>
</x-layouts.admin>

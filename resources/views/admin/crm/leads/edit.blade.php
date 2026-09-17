<x-layouts.admin 
    :pageTitle="'Edit Lead: ' . $lead->lead_number" 
    :pageSubtitle="__('crm.leads.subtitle')"
>
    <form method="POST" action="{{ route('admin.crm.leads.update', $lead->id) }}">
        @csrf
        @method('PUT')

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem;">
            <!-- Left Column: Personal Details -->
            <div class="card" style="padding: 1.5rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
                <h3 style="font-size: 1rem; font-weight: 700; margin-bottom: 1.25rem; border-bottom: 1px solid #F1F5F9; padding-bottom: 0.5rem; color: #0F172A;">
                    <i class="fa-solid fa-user text-sky-500 mr-2 ml-2"></i> {{ __('crm.leads.full_name') }} & {{ __('crm.leads.email') }}
                </h3>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem; color: #334155;">
                            {{ __('crm.leads.first_name') }} <span style="color: #DC2626;">*</span>
                        </label>
                        <input type="text" name="first_name" value="{{ old('first_name', $lead->first_name) }}" required
                            class="form-input" style="width: 100%; padding: 0.55rem 0.75rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; font-size: 0.875rem;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem; color: #334155;">
                            {{ __('crm.leads.last_name') }}
                        </label>
                        <input type="text" name="last_name" value="{{ old('last_name', $lead->last_name) }}"
                            class="form-input" style="width: 100%; padding: 0.55rem 0.75rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; font-size: 0.875rem;">
                    </div>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem; color: #334155;">
                        {{ __('crm.leads.email') }}
                    </label>
                    <input type="email" name="email" value="{{ old('email', $lead->email) }}"
                        class="form-input" style="width: 100%; padding: 0.55rem 0.75rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; font-size: 0.875rem;">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem; color: #334155;">
                            {{ __('crm.leads.phone') }}
                        </label>
                        <input type="text" name="phone" value="{{ old('phone', $lead->phone) }}"
                            class="form-input" style="width: 100%; padding: 0.55rem 0.75rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; font-size: 0.875rem;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem; color: #334155;">
                            {{ __('crm.leads.secondary_phone') }}
                        </label>
                        <input type="text" name="secondary_phone" value="{{ old('secondary_phone', $lead->secondary_phone) }}"
                            class="form-input" style="width: 100%; padding: 0.55rem 0.75rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; font-size: 0.875rem;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem; color: #334155;">
                            {{ __('crm.leads.company_name') }}
                        </label>
                        <input type="text" name="company_name" value="{{ old('company_name', $lead->company_name) }}"
                            class="form-input" style="width: 100%; padding: 0.55rem 0.75rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; font-size: 0.875rem;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem; color: #334155;">
                            {{ __('crm.leads.job_title') }}
                        </label>
                        <input type="text" name="job_title" value="{{ old('job_title', $lead->job_title) }}"
                            class="form-input" style="width: 100%; padding: 0.55rem 0.75rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; font-size: 0.875rem;">
                    </div>
                </div>
            </div>

            <!-- Right Column: Status & Parameters -->
            <div class="card" style="padding: 1.5rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
                <h3 style="font-size: 1rem; font-weight: 700; margin-bottom: 1.25rem; border-bottom: 1px solid #F1F5F9; padding-bottom: 0.5rem; color: #0F172A;">
                    <i class="fa-solid fa-sliders text-indigo-500 mr-2 ml-2"></i> {{ __('crm.leads.status') }} & {{ __('crm.leads.owner') }}
                </h3>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem; color: #334155;">
                            {{ __('crm.leads.status') }} <span style="color: #DC2626;">*</span>
                        </label>
                        <select name="status" required class="form-select" style="width: 100%; padding: 0.55rem 0.75rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; font-size: 0.875rem;">
                            <option value="new" {{ old('status', $lead->status) === 'new' ? 'selected' : '' }}>{{ __('crm.status.new') }}</option>
                            <option value="contacted" {{ old('status', $lead->status) === 'contacted' ? 'selected' : '' }}>{{ __('crm.status.contacted') }}</option>
                            <option value="qualified" {{ old('status', $lead->status) === 'qualified' ? 'selected' : '' }}>{{ __('crm.status.qualified') }}</option>
                            <option value="unqualified" {{ old('status', $lead->status) === 'unqualified' ? 'selected' : '' }}>{{ __('crm.status.unqualified') }}</option>
                            <option value="converted" {{ old('status', $lead->status) === 'converted' ? 'selected' : '' }}>{{ __('crm.status.converted') }}</option>
                            <option value="lost" {{ old('status', $lead->status) === 'lost' ? 'selected' : '' }}>{{ __('crm.status.lost') }}</option>
                        </select>
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem; color: #334155;">
                            {{ __('crm.leads.priority') }} <span style="color: #DC2626;">*</span>
                        </label>
                        <select name="priority" required class="form-select" style="width: 100%; padding: 0.55rem 0.75rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; font-size: 0.875rem;">
                            <option value="low" {{ old('priority', $lead->priority) === 'low' ? 'selected' : '' }}>{{ __('crm.priorities.low') }}</option>
                            <option value="normal" {{ old('priority', $lead->priority) === 'normal' ? 'selected' : '' }}>{{ __('crm.priorities.normal') }}</option>
                            <option value="high" {{ old('priority', $lead->priority) === 'high' ? 'selected' : '' }}>{{ __('crm.priorities.high') }}</option>
                            <option value="urgent" {{ old('priority', $lead->priority) === 'urgent' ? 'selected' : '' }}>{{ __('crm.priorities.urgent') }}</option>
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem; color: #334155;">
                            {{ __('crm.leads.owner') }}
                        </label>
                        <select name="owner_id" class="form-select" style="width: 100%; padding: 0.55rem 0.75rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; font-size: 0.875rem;">
                            <option value="">-- Assign Owner --</option>
                            @foreach($owners as $u)
                                <option value="{{ $u->id }}" {{ old('owner_id', $lead->owner_id) == $u->id ? 'selected' : '' }}>
                                    {{ $u->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem; color: #334155;">
                            {{ __('crm.leads.estimated_value') }} ({{ currency_code() }})
                        </label>
                        <input type="number" step="0.01" name="estimated_value" value="{{ old('estimated_value', $lead->estimated_value) }}"
                            class="form-input" style="width: 100%; padding: 0.55rem 0.75rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; font-size: 0.875rem;">
                        <input type="hidden" name="currency" value="{{ $lead->currency ?? currency_code() }}">
                    </div>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem; color: #334155;">
                        {{ __('crm.leads.next_follow_up') }}
                    </label>
                    <input type="datetime-local" name="next_follow_up_at" 
                        value="{{ old('next_follow_up_at', $lead->next_follow_up_at?->format('Y-m-d\TH:i')) }}"
                        class="form-input" style="width: 100%; padding: 0.55rem 0.75rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; font-size: 0.875rem;">
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem; color: #334155;">
                        {{ __('crm.opportunities.lost_reason') }}
                    </label>
                    <input type="text" name="lost_reason" value="{{ old('lost_reason', $lead->lost_reason) }}"
                        class="form-input" style="width: 100%; padding: 0.55rem 0.75rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; font-size: 0.875rem;"
                        placeholder="Reason if marked lost...">
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem; color: #334155;">
                        {{ __('crm.activities.description') }} / Notes
                    </label>
                    <textarea name="notes" rows="3" class="form-textarea" style="width: 100%; padding: 0.55rem 0.75rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; font-size: 0.875rem;">{{ old('notes', $lead->notes) }}</textarea>
                </div>
            </div>
        </div>

        <div style="margin-top: 1.5rem; display: flex; justify-content: flex-end; gap: 0.75rem;">
            <a href="{{ route('admin.crm.leads.show', $lead->id) }}" class="btn btn-ghost">
                {{ __('app.actions.cancel') ?? 'Cancel' }}
            </a>
            <button type="submit" class="btn btn-primary" style="padding: 0.65rem 1.75rem; font-weight: 700;">
                <i class="fa-solid fa-check mr-2 ml-2"></i> Save Changes
            </button>
        </div>
    </form>
</x-layouts.admin>

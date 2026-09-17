<x-layouts.admin 
    :pageTitle="__('crm.dashboard.create_opportunity')" 
    :pageSubtitle="__('crm.opportunities.subtitle')"
>
    <form method="POST" action="{{ route('admin.crm.opportunities.store') }}">
        @csrf

        @if($lead)
            <input type="hidden" name="lead_id" value="{{ $lead->id }}">
        @endif

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem;">
            <!-- Left Column: Deal Details -->
            <div class="card" style="padding: 1.5rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
                <h3 style="font-size: 1rem; font-weight: 700; margin-bottom: 1.25rem; border-bottom: 1px solid #F1F5F9; padding-bottom: 0.5rem; color: #0F172A;">
                    <i class="fa-solid fa-handshake text-sky-500 mr-2 ml-2"></i> Deal Overview
                </h3>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem; color: #334155;">
                        {{ __('crm.opportunities.name') }} <span style="color: #DC2626;">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name', $lead ? 'Deal: ' . $lead->full_name : '') }}" required
                        class="form-input" style="width: 100%; padding: 0.55rem 0.75rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; font-size: 0.875rem;"
                        placeholder="e.g. Clinic Longevity Formulation Wholesale Batch">
                    @error('name') <span style="color: #DC2626; font-size: 0.75rem;">{{ $message }}</span> @enderror
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem; color: #334155;">
                            {{ __('crm.opportunities.value') }} ({{ currency_code() }}) <span style="color: #DC2626;">*</span>
                        </label>
                        <input type="number" step="0.01" name="value" value="{{ old('value', $lead->estimated_value ?? 0) }}" required
                            class="form-input" style="width: 100%; padding: 0.55rem 0.75rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; font-size: 0.875rem;">
                        <input type="hidden" name="currency" value="{{ currency_code() }}">
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem; color: #334155;">
                            {{ __('crm.opportunities.expected_close') }}
                        </label>
                        <input type="date" name="expected_close_date" value="{{ old('expected_close_date', now()->addMonth()->format('Y-m-d')) }}"
                            class="form-input" style="width: 100%; padding: 0.55rem 0.75rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; font-size: 0.875rem;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem; color: #334155;">
                            {{ __('crm.opportunities.customer') }}
                        </label>
                        <select name="customer_id" class="form-select" style="width: 100%; padding: 0.55rem 0.75rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; font-size: 0.875rem;">
                            <option value="">-- Direct Customer --</option>
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}" {{ (old('customer_id') == $c->id || ($lead && $lead->customer_id == $c->id)) ? 'selected' : '' }}>
                                    {{ $c->name }} ({{ $c->phone }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem; color: #334155;">
                            {{ __('crm.opportunities.company') }}
                        </label>
                        <select name="company_id" class="form-select" style="width: 100%; padding: 0.55rem 0.75rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; font-size: 0.875rem;">
                            <option value="">-- No Company --</option>
                            @foreach($companies as $comp)
                                <option value="{{ $comp->id }}" {{ (old('company_id') == $comp->id || ($lead && $lead->company_id == $comp->id)) ? 'selected' : '' }}>
                                    {{ $comp->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem; color: #334155;">
                        {{ __('crm.activities.description') }}
                    </label>
                    <textarea name="description" rows="3" class="form-textarea" style="width: 100%; padding: 0.55rem 0.75rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; font-size: 0.875rem;">{{ old('description') }}</textarea>
                </div>
            </div>

            <!-- Right Column: Pipeline & Assignment -->
            <div class="card" style="padding: 1.5rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
                <h3 style="font-size: 1rem; font-weight: 700; margin-bottom: 1.25rem; border-bottom: 1px solid #F1F5F9; padding-bottom: 0.5rem; color: #0F172A;">
                    <i class="fa-solid fa-route text-indigo-500 mr-2 ml-2"></i> Pipeline & Assignment
                </h3>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem; color: #334155;">
                        {{ __('crm.leads.pipeline') }} <span style="color: #DC2626;">*</span>
                    </label>
                    <select name="pipeline_id" id="pipelineSelect" required class="form-select" style="width: 100%; padding: 0.55rem 0.75rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; font-size: 0.875rem;">
                        @foreach($pipelines as $p)
                            <option value="{{ $p->id }}" {{ old('pipeline_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem; color: #334155;">
                        {{ __('crm.opportunities.stage') }} <span style="color: #DC2626;">*</span>
                    </label>
                    <select name="stage_id" id="stageSelect" required class="form-select" style="width: 100%; padding: 0.55rem 0.75rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; font-size: 0.875rem;">
                        @if($pipelines->isNotEmpty())
                            @foreach($pipelines->first()->stages as $st)
                                <option value="{{ $st->id }}" {{ old('stage_id') == $st->id ? 'selected' : '' }}>
                                    {{ $st->name }} ({{ $st->probability }}%)
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem; color: #334155;">
                        {{ __('crm.opportunities.owner') }} <span style="color: #DC2626;">*</span>
                    </label>
                    <select name="owner_id" required class="form-select" style="width: 100%; padding: 0.55rem 0.75rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; font-size: 0.875rem;">
                        @foreach($owners as $u)
                            <option value="{{ $u->id }}" {{ old('owner_id', auth()->id()) == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem; color: #334155;">
                        {{ __('crm.leads.campaign') }}
                    </label>
                    <select name="campaign_id" class="form-select" style="width: 100%; padding: 0.55rem 0.75rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; font-size: 0.875rem;">
                        <option value="">-- No Campaign / Direct --</option>
                        @foreach($campaigns as $camp)
                            <option value="{{ $camp->id }}" {{ old('campaign_id') == $camp->id ? 'selected' : '' }}>{{ $camp->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div style="margin-top: 1.5rem; display: flex; justify-content: flex-end; gap: 0.75rem;">
            <a href="{{ route('admin.crm.opportunities.index') }}" class="btn btn-ghost">
                {{ __('app.actions.cancel') ?? 'Cancel' }}
            </a>
            <button type="submit" class="btn btn-primary" style="padding: 0.65rem 1.75rem; font-weight: 700;">
                <i class="fa-solid fa-check mr-2 ml-2"></i> {{ __('crm.dashboard.create_opportunity') }}
            </button>
        </div>
    </form>
</x-layouts.admin>

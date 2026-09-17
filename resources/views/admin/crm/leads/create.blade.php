<x-layouts.admin 
    :pageTitle="__('crm.dashboard.create_lead')" 
    :pageSubtitle="__('crm.leads.subtitle')"
>
    <!-- Duplicate Warning Banner (dynamically shown via JS) -->
    <div id="duplicateAlert" style="display: none; background: #FEF2F2; border: 1px solid #F87171; border-radius: 0.5rem; padding: 1rem; margin-bottom: 1.5rem;">
        <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
            <i class="fa-solid fa-triangle-exclamation text-red-600 text-lg mt-0.5"></i>
            <div>
                <strong style="color: #991B1B; font-size: 0.95rem;">{{ __('crm.leads.duplicate_warning') }}</strong>
                <p id="duplicateDetails" style="margin: 0.25rem 0 0; color: #7F1D1D; font-size: 0.85rem;"></p>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.crm.leads.store') }}">
        @csrf

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem;">
            <!-- Left Column: Personal & Contact Details -->
            <div class="card" style="padding: 1.5rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
                <h3 style="font-size: 1rem; font-weight: 700; margin-bottom: 1.25rem; border-bottom: 1px solid #F1F5F9; padding-bottom: 0.5rem; color: #0F172A;">
                    <i class="fa-solid fa-user text-sky-500 mr-2 ml-2"></i> {{ __('crm.leads.full_name') }} & {{ __('crm.leads.email') }}
                </h3>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem; color: #334155;">
                            {{ __('crm.leads.first_name') }} <span style="color: #DC2626;">*</span>
                        </label>
                        <input type="text" name="first_name" value="{{ old('first_name') }}" required
                            class="form-input" style="width: 100%; padding: 0.55rem 0.75rem; border-radius: 0.5rem; border: 1px solid {{ $errors->has('first_name') ? '#DC2626' : '#CBD5E1' }}; font-size: 0.875rem;">
                        @error('first_name') <span style="color: #DC2626; font-size: 0.75rem;">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem; color: #334155;">
                            {{ __('crm.leads.last_name') }}
                        </label>
                        <input type="text" name="last_name" value="{{ old('last_name') }}"
                            class="form-input" style="width: 100%; padding: 0.55rem 0.75rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; font-size: 0.875rem;">
                    </div>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem; color: #334155;">
                        {{ __('crm.leads.email') }}
                    </label>
                    <input type="email" id="leadEmail" name="email" value="{{ old('email') }}" onblur="checkDuplicates()"
                        class="form-input" style="width: 100%; padding: 0.55rem 0.75rem; border-radius: 0.5rem; border: 1px solid {{ $errors->has('email') ? '#DC2626' : '#CBD5E1' }}; font-size: 0.875rem;"
                        placeholder="client@example.com">
                    @error('email') <span style="color: #DC2626; font-size: 0.75rem;">{{ $message }}</span> @enderror
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem; color: #334155;">
                            {{ __('crm.leads.phone') }}
                        </label>
                        <input type="text" id="leadPhone" name="phone" value="{{ old('phone') }}" onblur="checkDuplicates()"
                            class="form-input" style="width: 100%; padding: 0.55rem 0.75rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; font-size: 0.875rem;"
                            placeholder="+966 50 123 4567">
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem; color: #334155;">
                            {{ __('crm.leads.secondary_phone') }}
                        </label>
                        <input type="text" name="secondary_phone" value="{{ old('secondary_phone') }}"
                            class="form-input" style="width: 100%; padding: 0.55rem 0.75rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; font-size: 0.875rem;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem; color: #334155;">
                            {{ __('crm.leads.company_name') }}
                        </label>
                        <input type="text" name="company_name" value="{{ old('company_name') }}"
                            class="form-input" style="width: 100%; padding: 0.55rem 0.75rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; font-size: 0.875rem;"
                            placeholder="Al-Amal Clinic / Self">
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem; color: #334155;">
                            {{ __('crm.leads.job_title') }}
                        </label>
                        <input type="text" name="job_title" value="{{ old('job_title') }}"
                            class="form-input" style="width: 100%; padding: 0.55rem 0.75rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; font-size: 0.875rem;"
                            placeholder="Chief Medical Officer / Longevity Client">
                    </div>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem; color: #334155;">
                        {{ __('app.country') ?? 'Country' }}
                    </label>
                    <select name="country_id" class="form-select" style="width: 100%; padding: 0.55rem 0.75rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; font-size: 0.875rem;">
                        <option value="">-- Select Country --</option>
                        @foreach($countries as $c)
                            <option value="{{ $c->id }}" {{ old('country_id') == $c->id ? 'selected' : '' }}>
                                {{ app()->getLocale() === 'ar' ? $c->name_ar : $c->name_en }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Right Column: CRM Classification & Assignment -->
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
                            <option value="new" {{ old('status', 'new') === 'new' ? 'selected' : '' }}>{{ __('crm.status.new') }}</option>
                            <option value="contacted" {{ old('status') === 'contacted' ? 'selected' : '' }}>{{ __('crm.status.contacted') }}</option>
                            <option value="qualified" {{ old('status') === 'qualified' ? 'selected' : '' }}>{{ __('crm.status.qualified') }}</option>
                            <option value="unqualified" {{ old('status') === 'unqualified' ? 'selected' : '' }}>{{ __('crm.status.unqualified') }}</option>
                            <option value="lost" {{ old('status') === 'lost' ? 'selected' : '' }}>{{ __('crm.status.lost') }}</option>
                        </select>
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem; color: #334155;">
                            {{ __('crm.leads.priority') }} <span style="color: #DC2626;">*</span>
                        </label>
                        <select name="priority" required class="form-select" style="width: 100%; padding: 0.55rem 0.75rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; font-size: 0.875rem;">
                            <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>{{ __('crm.priorities.low') }}</option>
                            <option value="normal" {{ old('priority', 'normal') === 'normal' ? 'selected' : '' }}>{{ __('crm.priorities.normal') }}</option>
                            <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>{{ __('crm.priorities.high') }}</option>
                            <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>{{ __('crm.priorities.urgent') }}</option>
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem; color: #334155;">
                            {{ __('crm.leads.source') }}
                        </label>
                        <select name="source_id" class="form-select" style="width: 100%; padding: 0.55rem 0.75rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; font-size: 0.875rem;">
                            <option value="">-- Select Source --</option>
                            @foreach($sources as $s)
                                <option value="{{ $s->id }}" {{ old('source_id') == $s->id ? 'selected' : '' }}>
                                    {{ $s->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem; color: #334155;">
                            {{ __('crm.leads.campaign') }}
                        </label>
                        <select name="campaign_id" class="form-select" style="width: 100%; padding: 0.55rem 0.75rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; font-size: 0.875rem;">
                            <option value="">-- None / Organic --</option>
                            @foreach($campaigns as $camp)
                                <option value="{{ $camp->id }}" {{ old('campaign_id') == $camp->id ? 'selected' : '' }}>
                                    {{ $camp->name }}
                                </option>
                            @endforeach
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
                                <option value="{{ $u->id }}" {{ old('owner_id', auth()->id()) == $u->id ? 'selected' : '' }}>
                                    {{ $u->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem; color: #334155;">
                            {{ __('crm.leads.estimated_value') }} ({{ currency_code() }})
                        </label>
                        <input type="number" step="0.01" name="estimated_value" value="{{ old('estimated_value', 0) }}"
                            class="form-input" style="width: 100%; padding: 0.55rem 0.75rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; font-size: 0.875rem;">
                        <input type="hidden" name="currency" value="{{ currency_code() }}">
                    </div>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem; color: #334155;">
                        {{ __('crm.leads.next_follow_up') }}
                    </label>
                    <input type="datetime-local" name="next_follow_up_at" value="{{ old('next_follow_up_at', now()->addDays(2)->format('Y-m-d\TH:i')) }}"
                        class="form-input" style="width: 100%; padding: 0.55rem 0.75rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; font-size: 0.875rem;">
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem; color: #334155;">
                        {{ __('crm.activities.description') }} / Notes
                    </label>
                    <textarea name="notes" rows="3" class="form-textarea" style="width: 100%; padding: 0.55rem 0.75rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; font-size: 0.875rem;" placeholder="Client interest: NMN / Resveratrol Cellular Longevity Package">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        <div style="margin-top: 1.5rem; display: flex; justify-content: flex-end; gap: 0.75rem;">
            <a href="{{ route('admin.crm.leads.index') }}" class="btn btn-ghost">
                {{ __('app.actions.cancel') ?? 'Cancel' }}
            </a>
            <button type="submit" class="btn btn-primary" style="padding: 0.65rem 1.75rem; font-weight: 700;">
                <i class="fa-solid fa-check mr-2 ml-2"></i> {{ __('crm.dashboard.create_lead') }}
            </button>
        </div>
    </form>

    <script>
        function checkDuplicates() {
            const email = document.getElementById('leadEmail').value.trim();
            const phone = document.getElementById('leadPhone').value.trim();
            if (!email && !phone) return;

            const url = `{{ route('admin.crm.leads.check-duplicates') }}?email=${encodeURIComponent(email)}&phone=${encodeURIComponent(phone)}`;
            fetch(url)
                .then(r => r.json())
                .then(data => {
                    const alert = document.getElementById('duplicateAlert');
                    const details = document.getElementById('duplicateDetails');
                    if (data.has_duplicates) {
                        let text = '';
                        if (data.duplicate_leads.length > 0) {
                            text += `Found ${data.duplicate_leads.length} matching lead(s): ${data.duplicate_leads.map(l => l.lead_number).join(', ')}. `;
                        }
                        if (data.existing_customers.length > 0) {
                            text += `Found ${data.existing_customers.length} registered customer(s): ${data.existing_customers.map(c => c.name).join(', ')}.`;
                        }
                        details.textContent = text;
                        alert.style.display = 'block';
                    } else {
                        alert.style.display = 'none';
                    }
                })
                .catch(() => {});
        }
    </script>
</x-layouts.admin>

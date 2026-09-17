<x-layouts.admin 
    :pageTitle="'Create Campaign'" 
    :pageSubtitle="__('crm.campaigns.subtitle')"
>
    <form method="POST" action="{{ route('admin.crm.campaigns.store') }}">
        @csrf

        <div style="max-width: 700px; margin: 0 auto; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0); padding: 1.5rem; border-radius: 0.75rem;">
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem; color: #334155;">
                    {{ __('crm.campaigns.campaign_name') }} <span style="color: #DC2626;">*</span>
                </label>
                <input type="text" name="name" required class="form-input" style="width: 100%; padding: 0.55rem 0.75rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; font-size: 0.875rem;" placeholder="e.g. Ramadan Cellular Longevity Promo">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem; color: #334155;">
                        {{ __('crm.campaigns.type') }} <span style="color: #DC2626;">*</span>
                    </label>
                    <select name="type" required class="form-select" style="width: 100%; padding: 0.55rem 0.75rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; font-size: 0.875rem;">
                        <option value="social">Social Media (Instagram/TikTok)</option>
                        <option value="paid_ads">Paid Search / Google Ads</option>
                        <option value="email">Email Newsletter</option>
                        <option value="sms">SMS Marketing</option>
                        <option value="referral">Physician Referral</option>
                        <option value="event">Wellness Summit / Event</option>
                    </select>
                </div>

                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem; color: #334155;">
                        {{ __('crm.campaigns.budget') }} ({{ currency_code() }})
                    </label>
                    <input type="number" step="0.01" name="budget" value="0.00" class="form-input" style="width: 100%; padding: 0.55rem 0.75rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; font-size: 0.875rem;">
                    <input type="hidden" name="currency" value="{{ currency_code() }}">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem; color: #334155;">
                        utm_source
                    </label>
                    <input type="text" name="utm_source" class="form-input" style="width: 100%; padding: 0.55rem 0.75rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; font-size: 0.875rem;" placeholder="instagram / google">
                </div>

                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem; color: #334155;">
                        utm_campaign
                    </label>
                    <input type="text" name="utm_campaign" class="form-input" style="width: 100%; padding: 0.55rem 0.75rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; font-size: 0.875rem;" placeholder="longevity_q3">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem; color: #334155;">
                        Status
                    </label>
                    <select name="status" class="form-select" style="width: 100%; padding: 0.55rem 0.75rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; font-size: 0.875rem;">
                        <option value="active">Active</option>
                        <option value="draft">Draft</option>
                        <option value="paused">Paused</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>

                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem; color: #334155;">
                        Owner
                    </label>
                    <select name="owner_id" class="form-select" style="width: 100%; padding: 0.55rem 0.75rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; font-size: 0.875rem;">
                        <option value="">-- Select Owner --</option>
                        @foreach($owners as $u)
                            <option value="{{ $u->id }}" {{ $u->id == auth()->id() ? 'selected' : '' }}>{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                <a href="{{ route('admin.crm.campaigns.index') }}" class="btn btn-ghost">Cancel</a>
                <button type="submit" class="btn btn-primary" style="padding: 0.65rem 1.75rem; font-weight: 700;">
                    <i class="fa-solid fa-check mr-1 ml-1"></i> Create Campaign
                </button>
            </div>
        </div>
    </form>
</x-layouts.admin>

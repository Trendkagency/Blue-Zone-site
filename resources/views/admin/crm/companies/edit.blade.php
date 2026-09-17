<x-layouts.admin 
    :pageTitle="'Edit Company: ' . $company->name" 
    :pageSubtitle="__('crm.companies.subtitle')"
>
    <form method="POST" action="{{ route('admin.crm.companies.update', $company->id) }}">
        @csrf
        @method('PUT')

        <div style="max-width: 700px; margin: 0 auto; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0); padding: 1.5rem; border-radius: 0.75rem;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem; color: #334155;">
                        {{ __('crm.companies.company_name') }} <span style="color: #DC2626;">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name', $company->name) }}" required class="form-input" style="width: 100%; padding: 0.55rem 0.75rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; font-size: 0.875rem;">
                </div>

                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem; color: #334155;">
                        {{ __('crm.companies.legal_name') }}
                    </label>
                    <input type="text" name="legal_name" value="{{ old('legal_name', $company->legal_name) }}" class="form-input" style="width: 100%; padding: 0.55rem 0.75rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; font-size: 0.875rem;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem; color: #334155;">
                        {{ __('crm.leads.email') }}
                    </label>
                    <input type="email" name="email" value="{{ old('email', $company->email) }}" class="form-input" style="width: 100%; padding: 0.55rem 0.75rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; font-size: 0.875rem;">
                </div>

                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem; color: #334155;">
                        {{ __('crm.leads.phone') }}
                    </label>
                    <input type="text" name="phone" value="{{ old('phone', $company->phone) }}" class="form-input" style="width: 100%; padding: 0.55rem 0.75rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; font-size: 0.875rem;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem; color: #334155;">
                        {{ __('crm.companies.industry') }}
                    </label>
                    <input type="text" name="industry" value="{{ old('industry', $company->industry) }}" class="form-input" style="width: 100%; padding: 0.55rem 0.75rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; font-size: 0.875rem;">
                </div>

                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem; color: #334155;">
                        {{ __('crm.companies.tax_number') }}
                    </label>
                    <input type="text" name="tax_number" value="{{ old('tax_number', $company->tax_number) }}" class="form-input" style="width: 100%; padding: 0.55rem 0.75rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; font-size: 0.875rem;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem; color: #334155;">
                        Account Status
                    </label>
                    <select name="status" class="form-select" style="width: 100%; padding: 0.55rem 0.75rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; font-size: 0.875rem;">
                        <option value="active" {{ old('status', $company->status) === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="lead" {{ old('status', $company->status) === 'lead' ? 'selected' : '' }}>Lead</option>
                        <option value="partner" {{ old('status', $company->status) === 'partner' ? 'selected' : '' }}>Partner</option>
                        <option value="inactive" {{ old('status', $company->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.35rem; color: #334155;">
                        Sales Owner
                    </label>
                    <select name="owner_id" class="form-select" style="width: 100%; padding: 0.55rem 0.75rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; font-size: 0.875rem;">
                        <option value="">-- Unassigned --</option>
                        @foreach($owners as $u)
                            <option value="{{ $u->id }}" {{ old('owner_id', $company->owner_id) == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem;">
                <a href="{{ route('admin.crm.companies.show', $company->id) }}" class="btn btn-ghost">
                    {{ __('app.actions.cancel') ?? 'Cancel' }}
                </a>
                <button type="submit" class="btn btn-primary" style="padding: 0.65rem 1.75rem; font-weight: 700;">
                    <i class="fa-solid fa-check mr-1 ml-1"></i> Update Company
                </button>
            </div>
        </div>
    </form>
</x-layouts.admin>

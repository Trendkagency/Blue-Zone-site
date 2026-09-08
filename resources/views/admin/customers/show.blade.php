<x-layouts.admin 
    :pageTitle="$customer['name'] ?? __('admin.customers.title')" 
    :pageSubtitle="__('admin.customers.show_subtitle')"
    :breadcrumbs="[__('admin.menu.customers') => route('admin.customers.index'), ($customer['name'] ?? 'Client') => route('admin.customers.show', $customer['id'] ?? 1)]"
>
    <x-slot name="actions">
        <div class="flex items-center gap-2 flex-wrap">
            @if(empty($customer['deleted_at']))
                <a href="{{ route('admin.customers.edit', $customer['id'] ?? 1) }}" class="btn btn-primary font-bold shadow-sm">
                    <i class="fa-solid fa-user-pen mr-1.5 ml-1.5"></i> {{ __('admin.customers.edit_profile') }}
                </a>

                <form method="POST" action="{{ route('admin.customers.toggle-status', $customer['id'] ?? 1) }}" class="inline">
                    @csrf
                    @if(($customer['status'] ?? 'active') === 'active')
                        <button type="submit" class="btn btn-secondary font-bold text-amber-600 hover:text-amber-700 hover:bg-amber-50 dark:hover:bg-amber-900/30 border border-amber-300 dark:border-amber-700" title="{{ __('admin.customers.deactivate_title') }}">
                            <i class="fa-solid fa-user-slash mr-1.5 ml-1.5"></i> {{ __('admin.customers.deactivate') }}
                        </button>
                    @else
                        <button type="submit" class="btn btn-secondary font-bold text-emerald-600 hover:text-emerald-700 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 border border-emerald-300 dark:border-emerald-700" title="{{ __('admin.customers.activate_title') }}">
                            <i class="fa-solid fa-user-check mr-1.5 ml-1.5"></i> {{ __('admin.customers.activate') }}
                        </button>
                    @endif
                </form>
            @endif

            <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary font-bold">
                <i class="fa-solid fa-arrow-left rtl:rotate-180 mr-1.5 ml-1.5"></i> {{ __('admin.customers.back_to_list') }}
            </a>
        </div>
    </x-slot>

    <div class="customer-dossier-grid">
        <div>
            <!-- Stats Row -->
            <div class="customer-stats-grid">
                <div class="card stat-card stat-accent">
                    <div class="stat-label">{{ __('admin.customers.total_spent') }}</div>
                    <div class="stat-value text-success">@currency((float)($customer['total_spent'] ?? 0))</div>
                </div>
                <div class="card stat-card">
                    <div class="stat-label">{{ __('admin.customers.orders_count') }}</div>
                    <div class="stat-value">{{ $customer['orders_count'] ?? 0 }}</div>
                </div>
                <div class="card stat-card">
                    <div class="stat-label">{{ __('admin.customers.avg_order_value') }}</div>
                    <div class="stat-value" style="font-size: 1.35rem;">@currency((float)($customer['avg_order_value'] ?? 0))</div>
                </div>
                <div class="card stat-card stat-success">
                    <div class="stat-label">{{ __('admin.customers.member_tier') }}</div>
                    <div class="stat-value" style="font-size: 1.25rem;">{{ $customer['tier'] ?? 'Member' }}</div>
                </div>
            </div>

            <!-- Customer Orders History -->
            <div class="card">
                <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                    <h3 class="card-title">{{ __('admin.customers.order_history') }}</h3>
                    <span class="badge badge-subtle">{{ count($orders ?? []) }} {{ __('admin.customers.orders') }}</span>
                </div>
                <div class="table-responsive" style="border: none; border-radius: 0;">
                    <table class="table" style="min-width: 520px;">
                        <thead>
                            <tr>
                                <th>{{ __('admin.orders.order_number') }}</th>
                                <th>{{ __('admin.orders.date') }}</th>
                                <th>{{ __('admin.orders.status') }}</th>
                                <th>{{ __('admin.orders.amount') }}</th>
                                <th style="text-align: center;">{{ __('app.actions.view') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders ?? [] as $o)
                                <tr>
                                    <td class="font-bold text-primary">{{ $o['order_number'] ?? ('#' . ($o['id'] ?? '')) }}</td>
                                    <td>{{ $o['date'] ?? '-' }}</td>
                                    <td><x-status-badge :status="$o['status'] ?? 'pending'" /></td>
                                    <td class="font-bold">@currency((float)($o['total'] ?? 0))</td>
                                    <td style="text-align: center;">
                                        <a href="{{ route('admin.orders.show', $o['id'] ?? ($o['order_number'] ?? 1)) }}" class="action-btn" title="{{ __('app.actions.view') }}">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="text-align: center; padding: 2.5rem 1rem;" class="text-secondary">
                                        <i class="fa-solid fa-receipt" style="font-size: 1.75rem; margin-bottom: 0.5rem; opacity: 0.4; display: block;"></i>
                                        {{ __('admin.customers.no_orders') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Column: Contact & Addresses -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <div class="card" style="padding: 1.5rem;">
                <h4 style="font-size: 1rem; font-weight: 800; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fa-solid fa-address-card text-primary"></i>
                    {{ __('admin.customers.contact_info') }}
                </h4>
                <div class="text-sm" style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <div><strong>{{ __('admin.customers.email') }}:</strong> <a href="mailto:{{ $customer['email'] ?? '' }}" class="text-primary hover:underline">{{ $customer['email'] ?? '-' }}</a></div>
                    <div>
                        <strong>{{ __('admin.customers.phone') }}:</strong> 
                        <span class="inline-flex items-center gap-1.5">
                            {{ $customer['phone'] ?? '-' }}
                            @if(!empty($customer['phone']))
                                @php
                                    $cleanPhone = preg_replace('/[^0-9]/', '', $customer['phone']);
                                @endphp
                                <a href="https://wa.me/{{ $cleanPhone }}" target="_blank" class="text-emerald-600 hover:text-emerald-700" title="WhatsApp">
                                    <i class="fa-brands fa-whatsapp"></i>
                                </a>
                            @endif
                        </span>
                    </div>
                    <div><strong>{{ __('admin.customers.city') }}:</strong> {{ $customer['city'] ?? '-' }}{{ !empty($customer['country']) ? ', ' . $customer['country'] : '' }}</div>
                    @if(!empty($customer['address']))
                        <div><strong>{{ __('admin.customers.address') }}:</strong> {{ $customer['address'] }}</div>
                    @endif
                    <div><strong>{{ __('admin.customers.order_status') }}:</strong> <x-status-badge :status="$customer['status'] ?? 'active'" /></div>
                    @if(!empty($customer['registered_at']))
                        <div><strong>{{ __('admin.customers.member_since') }}:</strong> {{ $customer['registered_at'] }}</div>
                    @endif
                    @if(!empty($customer['last_order_date']))
                        <div><strong>{{ __('admin.customers.last_purchase') }}:</strong> {{ $customer['last_order_date'] }}</div>
                    @endif
                </div>

                <div class="pt-3 mt-3 border-t border-gray-100 dark:border-gray-800">
                    <a href="{{ route('admin.customers.edit', $customer['id']) }}" class="btn btn-secondary btn-sm w-full font-bold flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-pen-to-square"></i> {{ __('admin.customers.edit_or_reset') }}
                    </a>
                </div>
            </div>

            @php
                $addresses = $customer['addresses'] ?? [];
            @endphp
            <div class="card" style="padding: 1.5rem;">
                <h4 style="font-size: 1rem; font-weight: 800; margin-bottom: 1rem; display: flex; align-items: center; justify-content: space-between;">
                    <span style="display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fa-solid fa-map-location-dot text-primary"></i>
                        {{ __('admin.customers.saved_addresses') }}
                    </span>
                    <span class="badge badge-subtle" style="font-size: 0.75rem;">{{ count($addresses) }}</span>
                </h4>
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    @forelse($addresses as $ad)
                        <div style="background: var(--color-bg-subtle); padding: 0.875rem 1rem; border-radius: var(--radius-md); font-size: 0.8125rem; border: 1px solid var(--color-border);">
                            <div class="font-bold" style="display: flex; justify-content: space-between; align-items: center;">
                                <span>{{ $ad['title'] ?? ($ad['recipient'] ?? __('admin.customers.primary_address')) }}</span>
                                @if(!empty($ad['is_default']))
                                    <span class="badge badge-primary" style="font-size: 0.65rem; padding: 0.15rem 0.5rem;">{{ __('admin.customers.default') }}</span>
                                @endif
                            </div>
                            <div class="text-secondary" style="margin-top: 0.35rem; line-height: 1.4;">
                                {{ $ad['street'] ?? ($ad['address'] ?? '') }}{{ !empty($ad['city']) ? ', ' . $ad['city'] : '' }}{{ !empty($ad['country']) ? ', ' . $ad['country'] : '' }}
                            </div>
                            @if(!empty($ad['postal_code']))
                                <div class="text-secondary" style="font-size: 0.75rem; margin-top: 0.25rem;">
                                    <span class="font-medium">{{ __('admin.customers.postal_code') }}:</span> {{ $ad['postal_code'] }}
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-secondary text-sm" style="text-align: center; padding: 1.5rem 1rem; background: var(--color-bg-subtle); border-radius: var(--radius-md); border: 1px dashed var(--color-border);">
                            <i class="fa-solid fa-location-dot" style="font-size: 1.5rem; margin-bottom: 0.5rem; opacity: 0.4; display: block;"></i>
                            {{ __('admin.customers.no_addresses') }}
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>

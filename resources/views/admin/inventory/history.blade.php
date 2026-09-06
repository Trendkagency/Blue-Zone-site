<x-layouts.admin 
    :pageTitle="__('admin.menu.stock_history')" 
    pageSubtitle="Auditable ledger tracking every single inbound, outbound, transfer, and sale transaction."
    :breadcrumbs="['Inventory' => route('admin.inventory.index'), 'Movement Audit Ledger' => route('admin.inventory.history')]"
>
    <!-- Filter Bar -->
    <div class="shop-toolbar" style="margin-bottom: 1.5rem;">
        <div class="search-wrapper" style="max-width: 320px;">
            <svg class="search-icon" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input type="text" class="form-control search-input text-sm" placeholder="Filter by Log ID, product, user...">
        </div>

        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
            <select class="form-select text-sm" style="width: auto;">
                <option value="">All Movement Types</option>
                <option value="transfer">Stock Transfer</option>
                <option value="online_sale">Online Sale</option>
                <option value="offline_sale">Offline Sale</option>
                <option value="return">Return</option>
                <option value="damaged">Damaged</option>
                <option value="adjustment">Manual Adjustment</option>
            </select>

            <button type="button" class="btn btn-secondary btn-sm">
                📥 {{ __('app.actions.export') }}
            </button>
        </div>
    </div>

    <!-- Movement Ledger Table -->
    <div class="card">
        <div class="table-responsive" style="border: none; border-radius: 0;">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('admin.inventory.movement_id') }}</th>
                        <th>Product & Variant</th>
                        <th>{{ __('admin.inventory.movement_type') }}</th>
                        <th>Routing (From → To)</th>
                        <th>Qty Delta</th>
                        <th>Prev → New</th>
                        <th>{{ __('admin.inventory.logged_user') }}</th>
                        <th>Timestamp & Justification</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($movements as $m)
                        <tr>
                            <td class="font-mono text-xs font-bold text-primary">{{ $m['id'] }}</td>
                            <td>
                                <div class="font-bold text-sm">{{ $m['product_name_en'] }}</div>
                                <div class="text-xs text-muted font-mono">{{ $m['sku'] }}</div>
                            </td>
                            <td>
                                <span class="badge badge-neutral text-xs">{{ $m['movement_type'] }}</span>
                            </td>
                            <td class="text-xs">
                                <div><strong>From:</strong> {{ $m['from_location'] }}</div>
                                <div><strong>To:</strong> {{ $m['to_location'] }}</div>
                            </td>
                            <td class="font-bold {{ $m['quantity'] < 0 ? 'text-danger' : 'text-success' }}">
                                {{ $m['quantity'] > 0 ? '+' : '' }}{{ $m['quantity'] }}
                            </td>
                            <td class="text-xs font-mono">
                                {{ $m['previous_quantity'] }} → <strong>{{ $m['new_quantity'] }}</strong>
                            </td>
                            <td class="text-xs">
                                👤 {{ $m['user'] }}
                            </td>
                            <td class="text-xs text-muted">
                                <div>{{ $m['date'] }} {{ $m['time'] }}</div>
                                <div class="text-secondary" style="font-style: italic;">"{{ $m['reason'] }}"</div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <x-pagination :currentPage="$currentPage" :totalPages="$totalPages" :totalItems="count($movements)" />
    </div>
</x-layouts.admin>

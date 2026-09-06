<x-layouts.admin 
    :pageTitle="__('admin.menu.categories')" 
    pageSubtitle="Configure biological systems, longevity categories, and clinical subcategories."
    :breadcrumbs="['Categories' => route('admin.categories.index')]"
>
    <x-slot name="actions">
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
            + New Category System
        </a>
    </x-slot>

    <div class="card">
        <div class="table-responsive" style="border: none; border-radius: 0;">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 60px;">Sort</th>
                        <th>Category System</th>
                        <th>Arabic Designation</th>
                        <th>Subcategories</th>
                        <th>Formulations</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $cat)
                        <tr>
                            <td class="font-bold text-muted text-center">{{ $cat['sort_order'] }}</td>
                            <td>
                                <div class="font-bold text-sm text-primary">
                                    <a href="{{ route('admin.categories.edit', $cat['id']) }}">
                                        {{ $cat['name_en'] }}
                                    </a>
                                </div>
                                <div class="text-xs text-muted">{{ $cat['slug'] }}</div>
                            </td>
                            <td class="font-bold" dir="rtl">{{ $cat['name_ar'] }}</td>
                            <td>
                                <div style="display: flex; gap: 0.35rem; flex-wrap: wrap;">
                                    @foreach($cat['subcategories'] as $sub)
                                        <span class="badge badge-neutral text-xs">{{ $sub['name_en'] }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="font-bold">{{ $cat['products_count'] }} products</td>
                            <td>
                                <x-status-badge :status="$cat['status']" />
                            </td>
                            <td>
                                <div class="table-actions">
                                    <a href="{{ route('admin.categories.edit', $cat['id']) }}" class="action-btn" title="Edit">
                                        ✏️
                                    </a>
                                    <button type="button" class="action-btn action-danger" onclick="openModal('deleteCategoryModal')" title="Delete">
                                        🗑️
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <x-pagination :currentPage="$currentPage" :totalPages="$totalPages" :totalItems="count($categories)" />
    </div>

    <x-confirmation-modal 
        id="deleteCategoryModal" 
        title="Delete Category" 
        message="Are you sure you wish to delete this category? Associated formulations will need to be re-assigned." 
        confirmText="Delete Category" 
        confirmType="btn-danger" 
    />
</x-layouts.admin>

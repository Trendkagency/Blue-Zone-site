<x-layouts.admin 
    :pageTitle="__('admin.menu.wellness')" 
    pageSubtitle="Manage clinical dispatches, educational articles, and biochemical research publications."
    :breadcrumbs="['Content' => route('admin.content.index'), 'Wellness Articles' => route('admin.content.wellness')]"
>
    <x-slot name="actions">
        <button type="button" class="btn btn-primary">+ Write New Article</button>
    </x-slot>

    <div class="card">
        <div class="table-responsive" style="border: none; border-radius: 0;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Article Title</th>
                        <th>Author</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Date Published</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="font-bold text-primary">Targeting Mitochondrial Senescence with Bio-Identical Co-Factors</td>
                        <td>Dr. Henrik Lindqvist</td>
                        <td><span class="badge badge-accent text-xs">Cellular Research</span></td>
                        <td><span class="badge badge-success text-xs">Published</span></td>
                        <td class="text-xs text-muted">2026-08-15</td>
                        <td><button type="button" class="action-btn">✏️</button></td>
                    </tr>
                    <tr>
                        <td class="font-bold text-primary">The Greek Mountain Herb Matrix: Pharmacokinetics of Wild Flavones</td>
                        <td>Dr. Sofia Kourakis</td>
                        <td><span class="badge badge-neutral text-xs">Botanical Assays</span></td>
                        <td><span class="badge badge-success text-xs">Published</span></td>
                        <td class="text-xs text-muted">2026-07-28</td>
                        <td><button type="button" class="action-btn">✏️</button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>

<x-layouts.admin 
<<<<<<< HEAD
    :pageTitle="__('admin.menu.wellness')" 
    pageSubtitle="Manage clinical dispatches, educational articles, and biochemical research publications."
    :breadcrumbs="['Content' => route('admin.content.index'), 'Wellness Articles' => route('admin.content.wellness')]"
>
    <x-slot name="actions">
        <button type="button" class="btn btn-primary">+ Write New Article</button>
=======
    :pageTitle="__('admin.content.wellness_title')" 
    :pageSubtitle="__('admin.content.wellness_subtitle')"
    :breadcrumbs="[__('admin.menu.content') => route('admin.content.index'), __('admin.content.wellness_title') => route('admin.content.wellness')]"
>
    <x-slot name="actions">
        <button type="button" class="btn btn-primary">{{ __('admin.content.write_article_btn') }}</button>
>>>>>>> origin/main
    </x-slot>

    <div class="card">
        <div class="table-responsive" style="border: none; border-radius: 0;">
            <table class="table">
                <thead>
                    <tr>
<<<<<<< HEAD
                        <th>Article Title</th>
                        <th>Author</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Date Published</th>
                        <th>Actions</th>
=======
                        <th>{{ app()->getLocale() == 'ar' ? 'عنوان المقال والبحث' : 'Article Title' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'الباحث / الكاتب' : 'Author' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'التصنيف' : 'Category' }}</th>
                        <th>{{ __('admin.orders.status') }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'تاريخ النشر' : 'Date Published' }}</th>
                        <th>{{ __('app.actions.actions') }}</th>
>>>>>>> origin/main
                    </tr>
                </thead>
                <tbody>
                    <tr>
<<<<<<< HEAD
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
=======
                        <td class="font-bold text-primary">
                            {{ app()->getLocale() == 'ar' ? 'استهداف الشيخوخة الخلوية للميتوكوندريا بالعوامل المساعدة الحيوية المطابقة' : 'Targeting Mitochondrial Senescence with Bio-Identical Co-Factors' }}
                        </td>
                        <td>Dr. Henrik Lindqvist</td>
                        <td><span class="badge badge-accent text-xs">{{ app()->getLocale() == 'ar' ? 'الأبحاث الخلوية' : 'Cellular Research' }}</span></td>
                        <td><span class="badge badge-success text-xs">{{ app()->getLocale() == 'ar' ? 'منشور' : 'Published' }}</span></td>
                        <td class="text-xs text-muted">2026-08-15</td>
                        <td>
                            <button type="button" class="action-btn" title="{{ __('app.actions.edit') }}">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td class="font-bold text-primary">
                            {{ app()->getLocale() == 'ar' ? 'مصفوفة الأعشاب الجبلية اليونانية: الحركية الدوائية للفلافونويد البري' : 'The Greek Mountain Herb Matrix: Pharmacokinetics of Wild Flavones' }}
                        </td>
                        <td>Dr. Sofia Kourakis</td>
                        <td><span class="badge badge-neutral text-xs">{{ app()->getLocale() == 'ar' ? 'الفحوصات النباتية' : 'Botanical Assays' }}</span></td>
                        <td><span class="badge badge-success text-xs">{{ app()->getLocale() == 'ar' ? 'منشور' : 'Published' }}</span></td>
                        <td class="text-xs text-muted">2026-07-28</td>
                        <td>
                            <button type="button" class="action-btn" title="{{ __('app.actions.edit') }}">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                        </td>
>>>>>>> origin/main
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>

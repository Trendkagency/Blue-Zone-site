<x-layouts.admin 
    :pageTitle="__('admin.content.wellness_title')" 
    :pageSubtitle="__('admin.content.wellness_subtitle')"
    :breadcrumbs="[__('admin.menu.content') => route('admin.content.index'), __('admin.content.wellness_title') => route('admin.content.wellness')]"
>
    <x-slot name="actions">
        <button type="button" class="btn btn-primary">{{ __('admin.content.write_article_btn') }}</button>
    </x-slot>

    <div class="card">
        <div class="table-responsive" style="border: none; border-radius: 0;">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ app()->getLocale() == 'ar' ? 'عنوان المقال والبحث' : 'Article Title' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'الباحث / الكاتب' : 'Author' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'التصنيف' : 'Category' }}</th>
                        <th>{{ __('admin.orders.status') }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'تاريخ النشر' : 'Date Published' }}</th>
                        <th>{{ __('app.actions.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
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
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>

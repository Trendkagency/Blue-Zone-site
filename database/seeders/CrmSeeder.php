<?php

namespace Database\Seeders;

use App\Models\CrmLeadSource;
use App\Models\CrmPipeline;
use App\Models\CrmPipelineStage;
use App\Models\CrmSegment;
use App\Models\CrmTag;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CrmSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Lead Sources
        $sources = [
            ['name_en' => 'Website Direct', 'name_ar' => 'الموقع الإلكتروني', 'slug' => 'website', 'icon' => 'fa-globe', 'color' => '#0284C7'],
            ['name_en' => 'WhatsApp Inquiries', 'name_ar' => 'استفسارات واتساب', 'slug' => 'whatsapp', 'icon' => 'fa-whatsapp', 'color' => '#16A34A'],
            ['name_en' => 'Physician Referral', 'name_ar' => 'إحالة أطباء', 'slug' => 'referral', 'icon' => 'fa-user-doctor', 'color' => '#7C3AED'],
            ['name_en' => 'Instagram Ads', 'name_ar' => 'إعلانات إنستغرام', 'slug' => 'instagram', 'icon' => 'fa-instagram', 'color' => '#E1306C'],
            ['name_en' => 'TikTok Longevity Content', 'name_ar' => 'محتوى تيك توك', 'slug' => 'tiktok', 'icon' => 'fa-tiktok', 'color' => '#0F172A'],
            ['name_en' => 'Clinic / Pharmacy Partner', 'name_ar' => 'شراكة عيادة / صيدلية', 'slug' => 'clinic-partner', 'icon' => 'fa-hospital', 'color' => '#D97706'],
            ['name_en' => 'Wellness Summit / Event', 'name_ar' => 'معارض ومؤتمرات العافية', 'slug' => 'event', 'icon' => 'fa-calendar-star', 'color' => '#2563EB'],
            ['name_en' => 'Other', 'name_ar' => 'أخرى', 'slug' => 'other', 'icon' => 'fa-ellipsis', 'color' => '#64748B'],
        ];

        foreach ($sources as $idx => $s) {
            CrmLeadSource::firstOrCreate(
                ['slug' => $s['slug']],
                [
                    'name_en' => $s['name_en'],
                    'name_ar' => $s['name_ar'],
                    'icon' => $s['icon'],
                    'color' => $s['color'],
                    'is_active' => true,
                    'sort_order' => $idx + 1,
                ]
            );
        }

        // 2. Default Pipeline: Retail Longevity Orders
        $retailPipeline = CrmPipeline::firstOrCreate(
            ['slug' => 'retail-longevity-sales'],
            [
                'name_en' => 'Retail Longevity Formulation Pipeline',
                'name_ar' => 'مسار مبيعات التركيبات الفردية',
                'is_default' => true,
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        $retailStages = [
            ['name_en' => 'New Lead', 'name_ar' => 'عميل محتمل جديد', 'prob' => 10, 'won' => false, 'lost' => false, 'color' => '#64748B'],
            ['name_en' => 'Contacted & Qualified', 'name_ar' => 'تم التواصل والتأهيل', 'prob' => 30, 'won' => false, 'lost' => false, 'color' => '#0284C7'],
            ['name_en' => 'Formulation Consultation', 'name_ar' => 'استشارة التركيبة المناسبة', 'prob' => 60, 'won' => false, 'lost' => false, 'color' => '#F59E0B'],
            ['name_en' => 'Checkout / Order Placed', 'name_ar' => 'تم إصدار الطلب', 'prob' => 90, 'won' => false, 'lost' => false, 'color' => '#8B5CF6'],
            ['name_en' => 'Delivered & Closed', 'name_ar' => 'تم التسليم بنجاح (فوز)', 'prob' => 100, 'won' => true, 'lost' => false, 'color' => '#10B981'],
            ['name_en' => 'Lost / Disqualified', 'name_ar' => 'غير مؤهل / خسارة', 'prob' => 0, 'won' => false, 'lost' => true, 'color' => '#EF4444'],
        ];

        foreach ($retailStages as $idx => $st) {
            CrmPipelineStage::firstOrCreate(
                ['pipeline_id' => $retailPipeline->id, 'slug' => Str::slug($st['name_en'])],
                [
                    'name_en' => $st['name_en'],
                    'name_ar' => $st['name_ar'],
                    'probability' => $st['prob'],
                    'is_won' => $st['won'],
                    'is_lost' => $st['lost'],
                    'is_active' => true,
                    'sort_order' => $idx + 1,
                    'color' => $st['color'],
                ]
            );
        }

        // 3. Second Pipeline: B2B Clinic & Distributor Pipeline
        $b2bPipeline = CrmPipeline::firstOrCreate(
            ['slug' => 'b2b-clinic-partnerships'],
            [
                'name_en' => 'B2B Clinic & Pharmacy Partnerships',
                'name_ar' => 'مسار شراكات العيادات والصيدليات',
                'is_default' => false,
                'is_active' => true,
                'sort_order' => 2,
            ]
        );

        $b2bStages = [
            ['name_en' => 'Initial Outreach', 'name_ar' => 'تواصل أولي', 'prob' => 15, 'won' => false, 'lost' => false, 'color' => '#64748B'],
            ['name_en' => 'Scientific Presentation', 'name_ar' => 'عرض المواد العلمية', 'prob' => 40, 'won' => false, 'lost' => false, 'color' => '#0284C7'],
            ['name_en' => 'Wholesale Proposal', 'name_ar' => 'عرض الأسعار بالجملة', 'prob' => 70, 'won' => false, 'lost' => false, 'color' => '#F59E0B'],
            ['name_en' => 'Contract Signed & Stock Delivered', 'name_ar' => 'توقيع العقد وتوريد الشحنة', 'prob' => 100, 'won' => true, 'lost' => false, 'color' => '#10B981'],
            ['name_en' => 'Lost Proposal', 'name_ar' => 'عرض مرفوض', 'prob' => 0, 'won' => false, 'lost' => true, 'color' => '#EF4444'],
        ];

        foreach ($b2bStages as $idx => $st) {
            CrmPipelineStage::firstOrCreate(
                ['pipeline_id' => $b2bPipeline->id, 'slug' => Str::slug($st['name_en'])],
                [
                    'name_en' => $st['name_en'],
                    'name_ar' => $st['name_ar'],
                    'probability' => $st['prob'],
                    'is_won' => $st['won'],
                    'is_lost' => $st['lost'],
                    'is_active' => true,
                    'sort_order' => $idx + 1,
                    'color' => $st['color'],
                ]
            );
        }

        // 4. Standard CRM Tags
        $tags = [
            ['name_en' => 'VIP Longevity Member', 'name_ar' => 'عضو VIP للنقاء وطول العمر', 'slug' => 'vip', 'color' => '#7C3AED'],
            ['name_en' => 'High Lifetime Value', 'name_ar' => 'قيمة عميل مرتفعة', 'slug' => 'high-value', 'color' => '#059669'],
            ['name_en' => 'At Risk (90d+)', 'name_ar' => 'معرض للفقدان (90+ يوم)', 'slug' => 'at-risk', 'color' => '#D97706'],
            ['name_en' => 'Cellular NAD+ Interest', 'name_ar' => 'مهتم بمركبات NAD+', 'slug' => 'nad-interest', 'color' => '#0284C7'],
            ['name_en' => 'Clinic / Doctor Wholesale', 'name_ar' => 'طبيب / عيادة جملة', 'slug' => 'clinic-wholesale', 'color' => '#2563EB'],
        ];

        foreach ($tags as $t) {
            CrmTag::firstOrCreate(
                ['slug' => $t['slug']],
                [
                    'name_en' => $t['name_en'],
                    'name_ar' => $t['name_ar'],
                    'color' => $t['color'],
                ]
            );
        }

        // 5. Default Dynamic Segments
        $segments = [
            [
                'name_en' => 'High-Value VIP Clients',
                'name_ar' => 'عملاء VIP ذوي القيمة العالية',
                'slug' => 'high-value',
                'description' => 'Clients who have spent 3,000+ SAR in completed delivered orders.',
                'rules' => ['min_spent' => 3000],
            ],
            [
                'name_en' => 'Repeat Longevity Buyers',
                'name_ar' => 'عملاء متكررو الشراء',
                'slug' => 'repeat-customers',
                'description' => 'Clients with 2 or more delivered longevity formulation orders.',
                'rules' => ['min_orders' => 2],
            ],
            [
                'name_en' => 'At-Risk Churn Window',
                'name_ar' => 'عملاء في دائرة الخطر (90+ يوم)',
                'slug' => 'at-risk',
                'description' => 'Clients with previous delivered orders who have not ordered in over 90 days.',
                'rules' => ['days_inactive' => 90],
            ],
            [
                'name_en' => 'Inactive Lapsed Accounts',
                'name_ar' => 'حسابات غير نشطة (180+ يوم)',
                'slug' => 'inactive',
                'description' => 'Clients with no delivered orders in 180+ days.',
                'rules' => ['days_inactive' => 180],
            ],
        ];

        foreach ($segments as $seg) {
            CrmSegment::firstOrCreate(
                ['slug' => $seg['slug']],
                [
                    'name_en' => $seg['name_en'],
                    'name_ar' => $seg['name_ar'],
                    'description' => $seg['description'],
                    'rules' => $seg['rules'],
                    'is_active' => true,
                ]
            );
        }

        // Evaluate segment initial counts
        app(\App\Services\CrmSegmentService::class)->refreshSegmentCounts();
    }
}

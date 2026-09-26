<?php

namespace App\View\ViewModels;

class RoleViewModel
{
    /**
     * Standard action operations supported per module.
     *
     * @return array<string, array<string, string>>
     */
    public static function actions(): array
    {
        return [
            'view' => [
                'en' => 'View',
                'ar' => 'عرض',
                'icon' => 'fa-eye',
                'desc' => 'Read and inspect records',
            ],
            'create' => [
                'en' => 'Create',
                'ar' => 'إضافة',
                'icon' => 'fa-plus',
                'desc' => 'Add new records',
            ],
            'edit' => [
                'en' => 'Edit',
                'ar' => 'تعديل',
                'icon' => 'fa-pen-to-square',
                'desc' => 'Modify existing records',
            ],
            'delete' => [
                'en' => 'Delete',
                'ar' => 'حذف',
                'icon' => 'fa-trash',
                'desc' => 'Remove or archive records',
            ],
        ];
    }

    /**
     * Categorized system modules taxonomy by functional domain.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function categorizedModules(): array
    {
        return [
            'mr_crm' => [
                'label' => [
                    'en' => 'Medical Representative (CRM & Field Operations)',
                    'ar' => 'إدارة المندوب الطبي والعمليات الميدانية (CRM)',
                ],
                'icon' => 'fa-user-doctor',
                'color' => '#0A4F78',
                'modules' => [
                    'mr_dashboard' => [
                        'name_en' => 'MR CRM Dashboard & Calendar',
                        'name_ar' => 'لوحة تحكم CRM والتقويم الشامل للمناديب',
                        'desc_en' => 'Access CRM schedule calendar, right-click quick events, and rep scorecard.',
                        'desc_ar' => 'متابعة تقويم جدول المندوب وإجراءات الزر الأيمن ومؤشرات الأداء.',
                    ],
                    'mr_visits' => [
                        'name_en' => 'Field Visits & GPS Verification',
                        'name_ar' => 'الزيارات الميدانية والتحقق الجغرافي (GPS)',
                        'desc_en' => 'Execute check-in/out, log doctor feedbacks, verify GPS geofence radiuses.',
                        'desc_ar' => 'تنفيذ وتسجيل الزيارات والتحقق من النطاق الجغرافي وملاحظات الأطباء.',
                    ],
                    'mr_contacts' => [
                        'name_en' => 'Doctors & Healthcare Facilities',
                        'name_ar' => 'الأطباء والعيادات والمراكز الصحية',
                        'desc_en' => 'Manage medical contact records, specialties, classifications (A/B/C).',
                        'desc_ar' => 'إدارة ملفات الأطباء وتخصصاتهم وتصنيفاتهم وتوزيع العيادات.',
                    ],
                    'mr_assignments' => [
                        'name_en' => 'Doctor & Territory Assignments',
                        'name_ar' => 'إسناد وتكليف محافظ الأطباء للمناديب',
                        'desc_en' => 'Assign and reallocate doctor portfolios and target frequencies to reps.',
                        'desc_ar' => 'توزيع وتكليف الأطباء والمحافظ الطبية على مناديب الدعاية.',
                    ],
                    'mr_territories' => [
                        'name_en' => 'Geographic Areas & Lines',
                        'name_ar' => 'المناطق الجغرافية والمربعات والخطوط',
                        'desc_en' => 'Define operational territory zones, boundaries, cities, and line assignments.',
                        'desc_ar' => 'هيكلة المناطق والمدن وخطوط السير الجغرافية للمناديب.',
                    ],
                    'mr_reports' => [
                        'name_en' => 'Coverage & Performance Analytics',
                        'name_ar' => 'تقارير التغطية وتحليلات الأداء الميداني',
                        'desc_en' => 'Inspect cycle coverage rates, visit completion ratios, and scorecards.',
                        'desc_ar' => 'تحليل نسب تغطية الدورات ونسب إنجاز الخطط وتقييمات المناديب.',
                    ],
                ],
            ],
            'commercial_crm' => [
                'label' => [
                    'en' => 'Commercial Sales CRM (B2B & Pipeline)',
                    'ar' => 'إدارة مبيعات الشركات والفرص التجارية (Sales CRM)',
                ],
                'icon' => 'fa-briefcase',
                'color' => '#2A8FC2',
                'modules' => [
                    'crm_leads' => [
                        'name_en' => 'Commercial Leads & Prospects',
                        'name_ar' => 'العملاء المحتملون والفرص التسويقية',
                        'desc_en' => 'Manage incoming commercial prospect leads, scores, and qualified stages.',
                        'desc_ar' => 'متابعة العملاء المحتملين وتأهيل الفرص التجارية.',
                    ],
                    'crm_opportunities' => [
                        'name_en' => 'Deals & Pipeline Stages',
                        'name_ar' => 'الصفقات والمراحل البيعية (Pipeline)',
                        'desc_en' => 'Manage sales deal values, negotiation stages, and won/lost tracking.',
                        'desc_ar' => 'إدارة الصفقات وقيمها ومراحل التفاوض وعروض الأسعار.',
                    ],
                    'crm_activities' => [
                        'name_en' => 'Client Meetings & Call Logs',
                        'name_ar' => 'المهام والاتصالات واجتماعات العملاء',
                        'desc_en' => 'Log commercial calls, client presentations, follow-ups, and tasks.',
                        'desc_ar' => 'تسجيل المكالمات والاجتماعات التجارية والمهام الدورية.',
                    ],
                ],
            ],
            'hr_workforce' => [
                'label' => [
                    'en' => 'Human Resources & Workforce Management',
                    'ar' => 'الموارد البشرية وشؤون الموظفين (HR)',
                ],
                'icon' => 'fa-people-roof',
                'color' => '#67B34A',
                'modules' => [
                    'hr_employees' => [
                        'name_en' => 'Employee Profiles & Contracts',
                        'name_ar' => 'ملفات الموظفين والعقود الرسمية',
                        'desc_en' => 'Personnel records, identification, hiring specs, and official contracts.',
                        'desc_ar' => 'الملفات الوظيفية والبيانات التعاقدية والشخصية للموظفين.',
                    ],
                    'hr_attendance' => [
                        'name_en' => 'Attendance & Time Clock (Punches)',
                        'name_ar' => 'سجل الحضور والانصراف وبصمة الدوام',
                        'desc_en' => 'Track daily check-ins/outs, late minutes, overtime, and manual punches.',
                        'desc_ar' => 'متابعة حركات الحضور والانصراف وساعات التأخير والعمل الإضافي.',
                    ],
                    'hr_departments' => [
                        'name_en' => 'Departments & Job Positions',
                        'name_ar' => 'الهيكل التنظيمي والأقسام والمسميات',
                        'desc_en' => 'Manage organizational hierarchy, departments, shifts, and job tiers.',
                        'desc_ar' => 'إدارة الهيكل الإداري والأقسام والورديات والمسميات الوظيفية.',
                    ],
                    'hr_payroll' => [
                        'name_en' => 'Compensation, Allowances & Payroll',
                        'name_ar' => 'مسيرات الرواتب والبدلات والمكافآت',
                        'desc_en' => 'Manage salary structures, housing/transport allowances, and bank transfers.',
                        'desc_ar' => 'إدارة هياكل الرواتب والبدلات ومسيرات التحويل البنكي.',
                    ],
                ],
            ],
            'commerce_logistics' => [
                'label' => [
                    'en' => 'Commerce, Warehouse & Inventory Logistics',
                    'ar' => 'المتجر والمستودعات والعمليات اللوجستية',
                ],
                'icon' => 'fa-boxes-stacked',
                'color' => '#062B49',
                'modules' => [
                    'products' => [
                        'name_en' => 'Products & Formulations Catalog',
                        'name_ar' => 'دليل المنتجات والتركيبات والتصنيفات',
                        'desc_en' => 'Catalog SKUs, prices, ingredients, formulations, and categories.',
                        'desc_ar' => 'إدارة كتالوج المنتجات والأسعار والتصنيفات والتركيبات.',
                    ],
                    'inventory' => [
                        'name_en' => 'Warehouse Stocks & Movements',
                        'name_ar' => 'المخزون والمستودعات وحركات الأصناف',
                        'desc_en' => 'Stock adjustments, inward/outward transfers, lot tracking, and audits.',
                        'desc_ar' => 'تسجيل حركات المخزون والتوريد والتحويلات وجرد المستودعات.',
                    ],
                    'orders' => [
                        'name_en' => 'Online Store Orders',
                        'name_ar' => 'طلبات المتجر الإلكتروني وعمليات الشحن',
                        'desc_en' => 'Customer orders, dispatch statuses, payments, and shipping labels.',
                        'desc_ar' => 'إدارة الطلبات وحالات الشحن والدفع وتتبع التوصيل.',
                    ],
                    'offline_sales' => [
                        'name_en' => 'Offline Counter & POS Sales',
                        'name_ar' => 'المبيعات المباشرة ونقاط البيع (POS)',
                        'desc_en' => 'Counter sales registers, direct receipts, and in-store cash outs.',
                        'desc_ar' => 'عمليات البيع المباشر والكاشير وإيصالات نقاط البيع.',
                    ],
                    'customers' => [
                        'name_en' => 'Customer Accounts & Directory',
                        'name_ar' => 'حسابات وبيانات العملاء والمرضى',
                        'desc_en' => 'Customer CRM dossiers, purchase history, loyalty, and communications.',
                        'desc_ar' => 'سجلات العملاء وسجل المشتريات ونقاط الولاء.',
                    ],
                    'invoices' => [
                        'name_en' => 'Invoices & Financial Receipts',
                        'name_ar' => 'الفواتير الضريبية وسندات القبض',
                        'desc_en' => 'ZATCA-compliant invoices, VAT calculations, and credit notes.',
                        'desc_ar' => 'إصدار وتدقيق الفواتير الضريبية وسندات الصرف والقبض.',
                    ],
                    'reports' => [
                        'name_en' => 'Financial & Stock Reports',
                        'name_ar' => 'التقارير المالية والمحاسبية والمخزنية',
                        'desc_en' => 'Sales revenue trends, inventory valuation, and P&L summaries.',
                        'desc_ar' => 'تقارير المبيعات وحركات المستودعات والتقارير الإحصائية.',
                    ],
                ],
            ],
            'system_control' => [
                'label' => [
                    'en' => 'System Security, Governance & Administration',
                    'ar' => 'أمن النظام والحوكمة وإدارة الصلاحيات',
                ],
                'icon' => 'fa-shield-halved',
                'color' => '#8B5CF6',
                'modules' => [
                    'users' => [
                        'name_en' => 'Staff User Accounts & Credentials',
                        'name_ar' => 'حسابات موظفي النظام وبيانات الدخول',
                        'desc_en' => 'Create staff logins, assign roles, activate/suspend accounts, impersonate.',
                        'desc_ar' => 'إنشاء مستخدمي النظام وتعيين الأدوار وتجميد الحسابات ومحاكاة الدخول.',
                    ],
                    'roles' => [
                        'name_en' => 'Roles & Granular Permissions Matrix',
                        'name_ar' => 'مصفوفة الأدوار والصلاحيات المتقدمة',
                        'desc_en' => 'Manage system role hierarchies, granular matrix checkboxes, and policies.',
                        'desc_ar' => 'تخصيص الأدوار الأمنية والتحكم في مصفوفة الصلاحيات الدقيقة.',
                    ],
                    'content' => [
                        'name_en' => 'Content & CMS Pages',
                        'name_ar' => 'إدارة المحتوى والصفحات والمقالات',
                        'desc_en' => 'Wellness journal, blog articles, brand pages, and FAQs.',
                        'desc_ar' => 'إدارة المقالات والمحتوى الترويجي والصفحات التعريفية.',
                    ],
                    'settings' => [
                        'name_en' => 'System & Store Settings',
                        'name_ar' => 'إعدادات النظام والتهيئة العامة',
                        'desc_en' => 'Branding tokens, currencies, VAT taxes, gateways, and configurations.',
                        'desc_ar' => 'إعدادات المتجر والعملات والضرائب وبوابات الربط والتهيئة العامة.',
                    ],
                    'notifications' => [
                        'name_en' => 'System & Push Notifications',
                        'name_ar' => 'الإشعارات والتنبيهات المباشرة',
                        'desc_en' => 'Broadcast FCM push notifications, internal alert center, and SMS.',
                        'desc_ar' => 'إرسال الإشعارات وتنبيهات الأجهزة وتخصيص قنوات الإشعار.',
                    ],
                ],
            ],
        ];
    }

    /**
     * Flat module list key => label for backwards compatibility.
     *
     * @return array<string, string>
     */
    public static function modules(): array
    {
        $flat = [];
        foreach (self::categorizedModules() as $domain) {
            foreach ($domain['modules'] as $modKey => $meta) {
                $flat[$modKey] = app()->getLocale() === 'ar' ? $meta['name_ar'] : $meta['name_en'];
            }
        }
        return $flat;
    }

    /**
     * Pre-configured templates for specific system roles.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function templates(): array
    {
        return [
            'mr' => [
                'name' => 'Medical Representative (MR)',
                'name_ar' => 'مندوب دعاية طبية (MR)',
                'description' => 'Field sales representative focused on doctors, clinic visits, schedules, and territory coverage.',
                'permissions' => [
                    'mr_dashboard' => ['view' => true, 'create' => true, 'edit' => true, 'delete' => false],
                    'mr_visits' => ['view' => true, 'create' => true, 'edit' => true, 'delete' => false],
                    'mr_contacts' => ['view' => true, 'create' => false, 'edit' => false, 'delete' => false],
                    'mr_assignments' => ['view' => true, 'create' => false, 'edit' => false, 'delete' => false],
                    'mr_territories' => ['view' => true, 'create' => false, 'edit' => false, 'delete' => false],
                    'mr_reports' => ['view' => true, 'create' => false, 'edit' => false, 'delete' => false],
                    'products' => ['view' => true, 'create' => false, 'edit' => false, 'delete' => false],
                    'hr_attendance' => ['view' => true, 'create' => true, 'edit' => false, 'delete' => false],
                ],
            ],
            'mr_line_manager' => [
                'name' => 'MR Line Manager',
                'name_ar' => 'مشرف ومدير مناديب دعاية (Line Manager)',
                'description' => 'Supervises field representatives, allocates doctor portfolios, reviews GPS visits, and approves coverage plans.',
                'permissions' => [
                    'mr_dashboard' => ['view' => true, 'create' => true, 'edit' => true, 'delete' => true],
                    'mr_visits' => ['view' => true, 'create' => true, 'edit' => true, 'delete' => true],
                    'mr_contacts' => ['view' => true, 'create' => true, 'edit' => true, 'delete' => false],
                    'mr_assignments' => ['view' => true, 'create' => true, 'edit' => true, 'delete' => true],
                    'mr_territories' => ['view' => true, 'create' => true, 'edit' => true, 'delete' => false],
                    'mr_reports' => ['view' => true, 'create' => true, 'edit' => true, 'delete' => false],
                    'products' => ['view' => true, 'create' => false, 'edit' => false, 'delete' => false],
                    'users' => ['view' => true, 'create' => false, 'edit' => false, 'delete' => false],
                    'hr_attendance' => ['view' => true, 'create' => true, 'edit' => true, 'delete' => false],
                ],
            ],
            'sales_staff' => [
                'name' => 'Sales Staff (Commercial CRM)',
                'name_ar' => 'مسؤول مبيعات تجارية (Sales Staff)',
                'description' => 'Commercial sales representative managing B2B leads, opportunities, proposals, and deals.',
                'permissions' => [
                    'crm_leads' => ['view' => true, 'create' => true, 'edit' => true, 'delete' => false],
                    'crm_opportunities' => ['view' => true, 'create' => true, 'edit' => true, 'delete' => false],
                    'crm_activities' => ['view' => true, 'create' => true, 'edit' => true, 'delete' => false],
                    'offline_sales' => ['view' => true, 'create' => true, 'edit' => true, 'delete' => false],
                    'orders' => ['view' => true, 'create' => true, 'edit' => false, 'delete' => false],
                    'customers' => ['view' => true, 'create' => true, 'edit' => true, 'delete' => false],
                    'invoices' => ['view' => true, 'create' => false, 'edit' => false, 'delete' => false],
                    'products' => ['view' => true, 'create' => false, 'edit' => false, 'delete' => false],
                    'hr_attendance' => ['view' => true, 'create' => true, 'edit' => false, 'delete' => false],
                ],
            ],
            'inventory_staff' => [
                'name' => 'Inventory Staff',
                'name_ar' => 'أمين مستودع ومخزون (Inventory Staff)',
                'description' => 'Manages warehouse movements, lot receiving, dispatching, stock audits, and counts.',
                'permissions' => [
                    'inventory' => ['view' => true, 'create' => true, 'edit' => true, 'delete' => false],
                    'products' => ['view' => true, 'create' => false, 'edit' => true, 'delete' => false],
                    'orders' => ['view' => true, 'create' => false, 'edit' => true, 'delete' => false],
                    'reports' => ['view' => true, 'create' => false, 'edit' => false, 'delete' => false],
                    'hr_attendance' => ['view' => true, 'create' => true, 'edit' => false, 'delete' => false],
                ],
            ],
            'hr_manager' => [
                'name' => 'HR Manager',
                'name_ar' => 'مدير الموارد البشرية (HR Manager)',
                'description' => 'Manages personnel dossiers, attendance time clock approvals, departments, shifts, and compensation.',
                'permissions' => [
                    'hr_employees' => ['view' => true, 'create' => true, 'edit' => true, 'delete' => true],
                    'hr_attendance' => ['view' => true, 'create' => true, 'edit' => true, 'delete' => true],
                    'hr_departments' => ['view' => true, 'create' => true, 'edit' => true, 'delete' => true],
                    'hr_payroll' => ['view' => true, 'create' => true, 'edit' => true, 'delete' => false],
                    'users' => ['view' => true, 'create' => false, 'edit' => false, 'delete' => false],
                    'notifications' => ['view' => true, 'create' => true, 'edit' => false, 'delete' => false],
                ],
            ],
            'super_admin' => [
                'name' => 'Super Admin',
                'name_ar' => 'المدير العام للنظام (Super Admin)',
                'description' => 'Root unrestricted authority across all application modules, security matrices, and settings.',
                'permissions' => ['*'],
            ],
        ];
    }
}

@extends('components.layouts.admin')
@section('title', app()->getLocale() === 'ar' ? 'إنهاء الخدمة' : 'Offboarding & Settlements')
@section('content')
<div style="padding:2rem;">
    <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1.5rem;">
        <i class="fa-solid fa-person-walking-arrow-right" style="font-size:2rem;color:var(--red-400, #94a3b8);"></i>
        <div>
            <h1 style="font-size:1.5rem;font-weight:700;margin:0;">{{ app()->getLocale() === 'ar' ? 'إنهاء الخدمة' : 'Offboarding & Settlements' }}</h1>
            <p style="color:#94a3b8;margin:.25rem 0 0;">{{ app()->getLocale() === 'ar' ? 'قيد التطوير — يرجى المتابعة لاحقاً' : 'This module is under development — coming soon.' }}</p>
        </div>
    </div>
    <div style="background:#1e293b;border-radius:.75rem;border:1px solid #334155;padding:3rem;text-align:center;color:#94a3b8;">
        <i class="fa-solid fa-hammer" style="font-size:3rem;margin-bottom:1rem;display:block;opacity:.4;"></i>
        <p style="font-size:1.1rem;font-weight:600;">{{ app()->getLocale() === 'ar' ? 'هذا القسم قيد البناء' : 'Module Under Construction' }}</p>
        <p style="font-size:.85rem;">{{ app()->getLocale() === 'ar' ? 'سيتم إطلاق هذه الميزة قريباً ضمن التحديث القادم.' : 'This feature will be available in an upcoming release.' }}</p>
        <a href="{{ route('admin.hr.dashboard') }}" style="margin-top:1rem;display:inline-flex;align-items:center;gap:.5rem;padding:.5rem 1.25rem;border-radius:.5rem;background:rgba(99,102,241,.2);color:#818cf8;text-decoration:none;font-size:.875rem;">
            <i class="fa-solid fa-arrow-left"></i>
            {{ app()->getLocale() === 'ar' ? 'العودة للوحة التحكم' : 'Back to HR Dashboard' }}
        </a>
    </div>
</div>
@endsection

<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'أنواع الإجازات والسياسات' : 'Leave Types & Policies'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'تهيئة أنواع الإجازات الرسمية وشروطها' : 'Configure leave quotas, paid leave policies and requirements'"
>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0; color: var(--text-color, #1E293B);">
                <i class="fa-solid fa-tags text-amber-500 mr-2 ml-2"></i> {{ app()->getLocale() === 'ar' ? 'أنواع الإجازات' : 'Leave Categories' }}
            </h2>
        </div>
        <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('addTypeModal').style.display='flex'">
            <i class="fa-solid fa-plus text-xs"></i> Add Leave Type
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="padding: 0.75rem 1rem; margin-bottom: 1rem; border-radius: 0.5rem; background: #DCFCE7; color: #166534; border: 1px solid #BBF7D0;">
            {{ session('success') }}
        </div>
    @endif

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.25rem;">
        @forelse($types as $lt)
            <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem;">
                    <div>
                        <h3 style="font-size: 1rem; font-weight: 700; margin: 0; color: #0284C7;">{{ $lt->name_en }}</h3>
                        <span style="font-size: 0.8rem; color: #64748B;">{{ $lt->name_ar }}</span>
                    </div>
                    <span class="badge" style="background: {{ $lt->is_paid ? '#DCFCE7' : '#F1F5F9' }}; color: {{ $lt->is_paid ? '#166534' : '#475569' }}; padding: 0.2rem 0.5rem; border-radius: 999px; font-weight: 700; font-size: 0.75rem;">
                        {{ $lt->is_paid ? 'Paid' : 'Unpaid' }}
                    </span>
                </div>
                <div style="font-size: 1.8rem; font-weight: 800; color: #1E293B; margin: 0.5rem 0;">
                    {{ $lt->annual_days }} <span style="font-size: 0.85rem; font-weight: 600; color: #64748B;">days / year</span>
                </div>
                <div style="font-size: 0.8rem; color: #64748B; display: flex; flex-direction: column; gap: 0.25rem; border-top: 1px solid #F1F5F9; padding-top: 0.5rem;">
                    <div><i class="fa-solid fa-file-arrow-up text-xs mr-1 ml-1"></i> Requires Attachment: <strong>{{ $lt->requires_attachment ? 'Yes' : 'No' }}</strong></div>
                    <div><i class="fa-solid fa-share-nodes text-xs mr-1 ml-1"></i> Carry Forward: <strong>{{ $lt->carry_forward ? 'Yes' : 'No' }}</strong></div>
                </div>
            </div>
        @empty
            <p style="color: #94A3B8; text-align: center; grid-column: 1 / -1;">No leave policies configured.</p>
        @endforelse
    </div>

    <!-- Add Type Modal -->
    <div id="addTypeModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div style="background: #ffffff; border-radius: 0.75rem; padding: 1.5rem; width: 100%; max-width: 450px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="font-size: 1.1rem; font-weight: 700; margin: 0;">Add Leave Type</h3>
                <button type="button" onclick="document.getElementById('addTypeModal').style.display='none'" style="background: none; border: none; font-size: 1.25rem; cursor: pointer;">&times;</button>
            </div>
            <form action="{{ route('admin.hr.leave.types.store') }}" method="POST">
                @csrf
                <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Name (English) *</label>
                        <input type="text" name="name_en" required class="form-control" placeholder="e.g. Annual Leave" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Name (Arabic) *</label>
                        <input type="text" name="name_ar" required class="form-control" placeholder="e.g. إجازة سنوية" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Annual Entitlement (Days) *</label>
                        <input type="number" step="0.5" name="annual_days" value="21" required class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                    </div>
                    <div style="display: flex; gap: 1rem;">
                        <label style="font-size: 0.8rem; display: flex; align-items: center; gap: 0.4rem;">
                            <input type="checkbox" name="is_paid" value="1" checked> Paid Leave
                        </label>
                        <label style="font-size: 0.8rem; display: flex; align-items: center; gap: 0.4rem;">
                            <input type="checkbox" name="carry_forward" value="1"> Carry Forward
                        </label>
                    </div>
                    <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 1rem;">
                        <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('addTypeModal').style.display='none'">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm">Save Policy</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>

<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'العروض الوظيفية' : 'Job Offers'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'إصدار ومتابعة العروض الوظيفية الرسمية' : 'Generate, send and track formal job offers and responses'"
>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0; color: var(--text-color, #1E293B);">
                <i class="fa-solid fa-file-signature text-emerald-500 mr-2 ml-2"></i> {{ app()->getLocale() === 'ar' ? 'العروض الوظيفية' : 'Job Offers' }}
            </h2>
        </div>
        <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('issueOfferModal').style.display='flex'">
            <i class="fa-solid fa-plus text-xs"></i> Issue New Offer
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="padding: 0.75rem 1rem; margin-bottom: 1rem; border-radius: 0.5rem; background: #DCFCE7; color: #166534; border: 1px solid #BBF7D0;">
            {{ session('success') }}
        </div>
    @endif

    <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
        <div class="table-responsive">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                <thead>
                    <tr style="border-bottom: 2px solid #E2E8F0; text-align: left; color: #64748B;">
                        <th style="padding: 0.75rem 0.5rem;">Candidate</th>
                        <th style="padding: 0.75rem 0.5rem;">Position</th>
                        <th style="padding: 0.75rem 0.5rem;">Offered Salary</th>
                        <th style="padding: 0.75rem 0.5rem;">Start Date</th>
                        <th style="padding: 0.75rem 0.5rem;">Offer Expiry</th>
                        <th style="padding: 0.75rem 0.5rem;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($offers as $offer)
                        <tr style="border-bottom: 1px solid #F1F5F9;">
                            <td style="padding: 0.75rem 0.5rem; font-weight: 700; color: #0284C7;">
                                {{ $offer->candidate?->first_name }} {{ $offer->candidate?->last_name }}
                            </td>
                            <td style="padding: 0.75rem 0.5rem;">{{ $offer->position?->name_en ?? '—' }}</td>
                            <td style="padding: 0.75rem 0.5rem; font-weight: 700; color: #059669;">
                                {{ number_format($offer->salary, 2) }} {{ currency_code() }}
                            </td>
                            <td style="padding: 0.75rem 0.5rem;">{{ $offer->start_date }}</td>
                            <td style="padding: 0.75rem 0.5rem; color: #64748B;">{{ $offer->offer_expiry_date }}</td>
                            <td style="padding: 0.75rem 0.5rem;">
                                <span class="badge" style="background: {{ $offer->status === 'accepted' ? '#DCFCE7' : ($offer->status === 'sent' ? '#FEF3C7' : '#FEE2E2') }}; padding: 0.2rem 0.5rem; border-radius: 999px; font-weight: 700;">
                                    {{ ucfirst($offer->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 3rem; color: #94A3B8;">No job offers generated yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top: 1rem;">
            {{ $offers->links() }}
        </div>
    </div>

    <!-- Issue Offer Modal -->
    <div id="issueOfferModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div style="background: #ffffff; border-radius: 0.75rem; padding: 1.5rem; width: 100%; max-width: 500px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="font-size: 1.1rem; font-weight: 700; margin: 0;">Issue Job Offer</h3>
                <button type="button" onclick="document.getElementById('issueOfferModal').style.display='none'" style="background: none; border: none; font-size: 1.25rem; cursor: pointer;">&times;</button>
            </div>
            <form action="{{ route('admin.hr.recruitment.offers.store') }}" method="POST">
                @csrf
                <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Candidate *</label>
                        <select name="candidate_id" required class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                            @foreach($candidates as $cand)
                                <option value="{{ $cand->id }}">{{ $cand->first_name }} {{ $cand->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Department *</label>
                        <select name="department_id" required class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name_en }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Position *</label>
                        <select name="position_id" required class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                            @foreach($positions as $pos)
                                <option value="{{ $pos->id }}">{{ $pos->name_en }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                        <div>
                            <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Offered Salary *</label>
                            <input type="number" step="0.01" name="salary" required class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                        </div>
                        <div>
                            <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Employment Type</label>
                            <select name="employment_type" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                                <option value="full_time">Full Time</option>
                                <option value="part_time">Part Time</option>
                                <option value="contract">Contract</option>
                            </select>
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                        <div>
                            <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Start Date *</label>
                            <input type="date" name="start_date" required class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                        </div>
                        <div>
                            <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Offer Expiry Date *</label>
                            <input type="date" name="offer_expiry_date" required class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                        </div>
                    </div>
                    <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 1rem;">
                        <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('issueOfferModal').style.display='none'">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm">Issue Offer</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>

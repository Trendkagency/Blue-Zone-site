<x-layouts.admin 
    :pageTitle="__('crm.pipelines.title')" 
    :pageSubtitle="__('crm.pipelines.subtitle')"
>
    <!-- New Pipeline Form Card -->
    <div class="card" style="padding: 1.25rem 1.5rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0); margin-bottom: 1.5rem;">
        <h3 style="font-size: 1rem; font-weight: 700; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fa-solid fa-plus text-sky-500"></i> Create New Pipeline
        </h3>
        <form method="POST" action="{{ route('admin.crm.pipelines.store') }}" style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: flex-end;">
            @csrf
            <div style="flex: 1; min-width: 200px;">
                <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem;">Pipeline Name (EN)</label>
                <input type="text" name="name_en" required placeholder="e.g. Clinic Bulk Distribution" class="form-input" style="width: 100%; padding: 0.45rem 0.65rem; border-radius: 0.35rem; border: 1px solid #CBD5E1; font-size: 0.85rem;">
            </div>

            <div style="flex: 1; min-width: 200px;">
                <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem;">Pipeline Name (AR)</label>
                <input type="text" name="name_ar" required placeholder="مسار توزيع العيادات" class="form-input" style="width: 100%; padding: 0.45rem 0.65rem; border-radius: 0.35rem; border: 1px solid #CBD5E1; font-size: 0.85rem;">
            </div>

            <button type="submit" class="btn btn-primary btn-sm" style="padding: 0.5rem 1rem;">
                <i class="fa-solid fa-check mr-1 ml-1"></i> Add Pipeline
            </button>
        </form>
    </div>

    <!-- Existing Pipelines List -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        @foreach($pipelines as $pipeline)
            <div class="card" style="padding: 1.5rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; border-bottom: 1px solid #F1F5F9; padding-bottom: 0.75rem;">
                    <div>
                        <strong style="font-size: 1.1rem; color: #0F172A;">{{ $pipeline->name }}</strong>
                        <span style="font-size: 0.75rem; color: #64748B; margin-left: 0.5rem; margin-right: 0.5rem;">({{ $pipeline->slug }})</span>
                        @if($pipeline->is_default)
                            <span style="font-size: 0.7rem; font-weight: 700; background: #E0F2FE; color: #0284C7; padding: 0.15rem 0.45rem; border-radius: 0.25rem;">Default</span>
                        @endif
                    </div>
                </div>

                <!-- Stages Grid -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 0.75rem; margin-bottom: 1rem;">
                    @foreach($pipeline->stages as $stage)
                        <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-top: 3px solid {{ $stage->color ?? '#0284C7' }}; border-radius: 0.5rem; padding: 0.75rem;">
                            <div style="font-weight: 700; font-size: 0.85rem; color: #0F172A;">{{ $stage->name }}</div>
                            <div style="font-size: 0.75rem; color: #64748B; margin-top: 0.25rem;">
                                Probability: <strong style="color: #0284C7;">{{ $stage->probability }}%</strong>
                            </div>
                            @if($stage->is_won)
                                <span style="font-size: 0.65rem; font-weight: 700; background: #DCFCE7; color: #16A34A; padding: 0.1rem 0.35rem; border-radius: 0.2rem; display: inline-block; margin-top: 0.35rem;">WON STAGE</span>
                            @elseif($stage->is_lost)
                                <span style="font-size: 0.65rem; font-weight: 700; background: #FEE2E2; color: #DC2626; padding: 0.1rem 0.35rem; border-radius: 0.2rem; display: inline-block; margin-top: 0.35rem;">LOST STAGE</span>
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- Quick Add Stage Form -->
                <form method="POST" action="{{ route('admin.crm.pipelines.stages.store', $pipeline->id) }}" style="display: flex; flex-wrap: wrap; gap: 0.5rem; align-items: center; background: #F1F5F9; padding: 0.75rem; border-radius: 0.5rem;">
                    @csrf
                    <span style="font-size: 0.8rem; font-weight: 700; color: #475569;">+ Add Stage:</span>
                    <input type="text" name="name_en" required placeholder="Stage (EN)" class="form-input" style="padding: 0.35rem 0.55rem; font-size: 0.8rem; border-radius: 0.35rem; border: 1px solid #CBD5E1; width: 140px;">
                    <input type="text" name="name_ar" required placeholder="Stage (AR)" class="form-input" style="padding: 0.35rem 0.55rem; font-size: 0.8rem; border-radius: 0.35rem; border: 1px solid #CBD5E1; width: 140px;">
                    <input type="number" name="probability" required placeholder="Prob %" min="0" max="100" class="form-input" style="padding: 0.35rem 0.55rem; font-size: 0.8rem; border-radius: 0.35rem; border: 1px solid #CBD5E1; width: 80px;">
                    <label style="font-size: 0.75rem; display: flex; align-items: center; gap: 0.25rem;">
                        <input type="checkbox" name="is_won" value="1"> Won
                    </label>
                    <label style="font-size: 0.75rem; display: flex; align-items: center; gap: 0.25rem;">
                        <input type="checkbox" name="is_lost" value="1"> Lost
                    </label>
                    <button type="submit" class="btn btn-secondary btn-xs">Add Stage</button>
                </form>
            </div>
        @endforeach
    </div>
</x-layouts.admin>

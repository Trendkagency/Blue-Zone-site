<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CrmPipeline;
use App\Models\CrmPipelineStage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CrmPipelineController extends Controller
{
    /**
     * Display pipelines and stages.
     */
    public function index()
    {
        $pipelines = CrmPipeline::with(['stages' => function ($q) {
            $q->orderBy('sort_order');
        }])->orderBy('sort_order')->get();

        return view('admin.crm.pipelines.index', compact('pipelines'));
    }

    /**
     * Store new pipeline.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name_en' => ['required', 'string', 'max:100'],
            'name_ar' => ['required', 'string', 'max:100'],
        ]);

        $pipeline = CrmPipeline::create([
            'name_en' => $request->name_en,
            'name_ar' => $request->name_ar,
            'slug' => Str::slug($request->name_en),
            'is_default' => false,
            'is_active' => true,
        ]);

        // Create default stages for new pipeline
        $defaultStages = [
            ['name_en' => 'New', 'name_ar' => 'جديد', 'prob' => 10, 'won' => false, 'lost' => false, 'color' => '#64748B'],
            ['name_en' => 'Qualified', 'name_ar' => 'مؤهل', 'prob' => 40, 'won' => false, 'lost' => false, 'color' => '#0EA5E9'],
            ['name_en' => 'Proposal', 'name_ar' => 'تقديم العرض', 'prob' => 70, 'won' => false, 'lost' => false, 'color' => '#F59E0B'],
            ['name_en' => 'Won', 'name_ar' => 'تم الإغلاق بنجاح', 'prob' => 100, 'won' => true, 'lost' => false, 'color' => '#10B981'],
            ['name_en' => 'Lost', 'name_ar' => 'خسارة الصفقة', 'prob' => 0, 'won' => false, 'lost' => true, 'color' => '#EF4444'],
        ];

        foreach ($defaultStages as $idx => $st) {
            CrmPipelineStage::create([
                'pipeline_id' => $pipeline->id,
                'name_en' => $st['name_en'],
                'name_ar' => $st['name_ar'],
                'slug' => Str::slug($st['name_en']),
                'probability' => $st['prob'],
                'is_won' => $st['won'],
                'is_lost' => $st['lost'],
                'sort_order' => $idx + 1,
                'color' => $st['color'],
            ]);
        }

        return back()->with('success', __('crm.pipelines.created_successfully'));
    }

    /**
     * Store new stage.
     */
    public function storeStage(Request $request, int $pipelineId)
    {
        $request->validate([
            'name_en' => ['required', 'string', 'max:100'],
            'name_ar' => ['required', 'string', 'max:100'],
            'probability' => ['required', 'integer', 'between:0,100'],
            'color' => ['nullable', 'string', 'max:20'],
        ]);

        $maxSort = CrmPipelineStage::where('pipeline_id', $pipelineId)->max('sort_order') ?? 0;

        CrmPipelineStage::create([
            'pipeline_id' => $pipelineId,
            'name_en' => $request->name_en,
            'name_ar' => $request->name_ar,
            'slug' => Str::slug($request->name_en),
            'probability' => $request->probability,
            'color' => $request->color ?? '#0EA5E9',
            'sort_order' => $maxSort + 1,
            'is_won' => $request->boolean('is_won'),
            'is_lost' => $request->boolean('is_lost'),
        ]);

        return back()->with('success', __('crm.pipelines.stage_created_successfully'));
    }
}

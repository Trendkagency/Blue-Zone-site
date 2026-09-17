<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CrmSegment;
use App\Services\CrmSegmentService;
use Illuminate\Http\Request;

class CrmSegmentController extends Controller
{
    public function __construct(
        protected CrmSegmentService $segmentService
    ) {}

    /**
     * Display list of customer segments.
     */
    public function index()
    {
        $segments = CrmSegment::active()->get();
        return view('admin.crm.segments.index', compact('segments'));
    }

    /**
     * Show customers falling into this segment.
     */
    public function show(int $id)
    {
        $segment = CrmSegment::findOrFail($id);
        $customers = $this->segmentService->getCustomersForSegment($segment);

        return view('admin.crm.segments.show', compact('segment', 'customers'));
    }

    /**
     * Refresh all segment counts.
     */
    public function refresh()
    {
        $this->segmentService->refreshSegmentCounts();
        return back()->with('success', __('crm.segments.refreshed_successfully'));
    }
}

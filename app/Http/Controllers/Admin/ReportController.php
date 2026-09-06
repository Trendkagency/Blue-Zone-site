<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\View\ViewModels\ReportViewModel;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        $kpi = ReportViewModel::kpi();
        $salesData = ReportViewModel::salesData();

        return view('admin.reports.index', [
            'kpi' => $kpi,
            'salesData' => $salesData,
        ]);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\View\ViewModels\SettingViewModel;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        $settings = SettingViewModel::all();

        return view('admin.settings.index', [
            'settings' => $settings,
        ]);
    }
}

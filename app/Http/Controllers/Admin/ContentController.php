<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\View\ViewModels\ContentViewModel;
use Illuminate\View\View;

class ContentController extends Controller
{
    public function index(): View
    {
        $content = ContentViewModel::all();

        return view('admin.content.index', [
            'content' => $content,
        ]);
    }

    public function banners(): View
    {
        $content = ContentViewModel::all();
        return view('admin.content.banners', ['hero' => $content['hero']]);
    }

    public function story(): View
    {
        $content = ContentViewModel::all();
        return view('admin.content.story', ['zones' => $content['zones']]);
    }

    public function wellness(): View
    {
        return view('admin.content.wellness');
    }

    public function faqs(): View
    {
        $content = ContentViewModel::all();
        return view('admin.content.faqs', ['faqs' => $content['faqs']]);
    }
}

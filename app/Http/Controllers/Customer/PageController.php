<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\View\ViewModels\ContentViewModel;
use Illuminate\View\View;

class PageController extends Controller
{
    public function about(): View
    {
        $content = ContentViewModel::all();
        return view('customer.pages.about', ['zones' => $content['zones']]);
    }

    public function science(): View
    {
        return view('customer.pages.science');
    }

    public function team(): View
    {
        return view('customer.pages.team');
    }

    public function contact(): View
    {
        return view('customer.pages.contact');
    }

    public function faqs(): View
    {
        $content = ContentViewModel::all();
        return view('customer.pages.faqs', ['faqs' => $content['faqs']]);
    }

    public function privacy(): View
    {
        return view('customer.pages.privacy');
    }

    public function blog(): View
    {
        return view('customer.pages.blog');
    }

    public function terms(): View
    {
        return view('customer.pages.terms');
    }
}

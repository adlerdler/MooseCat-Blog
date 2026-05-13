<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class FrontendController extends Controller
{
    public function home(): View
    {
        return view('frontend.index');
    }

    public function blog(): View
    {
        return view('frontend.blog');
    }

    public function projects(): View
    {
        return view('frontend.projects');
    }

    public function resources(): View
    {
        return view('frontend.resources');
    }

    public function videos(): View
    {
        return view('frontend.videos');
    }

    public function adminLogin(): View
    {
        return view('frontend.index');
    }

    public function admin(): View
    {
        return view('frontend.index');
    }
}
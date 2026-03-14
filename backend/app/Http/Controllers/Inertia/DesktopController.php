<?php

namespace App\Http\Controllers\Inertia;

use Illuminate\Routing\Controller;
use Inertia\Inertia;

class DesktopController extends Controller
{
    public function create()
    {
        return Inertia::render('Desktop', [
            'title' => 'Desktop',
        ]);
    }
}

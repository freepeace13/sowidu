<?php

namespace App\Http\Controllers\Inertia\Auth;

use Illuminate\Routing\Controller;
use Inertia\Inertia;

class EmployeeController extends Controller
{
    public function create()
    {
        return Inertia::render('Auth/Employees', [
            'title' => 'Employees',
        ]);
    }
}

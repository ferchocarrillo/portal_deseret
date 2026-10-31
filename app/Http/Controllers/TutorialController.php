<?php

namespace App\Http\Controllers;

use App\Models\Tutorial;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TutorialController extends Controller
{
    public function index()
    {
        $tutorials = Tutorial::where('is_active', true)
            ->orderBy('category')
            ->orderBy('order')
            ->get();

        return Inertia::render('Tutorials/Index', [
            'tutorials' => $tutorials
        ]);
    }
}

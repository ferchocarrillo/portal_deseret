<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class ConfiguracionController extends Controller
{
    public function index()
    {
        return Inertia::render('admin/Configuracion/Index');
    }
}

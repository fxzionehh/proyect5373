<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\Role;
use Inertia\Inertia;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::latest()->get();

        return Inertia::render('Admin/Roles/Index', [
            'roles' => $roles
        ]);
    }
}
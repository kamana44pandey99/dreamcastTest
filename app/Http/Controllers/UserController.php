<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // Show the form to add users
    public function showForm()
    {
        $roles = Role::all();
        $users = User::with('role')->get();
        return view('form', compact('roles','users'));
    }
}


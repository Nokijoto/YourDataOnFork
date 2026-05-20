<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $users = \App\Models\User::all(['name', 'email']);
    $services = \App\Models\SherlockService::where('is_active', true)->get(['id', 'name', 'url_pattern']);
    $rules = \App\Models\SherlockRule::all(['username', 'service_id', 'is_found']);
    return view('welcome', compact('users', 'services', 'rules'));
});

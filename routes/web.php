<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $users = \App\Models\User::all(['name', 'email']);
    $services = \App\Models\SherlockService::where('is_active', true)->get(['id', 'name', 'url_pattern']);
    $rules = \App\Models\SherlockRule::all(['username', 'service_id', 'is_found']);
    $breaches = \App\Models\PwnedBreach::where('is_active', true)->get(['id', 'name', 'breach_date', 'compromised_data']);
    $pwnedRules = \App\Models\PwnedRule::all(['email', 'breach_id', 'is_pwned', 'custom_password', 'custom_username', 'custom_phone', 'custom_name']);
    return view('welcome', compact('users', 'services', 'rules', 'breaches', 'pwnedRules'));
});

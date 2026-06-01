<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        // Settings page can hold preferences, API limits, 2FA settings etc.
        return view('settings.index');
    }

    public function update(Request $request)
    {
        // Dummy update method
        return back()->with('success', 'Pengaturan berhasil disimpan.');
    }
}

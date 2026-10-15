<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolProfile;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    /**
     * Display the system settings page.
     */
    public function index(Request $request): View
    {
        $schoolProfile = SchoolProfile::first();

        $breadcrumbs = [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'Pengaturan Sistem', 'url' => null],
        ];

        return view('admin.settings.index', compact('schoolProfile', 'breadcrumbs'));
    }
}

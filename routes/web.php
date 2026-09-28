<?php

use App\Http\Controllers\AuthController;
use App\Models\Company;
use App\Models\Job;
use App\Models\Major;
use App\Models\Skill;
use Illuminate\Support\Facades\Route;

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// List of supported landing section paths
$landingSections = 'beranda|platform|sekolah|fitur|fitur-unggulan|cara-kerja|statistik|faq';

// Root landing page route
Route::get('/', function () {
    $stats = [
        'majors_count' => Major::count(),
        'skills_count' => Skill::count(),
        'jobs_count' => Job::count(),
        'companies_count' => Company::count(),
    ];
    $initialSection = 'beranda';

    return view('welcome', compact('stats', 'initialSection'));
})->name('landing');

// Section routes: /beranda, /platform, /sekolah, /fitur-unggulan, /cara-kerja, /statistik, /faq
Route::get('/{section}', function (string $section) {
    // Normalize aliases
    if ($section === 'fitur') {
        $section = 'fitur-unggulan';
    }

    $stats = [
        'majors_count' => Major::count(),
        'skills_count' => Skill::count(),
        'jobs_count' => Job::count(),
        'companies_count' => Company::count(),
    ];
    $initialSection = $section;

    return view('welcome', compact('stats', 'initialSection'));
})->where('section', $landingSections)->name('landing.section');

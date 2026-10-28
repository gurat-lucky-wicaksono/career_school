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

// Admin Area Routes (Protected by Auth & Role: Admin)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    });

    // Data Master Routes
    Route::resource('teachers', \App\Http\Controllers\Admin\TeacherController::class);
    Route::resource('students', \App\Http\Controllers\Admin\StudentController::class);
    Route::resource('majors', \App\Http\Controllers\Admin\MajorController::class);
    Route::resource('classes', \App\Http\Controllers\Admin\SchoolClassController::class);
    Route::resource('skills', \App\Http\Controllers\Admin\SkillController::class);

    // Kemitraan Industri Routes
    Route::get('/companies/verification', [\App\Http\Controllers\Admin\CompanyController::class, 'verification'])->name('companies.verification');
    Route::post('/companies/{company}/verify', [\App\Http\Controllers\Admin\CompanyController::class, 'verify'])->name('companies.verify');
    Route::resource('companies', \App\Http\Controllers\Admin\CompanyController::class);
    Route::resource('jobs', \App\Http\Controllers\Admin\JobController::class);

    // Informasi BKK (Pengumuman & Agenda)
    Route::resource('announcements', \App\Http\Controllers\Admin\AnnouncementController::class);
    Route::resource('agendas', \App\Http\Controllers\Admin\AgendaController::class);

    // Pusat Notifikasi
    Route::get('/notifications', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAllAsRead'])->name('notifications.markAllRead');

    // Pencarian Global / Command Palette
    Route::get('/search', [\App\Http\Controllers\Admin\GlobalSearchController::class, 'search'])->name('search');

    // Sistem & Pengaturan
    Route::get('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings');
});

// Portal Mitra Industri (DUDI) Routes (Protected by Auth & Role: Perusahaan)
Route::middleware(['auth', 'role:perusahaan'])->prefix('company')->name('company.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Company\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/', function () {
        return redirect()->route('company.dashboard');
    });

    // Kelola Lowongan
    Route::patch('/jobs/{job}/toggle-status', [\App\Http\Controllers\Company\JobController::class, 'toggleStatus'])->name('jobs.toggle-status');
    Route::resource('jobs', \App\Http\Controllers\Company\JobController::class);

    // Kelola Pelamar Masuk
    Route::get('/applications', [\App\Http\Controllers\Company\ApplicationController::class, 'index'])->name('applications.index');
    Route::get('/applications/{application}', [\App\Http\Controllers\Company\ApplicationController::class, 'show'])->name('applications.show');
    Route::patch('/applications/{application}/status', [\App\Http\Controllers\Company\ApplicationController::class, 'updateStatus'])->name('applications.update-status');

    // Profil Perusahaan
    Route::get('/profile', [\App\Http\Controllers\Company\ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [\App\Http\Controllers\Company\ProfileController::class, 'update'])->name('profile.update');

    // Global Search (Ctrl + K)
    Route::get('/search', [\App\Http\Controllers\Company\GlobalSearchController::class, 'search'])->name('search');
});

// Portal Siswa Vokasi Routes (Protected by Auth & Role: Siswa)
Route::middleware(['auth', 'role:siswa'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Student\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/', function () {
        return redirect()->route('student.dashboard');
    });

    // Eksplorasi Lowongan Industri & Alur Lamar
    Route::get('/jobs', [\App\Http\Controllers\Student\JobController::class, 'index'])->name('jobs.index');
    Route::get('/jobs/{job}', [\App\Http\Controllers\Student\JobController::class, 'show'])->name('jobs.show');
    Route::post('/jobs/{job}/apply', [\App\Http\Controllers\Student\JobController::class, 'apply'])->name('jobs.apply');

    // Manajemen Lamaran Saya & Pelacakan Seleksi
    Route::get('/applications', [\App\Http\Controllers\Student\ApplicationController::class, 'index'])->name('applications.index');
    Route::get('/applications/{application}', [\App\Http\Controllers\Student\ApplicationController::class, 'show'])->name('applications.show');
    Route::delete('/applications/{application}', [\App\Http\Controllers\Student\ApplicationController::class, 'destroy'])->name('applications.destroy');

    // Karya & Portofolio Siswa (CRUD)
    Route::resource('portfolios', \App\Http\Controllers\Student\PortfolioController::class);

    // Profil & Keahlian Mandiri Siswa
    Route::get('/profile', [\App\Http\Controllers\Student\ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [\App\Http\Controllers\Student\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/skills', [\App\Http\Controllers\Student\ProfileController::class, 'addSkill'])->name('profile.skills.add');
    Route::delete('/profile/skills/{skill}', [\App\Http\Controllers\Student\ProfileController::class, 'removeSkill'])->name('profile.skills.remove');
});

// Portal Guru Kejuruan & Pembimbing PKL Routes (Protected by Auth & Role: Guru)
Route::middleware(['auth', 'role:guru'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Teacher\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/', function () {
        return redirect()->route('teacher.dashboard');
    });

    // Validasi Keahlian Siswa Vokasi
    Route::get('/skills', [\App\Http\Controllers\Teacher\SkillValidationController::class, 'index'])->name('skills.index');
    Route::post('/skills/{studentSkill}/validate', [\App\Http\Controllers\Teacher\SkillValidationController::class, 'validateSkill'])->name('skills.validate');
    Route::post('/skills/bulk-validate', [\App\Http\Controllers\Teacher\SkillValidationController::class, 'bulkValidate'])->name('skills.bulk-validate');
    Route::delete('/skills/{studentSkill}/reject', [\App\Http\Controllers\Teacher\SkillValidationController::class, 'rejectSkill'])->name('skills.reject');

    // Pemantauan Siswa Bimbingan & PKL
    Route::get('/mentoring', [\App\Http\Controllers\Teacher\StudentMentoringController::class, 'index'])->name('mentoring.index');

    // Portofolio Siswa Bimbingan
    Route::get('/portfolios', [\App\Http\Controllers\Teacher\StudentMentoringController::class, 'portfolios'])->name('portfolios.index');
});

// Komunitas Vokasi & Social Network Routes (Protected by Auth for All Roles)
Route::middleware(['auth'])->group(function () {
    // Feed & Post Management
    Route::get('/feed', [\App\Http\Controllers\FeedController::class, 'index'])->name('feed.index');
    Route::post('/feed', [\App\Http\Controllers\FeedController::class, 'store'])->name('feed.store');
    Route::delete('/feed/{post}', [\App\Http\Controllers\FeedController::class, 'destroy'])->name('feed.destroy');

    // Social Interactions (Like, Comment, Bookmark, Follow)
    Route::post('/posts/{post}/like', [\App\Http\Controllers\SocialInteractionController::class, 'toggleLike'])->name('posts.like');
    Route::post('/posts/{post}/comments', [\App\Http\Controllers\SocialInteractionController::class, 'storeComment'])->name('posts.comments.store');
    Route::delete('/comments/{comment}', [\App\Http\Controllers\SocialInteractionController::class, 'destroyComment'])->name('comments.destroy');
    Route::post('/posts/{post}/bookmark', [\App\Http\Controllers\SocialInteractionController::class, 'toggleBookmark'])->name('posts.bookmark');
    Route::post('/users/{user}/follow', [\App\Http\Controllers\SocialInteractionController::class, 'toggleFollow'])->name('users.follow');

    // Bookmarks Collection
    Route::get('/bookmarks', [\App\Http\Controllers\UserProfileController::class, 'bookmarks'])->name('bookmarks.index');

    // Public Community Profile
    Route::get('/u/{username}', [\App\Http\Controllers\UserProfileController::class, 'show'])->name('community.profile');
});

// Pusat Informasi Publik BKK (Pengumuman & Agenda)
Route::get('/pengumuman', [\App\Http\Controllers\PublicInformationController::class, 'announcements'])->name('public.announcements');
Route::get('/pengumuman/{announcement}', [\App\Http\Controllers\PublicInformationController::class, 'showAnnouncement'])->name('public.announcements.show');
Route::get('/agenda', [\App\Http\Controllers\PublicInformationController::class, 'agendas'])->name('public.agendas');

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

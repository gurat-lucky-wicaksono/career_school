<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterCompanyRequest;
use App\Http\Requests\Auth\RegisterStudentRequest;
use App\Http\Requests\Auth\RegisterTeacherRequest;
use App\Models\Company;
use App\Models\Major;
use App\Models\Profile;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Show the login page.
     */
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('landing')->with('info', 'Anda sudah masuk.');
        }

        return view('auth.login');
    }

    /**
     * Handle an incoming login request.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->validated();

        $loginInput = trim($credentials['login']);
        $fieldType = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $attemptCredentials = [
            $fieldType => $loginInput,
            'password' => $credentials['password'],
        ];

        $remember = $request->boolean('remember');

        if (!Auth::attempt($attemptCredentials, $remember)) {
            throw ValidationException::withMessages([
                'login' => 'Email/username atau kata sandi yang Anda masukkan salah.',
            ]);
        }

        /** @var User $user */
        $user = Auth::user();

        if (!$user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'login' => 'Akun Anda dinonaktifkan oleh administrator sekolah. Silakan hubungi tim BKK.',
            ]);
        }

        $user->update(['last_login_at' => now()]);
        $request->session()->regenerate();

        // Role-based post-login redirection
        if ($user->role?->nama === 'admin' && Route::has('admin.dashboard')) {
            return redirect()->intended(route('admin.dashboard'))->with('success', 'Selamat datang kembali, Administrator!');
        }

        if ($user->role?->nama === 'perusahaan' && Route::has('company.dashboard')) {
            return redirect()->intended(route('company.dashboard'))->with('success', 'Selamat datang di Portal Mitra Industri!');
        }

        return redirect()->intended(route('landing'))->with('success', 'Selamat datang kembali, ' . $user->name . '!');
    }

    /**
     * Show the registration page (Student Activation & Company Registration).
     */
    public function showRegister(Request $request): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('landing')->with('info', 'Anda sudah masuk.');
        }

        $majors = Major::with('classes')->get();
        $classes = SchoolClass::orderBy('tingkat')->orderBy('nama')->get();
        $initialType = $request->query('type', 'siswa');

        return view('auth.register', compact('majors', 'classes', 'initialType'));
    }

    /**
     * Handle registration (Siswa NISN, Guru Kejuruan, or Mitra Perusahaan).
     */
    public function register(Request $request): RedirectResponse
    {
        $roleType = $request->input('role_type', 'siswa');

        if ($roleType === 'guru') {
            return $this->registerTeacher(app(RegisterTeacherRequest::class));
        }

        if ($roleType === 'perusahaan') {
            return $this->registerCompany(app(RegisterCompanyRequest::class));
        }

        return $this->registerStudent(app(RegisterStudentRequest::class));
    }

    /**
     * Process student registration via NISN.
     */
    protected function registerStudent(RegisterStudentRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $siswaRole = Role::where('nama', 'siswa')->firstOrFail();

        $user = DB::transaction(function () use ($data, $siswaRole) {
            $user = User::create([
                'role_id' => $siswaRole->id,
                'name' => $data['name'],
                'username' => strtolower($data['username']),
                'email' => strtolower($data['email']),
                'password' => Hash::make($data['password']),
                'is_active' => true,
            ]);

            $selectedClass = SchoolClass::find($data['class_id']);

            Student::create([
                'user_id' => $user->id,
                'nisn' => $data['nisn'],
                'class_id' => $data['class_id'],
                'major_id' => $data['major_id'],
                'angkatan' => date('Y'),
                'status' => 'aktif',
            ]);

            Profile::create([
                'user_id' => $user->id,
                'bio' => 'Siswa ' . ($selectedClass ? $selectedClass->nama : 'SMKN 1 Cirebon') . ' bidang keahlian vokasi.',
            ]);

            return $user;
        });

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('landing')->with('success', 'Aktivasi akun siswa berhasil! Selamat datang di Career School SMKN 1 Cirebon.');
    }

    /**
     * Process teacher registration.
     */
    protected function registerTeacher(RegisterTeacherRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $guruRole = Role::where('nama', 'guru')->firstOrFail();

        $user = DB::transaction(function () use ($data, $guruRole) {
            $user = User::create([
                'role_id' => $guruRole->id,
                'name' => $data['name'],
                'username' => strtolower($data['username']),
                'email' => strtolower($data['email']),
                'password' => Hash::make($data['password']),
                'is_active' => true,
            ]);

            \App\Models\Teacher::create([
                'user_id' => $user->id,
                'nip' => $data['nip'],
                'mata_pelajaran' => $data['mata_pelajaran'],
                'is_wali_kelas' => false,
            ]);

            Profile::create([
                'user_id' => $user->id,
                'bio' => 'Guru Kejuruan ' . $data['mata_pelajaran'] . ' SMKN 1 Cirebon.',
            ]);

            return $user;
        });

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('landing')->with('success', 'Pendaftaran akun guru berhasil! Selamat datang di Career School SMKN 1 Cirebon.');
    }

    /**
     * Process company partner registration.
     */
    protected function registerCompany(RegisterCompanyRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $perusahaanRole = Role::where('nama', 'perusahaan')->firstOrFail();

        $user = DB::transaction(function () use ($data, $perusahaanRole) {
            $user = User::create([
                'role_id' => $perusahaanRole->id,
                'name' => $data['name'],
                'username' => strtolower($data['username']),
                'email' => strtolower($data['email']),
                'password' => Hash::make($data['password']),
                'is_active' => true,
            ]);

            Company::create([
                'user_id' => $user->id,
                'nama' => $data['nama_perusahaan'],
                'bidang' => $data['bidang'],
                'kota' => $data['kota'] ?? 'Cirebon',
                'status_verifikasi' => 'menunggu',
            ]);

            Profile::create([
                'user_id' => $user->id,
                'bio' => 'Mitra Industri DUDI ' . $data['nama_perusahaan'],
                'telepon' => $data['telepon'] ?? null,
            ]);

            return $user;
        });

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('landing')->with('success', 'Pendaftaran mitra berhasil! Akun Anda sedang dalam antrean verifikasi tim BKK SMKN 1 Cirebon.');
    }


    /**
     * Log out the current user session.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('landing')->with('success', 'Anda telah berhasil keluar.');
    }
}

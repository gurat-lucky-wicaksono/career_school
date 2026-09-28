<?php

namespace App\Http\Controllers;

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
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'login.required' => 'Email atau username wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

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
            return $this->registerTeacher($request);
        }

        if ($roleType === 'perusahaan') {
            return $this->registerCompany($request);
        }

        return $this->registerStudent($request);
    }

    /**
     * Process student registration via NISN.
     */
    protected function registerStudent(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nisn' => ['required', 'digits:10', 'unique:students,nisn'],
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:150', 'unique:users,email'],
            'username' => ['required', 'string', 'min:3', 'max:50', 'alpha_dash', 'unique:users,username'],
            'major_id' => ['required', 'exists:majors,id'],
            'class_id' => ['required', 'exists:classes,id'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'nisn.required' => 'Nomor Induk Siswa Nasional (NISN) wajib diisi.',
            'nisn.digits' => 'NISN harus berjumlah tepat 10 digit angka.',
            'nisn.unique' => 'NISN ini sudah terdaftar / teraktivasi sebelumnya.',
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.unique' => 'Alamat email ini sudah digunakan.',
            'username.required' => 'Username wajib diisi.',
            'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, garis bawah, dan tanda hubung.',
            'username.unique' => 'Username ini sudah digunakan.',
            'major_id.required' => 'Jurusan kejuruan wajib dipilih.',
            'class_id.required' => 'Kelas wajib dipilih.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

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
    protected function registerTeacher(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nip' => ['required', 'string', 'max:20', 'unique:teachers,nip'],
            'name' => ['required', 'string', 'max:100'],
            'mata_pelajaran' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:150', 'unique:users,email'],
            'username' => ['required', 'string', 'min:3', 'max:50', 'alpha_dash', 'unique:users,username'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'nip.required' => 'Nomor Induk Pegawai (NIP) wajib diisi.',
            'nip.unique' => 'NIP ini sudah terdaftar sebelumnya.',
            'name.required' => 'Nama lengkap dan gelar wajib diisi.',
            'mata_pelajaran.required' => 'Mata pelajaran / bidang kejuruan wajib diisi.',
            'email.required' => 'Alamat email dinas/sekolah wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Alamat email ini sudah digunakan.',
            'username.required' => 'Username wajib diisi.',
            'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, garis bawah, dan tanda hubung.',
            'username.unique' => 'Username ini sudah digunakan.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

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
    protected function registerCompany(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama_perusahaan' => ['required', 'string', 'max:150'],
            'name' => ['required', 'string', 'max:100'],
            'bidang' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:150', 'unique:users,email'],
            'username' => ['required', 'string', 'min:3', 'max:50', 'alpha_dash', 'unique:users,username'],
            'telepon' => ['nullable', 'string', 'max:20'],
            'kota' => ['nullable', 'string', 'max:80'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'nama_perusahaan.required' => 'Nama instansi/perusahaan wajib diisi.',
            'name.required' => 'Nama penanggung jawab (PIC/HRD) wajib diisi.',
            'bidang.required' => 'Bidang industri/sektor usaha wajib diisi.',
            'email.required' => 'Alamat email instansi/perusahaan wajib diisi.',
            'email.unique' => 'Alamat email ini sudah terdaftar.',
            'username.required' => 'Username akun instansi wajib diisi.',
            'username.unique' => 'Username ini sudah digunakan.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

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

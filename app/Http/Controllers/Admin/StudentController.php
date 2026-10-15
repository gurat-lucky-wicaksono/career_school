<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Major;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StudentController extends Controller
{
    /**
     * Display a listing of students with search and filter capabilities.
     */
    public function index(Request $request): View
    {
        $query = Student::with(['user', 'class', 'major']);

        // Search logic
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nisn', 'like', "%{$search}%")
                  ->orWhere('angkatan', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('class', function ($cq) use ($search) {
                      $cq->where('nama', 'like', "%{$search}%");
                  })
                  ->orWhereHas('major', function ($mq) use ($search) {
                      $mq->where('nama', 'like', "%{$search}%")
                         ->orWhere('kode', 'like', "%{$search}%");
                  });
            });
        }

        // Filter: Major
        if ($filterMajor = $request->input('filter_major')) {
            $query->where('major_id', $filterMajor);
        }

        // Filter: Class
        if ($filterClass = $request->input('filter_class')) {
            $query->where('class_id', $filterClass);
        }

        // Filter: Status
        if ($filterStatus = $request->input('filter_status')) {
            $query->where('status', $filterStatus);
        }

        // Filter: Angkatan
        if ($filterAngkatan = $request->input('filter_angkatan')) {
            $query->where('angkatan', $filterAngkatan);
        }

        // Sorting
        $sortField = $request->input('sort', 'created_at');
        $sortDirection = $request->input('direction', 'desc');

        if (in_array($sortField, ['nisn', 'angkatan', 'status', 'created_at'])) {
            $query->orderBy($sortField, $sortDirection === 'asc' ? 'asc' : 'desc');
        } else {
            $query->latest();
        }

        $students = $query->paginate(10)->withQueryString();

        // Data for dropdown filters and create/edit forms
        $majors = Major::orderBy('nama')->get();
        $classes = SchoolClass::orderBy('tingkat')->orderBy('nama')->get();
        $angkatans = Student::select('angkatan')->distinct()->orderByDesc('angkatan')->pluck('angkatan');

        $breadcrumbs = [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'Data Siswa', 'url' => null],
        ];

        return view('admin.students.index', compact(
            'students', 
            'breadcrumbs', 
            'sortField', 
            'sortDirection', 
            'majors', 
            'classes', 
            'angkatans'
        ));
    }

    /**
     * Store a newly created student in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'nisn' => ['required', 'string', 'max:20', 'unique:students,nisn'],
            'major_id' => ['required', 'exists:majors,id'],
            'class_id' => ['required', 'exists:classes,id'],
            'angkatan' => ['required', 'digits:4'],
            'status' => ['required', 'in:aktif,lulus,pindah'],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        DB::transaction(function () use ($validated) {
            $roleSiswa = Role::firstOrCreate(['nama' => 'siswa'], ['label' => 'Peserta Didik / Siswa']);

            $usernameBase = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', explode('@', $validated['email'])[0]));
            $username = $usernameBase . '_' . rand(100, 999);

            $user = User::create([
                'role_id' => $roleSiswa->id,
                'name' => $validated['name'],
                'username' => $username,
                'email' => $validated['email'],
                'password' => Hash::make($validated['password'] ?? 'password123'),
                'is_active' => true,
            ]);

            Student::create([
                'user_id' => $user->id,
                'nisn' => $validated['nisn'],
                'major_id' => $validated['major_id'],
                'class_id' => $validated['class_id'],
                'angkatan' => $validated['angkatan'],
                'status' => $validated['status'],
            ]);
        });

        return redirect()->route('admin.students.index')->with('success', 'Data siswa berhasil ditambahkan.');
    }

    /**
     * Update the specified student in storage.
     */
    public function update(Request $request, Student $student): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($student->user_id)],
            'nisn' => ['required', 'string', 'max:20', Rule::unique('students', 'nisn')->ignore($student->id)],
            'major_id' => ['required', 'exists:majors,id'],
            'class_id' => ['required', 'exists:classes,id'],
            'angkatan' => ['required', 'digits:4'],
            'status' => ['required', 'in:aktif,lulus,pindah'],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        DB::transaction(function () use ($validated, $student) {
            $userData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
            ];

            if (!empty($validated['password'])) {
                $userData['password'] = Hash::make($validated['password']);
            }

            $student->user()->update($userData);

            $student->update([
                'nisn' => $validated['nisn'],
                'major_id' => $validated['major_id'],
                'class_id' => $validated['class_id'],
                'angkatan' => $validated['angkatan'],
                'status' => $validated['status'],
            ]);
        });

        return redirect()->route('admin.students.index')->with('success', 'Data siswa berhasil diperbarui.');
    }

    /**
     * Remove the specified student from storage.
     */
    public function destroy(Student $student): RedirectResponse
    {
        DB::transaction(function () use ($student) {
            $user = $student->user;
            $student->delete();
            if ($user) {
                $user->delete();
            }
        });

        return redirect()->route('admin.students.index')->with('success', 'Data siswa berhasil dihapus.');
    }
}

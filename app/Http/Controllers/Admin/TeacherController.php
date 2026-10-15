<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TeacherController extends Controller
{
    /**
     * Display a listing of teachers with search and filter capabilities.
     */
    public function index(Request $request): View
    {
        $query = Teacher::with(['user', 'classes']);

        // Search logic
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nip', 'like', "%{$search}%")
                  ->orWhere('mata_pelajaran', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Filter: Mata Pelajaran
        if ($filterMapel = $request->input('filter_mapel')) {
            $query->where('mata_pelajaran', $filterMapel);
        }

        // Filter: Status Wali Kelas
        if ($request->has('filter_wali') && $request->input('filter_wali') !== '') {
            $query->where('is_wali_kelas', (bool) $request->input('filter_wali'));
        }

        // Sorting
        $sortField = $request->input('sort', 'created_at');
        $sortDirection = $request->input('direction', 'desc');

        if (in_array($sortField, ['nip', 'mata_pelajaran', 'created_at'])) {
            $query->orderBy($sortField, $sortDirection === 'asc' ? 'asc' : 'desc');
        } else {
            $query->latest();
        }

        $teachers = $query->paginate(10)->withQueryString();

        // Distinct list for filter dropdown
        $availableMapels = Teacher::whereNotNull('mata_pelajaran')
            ->distinct()
            ->orderBy('mata_pelajaran')
            ->pluck('mata_pelajaran');

        $breadcrumbs = [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'Data Guru', 'url' => null],
        ];

        return view('admin.teachers.index', compact(
            'teachers', 
            'breadcrumbs', 
            'sortField', 
            'sortDirection', 
            'availableMapels'
        ));
    }

    /**
     * Store a newly created teacher in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'nip' => ['required', 'string', 'max:20', 'unique:teachers,nip'],
            'mata_pelajaran' => ['nullable', 'string', 'max:100'],
            'is_wali_kelas' => ['nullable', 'boolean'],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        DB::transaction(function () use ($validated, $request) {
            $roleGuru = Role::firstOrCreate(['nama' => 'guru'], ['label' => 'Tenaga Pendidik / Guru']);

            $usernameBase = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', explode('@', $validated['email'])[0]));
            $username = $usernameBase . '_' . rand(100, 999);

            $user = User::create([
                'role_id' => $roleGuru->id,
                'name' => $validated['name'],
                'username' => $username,
                'email' => $validated['email'],
                'password' => Hash::make($validated['password'] ?? 'password123'),
                'is_active' => true,
            ]);

            Teacher::create([
                'user_id' => $user->id,
                'nip' => $validated['nip'],
                'mata_pelajaran' => $validated['mata_pelajaran'] ?? null,
                'is_wali_kelas' => $request->boolean('is_wali_kelas'),
            ]);
        });

        return redirect()->route('admin.teachers.index')->with('success', 'Data tenaga pendidik berhasil ditambahkan ke sistem.');
    }

    /**
     * Update the specified teacher in storage.
     */
    public function update(Request $request, Teacher $teacher): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($teacher->user_id)],
            'nip' => ['required', 'string', 'max:20', Rule::unique('teachers', 'nip')->ignore($teacher->id)],
            'mata_pelajaran' => ['nullable', 'string', 'max:100'],
            'is_wali_kelas' => ['nullable', 'boolean'],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        DB::transaction(function () use ($validated, $request, $teacher) {
            $userData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
            ];

            if (!empty($validated['password'])) {
                $userData['password'] = Hash::make($validated['password']);
            }

            $teacher->user()->update($userData);

            $teacher->update([
                'nip' => $validated['nip'],
                'mata_pelajaran' => $validated['mata_pelajaran'] ?? null,
                'is_wali_kelas' => $request->boolean('is_wali_kelas'),
            ]);
        });

        return redirect()->route('admin.teachers.index')->with('success', 'Data tenaga pendidik berhasil diperbarui.');
    }

    /**
     * Remove the specified teacher from storage.
     */
    public function destroy(Teacher $teacher): RedirectResponse
    {
        DB::transaction(function () use ($teacher) {
            $user = $teacher->user;
            $teacher->delete();
            if ($user) {
                $user->delete();
            }
        });

        return redirect()->route('admin.teachers.index')->with('success', 'Data tenaga pendidik berhasil dihapus dari sistem.');
    }
}

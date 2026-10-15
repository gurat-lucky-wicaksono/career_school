<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CompanyController extends Controller
{
    /**
     * Display a listing of verified partner companies.
     */
    public function index(Request $request): View
    {
        $query = Company::with(['user'])->withCount('jobs');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('bidang', 'like', "%{$search}%")
                  ->orWhere('kota', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status_verifikasi', $status);
        }

        if ($bidang = $request->input('filter_bidang')) {
            $query->where('bidang', $bidang);
        }

        $sortField = $request->input('sort', 'created_at');
        $sortDirection = $request->input('direction', 'desc');

        if (in_array($sortField, ['nama', 'bidang', 'kota', 'status_verifikasi', 'jobs_count', 'created_at'])) {
            $query->orderBy($sortField, $sortDirection === 'asc' ? 'asc' : 'desc');
        } else {
            $query->latest();
        }

        $companies = $query->paginate(10)->withQueryString();

        $bidangs = Company::whereNotNull('bidang')->distinct()->orderBy('bidang')->pluck('bidang');

        $breadcrumbs = [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'Mitra Industri', 'url' => null],
        ];

        return view('admin.companies.index', compact('companies', 'breadcrumbs', 'sortField', 'sortDirection', 'bidangs'));
    }

    /**
     * Display a listing of pending partner companies awaiting verification.
     */
    public function verification(Request $request): View
    {
        $query = Company::with(['user'])->where('status_verifikasi', 'menunggu');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('bidang', 'like', "%{$search}%")
                  ->orWhere('kota', 'like', "%{$search}%");
            });
        }

        $pendingCompanies = $query->latest()->paginate(10)->withQueryString();

        $breadcrumbs = [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'Kemitraan Industri', 'url' => route('admin.companies.index')],
            ['label' => 'Verifikasi Mitra', 'url' => null],
        ];

        return view('admin.companies.verification', compact('pendingCompanies', 'breadcrumbs'));
    }

    /**
     * Store a newly created company in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'bidang' => ['nullable', 'string', 'max:100'],
            'kota' => ['nullable', 'string', 'max:80'],
            'alamat' => ['nullable', 'string'],
            'situs' => ['nullable', 'string', 'max:150'],
            'deskripsi' => ['nullable', 'string'],
            'status_verifikasi' => ['required', 'in:menunggu,terverifikasi,ditolak'],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        DB::transaction(function () use ($validated) {
            $roleDudi = Role::firstOrCreate(['nama' => 'perusahaan'], ['label' => 'Mitra Industri / DUDI']);

            $usernameBase = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', explode('@', $validated['email'])[0]));
            $username = $usernameBase . '_' . rand(100, 999);

            $user = User::create([
                'role_id' => $roleDudi->id,
                'name' => $validated['nama'],
                'username' => $username,
                'email' => $validated['email'],
                'password' => Hash::make($validated['password'] ?? 'password123'),
                'is_active' => true,
            ]);

            Company::create([
                'user_id' => $user->id,
                'nama' => $validated['nama'],
                'bidang' => $validated['bidang'] ?? null,
                'kota' => $validated['kota'] ?? null,
                'alamat' => $validated['alamat'] ?? null,
                'situs' => $validated['situs'] ?? null,
                'deskripsi' => $validated['deskripsi'] ?? null,
                'status_verifikasi' => $validated['status_verifikasi'],
                'verified_by' => $validated['status_verifikasi'] === 'terverifikasi' ? Auth::id() : null,
                'verified_at' => $validated['status_verifikasi'] === 'terverifikasi' ? now() : null,
            ]);
        });

        return redirect()->route('admin.companies.index')->with('success', 'Data mitra industri berhasil ditambahkan.');
    }

    /**
     * Update the specified company in storage.
     */
    public function update(Request $request, Company $company): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($company->user_id)],
            'bidang' => ['nullable', 'string', 'max:100'],
            'kota' => ['nullable', 'string', 'max:80'],
            'alamat' => ['nullable', 'string'],
            'situs' => ['nullable', 'string', 'max:150'],
            'deskripsi' => ['nullable', 'string'],
            'status_verifikasi' => ['required', 'in:menunggu,terverifikasi,ditolak'],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        DB::transaction(function () use ($validated, $company) {
            $userData = [
                'name' => $validated['nama'],
                'email' => $validated['email'],
            ];

            if (!empty($validated['password'])) {
                $userData['password'] = Hash::make($validated['password']);
            }

            $company->user()->update($userData);

            $updateData = [
                'nama' => $validated['nama'],
                'bidang' => $validated['bidang'] ?? null,
                'kota' => $validated['kota'] ?? null,
                'alamat' => $validated['alamat'] ?? null,
                'situs' => $validated['situs'] ?? null,
                'deskripsi' => $validated['deskripsi'] ?? null,
                'status_verifikasi' => $validated['status_verifikasi'],
            ];

            if ($validated['status_verifikasi'] === 'terverifikasi' && $company->status_verifikasi !== 'terverifikasi') {
                $updateData['verified_by'] = Auth::id();
                $updateData['verified_at'] = now();
            }

            $company->update($updateData);
        });

        return redirect()->route('admin.companies.index')->with('success', 'Data mitra industri berhasil diperbarui.');
    }

    /**
     * Verify or reject partner company.
     */
    public function verify(Request $request, Company $company): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'in:terverifikasi,ditolak'],
        ]);

        $status = $request->input('status');
        $company->update([
            'status_verifikasi' => $status,
            'verified_by' => Auth::id(),
            'verified_at' => now(),
        ]);

        $message = $status === 'terverifikasi' ? 'Mitra industri berhasil diverifikasi.' : 'Kemitraan industri ditolak.';

        return back()->with('success', $message);
    }

    /**
     * Remove the specified company from storage.
     */
    public function destroy(Company $company): RedirectResponse
    {
        DB::transaction(function () use ($company) {
            $user = $company->user;
            $company->delete();
            if ($user) {
                $user->delete();
            }
        });

        return redirect()->route('admin.companies.index')->with('success', 'Data mitra industri berhasil dihapus.');
    }
}

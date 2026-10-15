<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Show the company partner profile settings page.
     */
    public function index(): View
    {
        $user = auth()->user();
        $company = $user->company;

        $breadcrumbs = [
            ['label' => 'Dashboard Mitra', 'url' => route('company.dashboard')],
            ['label' => 'Profil Perusahaan', 'url' => null],
        ];

        return view('company.profile.index', compact('user', 'company', 'breadcrumbs'));
    }

    /**
     * Update the company partner profile.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $company = $user->company;

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'bidang' => ['nullable', 'string', 'max:100'],
            'kota' => ['nullable', 'string', 'max:80'],
            'alamat' => ['nullable', 'string'],
            'situs' => ['nullable', 'url', 'max:150'],
            'deskripsi' => ['nullable', 'string'],
            'telepon' => ['nullable', 'string', 'max:25'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $logoPath = $company->logo;
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = 'company_' . $company->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/companies'), $filename);
            $logoPath = 'uploads/companies/' . $filename;
        }

        $company->update([
            'nama' => $validated['nama'],
            'bidang' => $validated['bidang'] ?? $company->bidang,
            'kota' => $validated['kota'] ?? $company->kota,
            'alamat' => $validated['alamat'] ?? $company->alamat,
            'situs' => $validated['situs'] ?? $company->situs,
            'deskripsi' => $validated['deskripsi'] ?? $company->deskripsi,
            'logo' => $logoPath,
        ]);

        if ($user->profile) {
            $user->profile->update([
                'telepon' => $validated['telepon'] ?? $user->profile->telepon,
            ]);
        }

        return redirect()->route('company.profile')->with('success', 'Profil perusahaan berhasil diperbarui.');
    }
}

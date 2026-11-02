<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolProfile;
use App\Models\SchoolSetting;
use App\Services\SchoolSettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    /**
     * Display the system settings management page.
     */
    public function index(Request $request): View
    {
        $settings = SchoolSetting::all()->keyBy('key');

        $breadcrumbs = [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'Pengaturan Sistem', 'url' => null],
        ];

        return view('admin.settings.index', compact('settings', 'breadcrumbs'));
    }

    /**
     * Update school settings and operational configuration.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            // Identitas Lembaga
            'school_name' => ['required', 'string', 'max:150'],
            'school_npsn' => ['required', 'string', 'max:20'],
            'school_address' => ['required', 'string', 'max:500'],
            'school_phone' => ['nullable', 'string', 'max:50'],
            'school_email' => ['nullable', 'email', 'max:100'],
            'school_website' => ['nullable', 'url', 'max:150'],
            'principal_name' => ['required', 'string', 'max:150'],
            'principal_nip' => ['required', 'string', 'max:50'],

            // Struktur & Izin BKK
            'bkk_name' => ['required', 'string', 'max:150'],
            'bkk_leader_name' => ['required', 'string', 'max:150'],
            'bkk_leader_nip' => ['required', 'string', 'max:50'],
            'bkk_sk_number' => ['nullable', 'string', 'max:100'],
            'bkk_contact_phone' => ['nullable', 'string', 'max:50'],

            // Tahun Akademik & Aturan Sistem
            'active_academic_year' => ['required', 'string', 'max:20'],
            'active_semester' => ['required', 'in:Ganjil,Genap'],
            'max_active_applications_per_student' => ['required', 'integer', 'min:1', 'max:20'],
            'allow_student_registration' => ['nullable', 'boolean'],
        ]);

        // Ensure boolean cast for checkbox
        $validated['allow_student_registration'] = $request->boolean('allow_student_registration');

        // Update settings in database and invalidate cache
        SchoolSettingService::updateBatch($validated, auth()->user());

        // Keep legacy SchoolProfile table in sync if it exists
        $schoolProfile = SchoolProfile::first();
        if ($schoolProfile) {
            $schoolProfile->update([
                'nama_sekolah' => $validated['school_name'],
                'alamat' => $validated['school_address'],
                'telepon' => $validated['school_phone'] ?? $schoolProfile->telepon,
                'email' => $validated['school_email'] ?? $schoolProfile->email,
                'updated_by' => auth()->id(),
            ]);
        }

        return redirect()->route('admin.settings')
            ->with('success', 'Konfigurasi identitas lembaga dan parameter sistem berhasil diperbarui.');
    }
}

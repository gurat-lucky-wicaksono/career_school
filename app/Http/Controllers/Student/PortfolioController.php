<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Major;
use App\Models\Portfolio;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PortfolioController extends Controller
{
    /**
     * Get or initialize authenticated student model.
     */
    protected function getStudent(): Student
    {
        $user = auth()->user();
        $student = $user->student;

        if (!$student) {
            $defaultMajor = Major::first();
            $defaultClass = SchoolClass::first();
            $student = Student::create([
                'user_id' => $user->id,
                'nisn' => '00' . rand(10000000, 99999999),
                'class_id' => $defaultClass?->id,
                'major_id' => $defaultMajor?->id,
                'angkatan' => date('Y'),
                'status' => 'aktif',
            ]);
        }

        return $student;
    }

    /**
     * Display a listing of student's portfolios.
     */
    public function index(): View
    {
        $student = $this->getStudent();
        $portfolios = $student->portfolios()->latest()->get();

        $breadcrumbs = [
            ['label' => 'Dashboard Siswa', 'url' => route('student.dashboard')],
            ['label' => 'Karya & Portofolio', 'url' => null],
        ];

        return view('student.portfolios.index', compact('portfolios', 'student', 'breadcrumbs'));
    }

    /**
     * Show the form for creating a new portfolio.
     */
    public function create(): View
    {
        $student = $this->getStudent();

        $breadcrumbs = [
            ['label' => 'Dashboard Siswa', 'url' => route('student.dashboard')],
            ['label' => 'Karya & Portofolio', 'url' => route('student.portfolios.index')],
            ['label' => 'Tambah Proyek', 'url' => null],
        ];

        return view('student.portfolios.create', compact('student', 'breadcrumbs'));
    }

    /**
     * Store a newly created portfolio in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $student = $this->getStudent();

        $validated = $request->validate([
            'judul' => 'required|string|max:150',
            'kategori' => 'required|string|max:50',
            'deskripsi' => 'nullable|string|max:2000',
            'tautan' => 'nullable|url|max:255',
            'lampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png,zip|max:5120',
        ]);

        $filePath = null;
        if ($request->hasFile('lampiran')) {
            $filePath = $request->file('lampiran')->store('portfolios', 'public');
        }

        Portfolio::create([
            'student_id' => $student->id,
            'judul' => $validated['judul'],
            'kategori' => $validated['kategori'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'tautan' => $validated['tautan'] ?? null,
            'lampiran' => $filePath,
            'status' => 'terbit',
        ]);

        return redirect()->route('student.portfolios.index')
            ->with('success', 'Karya portofolio berhasil ditambahkan dan siap dinilai mitra industri.');
    }

    /**
     * Show the form for editing the specified portfolio.
     */
    public function edit(Portfolio $portfolio): View
    {
        $student = $this->getStudent();

        if ($portfolio->student_id !== $student->id) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengubah portofolio ini.');
        }

        $breadcrumbs = [
            ['label' => 'Dashboard Siswa', 'url' => route('student.dashboard')],
            ['label' => 'Karya & Portofolio', 'url' => route('student.portfolios.index')],
            ['label' => 'Edit ' . $portfolio->judul, 'url' => null],
        ];

        return view('student.portfolios.edit', compact('portfolio', 'student', 'breadcrumbs'));
    }

    /**
     * Update the specified portfolio in storage.
     */
    public function update(Request $request, Portfolio $portfolio): RedirectResponse
    {
        $student = $this->getStudent();

        if ($portfolio->student_id !== $student->id) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengubah portofolio ini.');
        }

        $validated = $request->validate([
            'judul' => 'required|string|max:150',
            'kategori' => 'required|string|max:50',
            'deskripsi' => 'nullable|string|max:2000',
            'tautan' => 'nullable|url|max:255',
            'lampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png,zip|max:5120',
        ]);

        $filePath = $portfolio->lampiran;
        if ($request->hasFile('lampiran')) {
            if ($portfolio->lampiran && Storage::disk('public')->exists($portfolio->lampiran)) {
                Storage::disk('public')->delete($portfolio->lampiran);
            }
            $filePath = $request->file('lampiran')->store('portfolios', 'public');
        }

        $portfolio->update([
            'judul' => $validated['judul'],
            'kategori' => $validated['kategori'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'tautan' => $validated['tautan'] ?? null,
            'lampiran' => $filePath,
        ]);

        return redirect()->route('student.portfolios.index')
            ->with('success', 'Data portofolio berhasil diperbarui.');
    }

    /**
     * Remove the specified portfolio from storage.
     */
    public function destroy(Portfolio $portfolio): RedirectResponse
    {
        $student = $this->getStudent();

        if ($portfolio->student_id !== $student->id) {
            abort(403, 'Anda tidak memiliki hak akses untuk menghapus portofolio ini.');
        }

        if ($portfolio->lampiran && Storage::disk('public')->exists($portfolio->lampiran)) {
            Storage::disk('public')->delete($portfolio->lampiran);
        }

        $portfolio->delete();

        return redirect()->route('student.portfolios.index')
            ->with('success', 'Karya portofolio berhasil dihapus.');
    }
}

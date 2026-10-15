<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Major;
use App\Models\SchoolClass;
use App\Models\Teacher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SchoolClassController extends Controller
{
    /**
     * Display a listing of classes with search and filtering.
     */
    public function index(Request $request): View
    {
        $query = SchoolClass::with(['major', 'waliKelas.user'])->withCount('students');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('tahun_ajaran', 'like', "%{$search}%")
                  ->orWhereHas('major', function ($mq) use ($search) {
                      $mq->where('nama', 'like', "%{$search}%")
                         ->orWhere('kode', 'like', "%{$search}%");
                  })
                  ->orWhereHas('waliKelas.user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($filterMajor = $request->input('filter_major')) {
            $query->where('major_id', $filterMajor);
        }

        if ($filterTingkat = $request->input('filter_tingkat')) {
            $query->where('tingkat', $filterTingkat);
        }

        if ($filterTahun = $request->input('filter_tahun')) {
            $query->where('tahun_ajaran', $filterTahun);
        }

        $sortField = $request->input('sort', 'nama');
        $sortDirection = $request->input('direction', 'asc');

        if (in_array($sortField, ['nama', 'tingkat', 'tahun_ajaran', 'students_count'])) {
            $query->orderBy($sortField, $sortDirection === 'asc' ? 'asc' : 'desc');
        } else {
            $query->orderBy('tingkat', 'asc')->orderBy('nama', 'asc');
        }

        $classes = $query->paginate(10)->withQueryString();

        $majors = Major::orderBy('nama')->get();
        $teachers = Teacher::with('user')->get();
        $tahunAjarans = SchoolClass::select('tahun_ajaran')->distinct()->orderByDesc('tahun_ajaran')->pluck('tahun_ajaran');

        $breadcrumbs = [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'Data Kelas', 'url' => null],
        ];

        return view('admin.classes.index', compact(
            'classes', 
            'breadcrumbs', 
            'sortField', 
            'sortDirection',
            'majors',
            'teachers',
            'tahunAjarans'
        ));
    }

    /**
     * Store a newly created class in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:50'],
            'major_id' => ['required', 'exists:majors,id'],
            'wali_kelas_id' => ['nullable', 'exists:teachers,id'],
            'tingkat' => ['required', 'integer', 'between:10,13'],
            'tahun_ajaran' => ['required', 'string', 'max:9'],
        ]);

        SchoolClass::create($validated);

        return redirect()->route('admin.classes.index')->with('success', 'Rombongan belajar / kelas baru berhasil ditambahkan.');
    }

    /**
     * Update the specified class in storage.
     */
    public function update(Request $request, SchoolClass $class): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:50'],
            'major_id' => ['required', 'exists:majors,id'],
            'wali_kelas_id' => ['nullable', 'exists:teachers,id'],
            'tingkat' => ['required', 'integer', 'between:10,13'],
            'tahun_ajaran' => ['required', 'string', 'max:9'],
        ]);

        $class->update($validated);

        return redirect()->route('admin.classes.index')->with('success', 'Data rombel / kelas berhasil diperbarui.');
    }

    /**
     * Remove the specified class from storage.
     */
    public function destroy(SchoolClass $class): RedirectResponse
    {
        $class->delete();

        return redirect()->route('admin.classes.index')->with('success', 'Data rombel / kelas berhasil dihapus.');
    }
}

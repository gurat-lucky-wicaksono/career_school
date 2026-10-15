<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Major;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MajorController extends Controller
{
    /**
     * Display a listing of vocational majors with search capabilities.
     */
    public function index(Request $request): View
    {
        $query = Major::withCount(['classes', 'students']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%")
                  ->orWhere('kepala_jurusan', 'like', "%{$search}%");
            });
        }

        $sortField = $request->input('sort', 'nama');
        $sortDirection = $request->input('direction', 'asc');

        if (in_array($sortField, ['kode', 'nama', 'kepala_jurusan', 'classes_count', 'students_count', 'created_at'])) {
            $query->orderBy($sortField, $sortDirection === 'asc' ? 'asc' : 'desc');
        } else {
            $query->orderBy('nama', 'asc');
        }

        $majors = $query->paginate(10)->withQueryString();

        $breadcrumbs = [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'Data Jurusan', 'url' => null],
        ];

        return view('admin.majors.index', compact('majors', 'breadcrumbs', 'sortField', 'sortDirection'));
    }

    /**
     * Store a newly created major in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode' => ['required', 'string', 'max:10', 'unique:majors,kode'],
            'nama' => ['required', 'string', 'max:100'],
            'kepala_jurusan' => ['nullable', 'string', 'max:100'],
            'keterangan' => ['nullable', 'string'],
        ]);

        Major::create($validated);

        return redirect()->route('admin.majors.index')->with('success', 'Data kompetensi keahlian/jurusan berhasil ditambahkan.');
    }

    /**
     * Update the specified major in storage.
     */
    public function update(Request $request, Major $major): RedirectResponse
    {
        $validated = $request->validate([
            'kode' => ['required', 'string', 'max:10', Rule::unique('majors', 'kode')->ignore($major->id)],
            'nama' => ['required', 'string', 'max:100'],
            'kepala_jurusan' => ['nullable', 'string', 'max:100'],
            'keterangan' => ['nullable', 'string'],
        ]);

        $major->update($validated);

        return redirect()->route('admin.majors.index')->with('success', 'Data jurusan berhasil diperbarui.');
    }

    /**
     * Remove the specified major from storage.
     */
    public function destroy(Major $major): RedirectResponse
    {
        $major->delete();

        return redirect()->route('admin.majors.index')->with('success', 'Data jurusan berhasil dihapus.');
    }
}

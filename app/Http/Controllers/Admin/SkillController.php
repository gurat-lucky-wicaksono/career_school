<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SkillController extends Controller
{
    /**
     * Display a listing of skills with search and filtering.
     */
    public function index(Request $request): View
    {
        $query = Skill::withCount(['students', 'jobs']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('kategori', 'like', "%{$search}%");
            });
        }

        if ($filterKategori = $request->input('filter_kategori')) {
            $query->where('kategori', $filterKategori);
        }

        $sortField = $request->input('sort', 'nama');
        $sortDirection = $request->input('direction', 'asc');

        if (in_array($sortField, ['nama', 'kategori', 'students_count', 'jobs_count', 'created_at'])) {
            $query->orderBy($sortField, $sortDirection === 'asc' ? 'asc' : 'desc');
        } else {
            $query->orderBy('kategori', 'asc')->orderBy('nama', 'asc');
        }

        $skills = $query->paginate(10)->withQueryString();

        $kategoris = Skill::whereNotNull('kategori')->distinct()->orderBy('kategori')->pluck('kategori');

        $breadcrumbs = [
            ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'Master Keahlian', 'url' => null],
        ];

        return view('admin.skills.index', compact(
            'skills', 
            'breadcrumbs', 
            'sortField', 
            'sortDirection',
            'kategoris'
        ));
    }

    /**
     * Store a newly created skill in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:60', 'unique:skills,nama'],
            'kategori' => ['nullable', 'string', 'max:50'],
        ]);

        Skill::create($validated);

        return redirect()->route('admin.skills.index')->with('success', 'Kompetensi keahlian baru berhasil ditambahkan.');
    }

    /**
     * Update the specified skill in storage.
     */
    public function update(Request $request, Skill $skill): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:60', Rule::unique('skills', 'nama')->ignore($skill->id)],
            'kategori' => ['nullable', 'string', 'max:50'],
        ]);

        $skill->update($validated);

        return redirect()->route('admin.skills.index')->with('success', 'Kompetensi keahlian berhasil diperbarui.');
    }

    /**
     * Remove the specified skill from storage.
     */
    public function destroy(Skill $skill): RedirectResponse
    {
        $skill->delete();

        return redirect()->route('admin.skills.index')->with('success', 'Kompetensi keahlian berhasil dihapus.');
    }
}

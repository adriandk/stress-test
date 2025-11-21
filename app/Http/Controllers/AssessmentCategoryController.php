<?php

namespace App\Http\Controllers;

use App\Models\AssessmentCategory;
use Illuminate\Http\Request;

class AssessmentCategoryController extends Controller
{
    /**
     * List kategori asesmen.
     * Bisa diakses STAFF BK & KONSELOR (auth saja).
     */
    public function index()
    {
        $categories = AssessmentCategory::ordered()->paginate(10);

        return view('assessment_categories.index', compact('categories'));
    }

    /**
     * Form create kategori.
     * Hanya KONSELOR (dibatasi di routes).
     */
    public function create()
    {
        return view('assessment_categories.create');
    }

    /**
     * Simpan kategori baru.
     * Hanya KONSELOR.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
            'sort_order'  => ['nullable', 'integer'],
        ]);

        $data['sort_order'] = $data['sort_order'] ?? 0;

        AssessmentCategory::create($data);

        return redirect()
            ->route('assessment-categories.index')
            ->with('success', 'Kategori asesmen berhasil ditambahkan.');
    }

    /**
     * Form edit kategori.
     * Hanya KONSELOR.
     */
    public function edit(AssessmentCategory $assessment_category)
    {
        return view('assessment_categories.edit', [
            'category' => $assessment_category,
        ]);
    }

    /**
     * Update kategori.
     * Hanya KONSELOR.
     */
    public function update(Request $request, AssessmentCategory $assessment_category)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
            'sort_order'  => ['nullable', 'integer'],
        ]);

        $data['sort_order'] = $data['sort_order'] ?? 0;

        $assessment_category->update($data);

        return redirect()
            ->route('assessment-categories.index')
            ->with('success', 'Kategori asesmen berhasil diperbarui.');
    }

    /**
     * Hapus kategori.
     * Hanya KONSELOR.
     * FK ke questions & risk_rules = SET NULL, jadi aman dihapus.
     */
    public function destroy(AssessmentCategory $assessment_category)
    {
        $assessment_category->delete();

        return redirect()
            ->route('assessment-categories.index')
            ->with('success', 'Kategori asesmen berhasil dihapus.');
    }
}
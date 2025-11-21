<?php

namespace App\Http\Controllers;

use App\Models\ArticleCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ArticleCategoryController extends Controller
{
    /**
     * List semua kategori (Staff BK & Konselor bisa akses).
     */
    public function index()
    {
        $categories = ArticleCategory::orderBy('created_at', 'desc')->paginate(10);

        return view('article_categories.index', compact('categories'));
    }

    /**
     * Detail 1 kategori (Staff BK & Konselor bisa lihat).
     */
    public function show(ArticleCategory $category)
    {
        return view('article_categories.show', compact('category'));
    }

    /**
     * Form create kategori (hanya konselor - di-protect di route).
     */
    public function create()
    {
        return view('article_categories.create');
    }

    /**
     * Simpan kategori baru (hanya konselor).
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'slug'        => ['nullable', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        // Generate slug unik kalau kosong
        $slug = $data['slug'] ?: $this->generateUniqueSlug($data['name']);
        $data['slug'] = $slug;

        ArticleCategory::create($data);

        return redirect()
            ->route('article-categories.index')
            ->with('success', 'Kategori artikel berhasil dibuat.');
    }

    /**
     * Form edit kategori (hanya konselor).
     */
    public function edit(ArticleCategory $category)
    {
        return view('article_categories.edit', compact('category'));
    }

    /**
     * Update kategori (hanya konselor).
     */
    public function update(Request $request, ArticleCategory $category)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'slug'        => ['nullable', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $slug = $data['slug'] ?: $this->generateUniqueSlug($data['name'], $category->id);
        $data['slug'] = $slug;

        $category->update($data);

        return redirect()
            ->route('article-categories.index')
            ->with('success', 'Kategori artikel berhasil diperbarui.');
    }

    /**
     * Hapus kategori (hanya konselor).
     * Tabel articles punya FK ke article_categories dengan ON DELETE RESTRICT,
     * jadi kalau sudah dipakai, akan gagal. Kita bisa tangani dengan try/catch.
     */
    public function destroy(ArticleCategory $category)
    {
        try {
            $category->delete();

            return redirect()
                ->route('article-categories.index')
                ->with('success', 'Kategori artikel berhasil dihapus.');
        } catch (\Throwable $e) {
            return redirect()
                ->route('article-categories.index')
                ->with('error', 'Kategori tidak bisa dihapus karena sudah digunakan oleh artikel.');
        }
    }

    /**
     * Helper: generate slug unik dari nama.
     */
    private function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;

        while (
            ArticleCategory::where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }
}
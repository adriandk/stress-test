<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    /**
     * INDEX INTERNAL (staff & konselor)
     * List semua artikel untuk pengelolaan.
     */
    public function index()
    {
        $articles = Article::with(['category', 'author'])
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('articles.index', compact('articles'));
    }

    /**
     * INDEX PUBLIK (mahasiswa anonim)
     * Hanya artikel yang sudah published.
     */
    public function publicIndex()
    {
        $articles = Article::published()
            ->with('category')
            ->orderByDesc('published_at')
            ->paginate(9);

        return view('articles.public_index', compact('articles'));
    }

    /**
     * Form create artikel (staff & konselor).
     */
    public function create()
    {
        $categories = ArticleCategory::orderBy('name')->get();

        return view('articles.create', compact('categories'));
    }

    /**
     * Simpan artikel baru.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'         => ['required', 'string', 'max:200'],
            'slug'          => ['nullable', 'string', 'max:220'],
            'category_id'   => ['required', 'exists:article_categories,id'],
            'thumbnail_url' => ['nullable', 'url', 'max:255'],
            'content'       => ['required', 'string'],
            'status'        => ['required', 'in:draft,pending'], // tidak bisa langsung published
        ]);

        // slug opsional: kalau tidak ada / kosong → generate
        $data['slug'] = ($data['slug'] ?? null) ?: $this->generateUniqueSlug($data['title']);

        // penulis = akun yang sedang login
        $data['created_by'] = Auth::id();

        $article = Article::create($data);

        return redirect()
            ->route('articles.index')
            ->with('success', 'Artikel berhasil dibuat sebagai ' . strtoupper($article->status) . '.');
    }

    /**
     * Form edit artikel.
     * Staff & konselor boleh (dengan aturan di authorizeEdit).
     */
    public function edit(Article $article)
    {
        $this->authorizeEdit($article);

        $categories = ArticleCategory::orderBy('name')->get();

        return view('articles.edit', compact('article', 'categories'));
    }

    /**
     * Update artikel.
     */
    public function update(Request $request, Article $article)
    {
        $this->authorizeEdit($article);

        $data = $request->validate([
            'title'         => ['required', 'string', 'max:200'],
            'slug'          => ['nullable', 'string', 'max:220'],
            'category_id'   => ['required', 'exists:article_categories,id'],
            'thumbnail_url' => ['nullable', 'url', 'max:255'],
            'content'       => ['required', 'string'],
            'status'        => ['required', 'in:draft,pending,published'],
        ]);

        // slug opsional: kalau kosong → generate, tapi ignore ID artikel ini sendiri
        $data['slug'] = ($data['slug'] ?? null) ?: $this->generateUniqueSlug($data['title'], $article->id);

        // Konselor bisa set langsung status ke published dari form,
        // kalau mau dipaksa lewat verify() saja, status di sini bisa dibatasi.
        $article->update($data);

        return redirect()
            ->route('articles.index')
            ->with('success', 'Artikel berhasil diperbarui.');
    }

    /**
     * Hapus artikel.
     */
    public function destroy(Article $article)
    {
        $this->authorizeEdit($article);

        $article->delete();

        return redirect()
            ->route('articles.index')
            ->with('success', 'Artikel berhasil dihapus.');
    }

    /**
     * Verifikasi artikel → hanya KONSELOR.
     * Mengubah status menjadi published + set verified_by, verified_at, published_at.
     */
    public function verify(Article $article)
    {
        $user = Auth::user();

        if ($user->account_type !== 'konselor') {
            abort(403, 'Hanya konselor yang dapat memverifikasi artikel.');
        }

        $article->update([
            'status'       => 'published',
            'verified_by'  => $user->id,
            'verified_at'  => now(),
            'published_at' => $article->published_at ?? now(),
        ]);

        return redirect()
            ->route('articles.index')
            ->with('success', 'Artikel berhasil diverifikasi dan dipublikasikan.');
    }

    /**
     * SHOW PUBLIK (detail artikel).
     * - Guest hanya boleh lihat jika published.
     * - Staff/konselor boleh lihat semua (preview).
     *
     * Route binding pakai slug: /articles/{article:slug}
     */
    public function show(Article $article)
    {
        $user = Auth::user();

        if (!$user && $article->status !== 'published') {
            abort(404);
        }

        $article->load(['category', 'author']);

        return view('articles.show', compact('article'));
    }

    // ========================
    // Helper & "authorization" sederhana
    // ========================

    /**
     * Generate slug unik berdasarkan judul.
     *
     * @param string $title
     * @param int|null $ignoreId  ID yang diabaikan saat cek unik (untuk update)
     */
    private function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $i    = 1;

        while (
            Article::where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }

    /**
     * Aturan sederhana siapa yang boleh edit/hapus artikel.
     *
     * - Konselor: boleh apa saja
     * - Staff BK: hanya boleh edit/hapus artikel yang dia buat sendiri
     */
    private function authorizeEdit(Article $article): void
    {
        $user = Auth::user();

        if (!$user) {
            abort(403);
        }

        // Konselor: full akses
        if ($user->account_type === 'konselor') {
            return;
        }

        // Staff BK: hanya boleh edit artikel yang dia buat
        if ($user->account_type === 'staff_bk' && $article->created_by === $user->id) {
            return;
        }

        abort(403);
    }
}
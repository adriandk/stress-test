<?php

namespace App\Http\Controllers;

use App\Models\AssessmentCategory;
use App\Models\AssessmentQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssessmentQuestionController extends Controller
{
    /**
     * List pertanyaan asesmen (bank soal).
     */
    public function index(Request $request)
    {
        $categories = AssessmentCategory::ordered()->get();

        $query = AssessmentQuestion::with(['category'])
            ->withCount('answerOptions')
            ->ordered();

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $questions = $query->paginate(10)->withQueryString();

        return view('assessment_questions.index', compact('questions', 'categories'));
    }

    /**
     * Form tambah pertanyaan.
     */
    public function create()
    {
        $categories = AssessmentCategory::ordered()->get();

        return view('assessment_questions.create', compact('categories'));
    }

    /**
     * Simpan pertanyaan baru.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'question_text' => ['required', 'string'],
            'category_id'   => ['nullable', 'exists:assessment_categories,id'],
            'sort_order'    => ['nullable', 'integer'],
            'is_active'     => ['nullable', 'boolean'],
        ]);

        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_active']  = $request->boolean('is_active', true);
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        AssessmentQuestion::create($data);

        return redirect()
            ->route('assessment-questions.index')
            ->with('success', 'Pertanyaan asesmen berhasil ditambahkan.');
    }

    /**
     * Form edit pertanyaan.
     */
    public function edit(AssessmentQuestion $assessment_question)
    {
        $categories = AssessmentCategory::ordered()->get();

        return view('assessment_questions.edit', [
            'question'   => $assessment_question,
            'categories' => $categories,
        ]);
    }

    /**
     * Update pertanyaan.
     */
    public function update(Request $request, AssessmentQuestion $assessment_question)
    {
        $data = $request->validate([
            'question_text' => ['required', 'string'],
            'category_id'   => ['nullable', 'exists:assessment_categories,id'],
            'sort_order'    => ['nullable', 'integer'],
            'is_active'     => ['nullable', 'boolean'],
        ]);

        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_active']  = $request->boolean('is_active', true);
        $data['updated_by'] = Auth::id();

        $assessment_question->update($data);

        return redirect()
            ->route('assessment-questions.index')
            ->with('success', 'Pertanyaan asesmen berhasil diperbarui.');
    }

    /**
     * Hapus pertanyaan (opsi jawaban akan ikut terhapus via FK CASCADE).
     */
    public function destroy(AssessmentQuestion $assessment_question)
    {
        $assessment_question->delete();

        return redirect()
            ->route('assessment-questions.index')
            ->with('success', 'Pertanyaan asesmen berhasil dihapus.');
    }
}
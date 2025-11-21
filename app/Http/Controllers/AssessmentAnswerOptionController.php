<?php

namespace App\Http\Controllers;

use App\Models\AssessmentAnswerOption;
use App\Models\AssessmentQuestion;
use Illuminate\Http\Request;

class AssessmentAnswerOptionController extends Controller
{
    /**
     * List opsi jawaban untuk satu pertanyaan.
     */
    public function index(AssessmentQuestion $question)
    {
        $options = $question->answerOptions()->ordered()->get();

        return view('assessment_answer_options.index', compact('question', 'options'));
    }

    /**
     * Form tambah opsi untuk satu pertanyaan.
     */
    public function create(AssessmentQuestion $question)
    {
        return view('assessment_answer_options.create', compact('question'));
    }

    /**
     * Simpan opsi baru.
     */
    public function store(Request $request, AssessmentQuestion $question)
    {
        $data = $request->validate([
            'option_label' => ['required', 'string', 'max:150'],
            'option_value' => ['required', 'integer'],
            'sort_order'   => ['nullable', 'integer'],
        ]);

        $data['sort_order']  = $data['sort_order'] ?? 0;
        $data['question_id'] = $question->id;

        AssessmentAnswerOption::create($data);

        return redirect()
            ->route('assessment-options.index', $question)
            ->with('success', 'Opsi jawaban berhasil ditambahkan.');
    }

    /**
     * Form edit opsi.
     */
    public function edit(AssessmentQuestion $question, AssessmentAnswerOption $option)
    {
        // jaga-jaga biar opsi memang milik question ini
        if ($option->question_id !== $question->id) {
            abort(404);
        }

        return view('assessment_answer_options.edit', compact('question', 'option'));
    }

    /**
     * Update opsi.
     */
    public function update(Request $request, AssessmentQuestion $question, AssessmentAnswerOption $option)
    {
        if ($option->question_id !== $question->id) {
            abort(404);
        }

        $data = $request->validate([
            'option_label' => ['required', 'string', 'max:150'],
            'option_value' => ['required', 'integer'],
            'sort_order'   => ['nullable', 'integer'],
        ]);

        $data['sort_order'] = $data['sort_order'] ?? 0;

        $option->update($data);

        return redirect()
            ->route('assessment-options.index', $question)
            ->with('success', 'Opsi jawaban berhasil diperbarui.');
    }

    /**
     * Hapus opsi.
     */
    public function destroy(AssessmentQuestion $question, AssessmentAnswerOption $option)
    {
        if ($option->question_id !== $question->id) {
            abort(404);
        }

        $option->delete();

        return redirect()
            ->route('assessment-options.index', $question)
            ->with('success', 'Opsi jawaban berhasil dihapus.');
    }
}
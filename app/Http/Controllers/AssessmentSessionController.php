<?php

namespace App\Http\Controllers;

use App\Models\AssessmentAnswerOption;
use App\Models\AssessmentQuestion;
use App\Models\AssessmentRiskRule;
use App\Models\AssessmentSession;
use App\Models\AssessmentSessionAnswer;
use App\Models\EmergencyContact;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class AssessmentSessionController extends Controller
{
    /**
     * Mulai asesmen: buat session baru lalu redirect ke form.
     * Akses: publik (mahasiswa anonim).
     */
    public function start(Request $request)
    {
        $session = AssessmentSession::create([
            'session_code'      => (string) Str::uuid(),
            'client_user_agent' => $request->userAgent(),
            'started_at'        => now(),
        ]);

        return redirect()->route('assessment.form', $session);
    }

    /**
     * Tampilkan form asesmen (list semua pertanyaan aktif).
     */
    public function showForm(AssessmentSession $session)
    {
        if ($session->completed_at) {
            return redirect()->route('assessment.result', $session);
        }

        $questions = AssessmentQuestion::with([
                'category',
                'answerOptions' => fn($q) => $q->ordered(),
            ])
            ->where('is_active', true)
            ->ordered()
            ->get();

        if ($questions->isEmpty()) {
            return view('assessment_sessions.form', [
                'session'   => $session,
                'questions' => $questions,
            ]);
        }

        return view('assessment_sessions.form', compact('session', 'questions'));
    }

    /**
     * Terima jawaban, simpan ke DB, hitung skor & risiko (global + per kategori).
     */
    public function submit(Request $request, AssessmentSession $session)
    {
        if ($session->completed_at) {
            return redirect()->route('assessment.result', $session);
        }

        $questions = AssessmentQuestion::with('answerOptions', 'category')
            ->where('is_active', true)
            ->ordered()
            ->get();

        if ($questions->isEmpty()) {
            return redirect()
                ->route('assessment.form', $session)
                ->withErrors(['general' => 'Belum ada pertanyaan asesmen yang aktif.']);
        }

        $answersInput = $request->input('answers', []);

        $errors = [];
        foreach ($questions as $q) {
            if (!array_key_exists($q->id, $answersInput)) {
                $errors["answers.{$q->id}"] = 'Pertanyaan ini wajib diisi.';
            }
        }

        if (!empty($errors)) {
            return back()->withErrors($errors)->withInput();
        }

        DB::beginTransaction();

        try {
            AssessmentSessionAnswer::where('session_id', $session->id)->delete();

            $totalScore      = 0;
            $categorySummary = [];
            $now             = now();

            foreach ($questions as $q) {
                $optionId = $answersInput[$q->id] ?? null;
                $option = $q->answerOptions->firstWhere('id', (int) $optionId);

                if (!$option) {
                    continue;
                }

                $score = (int) $option->option_value;

                AssessmentSessionAnswer::create([
                    'session_id'       => $session->id,
                    'question_id'      => $q->id,
                    'answer_option_id' => $option->id,
                    'answer_text'      => null,
                    'score_value'      => $score,
                    'created_at'       => $now,
                ]);

                $totalScore += $score;

                $catId  = $q->category_id;
                $catKey = $catId ?? 0;

                if (!isset($categorySummary[$catKey])) {
                    $categorySummary[$catKey] = [
                        'category_id'   => $catId,
                        'category_name' => $q->category?->name ?? 'Umum',
                        'total_score'   => 0,
                        'questions'     => 0,
                        'risk_level'    => null,
                        'risk_rule_id'  => null,
                        'description'   => null,
                    ];
                }

                $categorySummary[$catKey]['total_score'] += $score;
                $categorySummary[$catKey]['questions']++;
            }

            // Hitung risk per kategori
            foreach ($categorySummary as $key => &$catData) {
                $catId    = $catData['category_id'];
                $catTotal = $catData['total_score'] ?? 0;

                $catRuleQuery = AssessmentRiskRule::active()
                    ->where('min_total_score', '<=', $catTotal)
                    ->where('max_total_score', '>=', $catTotal)
                    ->orderBy('min_total_score', 'desc');

                if ($catId !== null) {
                    $catRuleQuery->where('category_id', $catId);
                } else {
                    $catRuleQuery->whereNull('category_id');
                }

                $catRule = $catRuleQuery->first();

                if (!$catRule) {
                    $catRule = AssessmentRiskRule::active()
                        ->whereNull('category_id')
                        ->where('min_total_score', '<=', $catTotal)
                        ->where('max_total_score', '>=', $catTotal)
                        ->orderBy('min_total_score', 'desc')
                        ->first();
                }

                $catData['risk_level']   = $catRule?->risk_level;
                $catData['risk_rule_id'] = $catRule?->id;
                $catData['description']  = $catRule?->description;
            }
            unset($catData);

            // Hitung risk global
            $globalRule = AssessmentRiskRule::active()
                ->whereNull('category_id')
                ->where('min_total_score', '<=', $totalScore)
                ->where('max_total_score', '>=', $totalScore)
                ->orderBy('min_total_score', 'desc')
                ->first();

            $riskLevel = $globalRule?->risk_level;

            $summary = [
                'total_score'      => $totalScore,
                'risk_level'       => $riskLevel,
                'category_summary' => array_values($categorySummary),
                'matched_rule_id'  => $globalRule?->id,
                'description'      => $globalRule?->description,
            ];

            $session->update([
                'total_score'  => $totalScore,
                'risk_level'   => $riskLevel,
                'summary_json' => $summary,
                'completed_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('assessment.result', $session);
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()
                ->withErrors(['general' => 'Terjadi kesalahan saat menyimpan jawaban. Silakan coba lagi.'])
                ->withInput();
        }
    }

    /**
     * Tampilkan hasil asesmen ke mahasiswa.
     */
    public function result(AssessmentSession $session)
    {
        if (!$session->completed_at) {
            return redirect()->route('assessment.form', $session);
        }

        $summary = $session->summary_json;
        
        // Pastikan summary adalah array
        if (is_string($summary)) {
            $summary = json_decode($summary, true) ?? [];
        }
        
        if (!is_array($summary)) {
            $summary = [];
        }

        // Ambil rule global
        $rule = null;
        if (!empty($summary['matched_rule_id'])) {
            $rule = AssessmentRiskRule::find($summary['matched_rule_id']);
        }
        
        // Fallback jika rule tidak ditemukan
        if (!$rule && $session->total_score !== null) {
            $rule = AssessmentRiskRule::active()
                ->whereNull('category_id')
                ->where('min_total_score', '<=', $session->total_score)
                ->where('max_total_score', '>=', $session->total_score)
                ->orderBy('min_total_score', 'desc')
                ->first();
        }

        // Ambil category summary
        $categorySummary = $summary['category_summary'] ?? [];

        $contacts = EmergencyContact::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('assessment_sessions.result', [
            'session'         => $session,
            'summary'         => $summary,
            'categorySummary' => $categorySummary,
            'rule'            => $rule,
            'contacts'        => $contacts,
        ]);
    }
}
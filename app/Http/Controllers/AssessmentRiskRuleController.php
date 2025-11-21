<?php

namespace App\Http\Controllers;

use App\Models\AssessmentCategory;
use App\Models\AssessmentRiskRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssessmentRiskRuleController extends Controller
{
    /**
     * INDEX: lihat semua aturan risiko.
     * Bisa diakses STAFF BK & KONSELOR (auth).
     */
    public function index()
    {
        $rules = AssessmentRiskRule::with('category', 'creator')
            ->active()
            ->orderBy('category_id')
            ->orderBy('min_total_score')
            ->get();

        // Juga tampilkan yang non-aktif kalau mau lengkap:
        $allRules = AssessmentRiskRule::with('category', 'creator')
            ->orderBy('category_id')
            ->orderBy('min_total_score')
            ->paginate(15);

        return view('assessment_risk_rules.index', [
            'rulesActive' => $rules,
            'rules'       => $allRules,
        ]);
    }

    /**
     * FORM CREATE: hanya KONSELOR (dibatasi di routes).
     */
    public function create()
    {
        $categories = AssessmentCategory::ordered()->get();

        return view('assessment_risk_rules.create', compact('categories'));
    }

    /**
     * STORE: simpan rule baru.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id'     => ['nullable', 'exists:assessment_categories,id'],
            'min_total_score' => ['required', 'integer'],
            'max_total_score' => ['required', 'integer'],
            'risk_level'      => ['required', 'in:low,medium,high'],
            'description'     => ['nullable', 'string', 'max:255'],
            'is_active'       => ['nullable', 'boolean'],
        ]);

        // Validasi logika min <= max
        if ($data['min_total_score'] > $data['max_total_score']) {
            return back()
                ->withErrors(['min_total_score' => 'Skor minimum tidak boleh lebih besar dari skor maksimum.'])
                ->withInput();
        }

        $data['is_active']  = $request->boolean('is_active', true);
        $data['created_by'] = Auth::id();

        AssessmentRiskRule::create($data);

        return redirect()
            ->route('assessment-risk-rules.index')
            ->with('success', 'Aturan risiko berhasil ditambahkan.');
    }

    /**
     * FORM EDIT: hanya KONSELOR.
     */
    public function edit(AssessmentRiskRule $assessment_risk_rule)
    {
        $categories = AssessmentCategory::ordered()->get();

        return view('assessment_risk_rules.edit', [
            'rule'       => $assessment_risk_rule,
            'categories' => $categories,
        ]);
    }

    /**
     * UPDATE: perbarui rule.
     */
    public function update(Request $request, AssessmentRiskRule $assessment_risk_rule)
    {
        $data = $request->validate([
            'category_id'     => ['nullable', 'exists:assessment_categories,id'],
            'min_total_score' => ['required', 'integer'],
            'max_total_score' => ['required', 'integer'],
            'risk_level'      => ['required', 'in:low,medium,high'],
            'description'     => ['nullable', 'string', 'max:255'],
            'is_active'       => ['nullable', 'boolean'],
        ]);

        if ($data['min_total_score'] > $data['max_total_score']) {
            return back()
                ->withErrors(['min_total_score' => 'Skor minimum tidak boleh lebih besar dari skor maksimum.'])
                ->withInput();
        }

        $data['is_active'] = $request->boolean('is_active', true);

        $assessment_risk_rule->update($data);

        return redirect()
            ->route('assessment-risk-rules.index')
            ->with('success', 'Aturan risiko berhasil diperbarui.');
    }

    /**
     * DELETE: hanya KONSELOR.
     */
    public function destroy(AssessmentRiskRule $assessment_risk_rule)
    {
        $assessment_risk_rule->delete();

        return redirect()
            ->route('assessment-risk-rules.index')
            ->with('success', 'Aturan risiko berhasil dihapus.');
    }
}
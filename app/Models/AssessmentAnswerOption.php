<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentAnswerOption extends Model
{
    use HasFactory;

    protected $table = 'assessment_answer_options';

    protected $fillable = [
        'question_id',
        'option_label',
        'option_value',
        'sort_order',
    ];

    /**
     * Opsi milik satu pertanyaan.
     */
    public function question()
    {
        return $this->belongsTo(AssessmentQuestion::class, 'question_id');
    }

    /**
     * Semua jawaban sesi yang memilih opsi ini.
     */
    public function sessionAnswers()
    {
        return $this->hasMany(AssessmentSessionAnswer::class, 'answer_option_id');
    }

    /**
     * Scope: urutkan opsi sesuai sort_order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
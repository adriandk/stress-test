<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentSessionAnswer extends Model
{
    use HasFactory;

    protected $table = 'assessment_session_answers';

    public $timestamps = false; // hanya punya created_at

    protected $fillable = [
        'session_id',
        'question_id',
        'answer_option_id',
        'answer_text',
        'score_value',
        'created_at',
    ];

    public function session()
    {
        return $this->belongsTo(AssessmentSession::class, 'session_id');
    }

    public function question()
    {
        return $this->belongsTo(AssessmentQuestion::class, 'question_id');
    }

    public function option()
    {
        return $this->belongsTo(AssessmentAnswerOption::class, 'answer_option_id');
    }
}
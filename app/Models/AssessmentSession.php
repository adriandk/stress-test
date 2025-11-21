<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentSession extends Model
{
    use HasFactory;

    protected $table = 'assessment_sessions';

    protected $fillable = [
        'session_code',
        'total_score',
        'risk_level',
        'summary_json',
        'client_user_agent',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'summary_json' => 'array',
        'started_at'   => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Route model binding pakai session_code, bukan id
    public function getRouteKeyName(): string
    {
        return 'session_code';
    }

    public function answers()
    {
        return $this->hasMany(AssessmentSessionAnswer::class, 'session_id');
    }
}
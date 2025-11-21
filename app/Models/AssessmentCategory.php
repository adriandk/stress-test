<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentCategory extends Model
{
    use HasFactory;

    protected $table = 'assessment_categories';

    protected $fillable = [
        'name',
        'description',
        'sort_order',
    ];

    /**
     * Satu kategori punya banyak pertanyaan.
     */
    public function questions()
    {
        return $this->hasMany(AssessmentQuestion::class, 'category_id');
    }

    /**
     * Satu kategori bisa punya banyak rule risiko.
     */
    public function riskRules()
    {
        return $this->hasMany(AssessmentRiskRule::class, 'category_id');
    }

    /**
     * Scope untuk urutkan kategori secara default.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}
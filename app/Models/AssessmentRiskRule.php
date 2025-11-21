<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentRiskRule extends Model
{
    use HasFactory;

    protected $table = 'assessment_risk_rules';

    protected $fillable = [
        'category_id',
        'min_total_score',
        'max_total_score',
        'risk_level',
        'description',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'min_total_score' => 'integer',
        'max_total_score' => 'integer',
    ];

    /**
     * Rule ini milik satu kategori (boleh null untuk global).
     */
    public function category()
    {
        return $this->belongsTo(AssessmentCategory::class, 'category_id');
    }

    /**
     * User (Account) yang membuat rule ini.
     */
    public function creator()
    {
        return $this->belongsTo(Account::class, 'created_by');
    }

    /**
     * Scope: hanya rule aktif.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Helper: label manusiwi untuk risk_level.
     */
    public function getRiskLevelLabelAttribute(): string
    {
        return match ($this->risk_level) {
            'low'    => 'Rendah',
            'medium' => 'Sedang',
            'high'   => 'Tinggi',
            default  => ucfirst((string) $this->risk_level),
        };
    }
}
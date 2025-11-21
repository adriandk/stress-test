<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentQuestion extends Model
{
    use HasFactory;

    protected $table = 'assessment_questions';

    protected $fillable = [
        'question_text',
        'category_id',
        'is_active',
        'sort_order',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Pertanyaan milik satu kategori (boleh null).
     */
    public function category()
    {
        return $this->belongsTo(AssessmentCategory::class, 'category_id');
    }

    /**
     * Pertanyaan punya banyak opsi jawaban.
     */
    public function answerOptions()
    {
        return $this->hasMany(AssessmentAnswerOption::class, 'question_id');
    }

    /**
     * User (Account) yang membuat pertanyaan.
     */
    public function creator()
    {
        return $this->belongsTo(Account::class, 'created_by');
    }

    /**
     * User (Account) yang mengupdate terakhir.
     */
    public function updater()
    {
        return $this->belongsTo(Account::class, 'updated_by');
    }

    /**
     * Jawaban-jawaban yang pernah diberikan untuk pertanyaan ini.
     */
    public function sessionAnswers()
    {
        return $this->hasMany(AssessmentSessionAnswer::class, 'question_id');
    }

    /**
     * Scope: hanya pertanyaan aktif.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: urutkan pertanyaan sesuai sort_order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
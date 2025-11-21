<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $table = 'articles';

    protected $fillable = [
        'title',
        'slug',
        'content',
        'thumbnail_url',
        'category_id',
        'status',
        'created_by',
        'verified_by',
        'verified_at',
        'published_at',
    ];

    protected $casts = [
        'verified_at'  => 'datetime',
        'published_at' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(ArticleCategory::class, 'category_id');
    }

    public function author()
    {
        return $this->belongsTo(Account::class, 'created_by');
    }

    public function verifier()
    {
        return $this->belongsTo(Account::class, 'verified_by');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
}
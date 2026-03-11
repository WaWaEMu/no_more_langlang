<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaseDiaryEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'adoption_case_id',
        'author_id',
        'photos',
        'content',
        'location',
    ];

    protected $casts = [
        'photos' => 'array',
    ];

    /**
     * Get the adoption case this diary entry belongs to.
     */
    public function adoptionCase()
    {
        return $this->belongsTo(AdoptionCase::class);
    }

    /**
     * Get the user who wrote this diary entry.
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Get comments on this diary entry.
     */
    public function comments()
    {
        return $this->hasMany(DiaryComment::class, 'diary_entry_id')->orderBy('created_at', 'asc');
    }
}

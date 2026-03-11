<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiaryComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'diary_entry_id',
        'author_id',
        'content',
    ];

    /**
     * Get the diary entry this comment belongs to.
     */
    public function diaryEntry()
    {
        return $this->belongsTo(CaseDiaryEntry::class, 'diary_entry_id');
    }

    /**
     * Get the user who wrote this comment.
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}

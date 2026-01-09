<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PetComment extends Model
{
    use HasFactory;

    protected $fillable = ['pet_id', 'user_id', 'parent_id', 'content'];

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(PetComment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(PetComment::class, 'parent_id')->with('user:id,name')->orderBy('created_at', 'asc');
    }

    public static function getByPetId(int $petId)
    {
        return self::with(['user:id,name', 'replies.user:id,name'])
            ->where('pet_id', $petId)
            ->whereNull('parent_id')
            ->orderBy('created_at', 'desc')
            ->get();
    }
}

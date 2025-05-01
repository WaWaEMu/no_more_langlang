<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PetSendableCity extends Model
{
    use HasFactory;

    protected $fillable = ['pet_id', 'city'];

    public function pet() {
        return $this->belongsTo(Pet::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PetDetail extends Model
{
    use HasFactory;

    protected $fillable = ['pet_id', 'adoption_description', 'health_description', 'adoption_condition'];

    public function pet() {
        return $this->belongsTo(Pet::class);
    }
}

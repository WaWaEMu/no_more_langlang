<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pet extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'city', 'town', 'is_stray', 'type', 'color',
        'fur_type', 'name', 'gender', 'age', 'is_neuter'
    ];

    public function sendableCitites() {
        return $this->hasMany(PetSendableCity::class);
    }

    public function images() {
        return $this->hasMany(PetImage::class)->orderBy('type')->orderBy('index');
    }

    // Each pet has a record, so the hasOne method is used, and the function name is singular
    public function detail() {
        return $this->hasOne(PetDetail::class);
    }
}

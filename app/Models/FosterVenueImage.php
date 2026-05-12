<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FosterVenueImage extends Model
{
    use HasFactory;

    protected $fillable = ['foster_venue_id', 'type', 'path', 'index'];

    public function venue()
    {
        return $this->belongsTo(FosterVenue::class, 'foster_venue_id');
    }
}

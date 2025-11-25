<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'title', 'city', 'town', 'is_stray', 'type', 'color',
        'fur_type', 'name', 'gender', 'age', 'is_neuter'
    ];

    public function sendableCities() {
        return $this->hasMany(PetSendableCity::class);
    }

    public function images() {
        return $this->hasMany(PetImage::class)->orderBy('type')->orderBy('index');
    }

    // Each pet has a record, so the hasOne method is used, and the function name is singular
    public function detail() {
        return $this->hasOne(PetDetail::class);
    }

    public function attachSendableCities(array $cities):void {
        foreach ($cities as $city) {
            $this->sendableCities()->create([
                'city' => $city
            ]);
        }
    }

    public function storeImages(array $images):void {
        $index = 0;
        $date = now()->format('Y_m_d');

        foreach ($images as $image) {
            $extension = $image->guessExtension();
            $filename = $this->id . '_' . $index . '.' . $extension;
            $path = $image->storeAs("images/{$date}", $filename, 'public');
            $this->images()->create([
                'type' => 'preview',
                'index' => $index,
                'path' => $path ?: null
            ]);

            $index++;
        }
    }

    public function storeDetail(array $details):void {
        $this->detail()->create($details);
    }
}

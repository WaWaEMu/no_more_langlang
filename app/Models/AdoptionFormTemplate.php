<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdoptionFormTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'schema',
        'is_default',
    ];

    protected $casts = [
        'schema' => 'array',
        'is_default' => 'boolean',
    ];

    /**
     * The user who owns this template.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Pets that use this template.
     */
    public function pets()
    {
        return $this->hasMany(Pet::class);
    }

    /**
     * Supported field types for the schema builder.
     */
    public static function supportedFieldTypes(): array
    {
        return ['text', 'textarea', 'select', 'radio', 'checkbox'];
    }
}

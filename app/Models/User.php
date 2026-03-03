<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    public function socialAccounts()
    {
        return $this->hasMany(SocialAccount::class);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function favorites()
    {
        return $this->belongsToMany(Pet::class, 'favorites')->withTimestamps();
    }

    public function updateProfile(array $data)
    {
        return $this->update([
            'name' => $data['name'],
        ]);
    }

    public function updatePassword(string $password)
    {
        return $this->update([
            'password' => bcrypt($password),
        ]);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function adoptionCases()
    {
        return $this->hasMany(AdoptionCase::class, 'adopter_id');
    }

    public function managedCases()
    {
        return $this->hasMany(AdoptionCase::class, 'owner_id');
    }

    /**
     * The adoption form templates created by this user.
     */
    public function adoptionFormTemplates()
    {
        return $this->hasMany(AdoptionFormTemplate::class);
    }
}

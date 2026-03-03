<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
    ];

    public function rentals(): HasMany
    {
        return $this->hasMany(Rental::class); //'App\Models\Rental'
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class); //'App\Models\Review'
    }
}

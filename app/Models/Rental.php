<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rental extends Model
{
    use HasFactory;

    protected $fillable = ['start_date', 'end_date', 'total_price', 'user_id', 'equipment_id'];

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function equipment() : BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

}

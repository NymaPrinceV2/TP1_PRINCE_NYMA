<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Equipment;

class Sport extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function equipments(): BelongsToMany
    {
        return $this->belongsToMany(Equipment::class);
    }
}

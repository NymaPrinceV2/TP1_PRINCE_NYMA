<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Sport;
use App\Models\Category;

class Equipment extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'daily_price', 'category_id'];

    public function sports(): BelongsToMany
    {
        return $this->belongsToMany(Sport::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}

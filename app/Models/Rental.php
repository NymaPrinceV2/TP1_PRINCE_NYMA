<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rental extends Model
{
    use HasFactory;

    protected $fillable = ['start_date', 'end_date', 'total_price', 'user_id', 'equipment_id'];

    public function language() : BelongsTo
    {
        return $this->belongsTo('App\Models\Language');
    }

}

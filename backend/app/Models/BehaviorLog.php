<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BehaviorLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'pet_id',
        'log_date',
        'behavior',
        'emotion',
        'appetite',
        'notes',
    ];

    protected $casts = [
        'log_date' => 'date',
    ];

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }
}

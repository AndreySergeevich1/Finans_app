<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shift extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'start_time',
        'end_time',
        'type',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        // Убираем касты для start_time и end_time
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getStartTimeAttribute($value)
    {
        return $value ? date('H:i', strtotime($value)) : null;
    }

    public function getEndTimeAttribute($value)
    {
        return $value ? date('H:i', strtotime($value)) : null;
    }
} 
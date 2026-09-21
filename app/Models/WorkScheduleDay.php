<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkScheduleDay extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'work_schedule_id',
        'day_of_week',
        'is_working_day',
    ];

    protected function casts(): array
    {
        return [
            'work_schedule_id' => 'integer',
            'day_of_week' => 'integer',
            'is_working_day' => 'boolean',
        ];
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(WorkSchedule::class, 'work_schedule_id');
    }
}

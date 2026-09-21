<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeTraining extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'employee_training';

    protected $fillable = [
        'employee_id',
        'training_program_id',
        'assigned_at',
        'completed_at',
        'status',
        'result_score',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'employee_id' => 'integer',
            'training_program_id' => 'integer',
            'assigned_at' => 'datetime',
            'completed_at' => 'datetime',
            'result_score' => 'decimal:2',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(TrainingProgram::class, 'training_program_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrainingCertificate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'training_program_id',
        'certificate_title',
        'certificate_number',
        'issue_date',
        'expiry_date',
        'file_path',
    ];

    protected function casts(): array
    {
        return [
            'employee_id' => 'integer',
            'training_program_id' => 'integer',
            'issue_date' => 'date',
            'expiry_date' => 'date',
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

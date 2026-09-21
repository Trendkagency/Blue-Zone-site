<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'document_type',
        'title',
        'document_number',
        'issue_date',
        'expiry_date',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
        'status',
        'notes',
        'uploaded_by',
    ];

    protected function casts(): array
    {
        return [
            'employee_id' => 'integer',
            'uploaded_by' => 'integer',
            'issue_date' => 'date',
            'expiry_date' => 'date',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}

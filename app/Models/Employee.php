<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'employee_number',
        'first_name',
        'last_name',
        'first_name_ar',
        'last_name_ar',
        'email',
        'phone',
        'alternate_phone',
        'date_of_birth',
        'gender',
        'marital_status',
        'nationality',
        'national_id',
        'address',
        'country_id',
        'city_id',
        'location_id',
        'department_id',
        'position_id',
        'manager_employee_id',
        'employment_type',
        'employment_status',
        'hire_date',
        'probation_start_date',
        'probation_end_date',
        'contract_start_date',
        'contract_end_date',
        'work_schedule_id',
        'basic_salary',
        'housing_allowance',
        'transportation_allowance',
        'other_allowance',
        'payment_method',
        'bank_name',
        'bank_account_number',
        'iban',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'country_id' => 'integer',
            'city_id' => 'integer',
            'department_id' => 'integer',
            'position_id' => 'integer',
            'manager_employee_id' => 'integer',
            'work_schedule_id' => 'integer',
            'date_of_birth' => 'date',
            'hire_date' => 'date',
            'probation_start_date' => 'date',
            'probation_end_date' => 'date',
            'contract_start_date' => 'date',
            'contract_end_date' => 'date',
            'basic_salary' => 'decimal:2',
            'housing_allowance' => 'decimal:2',
            'transportation_allowance' => 'decimal:2',
            'other_allowance' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'manager_employee_id');
    }

    public function directReports(): HasMany
    {
        return $this->hasMany(Employee::class, 'manager_employee_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function workSchedule(): BelongsTo
    {
        return $this->belongsTo(WorkSchedule::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(EmployeeDocument::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(EmployeeContract::class);
    }

    public function salaryHistory(): HasMany
    {
        return $this->hasMany(EmployeeSalaryHistory::class);
    }

    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    public function overtimeRequests(): HasMany
    {
        return $this->hasMany(OvertimeRequest::class);
    }

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function leaveBalances(): HasMany
    {
        return $this->hasMany(LeaveBalance::class);
    }

    public function salaryAssignments(): HasMany
    {
        return $this->hasMany(EmployeeSalaryAssignment::class);
    }

    public function payrollRecords(): HasMany
    {
        return $this->hasMany(PayrollRecord::class);
    }

    public function salaryAdvances(): HasMany
    {
        return $this->hasMany(SalaryAdvance::class);
    }

    public function loans(): HasMany
    {
        return $this->hasMany(EmployeeLoan::class);
    }

    public function performanceGoals(): HasMany
    {
        return $this->hasMany(PerformanceGoal::class);
    }

    public function performanceReviews(): HasMany
    {
        return $this->hasMany(PerformanceReview::class);
    }

    public function promotions(): HasMany
    {
        return $this->hasMany(EmployeePromotion::class);
    }

    public function trainingRecords(): HasMany
    {
        return $this->hasMany(EmployeeTraining::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(TrainingCertificate::class);
    }

    public function requests(): HasMany
    {
        return $this->hasMany(EmployeeRequest::class);
    }

    public function transfers(): HasMany
    {
        return $this->hasMany(EmployeeTransfer::class);
    }

    public function disciplinaryActions(): HasMany
    {
        return $this->hasMany(EmployeeDisciplinaryAction::class);
    }

    public function assetAssignments(): HasMany
    {
        return $this->hasMany(EmployeeAssetAssignment::class);
    }

    public function offboarding(): HasOne
    {
        return $this->hasOne(EmployeeOffboarding::class);
    }

    public function getFullNameAttribute(): string
    {
        if (app()->getLocale() === 'ar' && (!empty($this->first_name_ar) || !empty($this->last_name_ar))) {
            return trim(($this->first_name_ar ?? '') . ' ' . ($this->last_name_ar ?? ''));
        }

        return trim("{$this->first_name} {$this->last_name}");
    }

    public function getGrossSalaryAttribute(): float
    {
        return (float) ($this->basic_salary + $this->housing_allowance + $this->transportation_allowance + $this->other_allowance);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('employment_status', 'active');
    }

    public function scopeProbation(Builder $query): Builder
    {
        return $query->where('employment_status', 'probation');
    }

    public function scopeOnLeave(Builder $query): Builder
    {
        return $query->where('employment_status', 'on_leave');
    }
}

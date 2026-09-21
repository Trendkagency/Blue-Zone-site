<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('employees')) {
            Schema::create('employees', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('employee_number')->unique();
                $table->string('first_name');
                $table->string('last_name');
                $table->string('first_name_ar')->nullable();
                $table->string('last_name_ar')->nullable();
                $table->string('email')->nullable()->index();
                $table->string('phone')->nullable()->index();
                $table->string('alternate_phone')->nullable();
                $table->date('date_of_birth')->nullable();
                $table->string('gender')->nullable(); // male, female
                $table->string('marital_status')->nullable(); // single, married, divorced, widowed
                $table->string('nationality')->nullable();
                $table->string('national_id')->nullable()->index();
                $table->text('address')->nullable();
                $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete();
                $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete();
                $table->string('location_id')->nullable()->index();
                $table->foreign('location_id')->references('id')->on('locations')->nullOnDelete();
                $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
                $table->foreignId('position_id')->nullable()->constrained('positions')->nullOnDelete();
                $table->foreignId('manager_employee_id')->nullable()->constrained('employees')->nullOnDelete();
                
                // Employment details
                $table->string('employment_type')->default('full_time')->index(); // full_time, part_time, contract, freelancer, intern, temporary
                $table->string('employment_status')->default('active')->index(); // active, probation, on_leave, suspended, resigned, terminated, retired
                $table->date('hire_date')->index();
                $table->date('probation_start_date')->nullable();
                $table->date('probation_end_date')->nullable()->index();
                $table->date('contract_start_date')->nullable();
                $table->date('contract_end_date')->nullable()->index();
                $table->foreignId('work_schedule_id')->nullable()->constrained('work_schedules')->nullOnDelete();
                
                // Financials & Compensation
                $table->decimal('basic_salary', 12, 2)->default(0.00);
                $table->decimal('housing_allowance', 12, 2)->default(0.00);
                $table->decimal('transportation_allowance', 12, 2)->default(0.00);
                $table->decimal('other_allowance', 12, 2)->default(0.00);
                $table->string('payment_method')->nullable(); // bank_transfer, cheque, cash
                $table->string('bank_name')->nullable();
                $table->string('bank_account_number')->nullable();
                $table->string('iban')->nullable();
                $table->text('notes')->nullable();

                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('employee_documents')) {
            Schema::create('employee_documents', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
                $table->string('document_type')->index(); // national_id, passport, contract, education_certificate, experience_certificate, medical_certificate, insurance, tax_document, bank_document, other
                $table->string('title');
                $table->string('document_number')->nullable();
                $table->date('issue_date')->nullable();
                $table->date('expiry_date')->nullable()->index();
                $table->string('file_path');
                $table->string('file_name')->nullable();
                $table->string('file_size')->nullable();
                $table->string('mime_type')->nullable();
                $table->string('status')->default('valid')->index(); // valid, expired, pending_review, rejected
                $table->text('notes')->nullable();
                $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('employee_contracts')) {
            Schema::create('employee_contracts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
                $table->string('contract_number')->unique();
                $table->string('contract_type')->default('fixed_term'); // fixed_term, permanent, temporary, internship
                $table->date('start_date')->index();
                $table->date('end_date')->nullable()->index();
                $table->decimal('basic_salary', 12, 2);
                $table->decimal('housing_allowance', 12, 2)->default(0.00);
                $table->decimal('transportation_allowance', 12, 2)->default(0.00);
                $table->decimal('other_allowance', 12, 2)->default(0.00);
                $table->unsignedInteger('probation_days')->default(90);
                $table->unsignedInteger('notice_period_days')->default(30);
                $table->unsignedInteger('working_hours_per_week')->default(40);
                $table->string('status')->default('active')->index(); // active, expired, terminated, renewed
                $table->string('document_path')->nullable();
                $table->text('notes')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('employee_salary_history')) {
            Schema::create('employee_salary_history', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
                $table->decimal('old_salary', 12, 2);
                $table->decimal('new_salary', 12, 2);
                $table->date('effective_date')->index();
                $table->string('reason')->nullable();
                $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_salary_history');
        Schema::dropIfExists('employee_contracts');
        Schema::dropIfExists('employee_documents');
        Schema::dropIfExists('employees');
    }
};

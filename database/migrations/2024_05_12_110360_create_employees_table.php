<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->boolean('active_status')->default(true);
            $table->string('staff_number')->nullable();
            $table->string('sur_name')->nullable();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('gender')->nullable();
            $table->unsignedBigInteger('designation_id');
            $table->date('last_promotion')->nullable();
            $table->string('level')->nullable();
            $table->string('step')->nullable();
            $table->string('cadre')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('height')->nullable();
            $table->string('weight')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->date('date_employed')->nullable();
            $table->date('exit_date')->nullable();
            $table->string('exit_reason')->nullable();
            $table->unsignedBigInteger('bank_id')->nullable();
            $table->string('account_number')->nullable();
            $table->string('blood_group')->nullable();
            $table->string('genotype')->nullable();
            $table->boolean('in_staff_qtrs')->default(false);
            $table->string('region')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('state_of_origin_id')->nullable();
            $table->unsignedBigInteger('LGA_of_origin_id')->nullable();
            $table->unsignedBigInteger('department_id');
            $table->string('home_address')->nullable();
            $table->string('contact_address')->nullable();
            $table->string('personal_phone')->nullable();
            $table->string('personal_email')->nullable();
            $table->string('office_location')->nullable();
            $table->string('employment_type')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('birth_place')->nullable();
            $table->string('spouse_name')->nullable();
            $table->date('marriage_date')->nullable();
            $table->unsignedBigInteger('nationality_id')->nullable();
            $table->unsignedBigInteger('qualifications_id')->nullable();
            $table->unsignedBigInteger('profession_id')->nullable();
            $table->string('confirmation_status')->nullable();
            $table->unsignedBigInteger('position_id')->nullable();
            $table->date('date_confirmed')->nullable();
            $table->unsignedBigInteger('pension_managers_id')->nullable();
            $table->string('pension_amount')->nullable();
            $table->string('deformity')->nullable();
            $table->string('salary')->nullable();
            $table->string('days_worked')->nullable();
            $table->string('tax_id')->nullable();
            $table->string('pension_pin')->nullable();
            $table->string('passport_number')->nullable();
            $table->string('residency_status')->nullable();
            $table->string('visa_type')->nullable();
            $table->date('visa_expiry')->nullable();
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('designation_id')->references('id')->on('designations')->onDelete('cascade');
            $table->foreign('bank_id')->references('id')->on('banks')->onDelete('cascade');
            // $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->foreign('state_of_origin_id')->references('id')->on('states')->onDelete('cascade');
            $table->foreign('LGA_of_origin_id')->references('id')->on('LGAs')->onDelete('cascade');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
            $table->foreign('nationality_id')->references('id')->on('nationalities')->onDelete('cascade');
            // $table->foreign('qualifications_id')->references('id')->on('qualifications')->onDelete('cascade');
            // $table->foreign('profession_id')->references('id')->on('professions')->onDelete('cascade');
            // $table->foreign('position_id')->references('id')->on('positions')->onDelete('cascade');
            // $table->foreign('pension_managers_id')->references('id')->on('pension_managers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employees');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salaries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('employee_id');
            $table->decimal('basic_amount', 10, 2);
            $table->decimal('allowances', 10, 2);
            $table->decimal('deductions', 10, 2);
            $table->decimal('bonuses', 10, 2)->nullable();
            $table->integer('days_absent')->default(0);
            $table->decimal('absentee_deduction', 10, 2)->nullable();
            $table->decimal('gross', 10, 2);
            $table->decimal('net', 10, 2);
            $table->string('salary_type');
            $table->string('month');
            $table->year('year');
            $table->string('currency');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('salaries');
    }
}

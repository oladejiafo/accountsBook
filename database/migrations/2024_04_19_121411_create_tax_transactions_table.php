<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaxTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tax_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tax_code_id');
            $table->unsignedBigInteger('tax_return_id');
            $table->unsignedBigInteger('tax_rate_id');
            $table->string('transaction_type');
            $table->decimal('amount', 15, 2);
            $table->unsignedBigInteger('company_id');
            // Add other necessary fields for tax transactions
            $table->timestamps();

            $table->foreign('tax_code_id')->references('id')->on('tax_codes')->onDelete('cascade');
            $table->foreign('tax_return_id')->references('id')->on('tax_returns')->onDelete('cascade');
            $table->foreign('tax_rate_id')->references('id')->on('tax_rates')->onDelete('cascade');
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
        Schema::dropIfExists('tax_transactions');
    }
}

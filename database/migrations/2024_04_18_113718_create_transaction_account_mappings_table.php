<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionAccountMappingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_account_mappings', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_type');
            $table->foreignId('debit_account_id')->nullable()->constrained('chart_of_accounts');
            $table->foreignId('credit_account_id')->nullable()->constrained('chart_of_accounts');
            $table->boolean('is_credit');
            $table->foreignId('company_id')->constrained('companies');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaction_account_mappings');
    }
}

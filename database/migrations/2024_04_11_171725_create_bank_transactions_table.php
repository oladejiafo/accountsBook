<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBankTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('bank_account_id')->nullable();
            $table->string('bank_transaction_id')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('type'); // Example: deposit, withdrawal, transfer
            $table->date('date');
            $table->decimal('amount', 10, 2);
            $table->string('description')->nullable();
            // Add other fields related to bank transactions
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
        Schema::dropIfExists('bank_transactions');
    }
}

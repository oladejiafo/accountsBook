<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('type');
            $table->date('date');
            $table->decimal('amount', 10, 2);
            $table->string('description')->nullable();

            $table->string('transaction_name')->nullable();
            $table->string('recipient_name')->nullable();
            $table->string('recipient_account_no')->nullable();
            $table->string('status')->nullable();
            $table->string('source')->nullable();
            $table->unsignedBigInteger('bank_id')->nullable();
            $table->unsignedBigInteger('from_account_id')->nullable();
            $table->unsignedBigInteger('to_account_id')->nullable();

            $table->foreign('bank_id')->references('id')->on('banks')->onDelete('set null');
            $table->foreign('from_account_id')->references('id')->on('chart_of_accounts')->onDelete('set null');
            $table->foreign('to_account_id')->references('id')->on('chart_of_accounts')->onDelete('set null');

            $table->foreignId('account_id')->constrained('chart_of_accounts')->onDelete('cascade');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
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
        Schema::dropIfExists('transactions');
    }
}

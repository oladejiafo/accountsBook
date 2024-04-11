<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReconciliationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reconciliations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('status');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('total_deposits', 10, 2)->nullable();
            $table->decimal('total_withdrawals', 10, 2)->nullable();
            $table->decimal('ending_balance', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->string('attachments')->nullable();
            $table->string('method')->nullable();
            // Add other fields as needed
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
        Schema::dropIfExists('reconciliations');
    }
}

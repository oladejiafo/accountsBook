<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_order_id');
            $table->date('date_issued');
            $table->text('description');
            $table->integer('quantity')->nullable();
            $table->decimal('rate', 10, 2);
            $table->decimal('total_amount', 10, 2);
            $table->string('payment_status')->default('unpaid');
            $table->timestamps();

            $table->foreign('service_order_id')->references('id')->on('service_orders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_invoices');
    }
}

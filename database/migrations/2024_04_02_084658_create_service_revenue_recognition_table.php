<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceRevenueRecognitionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_revenue_recognition', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_order_id');
            $table->date('recognition_date');
            $table->decimal('recognized_amount', 10, 2);
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
        Schema::dropIfExists('service_revenue_recognition');
    }
}

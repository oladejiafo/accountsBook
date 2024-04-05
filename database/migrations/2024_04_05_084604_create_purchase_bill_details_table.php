<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseBillDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_bill_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('billno');
            $table->foreign('billno')->references('billno')->on('purchase_bills')->onDelete('cascade');
            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->string('eway')->nullable();
            $table->string('veh')->nullable();
            $table->string('destination')->nullable();
            $table->string('po')->nullable();
            $table->string('cgst')->nullable();
            $table->string('sgst')->nullable();
            $table->string('igst')->nullable();
            $table->string('cess')->nullable();
            $table->string('tcs')->nullable();
            $table->string('total')->nullable();
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
        Schema::dropIfExists('purchase_bill_details');
    }
}

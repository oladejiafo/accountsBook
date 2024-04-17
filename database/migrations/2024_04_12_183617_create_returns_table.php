<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReturnsTable extends Migration
{
    public function up()
    {
        Schema::create('return_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('company_id'); // Added company_id
            $table->boolean('approval_required')->default(0);
            $table->string('exchange')->nullable(); 
            $table->string('return_authorization_number')->nullable();
            $table->date('return_date');
            $table->string('reason_for_return')->nullable(); // Reason for return
            $table->string('return_status')->default('pending'); // Return status
            $table->text('notes')->nullable(); // Additional notes
            // Add shipping details
            $table->string('carrier')->nullable();
            $table->string('tracking_number')->nullable();
            $table->decimal('shipping_cost', 10, 2)->nullable();
            // Add refund details
            $table->decimal('refund_amount', 10, 2)->nullable();
            $table->string('payment_method')->nullable();
            $table->string('transaction_id')->nullable();
            $table->timestamps();
            
            // Define foreign key constraints
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });
        
        Schema::create('returned_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('return_id');
            $table->unsignedBigInteger('product_id');
            $table->string('product_name');
            $table->integer('quantity');
            $table->string('condition')->nullable();
            $table->timestamps();

            // Define foreign key constraints
            $table->foreign('return_id')->references('id')->on('returns')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('returned_products');
        Schema::dropIfExists('return_transactions');
    }
}
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('name');
            $table->string('phone');
            $table->string('email')->unique();
            $table->text('address');
            $table->string('city');
            $table->string('country');
            $table->text('billing_address')->nullable();
            $table->text('shipping_address')->nullable();
            $table->string('customer_type')->nullable();
            $table->text('notes')->nullable();
            $table->string('payment_terms')->nullable();
            $table->decimal('balance', 11, 2)->default(0.00);
            $table->boolean('tax_exempt')->default(false);
            // Add custom fields as needed
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
        Schema::dropIfExists('customers');
    }
}

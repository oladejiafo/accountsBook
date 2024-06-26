<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFixedAssetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fixed_assets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('dept_id');
            $table->string('name');
            $table->string('office')->nullable();
            $table->text('description')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('asset_code')->nullable();
            $table->decimal('purchase_price', 10, 2);

            $table->date('acquisition_date');

            $table->string('depreciation_method')->nullable();
            $table->integer('useful_life')->nullable();
            $table->decimal('salvage_value', 15, 2)->default(0);
            $table->decimal('current_value', 15, 2)->nullable();
            $table->string('location')->nullable();
            $table->enum('status', ['active', 'disposed', 'sold', 'transferred'])->default('active');
            
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
        Schema::dropIfExists('fixed_assets');
    }
}

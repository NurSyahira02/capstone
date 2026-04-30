<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ninjavan_data', function (Blueprint $table) {
            $table->id();
            $table->string('Shipper_ID')->nullable();
            $table->string('Shipper_Name')->nullable();
            $table->string('Tracking_ID')->nullable();
            $table->string('Order_Granular_Status')->nullable();
            $table->string('Delivery_Type_Name')->nullable();
            $table->string('Service_Type')->nullable();
            $table->string('Service_Level')->nullable();
            $table->string('Parcel_Size_ID')->nullable();
            $table->decimal('Original_Weight', 10, 2)->nullable();
            $table->dateTime('Create_Time')->nullable();
            $table->date('Delivery_Date')->nullable();
            $table->integer('Gender')->nullable();
            $table->string('L1_Name')->nullable();
            $table->string('To_Postcode')->nullable();
            $table->decimal('Actual_delivery_fee', 10, 2)->nullable();
            $table->decimal('cod_collected', 10, 2)->nullable();
            $table->decimal('cod_fee', 10, 2)->nullable();
            $table->decimal('insured_value', 10, 2)->nullable();
            $table->decimal('insurance_fee', 10, 2)->nullable();
            $table->decimal('SST', 10, 2)->nullable();
            $table->decimal('Total_Charge', 10, 2)->nullable();
            $table->string('Country')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ninjavan_data');
    }
};

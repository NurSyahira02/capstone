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
        Schema::create('customer_ratings', function (Blueprint $table) {
            $table->id();
            $table->integer('rating_punctuality');
            $table->integer('rating_condition');
            $table->integer('rating_attitude');
            $table->string('preferred_courier');
            $table->string('choice_reason');
            $table->integer('rating_trust');
            $table->timestamps(); // This automatically creates created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_ratings');
    }
};

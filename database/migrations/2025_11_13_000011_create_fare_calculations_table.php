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
        Schema::create('fare_calculations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained('trips')->onDelete('cascade');
            $table->foreignId('fare_rule_id')->constrained('fare_rules')->onDelete('cascade');
            $table->integer('passenger_count');
            $table->decimal('unit_fare', 10, 2); // Fare per passenger/trip/km
            $table->decimal('total_fare', 10, 2); // passenger_count * unit_fare
            $table->text('calculation_details')->nullable(); // JSON with calculation breakdown
            $table->timestamps();
            
            $table->index(['trip_id', 'fare_rule_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fare_calculations');
    }
};

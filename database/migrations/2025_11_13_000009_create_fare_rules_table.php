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
        Schema::create('fare_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Fixed Fare - Route A-1"
            $table->string('type')->default('fixed'); // fixed, distance_based, special
            $table->foreignId('route_id')->nullable()->constrained('routes')->onDelete('cascade');
            $table->decimal('base_fare', 10, 2)->nullable(); // For fixed type
            $table->string('calculation_mode')->nullable(); // per_trip, per_km, per_passenger
            $table->text('description')->nullable();
            $table->date('effective_from');
            $table->date('effective_until')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fare_rules');
    }
};

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
        Schema::create('distance_fare_brackets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fare_rule_id')->constrained('fare_rules')->onDelete('cascade');
            $table->decimal('distance_from_km', 8, 2);
            $table->decimal('distance_to_km', 8, 2);
            $table->decimal('fare_per_passenger', 10, 2);
            $table->timestamps();
            
            $table->unique(['fare_rule_id', 'distance_from_km', 'distance_to_km'], 'dfb_rule_dist_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distance_fare_brackets');
    }
};

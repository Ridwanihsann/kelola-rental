<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rental_id')->constrained()->onDelete('cascade');
            $table->dateTime('actual_return_date');
            $table->unsignedInteger('penalty_fee')->default(0); // Late fee in IDR
            $table->unsignedInteger('final_total_price'); // Total + penalty
            $table->timestamps();

            // Each rental can only have one history record
            $table->unique('rental_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('histories');
    }
};

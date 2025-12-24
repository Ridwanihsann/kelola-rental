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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique(); // Unique code for QR
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedInteger('daily_price'); // Price in IDR
            $table->enum('status', ['available', 'rented'])->default('available');
            $table->string('image')->nullable(); // Path to image file
            $table->timestamps();

            // Indexes
            $table->index('status');
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};

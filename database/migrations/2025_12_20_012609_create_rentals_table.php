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
        Schema::create('rentals', function (Blueprint $table) {
            $table->id();
            $table->string('renter_name');
            $table->string('renter_phone', 20);
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedInteger('total_price'); // Calculated total in IDR
            $table->enum('status', ['active', 'done'])->default('active');
            $table->timestamps();

            // Indexes
            $table->index('status');
            $table->index('end_date');
            $table->index(['status', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rentals');
    }
};

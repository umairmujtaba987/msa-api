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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->string('phone_number');
            $table->enum('court', ['A', 'B']);
            $table->enum('sport', ['Cricket', 'Football']);
            $table->date('booking_date');
            $table->time('start_time');
            $table->time('end_time')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->enum('status', ['Pending', 'Confirmed', 'Paid', 'Cancelled'])->default('Pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};

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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            // ini otomatis bikin kolom user_id (unsignedBigInteger) + foreign key ke users.id
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // ini otomatis bikin kolom listing_id + foreign key ke listings.id
            $table->foreignId('listing_id')->constrained()->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('price_per_day')->unsigned()->default(0);
            $table->integer('total_days')->unsigned()->default(0);
            $table->integer('fee')->unsigned()->default(0);
            $table->integer('total_price')->unsigned()->default(0);
            $table->enum('status', ['waiting', 'approved', 'canceled'])->default('waiting');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }




};

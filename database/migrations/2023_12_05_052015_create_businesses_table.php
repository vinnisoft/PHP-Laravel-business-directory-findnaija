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
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->string('country')->nullable();
            $table->string('name')->nullable();
            $table->string('address')->nullable();
            $table->integer('category')->nullable();
            $table->string('buss_phone_number')->nullable();
            $table->time('buss_hours')->nullable();
            $table->string('website')->nullable();
            $table->string('owner_first_name')->nullable();
            $table->string('owner_last_name')->nullable();
            $table->string('identification')->nullable();
            $table->string('owner_phone_number')->nullable();
            $table->enum('hiring_for_buss', ['0', '1'])->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('businesses');
    }
};

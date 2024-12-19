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
        Schema::create('business_recommendations', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->string('business_name')->nullable();
            $table->string('business_address')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('customer_address')->nullable();
            $table->string('business_phone')->nullable();
            $table->string('website')->nullable();
            $table->string('detail')->nullable();
            $table->string('country')->nullable();
            $table->string('continent')->nullable();
            $table->string('state')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('user_latitude')->nullable();
            $table->string('user_longitude')->nullable();
            $table->integer('admin_id')->nullable();
            $table->enum('status', ['0', '1'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_recommendations');
    }
};

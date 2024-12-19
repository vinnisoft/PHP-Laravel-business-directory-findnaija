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
        Schema::table('chat_requests', function (Blueprint $table) {
            $table->timestamp('delete_requested_by')->nullable();
            $table->timestamp('delete_requested_to')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat_requests', function (Blueprint $table) {
            //
        });
    }
};

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
            $table->dateTime('deleted_msg_date_by')->nullable();
            $table->dateTime('deleted_msg_date_to')->nullable();
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

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
        Schema::table('business_conversation_members', function (Blueprint $table) {
            $table->enum('online', ['0', '1']);
            $table->integer('unread_messages');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('business_conversation_members', function (Blueprint $table) {
            //
        });
    }
};

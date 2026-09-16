<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('table_id')->constrained()->cascadeOnDelete();
            $table->string('session_token')->unique(); // Links device to the session
            $table->enum('status', ['active', 'closed'])->default('active'); // closed when paid
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_sessions');
    }
};

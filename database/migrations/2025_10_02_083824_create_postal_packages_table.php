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
        Schema::create('postal_packages', function (Blueprint $table) {
            $table->Increments('id');
            $table->unsignedInteger('user_id');

            $table->foreignId('sender_id')->constrained('people');
            $table->foreignId('receiver_id')->constrained('people');

            $table->decimal('weight', 8, 2);
            $table->decimal('length', 6, 2);
            $table->decimal('width', 6, 2);
            $table->decimal('height', 6, 2);
            $table->bigInteger('postage')->default(0);
            $table->string('tracking_code')->unique()->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('tracking_code');
            $table->index(['sender_id', 'receiver_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('postal_packages');
    }
};

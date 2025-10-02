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
        Schema::create('parcels', function (Blueprint $table) {
            $table->Increments('id');
            //$table->unsignedInteger('user_id');

            $table->unsignedInteger('sender_id');
            $table->unsignedInteger('receiver_id');

            $table->decimal('weight', 8, 2);
            $table->decimal('length', 6, 2);
            $table->decimal('width', 6, 2);
            $table->decimal('height', 6, 2);
            //$table->bigInteger('postage')->default(0);
            $table->string('tracking_code')->unique();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('sender_id')->references('id')->on('people');
            $table->foreign('receiver_id')->references('id')->on('people');
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

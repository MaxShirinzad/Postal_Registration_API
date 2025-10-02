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

            $table->string('sender_name');
            $table->string('sender_mobile');
            $table->string('sender_postal_code');
            $table->string('sender_address');
            $table->string('receiver_name');
            $table->string('receiver_mobile');
            $table->string('receiver_postal_code');
            $table->string('receiver_address');
            $table->float('weight');
            $table->float('length');
            $table->float('width');
            $table->float('height');
            $table->string('tracking_code')->unique()->nullable();

            $table->timestamps();
            $table->softDeletes();
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

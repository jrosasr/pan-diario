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
        Schema::create('beneficiaries', function (Blueprint $table) {
            $table->id();

            $table->string('full_name');
            $table->string('dni')->nullable();
            $table->string('birthdate')->nullable();
            $table->double('weight')->nullable();
            $table->text('address')->nullable();
            $table->string('photo')->nullable();
            $table->string('dni_photo')->nullable();
            $table->string('phone')->nullable();
            $table->string('alt_phone')->nullable();
            $table->string('diner');
            $table->boolean('active')->default(true);

            $table->string('qr_code')->unique()->nullable();

            $table->foreignId('team_id')->constrained()->onDelete('cascade');

            // Add unique index for 'dni' and 'team_id' combination
            $table->unique(['dni', 'team_id']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beneficiaries');
    }
};

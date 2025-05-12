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
        Schema::create('workdays', function (Blueprint $table) {
            $table->id();

            $table->date('started_at');
            $table->time('start_time_at', precision: 0);
            $table->time('end_time_at', precision: 0)->nullable();
            $table->enum('status', ['in-process', 'finished'])->default('in-process');

            $table->foreignId('team_id')->constrained()->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workdays');
    }
};

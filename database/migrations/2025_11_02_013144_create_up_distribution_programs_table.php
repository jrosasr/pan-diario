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
        Schema::create('up_distribution_programs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::table('deliveries', function (Blueprint $table) {
            $table->foreignId('up_distribution_program_id')->nullable()->constrained('up_distribution_programs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('up_distribution_programs');

        Schema::table('deliveries', function (Blueprint $table) {
            $table->dropForeign(['up_distribution_program_id']);
            $table->dropColumn('up_distribution_program_id');
        });
    }
};

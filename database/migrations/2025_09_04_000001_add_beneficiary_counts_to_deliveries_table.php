<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->unsignedInteger('men_seniors_count')->default(0);
            $table->unsignedInteger('women_seniors_count')->default(0);
            $table->unsignedInteger('men_count')->default(0);
            $table->unsignedInteger('women_count')->default(0);
            $table->unsignedInteger('boys_count')->default(0);
            $table->unsignedInteger('girls_count')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->dropColumn([
                'men_seniors_count',
                'women_seniors_count',
                'men_count',
                'women_count',
                'boys_count',
                'girls_count',
            ]);
        });
    }
};

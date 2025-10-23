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
        Schema::table('up_requests', function (Blueprint $table) {
            $table->string('status')->default('pending'); // e.g., 'pending', 'processed', 'completed', 'rejected'
            $table->datetime('rejected_at')->nullable();
            $table->string('rejected_reason')->nullable();
            $table->dateTime('requested_at')->nullable();
            $table->dateTime('processed_at')->nullable();
            $table->dateTime('completed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('up_requests', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('rejected_at');
            $table->dropColumn('rejected_reason');
            $table->dropColumn('requested_at');
            $table->dropColumn('processed_at');
            $table->dropColumn('completed_at');
        });
    }
};

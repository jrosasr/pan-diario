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
        Schema::create('up_accompaniments', function (Blueprint $table) {
            $table->id();
            // up_request_id foreign key
            $table->foreignId('up_request_id')->constrained('up_requests')->onDelete('cascade');
            // up_team_member_id foreign key
            $table->foreignId('up_team_member_id')->constrained('up_team_members')->onDelete('cascade');
            // beneficiary_id foreign key
            $table->foreignId('beneficiary_id')->constrained('beneficiaries')->onDelete('cascade');
            // church_id foreign key
            $table->foreignId('church_id')->nullable()->constrained('churches')->onDelete('cascade');
            // accompaniment_date timestamp
            $table->timestamp('accompaniment_date');
            // status que puede ser 'pending', 'scheduled', 'completed', 'cancelled'
            $table->enum('status', ['pending', 'scheduled', 'completed', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('up_accompaniments');
    }
};

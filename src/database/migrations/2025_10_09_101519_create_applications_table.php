<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('attendance_record_id')->constrained('attendance_records')->onDelete('cascade');

            $table->string('approval_status', 255)->default('pending');
            $table->date('application_date');
            $table->date('new_date')->nullable();
            $table->time('new_clock_in')->nullable();
            $table->time('new_clock_out')->nullable();
            $table->time('new_break_in')->nullable();
            $table->time('new_break_out')->nullable();
            $table->time('new_break2_in')->nullable();
            $table->time('new_break2_out')->nullable();

            $table->string('comment', 255)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};

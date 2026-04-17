<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('counseling_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('counselor_id')->constrained('counselors')->onDelete('cascade');
            $table->string('type');
            $table->date('date');
            $table->time('time');
            $table->string('status')->default('assigned'); // assigned, completed, cancelled
            $table->text('notes')->nullable();
            $table->text('summary')->nullable();
            $table->string('follow_up_needed')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('counseling_sessions');
    }
};

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
        // Clear obsolete records since the data structure is changing fundamentally
        \Illuminate\Support\Facades\DB::table('availability_requests')->truncate();

        Schema::table('availability_requests', function (Blueprint $table) {
            if (Schema::hasColumn('availability_requests', 'days')) {
                $table->dropColumn('days');
            }
            if (!Schema::hasColumn('availability_requests', 'date')) {
                $table->date('date')->after('counselor_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('availability_requests', function (Blueprint $table) {
            $table->dropColumn('date');
            $table->string('days');
        });
    }
};

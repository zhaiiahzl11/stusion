<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add username column as nullable first to avoid error on existing rows
        Schema::table('admins', function (Blueprint $table) {
            $table->string('username')->nullable()->after('name');
        });
        Schema::table('counselors', function (Blueprint $table) {
            $table->string('username')->nullable()->after('name');
        });
        Schema::table('students', function (Blueprint $table) {
            $table->string('username')->nullable()->after('name');
        });

        // 2. Populate username for existing rows, and fix non-standard emails
        foreach (['admins', 'counselors', 'students'] as $table) {
            $users = DB::table($table)->get();
            foreach ($users as $user) {
                // Generate a clean, lowercase username from name
                $username = strtolower(preg_replace('/[^A-Za-z0-9]/', '', $user->name));
                
                // If the generated username is empty, fallback to a name prefix or id
                if (empty($username)) {
                    $username = 'user' . $user->id;
                }

                // If email does not contain '@', make it a valid dummy email [name]@stusion.com
                $email = $user->email;
                if (!str_contains($email, '@')) {
                    $email = strtolower(preg_replace('/[^A-Za-z0-9]/', '', $user->name)) . '@stusion.com';
                }

                DB::table($table)->where('id', $user->id)->update([
                    'username' => $username,
                    'email' => $email
                ]);
            }
        }

        // 3. Make username NOT NULL and UNIQUE now that it is fully populated
        Schema::table('admins', function (Blueprint $table) {
            $table->string('username')->nullable(false)->unique()->change();
        });
        Schema::table('counselors', function (Blueprint $table) {
            $table->string('username')->nullable(false)->unique()->change();
        });
        Schema::table('students', function (Blueprint $table) {
            $table->string('username')->nullable(false)->unique()->change();
        });
    }

    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn('username');
        });
        Schema::table('counselors', function (Blueprint $table) {
            $table->dropColumn('username');
        });
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('username');
        });
    }
};

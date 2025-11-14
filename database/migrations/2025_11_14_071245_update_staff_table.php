<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        // -------------------------------
        // If SQLite, rebuild the table
        // -------------------------------
        if ($driver === 'sqlite') {

            Schema::create('staff_new', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('staff_no')->unique();
                $table->timestamps();
            });

            // Copy data → generate placeholder staff_no
            DB::table('staff')->orderBy('id')->chunk(100, function ($rows) {
                foreach ($rows as $row) {
                    DB::table('staff_new')->insert([
                        'id' => $row->id,
                        'name' => $row->name,
                        'staff_no' => 'TEMP-' . $row->id,  // placeholder
                        'created_at' => $row->created_at,
                        'updated_at' => $row->updated_at,
                    ]);
                }
            });

            Schema::drop('staff');
            Schema::rename('staff_new', 'staff');

            return;
        }

        // -------------------------------
        // If NOT SQLite (MySQL, PostgreSQL)
        // -------------------------------
        Schema::table('staff', function (Blueprint $table) {
            // Drop foreign keys
            $table->dropForeign(['user_id']);
            $table->dropForeign(['department_id']);

            // Drop the columns
            $table->dropColumn(['user_id', 'department_id']);

            // Add staff_no
            $table->string('staff_no')->unique()->after('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();

        // --------------------------------
        // SQLite rollback → rebuild table
        // --------------------------------
        if ($driver === 'sqlite') {

            Schema::create('staff_old', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('department_id')->nullable();
                $table->timestamps();
            });

            DB::table('staff')->orderBy('id')->chunk(100, function ($rows) {
                foreach ($rows as $row) {
                    DB::table('staff_old')->insert([
                        'id'         => $row->id,
                        'name'       => $row->name,
                        'created_at' => $row->created_at,
                        'updated_at' => $row->updated_at,
                    ]);
                }
            });

            Schema::drop('staff');
            Schema::rename('staff_old', 'staff');

            return;
        }

        // --------------------------------
        // MySQL/Postgres rollback
        // --------------------------------
        Schema::table('staff', function (Blueprint $table) {
            $table->dropColumn('staff_no');

            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();

            $table->foreign('user_id')
                ->references('id')->on('users')->onDelete('set null');

            $table->foreign('department_id')
                ->references('id')->on('departments')->onDelete('set null');
        });
    }
};

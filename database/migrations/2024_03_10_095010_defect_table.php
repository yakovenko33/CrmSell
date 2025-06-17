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
        if (!Schema::hasTable('defects')) {
            Schema::create('defects', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('name', 100);
                $table->string('alias', 50)->index();
                $table->string('created_by', 36)->index();
                $table->string('modified_user_id', 36)->index();
                $table->dateTime('created_at');
                $table->dateTime('updated_at');
            });
        }

        Schema::table('status', function (Blueprint $table) {
            if (Schema::hasColumn('status', 'type')) {
                $table->dropColumn('type');
            }
            $table->string('alias', 50)->index()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('defects');
    }
};

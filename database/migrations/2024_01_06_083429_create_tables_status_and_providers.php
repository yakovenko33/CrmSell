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
        if (!Schema::hasTable('providers')) {
            Schema::create('providers', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('name', 100);
                $table->string('created_by', 36)->index();
                $table->string('modified_user_id', 36)->index();
                $table->dateTime('created_at');
                $table->dateTime('updated_at');
            });
        }

        if (!Schema::hasTable('status')) {
            Schema::create('status', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('name', 100);
                $table->string('alias', 100);
                $table->string('type', 30);
                $table->string('created_by', 36)->index();
                $table->string('modified_user_id', 36)->index();
                $table->dateTime('created_at');
                $table->dateTime('updated_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('providers');
        Schema::dropIfExists('status');
    }
};

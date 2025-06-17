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
        if(!Schema::hasTable('goods')) {
            Schema::create('goods', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->dateTime('created_at')->nullable();
                $table->dateTime('updated_at')->nullable();
                $table->string('created_by', 36)->index();
                $table->string('modified_user_id', 36)->index();
                $table->string('vendor_code', 60);
                $table->string('name', 150);
                $table->boolean('deprecated');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goods');
    }
};

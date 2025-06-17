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
        Schema::create('brands', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->string('created_by', 36)->index();
            $table->string('modified_user_id', 36)->index();
            $table->string('name', 150);
            $table->boolean('deprecated');
        });

        DB::table('orders')->update(['comfy_brand' => '']);
        Schema::table('orders', function (Blueprint $table) {
            $table->string('comfy_brand', 36)->default('')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brands');
    }
};

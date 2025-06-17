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
        if(!Schema::hasTable('orders_audit')) {
            Schema::create('orders_audit', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid("parent_id")->index();
                $table->dateTime('date_created');
                $table->string('created_by', 36)->index();
                $table->string('field_name', 255);
                $table->string('data_type', 255);

                $table->string('before_value_string', 255);
                $table->string('after_value_string', 255);

                $table->longText('before_value_text');
                $table->longText('after_value_text');

                $table->foreign("parent_id")
                    ->references("id")
                    ->on("orders")
                    ->onDelete('cascade');
            });
        }
        //id, parent_id, date_created, created_by, field_name, data_type, before_value_string, before_value_text, after_value_string, after_value_text
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders_audit');
    }
};

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
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('created_by', 36)->index();
            $table->string('modified_user_id', 36)->index();
            $table->dateTime('created_at');
            $table->dateTime('updated_at');

            $table->string('goods_id', 36)->nullable()->index();
            $table->integer('amount_in_order'); //К-ть в замовленні
            $table->integer('amount_in_order_paid');
            $table->decimal('sell_price', 10,2);
            $table->decimal('cost', 10,2)->default(0.00);


            $table->date('date_check')->nullable();
            $table->string('order_number', 50);

            //$table->string('vendor_code', 100);//Артикул ALTER TABLE orders DROP COLUMN vendor_code, DROP COLUMN goods_name;
            //$table->string('goods_name', 150);

            $table->text('manager_comment');
            $table->text('comment');


            $table->string('comfy_code', 50);
            $table->string('comfy_goods_name', 200);
            $table->string('comfy_brand', 50);
            $table->string('comfy_category', 100);
            $table->decimal('comfy_price', 10,2);

            $table->string('status', 50)->index();
            $table->string('defect', 50)->index();


            $table->string('manager', 36)->index();
            $table->string('provider_start', 36)->index();
        });

        if(!Schema::hasTable('shipments')) {
            Schema::create('shipments', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid("order_id")->index();
                $table->date('shipment_date');
                $table->integer('amount');
                $table->dateTime('created_at');
                $table->dateTime('updated_at');
                $table->string('created_by', 36)->index();

                $table->foreign("order_id")
                    ->references("id")
                    ->on("orders")
                    ->onDelete('cascade');
            });
        }

        /*if(!Schema::hasTable('orders_audit')) {
            Schema::create('orders_audit', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid("parent_id")->index();


                $table->foreign("parent_id")
                    ->references("id")
                    ->on("orders")
                    ->onDelete('cascade');
            });
        }*/
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //Schema::dropIfExists('orders_audit');
        Schema::dropIfExists('shipments');
        Schema::dropIfExists('orders');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateColumnsInProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->text('url')->change();
            $table->text('categories')->change();
            $table->text('product_name')->change();
            $table->text('labels')->change();
            $table->text('image_url')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('url', 255)->change();
            $table->string('categories', 255)->change();
            $table->string('product_name', 255)->change();
            $table->string('labels', 255)->change();
            $table->string('image_url', 255)->change();
        });
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->uuid('app_id');
            $table->foreign('app_id')
                  ->references('uuid')
                  ->on('apps')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            $table->string('username')->nullable();
            $table->string('email')->nullable();
            $table->string('password');
            $table->string('description');
            $table->string('photo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accounts');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('apps', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->uuid('app_type_id');
            $table->foreign('app_type_id')
                ->references('uuid')
                ->on('app_types')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->string('name');
            $table->string('logo')->nullable();
            $table->text('description');
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
        Schema::dropIfExists('applications');
    }
}

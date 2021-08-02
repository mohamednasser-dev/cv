<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCvPersonalDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cv_personal_datas', function (Blueprint $table) {
            $table->bigIncrements('id')->nullable();
            $table->string('full_name')->nullable();
            $table->bigInteger('nationality_id')->unsigned()->nullable();
            $table->foreign('nationality_id')->references('id')->on('nationalities')->onDelete('restrict');
            $table->date('date_of_birth')->nullable();
            $table->bigInteger('social_status')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('web_site')->nullable();
            $table->string('address')->nullable();
            $table->string('mail')->nullable();
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->bigInteger('city_id')->unsigned()->nullable();
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('restrict');
            $table->bigInteger('cv_id')->unsigned()->nullable();
            $table->foreign('cv_id')->references('id')->on('cvs')->onDelete('restrict');
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
        Schema::dropIfExists('cv_personal_datas');
    }
}

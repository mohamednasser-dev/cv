<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCvCertificatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cv_certificats', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('certificate_name');
            $table->string('degree_specialization');
            $table->string('collage_name');
            $table->date('start_date');
            $table->date('end_date');
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
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
        Schema::dropIfExists('cv_certificats');
    }
}

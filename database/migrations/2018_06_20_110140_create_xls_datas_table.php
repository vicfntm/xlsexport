<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateXlsDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('xls_datas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('surname')->nullable();
            $table->integer('birth_year')->nullable();
            $table->string('occupation')->nullable();
            $table->integer('annual_salary')->nullable();
            $table->string('filename')->index();
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
        Schema::dropIfExists('xls_datas');
    }
}

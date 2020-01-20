<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBudgetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('budgets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('list');
            $table->string('business');
            $table->string('dis_business')->nullable();
            $table->string('project')->nullable();
            $table->string('activ')->nullable();
            $table->string('respons');
            $table->string('amount')->nullable();
            $table->double('price_per');
            $table->integer('unit');
            $table->integer('unitsap');
            $table->double('total');
            $table->string('explan')->nullable();
            $table->integer('unit_t');
            $table->integer('year');
            $table->string('status')->nullable();
            $table->string('field');
            $table->string('office');
            $table->integer('user_request_id');
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
        Schema::dropIfExists('budgets');
    }
}

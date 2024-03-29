<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStructuresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('structures', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('Company');
            $table->string('Division');
            $table->string('FundsCenterID');
            $table->string('FundID')->nullable();
            $table->string('CostCenterID')->nullable();
            $table->string('CostCenterTitle');
            $table->string('CostCenterName');
            $table->string('NT')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('structures');
    }
}

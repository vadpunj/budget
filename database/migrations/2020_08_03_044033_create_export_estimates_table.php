<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExportEstimatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('export_estimates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('version');
            $table->string('year');
            $table->string('fund_center');
            $table->string('center_money');
            $table->string('account');
            $table->string('budget');
            $table->string('user_id');
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
        Schema::dropIfExists('export_estimates');
    }
}

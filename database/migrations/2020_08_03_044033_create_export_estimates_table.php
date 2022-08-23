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
            $table->string('div_center');
            $table->string('fund_center');
            $table->string('account');
            // $table->string('id1');
            // $table->string('id2');
            $table->string('budget');
            $table->string('status');
            // $table->string('approve2');
            $table->string('approve_all')->nullable();
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

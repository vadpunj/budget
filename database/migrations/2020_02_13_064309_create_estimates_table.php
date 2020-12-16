<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEstimatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('estimates', function (Blueprint $table) {
          $table->bigIncrements('id');
          $table->integer('version');
          $table->integer('stat_year');
          $table->string('account');
          $table->double('budget');
          $table->string('center_money');
          $table->string('fund_center');
          $table->string('cost_title');
          $table->string('status')->nullable();
          $table->string('created_by');
          $table->string('approve_by1')->nullable();
          $table->string('approve_by2')->nullable();
          $table->string('reason')->nullable();
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
        Schema::dropIfExists('estimates');
    }
}

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
          $table->integer('stat_year');
          $table->string('account');
          $table->double('budget');
          $table->string('center_money');
          $table->string('status')->nullable();
          $table->text('explanation')->nullable();
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
      Schema::table('estimates', function (Blueprint $table) {
        $table->dropSoftDeletes();
      });
    }
}

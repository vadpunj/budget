<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
          $table->bigIncrements('id');
          $table->string('name');
          $table->string('emp_id');
          $table->string('pwd')->nullable();
          $table->tinyInteger('type');
          $table->string('field')->nullable();
          $table->string('cost_title')->nullable();
          $table->string('office')->nullable();
          $table->string('part')->nullable();
          $table->string('center_money')->nullable();
          $table->string('fund_center')->nullable();
          $table->string('division_center')->nullable();
          $table->string('tel')->nullable();
          $table->string('nt')->nullable();
          $table->string('user_id',10)->nullable();
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
        Schema::dropIfExists('users');
    }
}

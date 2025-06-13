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
        Schema::create('user_types', function(Blueprint $table){
            $table->smallIncrements('id');
            $table->string('name');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        DB::table('user_types')->insert([
          ['id' => 1, 'name' => 'Admin'],
          ['id' => 2, 'name' => 'Normal User'],
        ]);

        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->smallInteger('type_id')->unsigned();
            $table->foreign('type_id')->references('id')->on('user_types')->onDelete('restrict');

            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->boolean('active')->default(1);
            $table->rememberToken();
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
        Schema::dropIfExists('user_types');
        Schema::dropIfExists('users');
    }
}

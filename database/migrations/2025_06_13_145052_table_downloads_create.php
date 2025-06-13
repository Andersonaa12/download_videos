<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableDownloadsCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('download_status', function(Blueprint $table){
            $table->smallIncrements('id');
            $table->string('name');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        DB::table('download_status')->insert([
          ['id' => 1, 'name' => 'PENDIENTE'],
          ['id' => 2, 'name' => 'PROCESANDO'],
          ['id' => 3, 'name' => 'COMPLETADO'],
          ['id' => 4, 'name' => 'FALLIDO'],
        ]);

        Schema::create('downloads', function (Blueprint $table) {
            $table->id();

            $table->smallInteger('status_id')->unsigned();
            $table->foreign('status_id')->references('id')->on('download_status')->onDelete('restrict');

            $table->string('name');
            $table->string('url');
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->text('error_message')->nullable();
            
            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
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
        Schema::dropIfExists('downloads');
        Schema::dropIfExists('download_status');
    }
}

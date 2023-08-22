<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Students extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('name');
           // $table->string('grade');
            $table->unsignedBigInteger('grade');
            $table->string('email');
            $table->string('phone', 20);
            $table->boolean('is_deleted');
            $table->softDeletes();
            $table->timestamps();
           // $table->foreign('grade')->references('class_name')->on('master_class')->onDelete('cascade');
            $table->foreign('grade')->references('id')->on('master_class')->onDelete('cascade');

        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('students');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContracts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->enum('status', ['new', 'confirmed', 'expired'])->default('new');
            $table->dateTime('scheduled_to');
            $table->boolean('user_confirmation')->default(false);
            $table->boolean('worker_confirmation')->default(false);
            $table->double('value');
            $table->string('note')->nullable();

            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('service_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('service_id')->references('id')->on('services');
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
        Schema::dropIfExists('contracts');
    }
}

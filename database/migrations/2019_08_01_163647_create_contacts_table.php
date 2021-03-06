<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('directory_id');
            $table->uuid('uuid');
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('title')->nullable();
            $table->string('phone')->nullable();
            $table->string('image_url')->nullable();
            $table->string('profile_url')->nullable();
            $table->text('bio')->nullable();
            $table->string('sport')->nullable();
            $table->string('gender')->nullable();
            $table->json('meta');
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('gone_at')->nullable();
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
        Schema::dropIfExists('contacts');
    }
}

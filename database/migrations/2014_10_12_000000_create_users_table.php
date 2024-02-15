<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            /* $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps(); */

            $table->uuid('user_id');
            $table->string('name');
            $table->string('email')->unique();
            $table->enum('user_type', ['SuperAdmin', 'Admin', 'User']);
            $table->string('password');
            $table->boolean('status')->default(1)->comment('0-InActive, 1-Active');
            $table->string('profile_picture')->nullable();
            $table->string('token', 100)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            //$table->unsignedBigInteger('created_by')->nullable();
            //$table->unsignedBigInteger('updated_by')->nullable();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->rememberToken();
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
};

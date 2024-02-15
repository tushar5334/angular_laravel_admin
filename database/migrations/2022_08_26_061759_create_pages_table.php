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
        Schema::create('pages', function (Blueprint $table) {
            $table->uuid('page_id');
            $table->string('title', 255)->nullable();
            $table->string('meta_title', 255)->nullable();
            $table->string('meta_keywords', 255)->nullable();
            $table->text('description')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('name', 500)->unique();
            $table->string('page_slug', 600)->unique();
            $table->tinyInteger('is_static')->default(0)->comment('0-Static, 1-Dynamic');
            $table->boolean('status')->default(1)->comment('0-InActive, 1-Active');
            $table->string('page_header_image', 255)->nullable();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
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
        Schema::dropIfExists('pages');
    }
};

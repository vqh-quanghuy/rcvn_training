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
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('group_role' , 50);
            $table->string('last_login_ip' , 40)->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->tinyInteger('is_active')->comment("0: Không hoạt động , 1 : Hoạt động");
            $table->tinyInteger('is_delete')->comment("0: Bình thường , 1 : Đã xóa");
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
        Schema::dropIfExists('users');
    }
};

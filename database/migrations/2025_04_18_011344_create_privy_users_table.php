<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrivyUsersTable extends Migration
{
    public function up()
    {
        Schema::create('privy_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('privy_reference_number')->nullable();
            $table->string('privy_register_token')->nullable();
            $table->string('privy_id')->nullable();
            $table->string('privy_channel_id')->nullable();
            $table->string('privy_status')->nullable(); // e.g. waiting_verification, verified, rejected
            $table->json('privy_identity')->nullable(); // { nik, nama, tanggal_lahir }
            $table->json('privy_reject_reason')->nullable(); // { code, reason }
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('privy_users');
    }
}
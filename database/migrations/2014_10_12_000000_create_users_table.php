<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nip')->unique();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('department')->nullable(); // Operasi, Keuangan, Pengadaan dan Administrasi Umum, Hukum, SDM, HSSE, Komersial dan Pengembangan Bisnis
            $table->string('position')->nullable(); // Direktur Utama, Direktur Keuangan, Pembendaharaan, Kepala Departemen, Staff
            $table->string('role')->nullable();
            $table->string('employee_status')->nullable();
            $table->string('gender')->nullable();
            $table->string('identity_number')->nullable();
            $table->string('signature')->nullable();
            $table->rememberToken();
            $table->foreignId('current_team_id')->nullable();
            $table->string('profile_photo_path', 2048)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

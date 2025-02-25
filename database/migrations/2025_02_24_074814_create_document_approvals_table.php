<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Jalankan migrasi.
     */
    public function up()
    {
        Schema::create('document_approvals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('document_id');
            $table->string('document_type');
            $table->foreignId('approver_id')->constrained('users')->onDelete('cascade');
            $table->string('role')->index(); 
            $table->string('status')->default('0')->index(); 
            $table->text('comments')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse migrasi.
     */
    public function down()
    {
        Schema::dropIfExists('document_approvals');
    }
};
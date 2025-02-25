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
            $table->morphs('document'); // Bisa digunakan untuk manfee_documents & non_manfee_documents
            $table->foreignId('approver_id')->constrained('users')->onDelete('cascade'); // User yang melakukan approval
            $table->string('role'); // Posisi approval (Maker, Kadiv, dll)
            $table->tinyInteger('status')->default(0); // 0: Pending, 1: Approved, 2: Rejected, 3: Revised
            $table->text('comments')->nullable(); // Catatan dari approver (jika ada)
            $table->timestamp('approved_at')->nullable(); // Waktu disetujui
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
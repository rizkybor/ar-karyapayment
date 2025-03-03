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
        Schema::create('non_manfee_doc_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('non_manfee_documents')->onDelete('cascade');
            $table->foreignId('performed_by')->nullable()->constrained('users')->nullOnDelete();
            
            // 🔹 Menggunakan string, bukan ENUM
            $table->string('role', 50);
            $table->string('previous_status', 50)->nullable();
            $table->string('new_status', 50); 
            $table->string('action', 50);

            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('non_manfee_doc_histories');
    }
};
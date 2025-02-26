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
        Schema::create('manfee_doc_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('manfee_documents')->onDelete('cascade');
            $table->foreignId('performed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('role', 255);
            $table->string('previous_status', 255);
            $table->string('new_status', 255);
            $table->string('action', 255);
            $table->text('notes');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manfee_doc_histories');
    }
};

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
        Schema::create('non_manfee_doc_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('non_manfee_documents')->onDelete('cascade');
            $table->string('file_name', 255);
            $table->string('path', 255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('non_manfee_doc_attachments');
    }
};
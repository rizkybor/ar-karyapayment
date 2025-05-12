<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilePriviesTable extends Migration
{
    public function up()
    {
        Schema::create('file_privies', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('document_id');
            $table->string('category_type'); // 'management_fee' atau 'non_management_fee'
            $table->string('type_document'); // 'invoice', 'kwitansi', atau 'surat'

            $table->string('reference_number')->nullable();
            $table->string('document_token')->nullable();
            $table->string('status')->nullable();

            $table->timestamps();

            // Index biasa (optional)
            $table->index(['document_id', 'category_type']);

            // ✅ Tambahkan constraint unik
            $table->unique(['document_id', 'type_document'], 'unique_doc_type_per_document');
        });
    }

    public function down()
    {
        Schema::dropIfExists('file_privies');
    }
}
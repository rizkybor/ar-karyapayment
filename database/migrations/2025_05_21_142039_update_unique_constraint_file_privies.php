<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUniqueConstraintFilePrivies extends Migration
{
    public function up()
    {
        Schema::table('file_privies', function (Blueprint $table) {
            // Hapus constraint lama
            $table->dropUnique('unique_doc_type_per_document');

            // Tambahkan constraint baru dengan category_type
            $table->unique(['document_id', 'category_type', 'type_document'], 'unique_doc_type_per_document');
        });
    }

    public function down()
    {
        Schema::table('file_privies', function (Blueprint $table) {
            $table->dropUnique('unique_doc_type_per_document');

            // Restore constraint lama jika rollback
            $table->unique(['document_id', 'type_document'], 'unique_doc_type_per_document');
        });
    }
}
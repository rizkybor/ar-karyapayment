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
        Schema::create('manfee_doc_accumalated_costs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('manfee_documents')->onDelete('cascade');
            $table->string('account', 255);
            $table->decimal('total_expense_manfee', 5, 2);
            $table->decimal('nilai_manfee', 15, 2);
            $table->string('dpp', 255);
            $table->decimal('rate_ppn', 5, 2);
            $table->decimal('nilai_ppn', 15, 2);
            $table->decimal('total', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manfee_doc_accumalated_costs');
    }
};

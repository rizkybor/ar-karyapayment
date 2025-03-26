<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('non_manfee_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained('contracts')->onDelete('cascade');
            $table->string('invoice_number', 255);
            $table->string('receipt_number', 255);
            $table->string('letter_number', 255);
            $table->string('period', 255);
            $table->string('letter_subject', 255);
            $table->string('category', 255);
            $table->string('status', 255);
            $table->string('reason_rejected', 255);
            $table->string('path_rejected', 255);
            $table->string('last_reviewers', 255)->nullable();
            $table->boolean('is_active')->nullable()->default(null);
            $table->integer('created_by')->unsigned()->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('non_manfee_documents');
    }
};

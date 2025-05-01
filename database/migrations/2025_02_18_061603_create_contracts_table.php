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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->string('contract_number', 255);
            $table->string('title', 255);
            $table->string('category', 255);
            $table->string('employee_name', 255);
            $table->decimal('value', 15, 2);
            $table->date('contract_date');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('type', 50);
            $table->string('path', 255);
            $table->text('address');
            $table->string('work_unit', 255);
            $table->boolean('status')->nullable()->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};

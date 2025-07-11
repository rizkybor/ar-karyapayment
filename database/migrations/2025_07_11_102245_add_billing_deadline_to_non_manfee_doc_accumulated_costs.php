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
        Schema::table('non_manfee_doc_accumulated_costs', function (Blueprint $table) {
            $table->date('billing_deadline')->nullable()->after('total');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('non_manfee_doc_accumulated_costs', function (Blueprint $table) {
            $table->dropColumn('billing_deadline');
        });
    }
};

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
        Schema::table('orders', function (Blueprint $table) {
            // Add the parent_id column and make it nullable to allow for top-level orders
            $table->unsignedBigInteger('parent_id')->nullable()->after('id');

            // Set up the foreign key constraint that references the id on the same table
            $table->foreign('parent_id')->references('id')->on('orders')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['parent_id']);

            // Remove the parent_id column
            $table->dropColumn('parent_id');
        });
    }
};

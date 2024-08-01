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
        Schema::table('order_items_data', function (Blueprint $table) {
            // Add a nullable foreign key column
            $table->unsignedBigInteger('last_updated_by_user_id')->nullable()->after('updated_at');

            // Add the foreign key constraint
            $table->foreign('last_updated_by_user_id', 'order_item_data_last_updated_by_user_id_foreign')
                ->references('id')->on('users')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items_data', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign('order_data_last_updated_by_user_id_foreign');

            // Drop the column
            $table->dropColumn('last_updated_by_user_id');
        });
    }
};

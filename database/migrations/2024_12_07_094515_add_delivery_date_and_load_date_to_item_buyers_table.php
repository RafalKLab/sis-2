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
        Schema::table('item_buyers', function (Blueprint $table) {
            $table->date('delivery_date')->nullable()->after('address');
            $table->date('load_date')->nullable()->after('dep_country');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('item_buyers', function (Blueprint $table) {
            $table->dropColumn(['delivery_date', 'load_date']);
        });
    }
};

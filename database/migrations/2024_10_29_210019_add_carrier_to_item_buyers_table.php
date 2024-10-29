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
            $table->string('carrier')->after('address')->nullable();
            $table->string('trans_number')->after('carrier')->nullable();
            $table->string('last_country')->after('trans_number')->nullable();
            $table->string('dep_country')->after('last_country')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('item_buyers', function (Blueprint $table) {
            $table->dropColumn('carrier');
            $table->dropColumn('trans_number');
            $table->dropColumn('last_country');
            $table->dropColumn('dep_country');
        });
    }
};

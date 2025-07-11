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
        Schema::table('background_checks', function (Blueprint $table) {
            $table->decimal('cost', 8, 2)->nullable()->default(0)->after('amiqus_client_id')->comment('Total cost of all steps in the background check');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('background_checks', function (Blueprint $table) {
            $table->dropColumn('cost');
        });
    }
};

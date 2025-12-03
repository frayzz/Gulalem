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
        Schema::table('stores', function (Blueprint $table) {
            $table->string('city')->nullable()->after('slug');
            $table->string('region', 100)->nullable()->after('city');
            $table->string('street')->nullable()->after('region');
            $table->string('postal_code', 20)->nullable()->after('street');
            $table->string('phone', 32)->nullable()->after('postal_code');
            $table->string('logo')->nullable()->after('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn([
                'city',
                'region',
                'street',
                'postal_code',
                'phone',
                'logo',
            ]);
        });
    }
};

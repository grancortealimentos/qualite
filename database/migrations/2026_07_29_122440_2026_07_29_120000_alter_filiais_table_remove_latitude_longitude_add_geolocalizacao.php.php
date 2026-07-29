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
        Schema::table('filiais', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude']);
        });

        Schema::table('filiais', function (Blueprint $table) {
            $table->string('geolocalizacao')->nullable()->after('pais');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('filiais', function (Blueprint $table) {
            $table->dropColumn('geolocalizacao');
        });

        Schema::table('filiais', function (Blueprint $table) {
            $table->string('latitude')->nullable()->after('pais');
            $table->string('longitude')->nullable()->after('latitude');
        });
    }
};

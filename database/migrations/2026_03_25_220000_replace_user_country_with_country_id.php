<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('country_id')->nullable()->after('avatar_image');
            $table->foreign('country_id')->references('id')->on('lc_countries')->nullOnDelete();
        });

        DB::table('users')
            ->join('lc_countries', 'users.country', '=', 'lc_countries.official_name')
            ->whereNotNull('users.country')
            ->update([
                'users.country_id' => DB::raw('lc_countries.id'),
            ]);

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('country');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('country')->nullable()->after('avatar_image');
        });

        DB::table('users')
            ->leftJoin('lc_countries', 'users.country_id', '=', 'lc_countries.id')
            ->whereNotNull('users.country_id')
            ->update([
                'users.country' => DB::raw('lc_countries.official_name'),
            ]);

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['country_id']);
            $table->dropColumn('country_id');
        });
    }
};

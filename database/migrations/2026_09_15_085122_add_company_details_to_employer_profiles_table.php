<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employer_profiles', function (Blueprint $table) {
            $table->string('industry')->nullable()->after('company_name');
            $table->string('company_size')->nullable()->after('industry');
            $table->string('location')->nullable()->after('company_size');
            $table->string('website')->nullable()->after('location');
            $table->text('about')->nullable()->after('website');
            $table->string('logo_path')->nullable()->after('about');
        });
    }

    public function down(): void
    {
        Schema::table('employer_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'industry',
                'company_size',
                'location',
                'website',
                'about',
                'logo_path',
            ]);
        });
    }
};

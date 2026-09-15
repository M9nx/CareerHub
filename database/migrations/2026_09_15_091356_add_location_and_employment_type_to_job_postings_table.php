<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_postings', function (Blueprint $table) {
            $table->string('location')->nullable()->after('description');
            $table->string('employment_type')->nullable()->after('location');

            $table->index('location');
            $table->index('employment_type');
        });
    }

    public function down(): void
    {
        Schema::table('job_postings', function (Blueprint $table) {
            $table->dropIndex(['location']);
            $table->dropIndex(['employment_type']);
            $table->dropColumn(['location', 'employment_type']);
        });
    }
};

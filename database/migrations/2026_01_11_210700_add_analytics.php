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
        //*  adding last_login_at to users table to active users last 30 days in analytics dashboard
        schema::table('users', function (Blueprint $table) {
            $table->timestamp('last_login_at')->nullable();
        });
        //* adding viewCount to job_vacancies table to track number of views to calculate conversion rate for analytics dashboard
        schema::table('job_vacancies', function (Blueprint $table) {
            $table->integer('viewCount')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        schema::table('users', function (Blueprint $table) {
            $table->dropColumn('last_login_at');
        });
        schema::table('job_vacancies', function (Blueprint $table) {
            $table->dropColumn('viewCount');
        });
    }
};

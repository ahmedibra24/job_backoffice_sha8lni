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
        Schema::create('job_applications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->float('aiGeneratedScore', 2)->default(0);
            $table->longText('aiGeneratedFeedback')->nullable();
            $table->softDeletes();
            $table->timestamps();

            //* foreign keys
            $table->uuid('job_vacancy_id');
            $table->foreign('job_vacancy_id')->references('id')->on('job_vacancies')->onDelete('restrict');
            $table->uuid('resume_id');
            $table->foreign('resume_id')->references('id')->on('resumes')->onDelete('restrict');
            $table->uuid('applicant_id');
            $table->foreign('applicant_id')->references('id')->on('users')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};

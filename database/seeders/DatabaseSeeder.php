<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\JobApplication;
use App\Models\JobCategory;
use App\Models\JobVacancy;
use App\Models\Resume;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\Fluent\Concerns\Has;
use Faker\factory as Faker;
//! composer require fakerphp/faker  
//! yes   




class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        //* ---------------------------crate admin -------------------------------
        $faker = Faker::create();

        //? Get the first record matching the attributes. If the record is not found, create it.
        User::firstOrCreate([
            'email' => 'admin@admin.com',
        ],[
            'name' => 'Admin',
            'password' => Hash::make('12345678'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        //*-------------------------------------------------------seed data to test-----------------------------------------------------
        //! get data from json files
        $jobData = json_decode(file_get_contents(database_path('data/job_data.json')), true);
        $jobApplicationsData = json_decode(file_get_contents(database_path('data/job_applications.json')), true);

        //* ---------------------------- create job category -----------------

        foreach($jobData['jobCategories'] as $Category){
            JobCategory::firstOrCreate([
                'name'=>$Category,
            ]);
        }

        //* ----------------------------- create companies -----------------------

        //! to create company you should have to create owner user first 
        foreach($jobData['companies']as $company){

            //! create owner user
            $owner=User::firstOrCreate([
                'email' => $faker->unique()->safeEmail(),
            ],[
                'name' => $faker->name(),
                'password' => Hash::make('12345678'),
                'role' => 'recruiter',
                'email_verified_at' => now(),
            ]);

            //! create company
            Company::firstOrCreate([
                'name'=>$company['name']
            ],[
                'email'=>$faker->unique()->safeEmail(),
                'address'=>$company['address'],
                'industry'=>$company['industry'],
                'website'=>$company['website'],
                'owner_id'=>$owner->id,
            ]);
        }
       
        //* ----------------------------- create job vacancies -----------------------

        foreach ($jobData['jobVacancies'] as $jobVacancy) {
             //! get created company
             $companyData =  Company::where('name',$jobVacancy['company'])->firstOrFail();

             //! get created category
             $CategoryData = JobCategory::where('name',$jobVacancy['category'])->firstOrFail();

            //! create job vacancy
            JobVacancy::firstOrCreate([
                'company_id'=>$companyData->id,
                'title'=>$jobVacancy['title']
            ],[
                'description'=>$jobVacancy['description'],
                'location'=>$jobVacancy['location'],
                'salary'=>$jobVacancy['salary'],
                'type'=>$jobVacancy['type'],
                'category_id'=>$CategoryData->id,
            ]);
        }
        //* ----------------------------- create job applications -----------------------
        foreach ($jobApplicationsData['jobApplications'] as $jobApplication) {
            //! get random job vacancy
            $jobVacancyData = JobVacancy::inRandomOrder()->first();

            //! create applicant user
            $applicantData = User::firstOrCreate([
                'email' => $faker->unique()->safeEmail(),
            ],[
                'name' => $faker->name(),
                'password' => Hash::make('12345678'),
                'role' => 'applicant',
                'email_verified_at' => now(),
            ]);

            //! create resume
            $resumeData = Resume::firstOrCreate([
                'applicant_id' => $applicantData->id,
                ],[
                'fileName' => $jobApplication['resume']['filename'],
                'fileUri' => $jobApplication['resume']['fileUri'],
                'contactDetails' => $jobApplication['resume']['contactDetails'],
                'summary' => $jobApplication['resume']['summary'],
                'skills' => $jobApplication['resume']['skills'],
                'experience' => $jobApplication['resume']['experience'],
                'education' => $jobApplication['resume']['education'],
            ]);
            
            //! create job applications
            JobApplication::Create([
                'job_vacancy_id'=> $jobVacancyData->id,
                'status'=>$jobApplication['status'],
                'aiGeneratedScore'=>$jobApplication['aiGeneratedScore'],
                'aiGeneratedFeedback'=>$jobApplication['aiGeneratedFeedback'],
                'resume_id'=>$resumeData->id,
                'applicant_id'=>$applicantData->id,
            ]);
        }
        
        //* ------------------------------------------------------ end seed data to test---------------------------------------------

    }
}
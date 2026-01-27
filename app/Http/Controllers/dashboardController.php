<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\JobApplication;
use App\Models\JobVacancy;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class dashboardController extends Controller
{
    //* ==================================================== index ============================================== */

    public function index()
    {
        if(Auth::user()->role == 'admin') {
            $analytics = $this->adminDashboard();
        }elseif(Auth::user()->role == 'recruiter'){          
            $analytics = $this->recruiterDashboard();
        }

        return view('dashboard.index', compact('analytics'));
    }

    //* ======================================= Admin dashboard analytics===============================================*/

    private function adminDashboard()
    {

        //! -------------------------- most applied job vacancies------------------------*/

        //! original query (use subselect to count applications per job vacancy not joined)
        //? SELECT 
        //? job_vacancies.*,
        //? (
        //?     SELECT COUNT(*)
        //?     FROM job_applications
        //?     WHERE job_applications.job_vacancy_id = job_vacancies.id
        //? ) AS total_applications
        //?     FROM job_vacancies ORDER BY total_applications DESC LIMIT 5;

       $mostAppliedJob = JobVacancy::withCount('jobApplications as total_applications')
       ->whereNull('deleted_at')// only not deleted jobs (you can not write it because it's default behavior in laravel with soft deletes)
       ->orderBy('total_applications','desc')
       ->limit(5)
       ->get();



        //! -------------------------conversion rates (# of applications / # of views)---------------------*/

        //! better to calculate it in query
        $conversionRates=JobVacancy::withCount('jobApplications as total_applications')
        ->having('total_applications','>',0)
        ->orderBy('total_applications','desc')
        ->limit(5)
        ->get()
        ->map(function($job){
            $views=$job->viewCount;
            $applications=$job->total_applications;
            $conversionRate=$views>0 ? round(($applications / $views) * 100,2) : 0;
            $job->conversionRate=$conversionRate;
            return $job;
        });

        //! --------------------------------------------overview cards-----------------------------

        //! last 30 days active users (job seekers only)
        $activeUsers=User::where(   'last_login_at','>=',now()->subDays(30))
        ->where('role', '=','applicant')->count();

        //! total companies(not deleted)
        $totalCompanies=Company::whereNull('deleted_at')->count();

        //! total job vacancies(not deleted)
        $totalJobVacancies=JobVacancy::whereNull('deleted_at')->count();

        //! --------------------------- compile analytics data-----------------------------*/

        $analytics = [
            'activeUsers' => $activeUsers,
            'totalCompanies' => $totalCompanies,
            'totalJobVacancies' => $totalJobVacancies,
            'mostAppliedJob' => $mostAppliedJob,
            'conversionRates' => $conversionRates
        ];

        return $analytics;


    }

    //* ========================================== Company dashboard analytics ================================================*/

    private function recruiterDashboard()
    {
        $company=Auth::user()->companies;
        //! --------------------------- overview cards-----------------------------

        //! last 30 days active users (job seekers only applying to this company's jobs)
        $activeUsers=User::where(   'last_login_at','>=',now()->subDays(30))
        ->where('role', '=','applicant')
        ->whereHas('jobApplications', function($query) use ($company){
            $query->whereHas('jobVacancy', function($q) use ($company){
                $q->where('company_id', $company->id)
                ->whereNull('deleted_at');
            });
        })->count();

        //! total jobs vacancies for this company (not deleted)
        $totalJobs=$company->jobVacancies->whereNull('deleted_at')->count();

        //! total job applications for this company's jobs (not deleted)
        $totalJobVacancies=JobApplication::whereHas('jobVacancy', function($query) use ($company){
            $query->where('company_id', $company->id)
            ->whereNull('deleted_at');
        })->count();

        //!------------------- most applied job vacancies for this company (not deleted)-------------------------*/

        $mostAppliedJob=JobVacancy::withCount('jobApplications as total_applications')
        ->where('company_id', $company->id)
        ->whereNull('deleted_at')
        ->orderBy('total_applications','desc')
        ->limit(5)
        ->get();

        //! -----------------conversion rates for this company's jobs (# of applications / # of views)---------------*/
        $conversionRates=JobVacancy::withCount('jobApplications as total_applications')
        ->where('company_id', $company->id)
        ->having('total_applications','>',0)
        ->orderBy('total_applications','desc')
        ->limit(5)
        ->get()
        ->map(function($job){
            $views=$job->viewCount;
            $applications=$job->total_applications;
            $conversionRate=$views>0 ? round(($applications / $views) * 100,2) : 0;
            $job->conversionRate=$conversionRate;
            return $job;
        });

        //! --------------------------- compile analytics data-----------------------------*/

        $analytics = [
            'activeUsers' => $activeUsers,
            'totalCompanies' => $totalJobs,
            'totalJobVacancies' => $totalJobVacancies,
            'mostAppliedJob' => $mostAppliedJob,
            'conversionRates' => $conversionRates

        ];

        return $analytics;
    }
}

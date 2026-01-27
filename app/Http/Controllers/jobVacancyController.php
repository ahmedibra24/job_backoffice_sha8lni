<?php

namespace App\Http\Controllers;

use App\Http\Requests\jobCategoryRequest;
use App\Models\Company;
use App\Models\JobCategory;
use App\Models\JobVacancy;
use Illuminate\Contracts\Queue\Job;
use Illuminate\Http\Request;
use App\Http\Requests\jobVacancyRequest;
use Illuminate\Support\Facades\Auth;

class jobVacancyController extends Controller
{
    //* =======================================index======================================== */
    public function index(Request $request)
    {
        //! check if archived query param exists to show archived job vacancies
        //! also if the user is recruiter show only his company vacancies
        if ($request->has('archived')) {
            $query = JobVacancy::onlyTrashed()->latest();
            if(Auth::user()->role=='recruiter'){
                $query->where('company_id',Auth::user()->companies->id);
            }
        } else {
            $query = JobVacancy::latest();
            if(Auth::user()->role=='recruiter'){
                $query->where('company_id',Auth::user()->companies->id);
            }
        }

        $JobVacancies=$query->paginate(10)->onEachSide(1);

        return view('job-vacancy.index',compact('JobVacancies'));

    }
    //* =================================== types of job vacancies ============================ */
    public $types = ['Full-time', 'Contract', 'Remote', 'Hybrid'] ;

    //* ======================================= create ======================================== */
    public function create()
    {
        $types = $this->types;
        $companies =Company::all() ;
        $categories =JobCategory::all() ;
        return view('job-vacancy.create',compact('companies','categories','types')); 
    }
    //* ======================================= store ======================================== */
    public function store(jobVacancyRequest $request)
    {
        //! validate request
        $validated =$request->validated() ;

        //! get company id based on role
        $userId= Auth::user()->id;
        $company=Company::where('owner_id',$userId)->first();       
        $companyId = Auth::user()->role == 'admin' ? $validated['company_id'] : $company->id;

        //! create job vacancy
        JobVacancy::create([
            'title'=>$validated['title'],
            'description'=>$validated['description'],
            'location'=>$validated['location'],
            'salary'=>$validated['salary'],
            'type'=>$validated['type'],
            'company_id'=>$companyId,
            'category_id'=>$validated['category_id'],
            'viewCount'=>0,
        ]);
         // redirect
        return redirect()->route('job-vacancy.index')->with('success','job vacancy created successfully');
    }
    //* ======================================= show ======================================== */
    public function show(string $id)
    {
        $jobVacancy = JobVacancy::findOrFail($id) ;
        return view('job-vacancy.show',compact('jobVacancy'));
    }
    //* ======================================= edit ======================================== */      
    public function edit(string $id)
    {
        $types= $this->types ;
        $jobVacancy = JobVacancy::findOrFail($id) ;
        $companies =Company::all() ;
        $categories =JobCategory::all() ;

        return view('job-vacancy.edit',compact('jobVacancy','types','companies','categories'));
    }
    //* ======================================= update ======================================== */
    public function update(jobVacancyRequest $request, string $id)
    {
        $validated = $request->validated();
        $jobVacancy = JobVacancy::findOrFail($id);
        $jobVacancy->update($validated);

        //! redirect based on toShow query param in url to either show the job vacancy or go back to index
        if($request->query('toShow')=='true'){
            return redirect()->route('job-vacancy.show',$id)->with('success','job vacancy updated successfully');
        }
        return redirect()->route('job-vacancy.index')->with('success', 'job vacancy updated successfully');
        
    }
    //* ======================================= destroy ======================================== */
    public function destroy(Request $request ,string $id)
    {
        //! check if archived query param exists to delete permanently
        //! else soft delete
        if($request->has('archived')){
            $jobVacancy = JobVacancy::onlyTrashed()->findOrFail($id);
            $jobVacancy->forceDelete();
            return redirect()->route('job-vacancy.index', ['archived' => true])->with('success',  $jobVacancy->title.' job vacancy deleted permanently successfully');
        }
        else{
            $jobVacancy = JobVacancy::findOrFail($id);
            $jobVacancy->delete();
            return redirect()->route('job-vacancy.index')->with('success', $jobVacancy->title.' job vacancy archived successfully');
        }
    
    }
    //* ======================================= restore ======================================== */
    public function restore(string $id){
        //! restore soft deleted job vacancy
        $jobVacancy = JobVacancy::onlyTrashed()->findOrFail($id);
        $jobVacancy->restore();
        return redirect()->route('job-vacancy.index',['archived' => true])->with('success',  $jobVacancy->title.' job vacancy restored successfully');
    }
}

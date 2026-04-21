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
        $query = JobVacancy::latest();

        //! check if archived query param exists to show archived job vacancies
        if ($request->has('archived')) {
            $query = JobVacancy::onlyTrashed()->latest();
        }

        //! if the user is recruiter show only his company vacancies
        if(Auth::user()->role=='recruiter'){
            $query->where('company_id',Auth::user()->companies->id);
        }

        //! search by title, company name, location , type and salary with filter by type
        if ($request->has('search') && $request->has('filter')) {
            $search = $request->get('search');
            $filter = $request->get('filter');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhereHas('company', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhere('type', 'like', "%{$search}%")
                    ->orWhere('salary', 'like', "%{$search}%");
            });
            $query->where('type', 'like', "%{$filter}%");
            }

        //! search by title, company name, location , type and salary
        if ($request->has('search') && $request->filter == null) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhereHas('company', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhere('type', 'like', "%{$search}%")
                    ->orWhere('salary', 'like', "%{$search}%");
            });
        }

        //! filter by type
        if ($request->has('filter') && $request->search == null) {
            $filter = $request->get('filter');
            $query->where('type', 'like', "%{$filter}%");
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

    //* --------------------------------------- bulk destroy--------------------------------- */
    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return redirect()->back()->with('error', 'No job vacancies selected for deletion.');
        }
        //! check if archived query param exists to delete permanently
        //! else soft delete
        if($request->has('archived')){
            $jobVacancies = JobVacancy::onlyTrashed()->whereIn('id', $ids)->get();
            foreach ($jobVacancies as $jobVacancy) {
                $jobVacancy->forceDelete();
            }
            return redirect()->back()->with('success', 'Selected job vacancies deleted successfully.');
        } else {
            JobVacancy::whereIn('id', $ids)->delete();
            return redirect()->route('job-vacancy.index')->with('success', 'Selected job vacancies archived successfully');
        }
        
    }  
    
    //* ---------------------------------------- bulk restore--------------------------------- */
    public function bulkRestore(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return redirect()->back()->with('error', 'No job vacancies selected for restoration.');
        }
        $jobVacancies = JobVacancy::onlyTrashed()->whereIn('id', $ids)->get();
        foreach ($jobVacancies as $jobVacancy) {
            $jobVacancy->restore();
        }
        return redirect()->route('job-vacancy.index', ['archived' => true])->with('success', 'Selected job vacancies restored successfully.');
    }

}

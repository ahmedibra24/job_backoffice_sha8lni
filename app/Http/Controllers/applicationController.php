<?php

namespace App\Http\Controllers;

use App\Http\Requests\applicationRequest;
use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class applicationController extends Controller
{
    //* ---------------------------------------- index--------------------------------- */
    public function index(Request $request)
    {    
        $query = JobApplication::latest();

        //! check if archived query param exists to show archived applications
        if ($request->has('archived')) {
            $query = JobApplication::onlyTrashed()->latest();
        }
        
        //! also if the user is recruiter show only applications related to his company vacancies
        //? whereHas -> This would return only job vacancies that are related to active user.
        if(Auth::user()->role=='recruiter'){
            $query->whereHas('jobVacancy',function($q){
                $q->where('company_id',Auth::user()->companies->id);
            });
        }

        //!search & filter by status
        if ($request->has('search') && $request->has('filter')) {
            $search = $request->get('search');
            $filter = $request->get('filter');
            $query->where(function ($q) use ($search, $filter) {
                $q->whereHas('applicant', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhereHas('jobVacancy', function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhereHas('company', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                        });
                    })
                    ->where('status', 'like', "%{$filter}%");
            });
        }


        //! search by applicant name, company name, job vacancy title and status
        if ($request->has('search')&& $request->filter == null) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
            $q->whereHas('applicant', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })
                ->orWhere('status', 'like', "%{$search}%")
                ->orWhereHas('jobVacancy', function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhereHas('company', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                    });
                });
            });
        }

        //! filter by status
        if ($request->has('filter')&& $request->search == null) {
            $filter = $request->get('filter');
            $query->where('status', 'like', "%{$filter}%");
        }

        
        $applications=$query->paginate(10)->onEachSide(1);

        return view('application.index',compact('applications'));
    }
    //* ---------------------------------------- show--------------------------------- */
    public function show(string $id)
    {
        $application = JobApplication::findOrFail($id) ;
        return view('application.show',compact('application'));
    
    }
    //* ---------------------------------------- edit--------------------------------- */      
    public function edit(string $id)
    {
        $status = ['pending', 'accepted', 'rejected'];
        $application=JobApplication::findOrFail($id);
        return view('application.edit',compact('application','status'));

    }
    //* ---------------------------------------- update--------------------------------- */
    public function update(applicationRequest $request, string $id)
    {
        //! validate the request and update only status
        $validated =$request->validated() ;
        $application=JobApplication::findOrFail($id);
        $application->update([
            'status'=>$validated['status'],
        ]);

        //! redirect based on toShow query param in url to either show the application or go back to index

        if($request->query('toShow')=='true'){
            return redirect()->route('application.show',$id)->with('success','application updated successfully');
        }
        return  redirect()->route('application.index')->with('success','application updated successfully');
    }
    //* ---------------------------------------- destroy--------------------------------- */
    public function destroy(Request $request, string $id)
    {
        //! check if archived query param exists to delete permanently
        //! else soft delete
        if($request->has('archived')){
            $application = JobApplication::onlyTrashed()->findOrFail($id);
            $application->forceDelete();
            return redirect()->route('application.index', ['archived' => true])->with('success',  ' application deleted permanently successfully');
        }
        else{
            $application = JobApplication::findOrFail($id);
            $application->delete();
            return redirect()->route('application.index')->with('success', ' application archived successfully');
        }

    }
    //* ---------------------------------------- restore--------------------------------- */
    public function restore(string $id)
    {
        //! restore soft deleted application
        $application = JobApplication::onlyTrashed()->findOrFail($id);
        $application->restore();
        return redirect()->route('application.index', ['archived' => true])->with('success',  ' application restored successfully');
    }

    //* --------------------------------------- bulk destroy--------------------------------- */
    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return redirect()->back()->with('error', 'No applications selected for deletion.');
        }
        //! check if archived query param exists to delete permanently
        //! else soft delete
        if($request->has('archived')){
            $applications = JobApplication::onlyTrashed()->whereIn('id', $ids)->get();
            foreach ($applications as $application) {
                $application->forceDelete();
            }
            return redirect()->back()->with('success', 'Selected applications deleted successfully.');
        } else {
            JobApplication::whereIn('id', $ids)->delete();
            return redirect()->route('application.index')->with('success', ' Application archived successfully');
        }
        
    }  
    
    //* ---------------------------------------- bulk restore--------------------------------- */
    public function bulkRestore(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return redirect()->back()->with('error', 'No applications selected for restoration.');
        }
        $applications = JobApplication::onlyTrashed()->whereIn('id', $ids)->get();
        foreach ($applications as $application) {
            $application->restore();
        }
        return redirect()->route('application.index', ['archived' => true])->with('success', 'Selected applications restored successfully.');
    }


}

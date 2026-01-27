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
        //! check if archived query param exists to show archived applications
        //! also if the user is recruiter show only applications related to his company vacancies

        //? whereHas -> This would return only job vacancies that are related to active user.

        if ($request->has('archived')) {
            $query = JobApplication::onlyTrashed()->latest();
            if(Auth::user()->role=='recruiter'){
                $query->whereHas('jobVacancy',function($q){
                    $q->where('company_id',Auth::user()->companies->id);
                });
            }
        } else {
            $query = JobApplication::latest();
            if(Auth::user()->role=='recruiter'){
                $query->whereHas('jobVacancy',function($q){
                    $q->where('company_id',Auth::user()->companies->id);
                });
            }

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

}

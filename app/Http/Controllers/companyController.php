<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Http\Requests\companyRequest;
use App\Http\Requests\companyUpdateRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Pest\Matchers\Any;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use PHPUnit\Util\PHP\Job;
use App\Models\JobVacancy;

class companyController extends Controller
{
    //* ---------------------------------------- industries list--------------------------------- */
    public $industries = [
    'Technology',
    'Finance',
    'Healthcare',
    'Education',
    'Retail',
    'Manufacturing',
    'Hospitality',
    'Transportation',
    'Construction',
    'Real Estate',
    'Energy',
    'Telecommunications',
    'Entertainment',
    'Agriculture',
    'Government',
    ]; 
    //* ---------------------------------------- index --------------------------------- */ 
    public function index(Request $request)
    {
        $query = Company::latest();
        
        //! check if archived query param exists to show archived companies and search within them
        if ($request->has('archived')) {
            $query = Company::onlyTrashed()->latest();
        }
        //! search by company name or industry or adress or website
        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('industry', 'like', '%' . $searchTerm . '%')
                    ->orWhere('address', 'like', '%' . $searchTerm . '%')
                    ->orWhere('website', 'like', '%' . $searchTerm . '%');
            });
        } 

        $Companies=$query->paginate(10)->onEachSide(1);
        return view('company.index',compact('Companies'));

    }
    //* ---------------------------------------- create--------------------------------- */

    public function create()
    {
        $industries = $this->industries;
        return view('company.create', compact('industries'));
    }
    //* ---------------------------------------- store--------------------------------- */
    public function store(companyRequest $request)
    {
        DB::beginTransaction();
        try{
            //! validate and extract logo file
            $file = $request->file('logo'); // uploaded file
            $extension = $file->getClientOriginalExtension();
            $originalFileName = $file->getClientOriginalName(); // stored in DB
            $fileName = 'logo_' . time() . '.' . $extension; // unique storage name

            //! store in cloud (public visibility)
            // Returns a relative path like "logos/logo_123456.png"
            $path=$file->storeAs('logos',$fileName,'cloud',[
               'disk'=>'cloud',
               'visibility' => 'public'
            ]);
            if (!$path) {
            throw new \Exception('Failed to upload logo to storage.');
            }

            // dd([$path,$originalFileName,config('filesystems.disks.cloud.url').'/'.$path]);

            config('filesystems.disks.cloud.url').'/'.$path ;//? final url to display the file from cloud
            //! create owner user
            $owner = User::create([
                'name' => $request->input('owner_name'),
                'email' => $request->input('owner_email'),
                'password' => Hash::make($request->input('owner_password')),
                'role' => 'recruiter',
            ]);

            //! check if owner created successfully because it is required for company
            if (!$owner) {
                // delete image from cloud if owner creation failed
                if (isset($path)) {
                    Storage::disk('cloud')->delete($path);
                }
                return redirect()->back()->with('error', 'Failed to create owner user');
            }
            // dd(Company::getFillable());
            //! create company 

            $company = new Company();
            $company->name = $request->input('name');
            $company->email = $request->input('email');
            $company->address = $request->input('address');
            $company->industry = $request->input('industry');
            $company->website = $request->input('website');
            $company->logoName = $originalFileName;
            $company->logoUri = $path; // should not contain the domain
            $company->owner_id = $owner->id;
            $company->save();
            DB::commit();
            return  redirect()->route('company.index')->with('success', 'Company created successfully');
        }catch (\Exception $e) {
            DB::rollBack();
            // delete image from cloud if exists to avoid orphan files
            if (isset($path)) {
                Storage::disk('cloud')->delete($path);
            }

            // TEMPORARY - show full error with trace
            return back()->withInput()->with('error', 'Something went wrong - ' . $e->getMessage() . ' | File: ' . $e->getFile() . ' | Line: ' . $e->getLine());
        }
    }
    //* ---------------------------------------- get company--------------------------------- */

    //! helper function to get company by id or by auth user owner_id
    private function getCompany($id){
        if($id){
            $company=Company::findOrFail($id);
        }else{
            $userId= Auth::user()->id;
            $company=Company::where('owner_id',$userId)->firstOrFail();
        }
        return $company;

    }
    //* ---------------------------------------- show--------------------------------- */
    public function show( $id = null)
    {
        //? $id = null -> to handle my-company.show route for recruiter to show their company
        //? if $id is null get company by auth user owner_id
        $company=$this->getCompany($id);
        return view('company.show',compact('company'));
    }
    //* ---------------------------------------- edit--------------------------------- */
    public function edit( $id = null)
    {
        $company=$this->getCompany($id);
        $industries = $this->industries;
        return view('company.edit',compact('company', 'industries'));
    }
    //* ---------------------------------------- update--------------------------------- */
    public function update(companyUpdateRequest $request,  $id = null)
    {        
        $validated = $request->validated() ;
        $company = $this->getCompany($id);
        
        //! company update
        $company->update([
            'name' => $validated['name'],
            'address'=>$validated['address'],
            'industry'=>$validated['industry'],
            'website'=>$validated['website'],
            'email'=>$validated['email'],
        ]);

        //! handle logo update if new logo provided
        if($request->hasFile('logo')){
            //! delete old logo from cloud if exists to avoid orphan files
            if ($company->logoUri) {
                Storage::disk('cloud')->delete($company->logoUri);
            }
            //! validate and extract new logo file
            $file = $request->file('logo'); // uploaded file
            $extension = $file->getClientOriginalExtension();
            $originalFileName = $file->getClientOriginalName(); // stored in DB
            $fileName = 'logo_' . time() . '.' . $extension; // unique storage name

            //! store in cloud (public visibility)
            // Returns a relative path like "logos/logo_123456.png"
            $path=$file->storeAs('logos',$fileName,'cloud',[
               'disk'=>'cloud',
               'visibility' => 'public'
            ]);
            if (!$path) {
                throw new \Exception('Failed to upload logo to storage.');
            }
             //! update company with new logo data
             $company->logoName = $originalFileName;
             $company->logoUri = $path; // should not contain the domain
             $company->save();
        }

        //! owner update 
        $ownerData=[];
        $ownerData['name']= $validated['owner_name'];

        //! update password only if provided
        if($validated['owner_password']){
          $ownerData['password']= Hash::make($validated['owner_password']);  
        }
        $company->owner->update($ownerData);

        //! redirect based on role
        //! redirect based on toShow query param in url to either show the application or go back to index
        if(Auth::user()->role==='admin'){
            if($request->query('toShow')=='true'){
                return redirect()->route('company.show',$id)->with('success','Company updated successfully');
            }
            return redirect()->route('company.index')->with('success', 'Company updated successfully');
        }elseif (Auth::user()->role==='recruiter') {
            return redirect()->route('my-company.show')->with('success', 'Company updated successfully');
        }
    
       
    }
    //* ---------------------------------------- destroy--------------------------------- */
    public function destroy(Request $request, string $id)
    {
        //! check if archived query param exists to delete permanently
        //! else soft delete
        if($request->has('archived')){
            $company = Company::onlyTrashed()->findOrFail($id);
            JobVacancy::where('company_id', $company->id)->withTrashed()->get()->each->forceDelete();
            $company->forceDelete();
            return redirect()->route('company.index', ['archived' => true])->with('success',  $company->name.' Company deleted permanently successfully');
        }
        else{
            $company = Company::findOrFail($id);
            JobVacancy::where('company_id', $company->id)->delete();
            $company->delete();
            return redirect()->route('company.index')->with('success', $company->name.' Company archived successfully');
        }
    }

    //* --------------------------------------- bulk destroy--------------------------------- */
    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return redirect()->back()->with('error', 'No companies selected for deletion.');
        }
        //! check if archived query param exists to delete permanently
        //! else soft delete
        if($request->has('archived')){
            $companies = Company::onlyTrashed()->whereIn('id', $ids)->get();
            foreach ($companies as $company) {
                $company->forceDelete();
            }
            return redirect()->back()->with('success', 'Selected companies deleted successfully.');
        } else {
            $companies = Company::whereIn('id', $ids)->get();
            foreach ($companies as $company) {
                JobVacancy::where('company_id', $company->id)->get()->each->delete(); //? soft delete related job vacancies to avoid orphan records
                $company->delete();
            }
            return redirect()->route('company.index')->with('success', ' Company archived successfully');
        }
        
    }   
    //* ---------------------------------------- restore--------------------------------- */
    public function restore(string $id)
    {
        //! restore soft deleted company
        $company = Company::onlyTrashed()->findOrFail($id);
        $company->restore();
        JobVacancy::where('company_id', $company->id)->onlyTrashed()->get()->each->restore();
        return redirect()->route('company.index', ['archived' => true])->with('success',  $company->name.' company restored successfully');
    }
    //* ---------------------------------------- bulk restore--------------------------------- */
    public function bulkRestore(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return redirect()->back()->with('error', 'No companies selected for restoration.');
        }
        $companies = Company::onlyTrashed()->whereIn('id', $ids)->get();
        foreach ($companies as $company) {
            $company->restore();
            JobVacancy::where('company_id', $company->id)->withTrashed()->get()->each->restore(); //? restore related job vacancies
        }
        return redirect()->route('company.index', ['archived' => true])->with('success', 'Selected companies restored successfully.');
    }
}

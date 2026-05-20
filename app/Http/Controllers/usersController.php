<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;

class usersController extends Controller
{
    //? creating and updating user is from companyController as user is created along with company owner
    //? only displaying, archiving, restoring users is handled here
    
    //* ======================================= index ======================================== */
    public function index(Request $request)
    {
        $query = User::latest();

        //! check if archived query param exists to show archived users
        if ($request->has('archived')) {
            $query = User::onlyTrashed()->latest();
        }
        
        //! search by name, email and role with filter by role
        if ($request->has('search') && $request->has('filter')) {
            $search = $request->get('search');
            $filter = $request->get('filter');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('role', 'like', "%{$search}%");
            });
            $query->where('role', 'like', "%{$filter}%");
         }

        //! serach by name, email and role
        if ($request->has('search') && $request->filter == null) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('role', 'like', "%{$search}%");
            });
        }

        //! filter by role
        if ($request->has('filter') && $request->search == null) {
            $filter = $request->get('filter');
            $query->where('role', 'like', "%{$filter}%");
        }
        
        $users=$query->paginate(10)->onEachSide(1);
        return view('user.index',compact('users'));
    }

    //* ======================================= destroy ======================================== */
    public function destroy(Request $request, string $id)
    {
        //! check if archived query param exists to delete permanently
        //! else soft delete
        if($request->has('archived')){
            $user = User::onlyTrashed()->findOrFail($id);
            Company::withTrashed()->where('owner_id', $user->id)->get()->each->forceDelete();
            $user->forceDelete();
            return redirect()->route('user.index', ['archived' => true])->with('success',  ' user deleted permanently successfully');
        }
        else{
            $user = User::findOrFail($id);
            Company::where('owner_id', $user->id)->get()->each->delete();
            $user->delete();
            return redirect()->route('user.index')->with('success', $user->name.'  archived successfully');
        }

    }
    //* ======================================= restore ======================================== */
        public function restore(string $id)
    {
        //! restore soft deleted user
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();
        Company::onlyTrashed()->where('owner_id', $user->id)->get()->each->restore();
        return redirect()->route('user.index', ['archived' => true])->with('success',  $user->name.' restored successfully');
    }

    //* --------------------------------------- bulk destroy--------------------------------- */
    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return redirect()->back()->with('error', 'No users selected for deletion.');
        }
        //! check if archived query param exists to delete permanently
        //! else soft delete
        if($request->has('archived')){
            $users = User::onlyTrashed()->whereIn('id', $ids)->get();
            foreach ($users as $user) {
                Company::withTrashed()->where('owner_id', $user->id)->get()->each->forceDelete();
                $user->forceDelete();
            }
            return redirect()->back()->with('success', 'Selected users deleted successfully.');
        } else {
            $users = User::whereIn('id', $ids)->get();
            foreach ($users as $user) {
                Company::where('owner_id', $user->id)->get()->each->delete();
                $user->delete();
            }
            return redirect()->route('user.index')->with('success', 'Users archived successfully');
        }
        
    }  
    
    //* ---------------------------------------- bulk restore--------------------------------- */
    public function bulkRestore(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return redirect()->back()->with('error', 'No users selected for restoration.');
        }
        $users = User::onlyTrashed()->whereIn('id', $ids)->get();
        foreach ($users as $user) {
            $user->restore();
            Company::onlyTrashed()->where('owner_id', $user->id)->get()->each->restore();
        }
        return redirect()->route('user.index', ['archived' => true])->with('success', 'Selected users restored successfully.');
    }

}

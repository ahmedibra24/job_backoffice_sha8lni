<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class usersController extends Controller
{
    //? creating and updating user is from companyController as user is created along with company owner
    //? only displaying, archiving, restoring users is handled here
    
    //* ======================================= index ======================================== */
    public function index(Request $request)
    {
        //! check if archived query param exists to show archived users
        if ($request->has('archived')) {
            $query = User::onlyTrashed()->latest();
        } else {
            $query = User::latest();
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
            $user->forceDelete();
            return redirect()->route('user.index', ['archived' => true])->with('success',  ' user deleted permanently successfully');
        }
        else{
            $user = User::findOrFail($id);
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
        return redirect()->route('user.index', ['archived' => true])->with('success',  $user->name.' restored successfully');
    }
}

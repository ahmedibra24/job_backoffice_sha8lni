<?php

namespace App\Http\Controllers;

use App\Models\JobCategory;
use Illuminate\Http\Request;
use App\Http\Requests\jobCategoryRequest;
class categoryController extends Controller
{
    //* ---------------------------------------- index--------------------------------- */
    public function index(Request $request)
    {   
        //! check if archived query param exists to show archived categories
        if ($request->has('archived')) {
            $query = JobCategory::onlyTrashed()->latest();
        } else {
            $query = JobCategory::latest();
        }
        $categories=$query->paginate(10)->onEachSide(1);
        //? onEachSide(1) -> to appear 1 page on left and right from active page

        return view('category.index',compact('categories'));

    }
    //* ---------------------------------------- create--------------------------------- */
    public function create()
    {
        return view('category.create');
    }

    
    //* -------------------------------------- Store -------------------------------------*/
    public function store(jobCategoryRequest $request)
    {
        $category = JobCategory::create($request->validated());
        return redirect()->route('category.index')->with('success', 'Category created successfully');
    }

    //* -------------------------------------- edit -------------------------------------*/
    public function edit(string $id)
    {
        $category=JobCategory::findOrFail($id);
        return view('category.edit',compact('category'));
    }

    //* -------------------------------------- update -------------------------------------*/
    public function update(jobCategoryRequest $request, string $id)
    {
        $category = JobCategory::findOrFail($id);
        $category->update([
            'name' => $request->input('name'),
        ]);
        return redirect()->route('category.index')->with('success', 'Category updated successfully');
    }
    //* -------------------------------------- destroy -------------------------------------*/
    public function destroy(Request $request, string $id)
    {
        //! check if archived query param exists to delete permanently
        //! else soft delete
        if($request->has('archived')){
            $category = JobCategory::onlyTrashed()->findOrFail($id);
            $category->forceDelete();
            return redirect()->route('category.index', ['archived' => true])->with('success',  $category->name.' Category deleted permanently successfully');
        }
        else{
            $category = JobCategory::findOrFail($id);
            $category->delete();

            return redirect()->route('category.index')->with('success', $category->name.' Category archived successfully');
        }
    }
    //* -------------------------------------- restore -------------------------------------*/
    public function restore(string $id)
    {
        //! restore soft deleted category
        $category = JobCategory::onlyTrashed()->findOrFail($id);
        $category->restore();
        return redirect()->route('category.index', ['archived' => true])->with('success',  $category->name.' Category restored successfully');
    }
}

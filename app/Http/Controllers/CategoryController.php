<?php

namespace App\Http\Controllers;
use Auth;
use Crypt;
use App\Category;
use App\Team;
use App\Subcategory;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Requests\AddSubcategoryRequest;

class CategoryController extends HelpdeskController
{
     //Show all categories
     public function index() {
           
          //Get categories with subcategories and order by name on both models
          $categories = Category::with(['subcategories' => function($query) {
               $query->orderBy('name');
          }])->withCount('tickets')->orderBy('active', 'desc')->orderBy('name')->get();
          $data = [
               'categories' => $categories
          ];
          return view('app.admin.category-management-new', $data);
          // return view('app.admin.category-management', $data);
     }

     public function showCategory(Request $request)
     {
          $category = Category::with(['subcategories.teams', 'subcategories' => function($query) {
               $query->withCount('tickets')->orderBy('name');
          }])->where('id', $request->id)->firstOrFail();

          $data = [
               'category' => $category,
                    'teams' => Team::orderBy('name')->get(),
          ];
          return view('app.admin.subcategory-management', $data);
     }

     public function save(Request $request, Category $category)
     {
          $this->validate($request, [
               'name' => 'required|max:50',
          ]);
          $category->create([
               'name' => $request->name
          ]);
          flash()->success(null, 'Category Created');
          return redirect('/admin/category-management');
     }

     public function saveSubcategory(AddSubcategoryRequest $request)
     {
          $subcategory = Subcategory::create([
               'category_id' => $request->id,
               'name' => $request->name,
               'tags' => $request->tags,
               'location_matters' => ($request->location_matters ? 1 : 0),
               'created_by' => Auth::user()->id
          ]);

          return response()->json([
                    'category' => $request->id,
                    'subcategory_id' => $subcategory->id,
                    'name' => $subcategory->name,
                    'tags' => $subcategory->tags,
                    'ticket_count' => $subcategory->tickets->count(),
                    'location_matters' => $subcategory->location_matters,
                    'created_by' => Auth::user()->last_name . ', ' . Auth::user()->first_name,
                    'created_at' => $subcategory->created_at->toDayDateTimeString()
               ], 200);
     }

     public function inactivateSubcategory(Request $request)
     {
           $subcategory = Subcategory::where('id', $request->id)->firstOrFail();
           $subcategory->active = 0;
           $subcategory->save();
          return response()->json([
                    'category' => $subcategory->category->id,
                    'subcategory_id' => $subcategory->id,
                    'ticket_count' => $subcategory->tickets->count(),
                    'name' => $subcategory->name,
                    'tags' => $subcategory->tags,
                    'created_by' => Auth::user()->last_name . ', ' . Auth::user()->first_name,
                    'created_at' => $subcategory->created_at->toDayDateTimeString()
               ], 200);
     }

     public function activateSubcategory(Request $request)
     {
           $subcategory = Subcategory::where('id', $request->id)->firstOrFail();
           $subcategory->active = 1;
           $subcategory->save();
          return response()->json([
                    'category' => $subcategory->category->id,
                    'subcategory_id' => $subcategory->id,
                    'ticket_count' => $subcategory->tickets->count(),
                    'name' => $subcategory->name,
                    'tags' => $subcategory->tags,
                    'created_by' => Auth::user()->last_name . ', ' . Auth::user()->first_name,
                    'created_at' => $subcategory->created_at->toDayDateTimeString()
               ], 200);
     }

     public function inactivateCategory(Request $request)
     {
           $category = Category::where('id', $request->id)->firstOrFail();
           $category->active = 0;
           $category->save();
          return response()->json([
                    'category_id' => $category->id
               ], 200);
     }

      public function activateCategory(Request $request)
     {
           $category = Category::where('id', $request->id)->firstOrFail();
           $category->active = 1;
           $category->save();
          return response()->json([
                    'category_id' => $category->id
               ], 200);
     }

     public function editSubcategory(Request $request)
     {
          $this->validate($request, [
               'name' => 'required|max:50',
               'tags' => 'max:300',
          ]);
          $subcategory = Subcategory::where('id', $request->id)->firstOrFail();
           $subcategory->name = $request->name;
           $subcategory->tags = $request->tags;
           $subcategory->location_matters = ((bool)$request->location_matters == true ? 1 : 0);
           $subcategory->active = ((bool)$request->active == true ? 1 : 0);
           $subcategory->save();


          $subcategory->syncTeams(collect($request->teams)->lists('id')->toArray());
          return response()->json([
                    'category' => $subcategory->category->id,
                    'subcategory_id' => $subcategory->id,
                    'ticket_count' => $subcategory->tickets->count(),
                    'name' => $subcategory->name,
                    'tags' => $subcategory->tags,
                    'location_matters' => $subcategory->location_matters,
                    'created_by' => Auth::user()->last_name . ', ' . Auth::user()->first_name,
                    'created_at' => $subcategory->created_at->toDayDateTimeString()
               ], 200);
     }

     public function editCategory(Request $request)
     {
          //Vaidate the request
          $this->validate($request, [
             'name' => 'required|max:50',
         ]);
          //get the category and update the value
          $category = Category::where('id', $request->id)->firstOrFail();
          $category->name = $request->name;
          $category->save();
          flash()->success(null, 'Category Updated.');
          return redirect()->back();
     }

     public function removeSubcategory(Request $request)
     {
          $subcategory = Subcategory::where('id', $request->id)->firstOrFail();
          $subcategory->delete();
          return ['status' => 'success', 'subcategory_id' => $subcategory->id];
     }

     public function updateSubcategory(Request $request)
     {
          // return ['result' => $request->location_matters];
          $subcategory = Subcategory::where('id', $request->id)
               ->update([
                    'name' => $request->name,
                    'tags' => $request->tags,
                    'location_matters' => ((bool)$request->location_matters == true ? 1 : 0),
                    'active' => ((bool)$request->active == true ? 1 : 0)
               ]);
          return $subcategory;
     }

     public function deleteCategory(Request $request)
     {
          $category = Category::where('id', $request->id)->firstOrFail();
          $category->delete();
          return ['status' => 'success', 'category_id' => $category->id];
     }

}

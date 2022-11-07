<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $this->authorize('viewAny', Category::class);

        $request = request();
        $query = Category::query();

        if ($name = $request->query('name')) {
            $query->where('name', 'LIKE', "%{$name}%");
        }
        if ($status = $request->query('status')) {
            $query->where('status', '=', $status);
        }

        $entries = $query->withCount('products')->Paginate();

        $success = session()->get('success');

        $options = ['active', 'archived'];

        // dd($entries);
        return view('admin.categories.index', [
            'categories'=> $entries,
            'title'     => 'Categories List',
            'success'   => $success,
            'options'   => $options,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // $this->authorize('create', Category::class);

        $category = new Category();

        return view('admin.Categories.create', [
            'title'     => 'Create Category',
            'category'  => $category,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // $this->authorize('create', Category::class);

        // merge slug in model

        $request->validate(Category::validateRules());

        // sheck if image in request
        // if ($request->hasFile('image')) {
        //     $file = $request->file('image'); // UplodedFile Object

        //     $image_path = $file->storeAs('uploads',
        //         time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName()),
        //         'public');
            
        //     // merge image to the request
        //     $request->merge([
        //         'image_path' => $image_path,
        //     ]);
        // }

        
        $category = Category::create($request->all());

        return redirect()->route('categories.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        dd($category);
        // $this->authorize('view', $category);

        // return $category->Products()
        //                 // ->with('category:id,name,status')
        //                 // ->where('price', '>', 150)
        //                 // ->has('products)
        //                 ->orderBy('price', 'ASC')
        //                 ->get();

        return redirect()->route('products',['category' => $category->id]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        // $this->authorize('update', $category);

        // $category = Category::findOrFail($id);
        // if (!$category) {
        //     abort(404);
        // }
        $parents = Category::where('id', '<>', $category->id)->get() ;

        $title = 'Edit Category';

        return view('admin.categories.edit', compact('category', 'parents', 'title'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        // $this->authorize('update', $category);

        // $category = Category::find($id);

        $request->route('id');
        $request->merge([
            'slug' => Str::slug($request->name)
        ]);

        $request->validate(Category::validateRules());

        // sheck if image in request
        // if ($request->hasFile('image')) {
        //     $file = $request->file('image'); // UplodedFile Object

        //     // delete old image
        //     if ($category->image_path) {
        //         Storage::disk('public')->delete($category->image_path);
        //     }

        //     $image_path = $file->storeAs('uploads',
        //         time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName()),
        //         'public');
            
        //     // merge image to the request
        //     $request->merge([
        //         'image_path' => $image_path,
        //     ]);
        // }

        $category->update( $request->all() );

        return redirect()->route('categories.index')
            ->with('success', __('app.categories_update'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        // $this->authorize('delete', $category);

        // $category = Category::withoutGlobalScope('active')->findOrFail($id);

        if($category->products->count() != 0){
            return redirect()->back()
                ->with('error', __('Delete Failed Category Have (:num) Product', ['num' => $category->products->count()]));
        }

        $category->delete();
        return redirect()->back()
            ->with('success', __('app.categories_delete', ['name' => $category->name]));
    }

    /**
     * Change Status for specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function changeStatus(Category $category)
    {
        // $this->authorize('update', $category);

        if ($category->status == 'active') {
            $status = 'archived';
        }else if ($category->status == 'archived') {
            $status = 'active';
        }
        $category->update(
            ['status' => $status]
        );

        return redirect()->back();
    }

    public function deleteWithProducts(Category $category)
    {
        if($category->products->count() != 0){

            DB::beginTransaction();
            foreach ($category->products as $product) {
                $product->delete();
            }
            DB::commit();

            $category->delete();
            return redirect()->back()
                ->with('success', __('Forced Delete Category With (:num) Product', ['num' => $category->products->count()]));
        }
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $this->authorize('viewAny', Category::class);

        $categories = Category::withCount('products')
            ->when($request->query('name'), function($query, $value) { $query->where('name', 'LIKE', "%{$value}%"); })
            ->when($request->query('status'), function($query, $value) { $query->where('status', '=', $value); })
            ->paginate();

        return response()->json([
            'message'   => 'OK',
            'status'    => 200,
            'data'      => $categories,
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

        $request->validate(Category::validateRules());

        // merge slug => in model

        $category = Category::create($request->all());
        $category->refresh();

        return response()->json([
            'message'   => 'Category Created',
            'status'    => 201,
            'data'      => $category,
        ],201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $category = Category::findOrFail($id)->load('products');

        // $this->authorize('view', $category);

        return response()->json([
            'message'   => 'OK',
            'status'    => 200,
            'data'      => $category,
        ]);
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
        $request->validate([
            'name'        => 'sometimes|string|max:255|min:3',
            'description' => 'nullable|min:5',
            'status'      => 'in:active,archived'
        ]);
        
        // $this->authorize('update', $category);
        
        $category->update($request->all());
        // $category->refresh();

        return response()->json([
            'message'   => 'Category Updated',
            'status'    => 201,
            'data'      => $category,
        ],201);
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

        $category->delete();

        return response()->json([
            'message'   => 'Category Deleted',
            'status'    => 200,
        ]);
    }
}

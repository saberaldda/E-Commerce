<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $this->authorize('viewAny', Product::class);

        // for search
        $request = request();
        $query = Product::query();

        if ($name = $request->query('name')) {
            $query->where('name', 'LIKE', "%{$name}%");
        }
        if ($status = $request->query('status')) {
            $query->where('status', '=', $status);
        }

        $products = $query->withoutGlobalScopes([ActiveStatusScope::class])
            ->with('category')
            ->select(['products.*',])
            ->paginate();

        // for select menu (search)
        $options = ['active', 'draft'];

        return view('admin.products.index', [
            'products' => $products,
            'title'    => 'Products List',
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
        // $this->authorize('create', Product::class);

        $categories = Category::pluck('name', 'id');
        return view('admin.products.create', [
            'title'     => 'Create Product',
            'categories' => $categories,
            'product' => new Product(),
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
        // $this->authorize('create', Product::class);

        // merge slug in model

        $request->validate(Product::validateRules());

        // sheck if image in request
        if ($request->hasFile('image')) {
            $file = $request->file('image'); // UplodedFile Object

            $image_path = $file->storeAs('uploads',
                time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName()),
                'public');
            
            // merge image to the request
            $request->merge([
                'image_path' => $image_path,
            ]);
        }

        // merge user_id to the request
        $user_id = Auth::user()->id;
        $request->merge([
            'user_id' => $user_id,
        ]);

        $product = Product::create($request->all());

        return redirect()->route('products.index')
            ->with('success', __('app.products_store'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        // $this->authorize('view', $product);

        // $product = Product::withoutGlobalScope('')->findOrFail($id);

        // SELECT * FROM ratings WHERE rateable_id = ? AND rateable_type = 'App\Models\Product';
        // return $product->ratings;

        return redirect()->route('product.details', $product->slug);
        
        // return view('admin.products.show', [
        //     'title'     => 'Show Product',
        //     'product' => $product,
        // ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        // $this->authorize('update', $product);

        // $product = Product::withoutGlobalScope('active')->findOrFail($id);

        return view('admin.products.edit', [
            'title'     => 'Edit Product',
            'product'    => $product,
            // 'categories' => Category::all(),
            'categories' => Category::pluck('name', 'id'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        // $this->authorize('update', $product);

        // merge slug in model
        
        // $product = Product::withoutGlobalScope('active')->findOrFail($id);

        $request->validate(Product::validateRules());

        // sheck if image in request
        if ($request->hasFile('image')) {
            $file = $request->file('image'); // UplodedFile Object

            // delete old image
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }

            $image_path = $file->storeAs('uploads',
                time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName()),
                'public');
            
            // merge image to the request
            $request->merge([
                'image_path' => $image_path,
            ]);
        }

        $product->update($request->all());

        return redirect()->route('products.index')
            ->with('success', __('app.products_update'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        // $this->authorize('delete', $product);

        // $product = Product::findOrFail($id);

        $product->delete();

        return redirect()->route('products.index')
            ->with('success', __('app.products_delete', ['name' => $product->name]));
    }

    public function changeStatus(Product $product)
    {
        // $this->authorize('update', $product);

        if ($product->status == 'active') {
            $status = 'draft';
        }else if ($product->status == 'draft') {
            $status = 'active';
        }
        $product->update(
            ['status' => $status]
        );

        return redirect()->back();
    }
}

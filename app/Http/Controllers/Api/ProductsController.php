<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $products = Product::with('category')
            ->when($request->query('name'), function($query, $value) { $query->where('name', 'LIKE', "%{$value}%"); })
            ->when($request->query('status'), function($query, $value) { $query->where('status', '=', $value); })
            ->paginate();

        return response()->json([
            'message'   => 'OK',
            'status'    => 200,
            'data'      => $products,
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

        $product = Product::create($request->all());
        $product->refresh();

        return response()->json([
            'message'   => 'Product Created',
            'status'    => 201,
            'data'      => $product,
        ],201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        $product->load('category');

        return response()->json([
            'message'   => 'OK',
            'status'    => 200,
            'data'      => $product
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name'        => 'sometimes|required|max:255',
            'category_id' => 'required|int|exists:categories,id',
            'description' => 'nullable|min:5',
            'image'       => 'nullable|image|dimensions:width=760,height-760',
            'price'       => 'required|numeric|min:0',
            'quantity'    => 'required|int|min:0',
            'status'      => 'in:active,draft',
        ]);

        //]]// sheck if image in request
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

        return response()->json([
            'message'   => 'Product Updated',
            'status'    => 201,
            'data'      => $product,
        ],201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json([
            'message'   => 'Product Deleted',
            'code'      => 200,
            'data'      => $product,
        ]);
    }
}

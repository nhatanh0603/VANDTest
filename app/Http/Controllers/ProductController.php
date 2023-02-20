<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(string $store_id): Response
    {
        $this->checkStoreOwner($store_id);

        $validated = request()->validate([
            'limit' => 'sometimes|numeric'
        ]);

        return new Response([
            'data' => Store::find($store_id)->products()->paginate(isset($validated['limit']) ? $validated['limit'] : 10)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): Response
    {
        $validated = $this->productValidate($request);

        $result = $this->checkStoreOwner($validated['store_id']);

        if($result)
            Product::create([
                'store_id' => $validated['store_id'],
                'slug' => $validated['slug'],
                'display_name' => $validated['display_name'],
                'description' => $validated['description'],
                'price' => $validated['price'],
                'stock' => $validated['stock'],
                'created_at' => now()
            ]);

        return new Response([
            'message' => 'Product created successfully'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $store_id, string $id): Response
    {
        $this->checkStoreOwner($store_id);

        $product = Product::where('store_id', $store_id)->where('id', $id)->first();

        if(!$product)
            return new Response([
                'message' => 'Product not found.'
            ], 404);

        return new Response([
            'data' => $product
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): Response
    {
        $validated = $this->productValidate($request);

        $result = $this->checkStoreOwner($validated['store_id']);

        if($result)
            $product = Product::where('id', $id)
                            ->where('store_id', $validated['store_id'])
                            ->get();

            if(count($product) == 0)
                return new Response([
                    'message' => 'Product not found.'
                ], 404);

            $product->toQuery()->update([
                'store_id' => $validated['store_id'],
                'slug' => $validated['slug'],
                'display_name' => $validated['display_name'],
                'description' => $validated['description'],
                'price' => $validated['price'],
                'stock' => $validated['stock'],
                'updated_at' => now()
            ]);

        return new Response([
            'message' => 'Product updated successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $store_id, string $id): Response
    {
        $this->checkStoreOwner($store_id);

        $product = Product::where('store_id', $store_id)->where('id', $id)->first();

        if(!$product)
            return new Response([
                'message' => 'Product not found.'
            ], 404);

        $product->delete();

        return new Response([
            'message' => 'Product deleted successfully'
        ]);
    }

    /**
     * Search the specified resource from storage.
     */
    public function search(string $store_id, string $keyword): Response
    {
        $this->checkStoreOwner($store_id);

        $validated = request()->validate([
            'limit' => 'sometimes|numeric'
        ]);

        $result = Product::search($keyword)
                    ->where('store_id', $store_id)
                    ->paginate(isset($validated['limit']) ? $validated['limit'] : 5);

        return new Response($result);
    }

    public function productValidate(Request $request)
    {
        $validated = $request->validate([
            'store_id' => 'required|integer',
            'slug' => 'required|string|max:50|unique:products|alpha_dash',
            'display_name' => 'required|string',
            'description' => 'sometimes|string|nullable',
            'price' => 'required|decimal:2',
            'stock' => 'required|integer|between:0,65000'
        ]);

        return $validated;
    }

    private function checkStoreOwner(int $store_id)
    {
        $store = Store::where('user_id', auth()->user()->id)
                    ->where('id', $store_id)
                    ->get();

        if(count($store) == 0) {
            $validator = Validator::make([], []);
            $validator->errors()->add('unauthorized', 'This store does not belong to you or does not exist.');
            $exception = new ValidationException($validator);
            $exception->status(401);
            throw $exception;

            /* throw $exception->withMessages([
                'unauthorized' => 'This store does not belong to you or does not exist.'
            ]); */
        }

        return true;
    }
}

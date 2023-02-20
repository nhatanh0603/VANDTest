<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Validator;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $validated = request()->validate([
            'limit' => 'sometimes|numeric'
        ]);

        return new Response(auth()->user()->stores()->cursorPaginate(isset($validated['limit']) ? $validated['limit'] : 5));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): Response
    {
        $validated = $this->storeValidate($request);

        Store::create([
            'user_id' => auth()->user()->id,
            'name' => $validated['name'],
            'display_name' => $validated['display_name'],
            'description' => $validated['description'],
            'created_at' => now()
        ]);

        return new Response([
            'message' => 'Store created successfully'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): Response
    {
        return new Response([
            'data' => $this->checkStoreOwner($id)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): Response
    {
        $validated = $this->storeValidate($request);

        $store = $this->checkStoreOwner($id);

        $store->toQuery()->update([
            'name' => $validated['name'],
            'display_name' => $validated['display_name'],
            'description' => $validated['description'],
            'updated_at' => now()
        ]);

        return new Response([
            'message' => 'Store updated successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): Response
    {
        $store = $this->checkStoreOwner($id);

        $store->toQuery()->delete();

        return new Response([
            'message' => 'Store deleted successfully'
        ]);
    }

    /**
     * Search the specified resource from storage.
     */
    public function search(string $keyword): Response
    {
        $validated = request()->validate([
            'limit' => 'sometimes|numeric'
        ]);

        $result = Store::search($keyword)
                    ->where('user_id', auth()->user()->id)
                    ->paginate(isset($validated['limit']) ? $validated['limit'] : 5);

        return new Response($result);
    }

    public function storeValidate(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:stores|alpha_dash',
            'display_name' => 'required|string',
            'description' => 'sometimes|string|nullable',
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

        return $store;
    }
}

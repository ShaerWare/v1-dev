<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;


class ProductController extends Controller
{
   /* public function __construct()

     */

    public function index()
    {
        $products = Auth::user()->products; // Предполагается, что у пользователя есть отношение products
        return response()->json($products, 200);
    }

    // Создание продукта для текущего байера

    /**

     */

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
        ]);

        //$product = Auth::user()->products()->create($request->all());

       // return response()->json($product, 201);
    }

    // Обновление продукта текущего байера
    public function update(Request $request, $productId)
    {
        //$product = Auth::user()->products()->findOrFail($productId);

        //$product->update($request->all());

       // return response()->json($product, 200);
    }

    // Удаление продукта текущего байера
    public function destroy($productId)
    {
       // $product = Auth::user()->products()->findOrFail($productId);

       // $product->delete();

      //  return response()->json(null, 204);
    }
}

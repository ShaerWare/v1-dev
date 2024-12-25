<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class ProductControllerWeb extends Controller
{
    public function index()
    {
        $products = Auth::user()->products; // Связь products должна быть определена в модели User
        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $product = Auth::user()->products()->create($request->all());

        return redirect()->route('products.index')->with('success', 'Продукт успешно создан.');
    }

    public function edit($id)
    {
        $product = Auth::user()->products()->findOrFail($id);
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $product = Auth::user()->products()->findOrFail($id);
        $product->update($request->all());

        return redirect()->route('products.index')->with('success', 'Продукт успешно обновлён.');
    }

    public function destroy($id)
    {
        $product = Auth::user()->products()->findOrFail($id);
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Продукт успешно удалён.');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContentController extends Controller
{
    public function index()
    {
        $contents = Content::paginate();

        return view('admin.contents.index', compact('contents'));
    }

    public function create(Request $request)
    {
        Content::create($request->all());
        Toast::info('Контент добавлен.');
    }

    public function delete($id)
    {
        Content::findOrFail($id)->delete();
        Toast::info('Контент удален.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\SliderBanner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Orchid\Support\Facades\Toast;

class SliderBannerController extends Controller
{
    public function index()
    {
        $banners = SliderBanner::paginate();

        return view('admin.slider_banners.index', compact('banners'));
    }

    public function create(Request $request)
    {
        $bannerData = $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'link' => 'nullable|url',
        ]);

        if ($request->hasFile('image')) {
            $bannerData['image'] = $request->file('image')->store('banners', 'public');
        }

        SliderBanner::create($bannerData);
        Toast::info('Заставка успешно добавлена.');

        return redirect()->route('admin.slider_banners.index');
    }

    public function edit($id)
    {
        $banner = SliderBanner::findOrFail($id);

        return response()->json(['banner' => $banner]);
    }

    public function update(Request $request, $id)
    {
        $banner = SliderBanner::findOrFail($id);

        $bannerData = $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'link' => 'nullable|url',
        ]);

        if ($request->hasFile('image')) {
            // Удаляем старое изображение, если оно есть
            if ($banner->image) {
                Storage::disk('public')->delete($banner->image);
            }
            $bannerData['image'] = $request->file('image')->store('banners', 'public');
        }

        $banner->update($bannerData);
        Toast::info('Заставка успешно обновлена.');

        return redirect()->route('admin.slider_banners.index');
    }

    public function delete($id)
    {
        $banner = SliderBanner::findOrFail($id);

        // Удаляем изображение при удалении записи
        if ($banner->image) {
            Storage::disk('public')->delete($banner->image);
        }

        $banner->delete();
        Toast::info('Заставка слайдера удалена.');

        return redirect()->route('admin.slider_banners.index');
    }
}

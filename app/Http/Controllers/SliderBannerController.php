<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SliderBannerController extends Controller
{
    public function index()
    {
        $banners = SliderBanner::paginate();

        return view('admin.slider_banners.index', compact('banners'));
    }

    public function create(Request $request)
    {
        $bannerData = $request->get('banner');

        SliderBanner::create($bannerData);

        Toast::info('Заставка успешно добавлена.');
    }

    public function delete($id)
    {
        SliderBanner::findOrFail($id)->delete();
        Toast::info('Заставка слайдера удалена.');
    }
}

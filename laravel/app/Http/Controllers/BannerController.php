<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Banner;

class BannerController extends Controller
{
    public function redirect($id)
    {
    	$banner = Banner::findOrFail($id);

    	$banner->clicks = $banner->clicks + 1;
    	$banner->save();
    	
    	return redirect($banner->url);
    }

    public function get(Request $request, $position)
    {
    	$banner = getBanner($position);

    	if (!$banner) return view('banners.empty');

        $type = $request->get('type', 'main');
    	$bg = $request->get('bg', false);

    	return view('layouts.banner', compact('banner', 'type', 'bg'));
    }


    public function get_banners(Request $request)
    {
        $positions = $request->get('positions');

        if (!$positions) return '';

        $banners = [];

        foreach ($positions as $p) {
            $banner = getBanner($p);

            if (!$banner) continue;

            $banners[$p] = [
                'url' => $banner['fake_url'],
                'main_image' => $banner['main_image'],
                'tablet_image' => $banner['tablet_image'],
                'mobile_image' => $banner['mobile_image'],
            ];
        }

        return $banners;
    }
}
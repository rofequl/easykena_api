<?php

namespace App\Http\Controllers;


use App\Model\HomeSetup;
use App\Traits\FileUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class HomeController extends Controller
{
    use FileUpload;

    public function homeIndex()
    {
        $home = HomeSetup::all()->first();
        if (!$home) {
            $home = new HomeSetup();
            $home->home_slider = '';
            $home->save();
        }
        return $home;
    }

    public function sliderStore(Request $request)
    {
        $this->validate($request, [
            'imageList' => 'required',
        ]);
        $home = HomeSetup::all()->first();
        $photos = [];
        foreach ($request->imageList as $image) {
            if (array_key_exists("url", $image) && strlen($image['url']) > 200) {
                $photo = $this->saveImagesDWH($image['url'], 'upload/home/slider/', 920, 350);
                array_push($photos, $photo);
            } else {
                foreach (json_decode($home->home_slider) as $pho) {
                    if (strpos($image['url'], $pho)) {
                        array_push($photos, $pho);
                    }
                }
            }
        }
        $home->home_slider = json_encode($photos);
        $home->save();
        return $photos;

    }
}

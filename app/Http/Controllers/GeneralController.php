<?php

namespace App\Http\Controllers;

use App\Model\General;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\FileUpload;
use Illuminate\Support\Facades\File;

class GeneralController extends Controller
{
    use FileUpload;

    public function generalIndex()
    {
        $general = DB::table('generals')->get();
        if (!$general->isEmpty()) {
            return $general;
        } else {
            $general = DB::table('generals')->insert(
                ['maintenance_mode' => 0]
            );
            return DB::table('generals')->get();
        }
    }

    public function generalStore(Request $request)
    {
        $this->validate($request, [
            'app_name' => 'required|string',
            'logo_white' => 'required',
            'logo_black' => 'required',
        ]);
        $general = General::all()->first();
        if (strlen($request->logo_white) > 200) {
            if ($general->logo_white !== 'image/setup/logo_white.png') {
                File::delete(base_path('public/' . $general->logo_white));
            }
            $general->logo_white = $this->saveImages($request, 'logo_white', 'upload/general/');
        }

        if (strlen($request->logo_black) > 200) {
            if ($general->logo_black !== 'image/setup/logo_black.png') {
                File::delete(base_path('public/' . $general->logo_black));
            }
            $general->logo_black = $this->saveImages($request, 'logo_black', 'upload/general/');
        }
        $general->app_name = $request->app_name;
        $general->save();

        return DB::table('generals')->get();
    }

    public function languageActive(Request $request)
    {
        $this->validate($request, [
            'active' => 'required',
        ]);
        $request->active === 1 ? $data = 1 : $data = 0;
        $general = General::all()->first();
        $general->bangla_language = $data;
        $general->save();
        return $data;
    }

    public function languageDefault(Request $request)
    {
        $this->validate($request, [
            'type' => 'required',
        ]);
        $request->type === 2 ? $data = 2 : $data = 1;
        $general = General::all()->first();
        $general->default_language = $data;
        $general->save();
        return $data;
    }

    public function maintenanceActive(Request $request)
    {
        $this->validate($request, [
            'active' => 'required',
        ]);
        $request->active === 1 ? $data = 1 : $data = 0;
        $general = General::all()->first();
        $general->maintenance_mode = $data;
        $general->save();
        return $data;
    }

}

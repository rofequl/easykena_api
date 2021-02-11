<?php

namespace App\Http\Controllers;

use App\Model\General;
use App\Model\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\FileUpload;
use App\Traits\Slug;
use Illuminate\Support\Facades\File;

class SubcategoryController extends Controller
{
    use FileUpload;
    use Slug;

    public function index(Request $request)
    {
        return DB::table('sub_categories')->join('categories', 'categories.id', '=', 'sub_categories.category_id')
            ->select('categories.name as category', 'sub_categories.*')->get();
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:50|unique:sub_categories',
            'banner' => 'required',
            'category_id' => 'required',
        ]);
        $general = General::all()->first();
        if ($general->bangla_language == 1) {
            $this->validate($request, [
                'name_bd' => 'required',
            ]);
        }

        $banner = $this->saveImagesWH($request, 'banner', 'upload/product/subcategory/banner/', 800, 300);
        $slug = $this->slugText($request, 'name');

        $insert = DB::table('sub_categories')->insertGetId([
            'name' => $request->name,
            'name_bd' => $request->name_bd,
            'banner' => $banner,
            'category_id' => $request->category_id,
            'slug' => $slug,
        ]);
        return (array)DB::table('sub_categories')->join('categories', 'categories.id', '=', 'sub_categories.category_id')
            ->select('categories.name as category', 'sub_categories.*')->where('sub_categories.id', $insert)->first();
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|string|max:50|unique:sub_categories,name,' . $id,
            'banner' => 'required',
            'category_id' => 'required',
        ]);
        $general = General::all()->first();
        if ($general->bangla_language == 1) {
            $this->validate($request, [
                'name_bd' => 'required',
            ]);
        }
        $subcategory = SubCategory::findOrFail($id);
        $subcategory->name = $request->name;
        $subcategory->name_bd = $request->name_bd;
        if (strlen($request->banner) > 200) {
            File::delete(base_path('public/' . $subcategory->banner));
            $subcategory->banner = $this->saveImagesWH($request, 'banner', 'upload/product/subcategory/banner/', 800, 300);
        }
        $subcategory->category_id = $request->category_id;
        $subcategory->save();
        return (array)DB::table('sub_categories')->join('categories', 'categories.id', '=', 'sub_categories.category_id')
            ->select('categories.name as category', 'sub_categories.*')->where('sub_categories.id', $subcategory->id)->first();
    }

    public function destroy($id)
    {
        $product = DB::table('products')->where('subcategory_id', $id)->first();
        $subsubcategory = DB::table('sub_sub_categories')->where('subcategory_id', $id)->first();
        if ($product) return response()->json(['result' => 'Error', 'message' => 'Sub-Category already used create a product'], 200);
        if ($subsubcategory) return response()->json(['result' => 'Error', 'message' => 'Sub-Category already used create a Sub-Subcategory'], 200);
        $subcategory = SubCategory::findOrFail($id);
        File::delete(base_path('public/' . $subcategory->banner));
        $subcategory->delete();
    }
}

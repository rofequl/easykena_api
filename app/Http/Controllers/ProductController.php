<?php

namespace App\Http\Controllers;

use App\Model\Category;
use App\Model\General;
use App\Model\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\FileUpload;
use App\Traits\Slug;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    use FileUpload;
    use Slug;

    public function index(Request $request)
    {
        return DB::table('products')->get();
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:100|unique:products',
            'sort_desc' => 'max:200',
            'unit' => 'max:50',
            'quantity' => 'required',
            'price' => 'required',
            'thumb_image' => 'required',
        ]);

        $slug = $this->slugText($request, 'name');
        $thumbnail = $this->saveImagesWH($request, 'thumb_image', 'upload/product/thumbnail/', 450, 500);
        $insert = DB::table('products')->insertGetId([
            'name' => $request->name,
            'sort_desc' => $request->sort_desc,
            'unit' => $request->unit,
            'category_id' => $request->category_id,
            'subcategory_id' => (int)$request->subcategory_id,
            'sub_subcategory_id' => (int)$request->sub_subcategory_id,
            'quantity' => $request->quantity,
            'price' => $request->price,
            'description' => $request->description,
            'slug' => $slug,
            'thumb_image' => $thumbnail,
            'sku' => $this->sku(),
            'user_id' => Auth::user()->id,
        ]);
        return Product::findOrFail($insert);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|string|max:50|unique:categories,name,' . $id,
            'banner' => 'required',
            'icon' => 'required',
        ]);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
    }

    public function productListing(Request $request)
    {
        $conditions = [];
        $category_id = $request->category;
        $subcategory_id = $request->subcategory;
        $subsubcategory_id = $request->subsubcategory;

        $keyword = $request->keyword;

        if ($category_id) $conditions = array_merge($conditions, ['category_id' => $category_id]);
        if ($subcategory_id) $conditions = array_merge($conditions, ['subcategory_id' => $subcategory_id]);
        if ($subsubcategory_id) $conditions = array_merge($conditions, ['sub_subcategory_id' => $subsubcategory_id]);

        $product = Product::where($conditions);

        if ($keyword) $product = $product->where('name', 'like', '%' . $keyword . '%');

        return $product->get();
    }
}

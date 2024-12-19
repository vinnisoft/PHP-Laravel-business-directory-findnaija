<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Option;
use App\Models\CategoryGroup;
use Illuminate\Http\Request;
use App\DataTables\BusinessDataTable;
use DB, Validator;

class CategoryController extends Controller
{
    public function categories(Request $request)
    {
        $categories = Category::select('id', 'name', 'icon', 'category_on_home');
        if (isset($request->more) && $request->more == 0) {
            $categories->where('category_on_home', 1);
        }
        $categories = $categories->get();
        $categories->makeHidden(['icon_path']);
        return response()->json([
            'status' => count($categories) > 0 ? true : false,
            'message' => count($categories) > 0 ? '' : 'No category found!',
            'data' => $categories
        ]);
    }

    public function moreCategories(Request $request)
    {
        $categories = Category::select('id', 'name', 'icon', 'group_id');
        if (isset($request->more) && $request->more == '1') {
            $categories->where('show_on_home', '1');
        }
        $categories = $categories->get()->groupBy('group_id');
        $categories = $categories->mapWithKeys(function ($categoryGroup, $groupId) {
            return [CategoryGroup::find($groupId)->name => $categoryGroup];
        });

        $catArr = [];
        foreach ($categories as $key => $category) {
            $catArr[] = [
                'name' => $key,
                'data' => $category
            ];
        }

        return response()->json([
            'status' => count($categories) > 0,
            'message' => count($categories) > 0 ? '' : 'No category found!',
            'data' => $catArr
        ]);
    }
    
    public function subCategories(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',            
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()]);
        }
        $subCategories = SubCategory::select('id', 'name', 'category_id', 'icon')->where('category_id', $request->category_id)->get();
        $options = Option::select('id', 'name', 'category_id', 'icon')->where('category_id', $request->category_id)->get();
        return response()->json([
            'status' => count($subCategories) > 0 ? true : false,
            'message' => count($subCategories) > 0 ? '' : 'No sub category found!',
            'services' => $subCategories,
            'options' => $options
        ]);
    }

    public function graphicCategories()
    {
        $categories = Category::where('graphic_on_home', 1)->select('id', 'name', 'graphic_image')->get();
        $categories->makeHidden(['icon_path']);

        return response()->json([
            'status' => count($categories) > 0 ? true : false,
            'message' => count($categories) > 0 ? '' : 'No category found!',
            'data' => $categories
        ]);
    }

    public function groups()
    {
        $groups = CategoryGroup::select('id', 'name')->get();
        return response()->json([
            'status' => count($groups) > 0 ? true : false,
            'message' => count($groups) > 0 ? '' : 'No category group found!',
            'data' => $groups
        ]);
    }

    public function groupCategory($id)
    {
        $categories = Category::where('group_id', $id)->select('id', 'name', 'icon')->get();
        return response()->json([
            'status' => count($categories) > 0 ? true : false,
            'message' => count($categories) > 0 ? '' : 'No category found!',
            'data' => $categories
        ]);
    }
}
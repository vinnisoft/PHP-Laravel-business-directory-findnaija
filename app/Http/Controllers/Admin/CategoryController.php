<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\CategoryGroup;
use Illuminate\Http\Request;
use App\DataTables\CategoryDataTable;
use App\Models\User;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Option;
use Validator, DB;

class CategoryController extends Controller
{  
    public function index(CategoryDataTable $dataTable)
    {       
        return $dataTable->render('admin.category.index');
    }
    
    public function create()
    {
        $categoryGroup = CategoryGroup::orderBy('id', 'DESC')->pluck('name', 'id')->prepend('Select Category Group', '');
        return view('admin.category.create', compact('categoryGroup'));
    }
    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cat_icon' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()]);
        }

        DB::beginTransaction();
        try {

            if ($request->hasFile('cat_icon')) {
                $request['icon'] = uploadFile($request->file('cat_icon'), 'public/category');
            }
            if ($request->hasFile('cat_graphic_image')) {
                $request['graphic_image'] = uploadFile($request->file('cat_graphic_image'), 'public/category/graphic');
            }
            $category = Category::create($request->all());
            if (isset($request->service) && count($request->service) > 0) {
                foreach ($request->service as $service) {
                    if (isset($service['icon']) && isset($category->id)) {
                        $service['icon'] = uploadFile($service['icon'], 'public/category');
                        $service['category_id'] = $category->id;
                        SubCategory::create($service);
                    }
                }
            }
            if (isset($request->option) && count($request->option) > 0) {
                foreach ($request->option as $option) {
                    if (isset($option['icon']) && isset($category->id)) {
                        $option['icon'] = uploadFile($option['icon'], 'public/category');
                        $option['category_id'] = $category->id;
                        Option::create($option);
                    }
                }
            }
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Category has been successfully created!']);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function setCategory(Request $request)
    {
        switch ($request->name) {
            case 'category':
                Category::where('id', '!=', $request->categoryId)->update(['show_on_home' => '0']);
                Category::where('id', $request->categoryId)->update(['show_on_home' => '1']);
            break;
            case 'graphic':
                Category::where('id', $request->categoryId)->update(['graphic_on_home' => $request->value]);
            break;
            case 'category_on_home':
                Category::where('id', $request->categoryId)->update(['category_on_home' => $request->value]);
            break;
        }
        return response()->json(['status' => true, 'message' => ucwords($request->name).' has been successfully set to show on home!']);
        // $fieldName = 'graphic_on_home';
        // if ($request->name == 'category') {
        //     $fieldName = 'show_on_home';
        //     Category::where('id', '!=', $request->categoryId)->update([$fieldName => '0']);
        // }
        // if (Category::where('id', $request->categoryId)->update([$fieldName => $request->value])) {
        //     return response()->json(['status' => true, 'message' => ucwords($request->name).' has been successfully set to show on home!']);
        // }
        // return response()->json(['status' => false, 'message' => 'Something went wrong please try again!']);
    }
    
    public function edit($id)
    {
        $category = Category::where('id', $id)->first();
        $subCategories = SubCategory::where('category_id', $id)->get();
        $catOptions = Option::where('category_id', $id)->get();
        $categoryGroup = CategoryGroup::orderBy('id', 'DESC')->pluck('name', 'id')->prepend('Select Category Group', '');
        return view('admin.category.edit', compact('category', 'subCategories', 'catOptions', 'categoryGroup'));
    }
    
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'cat_icon' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',           
        ], [
            'cat_icon.image' => 'The category image must be an image.',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->messages()->first()]);
        }
        DB::beginTransaction();
        try {
            if ($request->hasFile('cat_icon')) {
                $request['icon'] = uploadFile($request->file('cat_icon'), 'public/category');
            }
            if ($request->hasFile('cat_graphic_image')) {
                $request['graphic_image'] = uploadFile($request->file('cat_graphic_image'), 'public/category/graphic');
            }
            Category::where('id', $id)->update($request->except('_token', 'cat_icon', '_method', 'icon_path', 'service', 'option', 'cat_graphic_image'));
            if (count($request->service) > 0) {
                foreach ($request->service as $service) {
                    if (!empty($service['icon'])) {
                        $service['icon'] = uploadFile($service['icon'], 'public/category');
                    } else {
                        $service['icon'] = $service['icon_name'];
                    }
                    if (isset($service['icon'])) {
                        $service['category_id'] = $id;
                        SubCategory::updateOrCreate(['id' => $service['id']], $service);
                    }
                }
            }
            if (count($request->option) > 0) {
                foreach ($request->option as $option) {
                    if (!empty($option['icon'])) {
                        $option['icon'] = uploadFile($option['icon'], 'public/category');
                    } else {
                        $option['icon'] = $option['icon_name'];
                    }
                    if (isset($option['icon'])) {
                        $option['category_id'] = $id;
                        Option::updateOrCreate(['id' => $option['id']], $option);
                    }
                }
            }
            DB::commit();
            return response()->json(['status' => true, 'message' => 'Category has been successfully created!']);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        if (Category::where('id', $id)->delete()) {
            return response()->json(['status' => true, 'message' => 'Category has been successfully deleted!']);
        }
        return response()->json(['status' => false, 'message' => 'Sonething went wrong please try again!']);
    }

    public function deleteSubCategory($id)
    {
        if (SubCategory::where('id', $id)->delete()) {
            return response()->json(['status' => true, 'message' => 'Service has been successfully deleted!']);
        }
        return response()->json(['status' => false, 'message' => 'Sonething went wrong please try again!']);
    }

    public function deleteOption($id)
    {
        if (Option::where('id', $id)->delete()) {
            return response()->json(['status' => true, 'message' => 'Option has been successfully deleted!']);
        }
        return response()->json(['status' => false, 'message' => 'Sonething went wrong please try again!']);
    }
}

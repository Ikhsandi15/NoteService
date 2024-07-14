<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function categoryLists()
    {
        $datas = Category::where('user_id', Auth::id())->get();

        return Helper::APIResponse('Successfully retrieved the category list', 200, null, $datas);
    }

    public function create(Request $req)
    {
        $validation = Validator::make($req->all(), [
            'category' => 'required|unique:categories'
        ]);

        if ($validation->fails()) {
            return Helper::APIResponse("Error Validation", 422, $validation->errors(), null);
        }

        Category::create([
            'category' => $req->category,
            'user_id' => Auth::id()
        ]);

        return Helper::APIResponse("success create", 200, null, null);
    }

    public function update(Request $req, $category_id)
    {
        $validation = Validator::make($req->all(), [
            'category' => 'required|unique:categories'
        ]);

        if ($validation->fails()) {
            return Helper::APIResponse("Error Validation", 422, $validation->errors(), null);
        }

        $category = Category::where('user_id', Auth::id())->where('id', $category_id)->first();

        if (!$category) {
            return Helper::APIResponse("failed found category", 404, 'not found', null);
        }

        $category->update($req->all());

        return Helper::APIResponse('success update category', 200, null, null);
    }

    public function search(Request $req)
    {
        $req->validate([
            'query' => 'required|string|min:1'
        ]);

        $query = $req->input('query');

        $categories = Category::where('user_id', Auth::id())
            ->where('category', 'LIKE', '%' . $query . '%')
            ->get();

        return Helper::APIResponse('success', 200, null, $categories);
    }
}

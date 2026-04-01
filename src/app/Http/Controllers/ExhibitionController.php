<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ExhibitionRequest;

class ExhibitionController extends Controller
{
    // 出品画面表示
    public function create()
    {
        $categories = Category::all();
        return view('items.create', compact('categories'));
    }

    // 出品処理
    public function store(ExhibitionRequest $request)
    {
        // 画像保存
        $path = $request->file('image')->store('items', 'public');

        // 商品登録（カテゴリは JSON 配列）
        Item::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'brand' => $request->brand,
            'description' => $request->description,
            'price' => $request->price,
            'category_ids' => $request->categories,
            'condition' => $request->condition,
            'status' => 1,
            'image_path' => $path,
        ]);

        return redirect('/')->with('success', '商品を出品しました');
    }
}

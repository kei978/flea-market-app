<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use App\Models\Comment;
use App\Http\Requests\CommentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'recommend');
        $keyword = $request->query('keyword');

        $searchCondition = function ($query) use ($keyword) {
            if ($keyword) {
                $query->where('title', 'like', "%{$keyword}%");
            }
        };

        // マイリストタブ
        if ($tab === 'mylist') {
            if (!Auth::check()) {
                return view('items.index', [
                    'items' => collect([]),
                    'tab' => 'mylist',
                ]);
            }
            $items = Auth::user()
                ->likes()
                ->where($searchCondition)
                ->get();
            return view('items.index', compact('items', 'tab'));
        }

        // おすすめタブ
        $items = Item::query()
            ->where($searchCondition)
            ->when(Auth::check(), function ($query) {
                // 自分の出品は除外
                $query->where('user_id', '<>', Auth::id());
            })
            ->get();

        return view('items.index', compact('items', 'tab'));
    }

    // 商品詳細
    public function show($id)
    {
        $item = Item::with(['user', 'comments.user'])->findOrFail($id);

        return view('items.show', compact('item'));
    }

    // いいね機能
    public function like($id)
    {
        $item = Item::findOrFail($id);
        $user = Auth::user();
        if ($item->isLikedBy($user)) {
            $item->likedUsers()->detach($user->id);
        } else {
            $item->likedUsers()->attach($user->id);
        }

        return back();
    }

    // コメント投稿
    public function comment(CommentRequest $request, $id)
    {
        Comment::create([
            'user_id' => Auth::id(),
            'item_id' => $id,
            'comment' => $request->comment,
        ]);

        return back();
    }
}

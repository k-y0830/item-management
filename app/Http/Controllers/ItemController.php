<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;

class ItemController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * 商品一覧
     */
    public function index()
    {
        // 商品一覧取得
        $items = Item::all();

        return view('item.index', compact('items'));
    }

    /**
     * 商品登録
     */
    public function add(Request $request)
    {
        // POSTリクエストのとき
        if ($request->isMethod('post')) {
            // バリデーション
            $this->validate($request, [
                'name' => 'required|max:100',
            ]);

            // 商品登録
            Item::create([
                'user_id' => Auth::user()->id,
                'name' => $request->name,
                'type' => $request->type,
                'detail' => $request->detail,
                'price' => $request->price,
                'stock' => $request->stock,
            ]);

            return redirect('/items');
        }

        return view('item.add');
    }

    /**
     * 編集ページ表示
     */
    public function edit($id)
    {
        $item = Item::where('id', '=', $id)->first();

        return view('item.edit')->with([
            'item' => $item,
        ]);
    }

    /**
     * 編集登録
     */
    public function editregister(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'integer',
            'stock' => 'integer',
        ]);

        $item = Item::where('id', '=', $id)->first();
        $item->name = $request->name;
        $item->type = $request->type;
        $item->detail = $request->detail;
        $item->price = $request->price;
        $item->stock = $request->stock;
        $item->save();

        return redirect('/items');
    }

    /**
     * 削除
     */
    public function delete(Request $request, $id)
    {
        $item = Item::where('id', '=', $id)->first();
        $item->delete();

        return redirect('/items');
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Type;

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
        // dd($items[0]->type);
        return view('item.index')->with([
            'items' => $items,
        ]);
    }

    /**
     * 商品登録
     */
    public function add(Request $request)
    {
        $type = Type::all();
        if (count($type) == 0) {
            return view('type.add');
        } else

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
                'type_id' => $request->type_id,
                'detail' => $request->detail,
                'price' => $request->price,
                'stock' => $request->stock,
            ]);

            return redirect('/items');
        }

        return view('item.add')->with([
            'type' => $type,
        ]);
    }

    /**
     * 編集ページ表示
     */
    public function edit($id)
    {
        $type = Type::all();

        $item = Item::where('id', '=', $id)->first();

        return view('item.edit')->with([
            'item' => $item,
            'type' => $type,
        ]);
    }

    /**
     * 編集登録
     */
    public function editregister(Request $request, $id)
    {
        $type = Type::all();

        $request->validate([
            'name' => 'required',
            'price' => 'nullable|integer',
            'stock' => 'nullable|integer',
        ]);

        $item = Item::where('id', '=', $id)->first();
        $item->name = $request->name;
        $item->type_id = $request->type_id;
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

    /**
     * 複数検索
     * 参考サイト:https://qiita.com/rentarouclass/items/23393b172f564290224f
     */
    public function search(Request $request)
    {
        $query = Item::query();
        if (!empty($request->input('keyword'))) {
            $search_split = mb_convert_kana($request->input('keyword'), 's');
            $search_split2 = preg_split('/[\s]+/', $search_split);
            foreach ($search_split2 as $keyword) {
                $query->Where('name', 'LIKE', "%$keyword%")
                ->orwhere('detail', 'LIKE', "%$keyword%")
                ->orwhere('price', 'LIKE', "%$keyword%")
                ->orwhere('stock', 'LIKE', "%$keyword%")
                ->orWhereHas('type', function ($query) use ($keyword){
                    $query->where('name', 'LIKE', "%$keyword%");
                });
            }
        }

        $items = $query->get();
        return view('item.index')->with([
                'items' => $items,
            ]);
    }
}

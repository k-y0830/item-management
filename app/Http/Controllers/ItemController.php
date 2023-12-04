<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Type;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Carbon\Carbon;
use Exception;
use Goodby\CSV\Import\Standard\LexerConfig;
use Goodby\CSV\Import\Standard\Lexer;
use Goodby\CSV\Import\Standard\Interpreter;

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

    /**
     * CSVインポート
     * 参考サイト：https://qiita.com/niconiconainu/items/bc8d0278bee99ae1f2ec
     */
    public function import(Request $request)
    {
        $item = new Item();
        // CSVファイルが存在するかの確認
        if ($request->hasFile('csvFile')) {
            //拡張子がCSVであるかの確認
            if ($request->csvFile->getClientOriginalExtension() !== "csv") {
                throw new Exception('不適切な拡張子です。');
            }
            //ファイルの保存
            $newCsvFileName = $request->csvFile->getClientOriginalName();
            $request->csvFile->storeAs('public/csv', $newCsvFileName);
        } else {
            throw new Exception('CSVファイルの取得に失敗しました。');
        }
        //保存したCSVファイルの取得
        $csv = Storage::disk('local')->get("public/csv/{$newCsvFileName}");
        // OS間やファイルで違う改行コードをexplode統一
        $csv = str_replace(array("\r\n", "\r"), "\n", $csv);
        // $csvを元に行単位のコレクション作成。explodeで改行ごとに分解
        $uploadedData = collect(explode("\n", $csv));
        $uploadedData->pop();
        // テーブルとCSVファイルのヘッダーの比較
        $header = collect($item->csvHeader());
        $uploadedHeader = collect(explode(",", $uploadedData->shift()));
        if ($header->count() !== $uploadedHeader->count()) {
            throw new Exception('Error:ヘッダーが一致しません');
        }
        // dd($uploadedData);
        // 連想配列のコレクションを作成
        //combine 一方の配列をキー、もう一方を値として一つの配列生成。haederをキーとして、一つ一つの$oneRecordと組み合わせて、連想配列のコレクション作成
        try {
            $items = $uploadedData->map(fn($oneRecord) => $header->combine(collect(explode(",", $oneRecord))));
        } catch (Exception $e) {
            throw new Exception('Error:ヘッダーが一致しません');
        }

        // アップロードしたCSVファイル内での重複チェック
        if ($items->duplicates("id")->count() > 0) {
            throw new Exception("Error:idの重複:" . $items->duplicates("id")->shift());
        }

        // 既存データとの重複チェック.pluckでDBに挿入したい$itemsのidのみ抽出
        $duplicateItem = DB::table('items')->whereIn('id', $items->pluck('id'))->get();
        // dd($duplicateItem);
        if ($duplicateItem->count() > 0) {
            throw new Exception("Error:idの重複:" . $duplicateItem->shift()->id);
        }
        // dd($items);
        // $itemsコレクションを配列にして、一括挿入
        // DB::table('items')->insert($items->toArray());

        foreach ($items as $item) {
            // dd($item);
            $i = new Item();
            $i->user_id = Auth::id();
            $i->name = $item['name'];
            $i->type_id = $item['type_id'];
            $i->detail = $item['detail'];
            $i->price = $item['price'];
            $i->stock = $item['stock'];
            $i->save();
        }
    }

    // public function import(Request $request)
    // {
    //     // https://blog.capilano-fw.com/?p=5022
    // }

    /**
     * CSVエクスポート
     * 参考サイト：https://your-school.jp/laravel-csv-download/293/
     */
    public function export(Request $request)
    {
        $items = Item::all();
        $now = Carbon::now();
        $csvHeader = [
            'id',
            'ユーザー',
            '名前',
            '種別',
            '詳細',
            '価格',
            '在庫数',
            '登録日',
            '更新日',
        ];
        $csvData = $items->toArray();

        $response = new StreamedResponse(function () use ($csvHeader, $csvData) {
            $handle = fopen('php://output', 'w');
            // 文字コードを変換して、文字化け回避
            stream_filter_prepend($handle, 'convert.iconv.utf-8/cp932//TRANSLIT');

            fputcsv($handle, $csvHeader);

            foreach ($csvData as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename='.$now->format('YmdHis').'.csv',
        ]);
        return $response;
    }
}

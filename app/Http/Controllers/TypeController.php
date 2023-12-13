<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Type;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TypeController extends Controller
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
     * 種別一覧
     */
    public function index(Request $request)
    {
        $pag_list = [
            0 => '5',
            1 => '10',
            2 => '100',
            3 => '200',
        ];

        $disp_list = $request->disp_list;

        if(empty($disp_list)) { // disp_list= が空値、またはURLになかった場合
            $disp_list = 5; // デフォルトの表示件数をセット
        }

        $type = Type::paginate($disp_list);

        return view('type.index')->with([
            'type' => $type,
            'pag_list' => $pag_list,
            'disp_list' => $disp_list,
        ]);
    }

    /**
     * 種別登録
     */
    public function add(Request $request)
    {
        // POSTリクエストのとき
        if ($request->isMethod('post')) {
            // バリデーション
            $this->validate($request, [
                'name' => 'required|max:100|unique:types,name',
            ]);

            // 種別登録
            Type::create([
                'user_id' => Auth::user()->id,
                'name' => $request->name,
            ]);

            return redirect('/type');
        }

        return view('type.add');
    }

    /**
     * 編集ページ表示
     */
    public function edit($id)
    {
        $type = Type::where('id', '=', $id)->first();

        return view('type.edit')->with([
            'type' => $type,
        ]);
    }

    /**
     * 編集登録
     */
    public function editregister(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:100|unique:types,name',
        ]);

        $type = Type::where('id', '=', $id)->first();
        $type->name = $request->name;
        $type->save();

        return redirect('/type');
    }

    /**
     * 削除
     */
    public function delete(Request $request, $id)
    {
        $items = Item::where('type_id', '=', $id)->get();

        $type = Type::where('id', '=', $id)->first();

        if (count($items) != 0) {
            echo '使用中のため削除出来ません';
        } else {
            $type->delete();
        }

        return redirect('/type');
    }

    /**
     * 検索
     */
    public function search(Request $request)
    {
        $pag_list = [
            0 => '5',
            1 => '10',
            2 => '100',
            3 => '200',
        ];

        $disp_list = $request->disp_list;

        if(empty($disp_list)) { // disp_list= が空値、またはURLになかった場合
            $disp_list = 5; // デフォルトの表示件数をセット
        }

        $query = Type::query();

        $keyword = $request->input('keyword');
        if (!empty($keyword)) {
            $search_split = mb_convert_kana($keyword, 's');
            $search_split2 = preg_split('/[\s]+/', $search_split);
            foreach ($search_split2 as $keyword) {
                $query->Where('name', 'LIKE', "%$keyword%");
            }
        }

        $type = $query->paginate(5);

        return view('type.index')->with([
                'type' => $type,
                'pag_list'=>$pag_list,
                'disp_list'=>$disp_list,
            ]);
    }

    /**
     * CSVエクスポート
     * 参考サイト：https://suzumura-tumiage.com/laravel/338/
     */
    public function export()
    {
        // ①HTTPヘッダーの設定
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment;'
        ];

        $fileName = Carbon::now()->format('YmdHis').'_typeList.csv';

        $callback = function()
        {
            // ②ストリームを作成してファイルに書き込めるようにする
            $stream = fopen('php://output', 'w');
            // ③CSVのヘッダ行の定義
            $head = [
                'id',
                '種別',
            ];
            // ④UTF-8からSJISにフォーマットを変更してExcelの文字化け対策
            mb_convert_variables('SJIS', 'UTF-8', $head);
            fputcsv($stream, $head);
            // ⑤データを取得してCSVファイルのデータレコードに顧客情報を挿入
            $types = Type::orderBy('id', 'asc');

            foreach ($types->cursor() as $type) {
                $data = [
                    $type->id,
                    $type->name,
                ];

                mb_convert_variables('SJIS', 'UTF-8', $data);
                fputcsv($stream, $data);
            }

            fclose($stream);
        };

        return response()->streamDownload($callback, $fileName, $headers);
    }
}

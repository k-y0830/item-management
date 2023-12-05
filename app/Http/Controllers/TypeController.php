<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Type;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;
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
    public function index()
    {
        $type = Type::all();

        return view('type.index')->with([
            'type' => $type,
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
                'name' => 'required|max:100',
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
            'name' => 'required',
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
        $type = Type::where('id', '=', $id)->first();
        $type->delete();

        return redirect('/type');
    }

    /**
     * 検索
     */
    public function search(Request $request)
    {
        $query = Type::query();
        if (!empty($request->input('keyword'))) {
            $search_split = mb_convert_kana($request->input('keyword'), 's');
            $search_split2 = preg_split('/[\s]+/', $search_split);
            foreach ($search_split2 as $keyword) {
                $query->Where('name', 'LIKE', "%$keyword%");
            }
        }

        $type = $query->get();

        return view('type.index')->with([
                'type' => $type,
            ]);
    }

    /**
     * CSVエクスポート
     * 参考サイト：https://your-school.jp/laravel-csv-download/293/
     */
    public function export(Request $request)
    {
        $items = Type::all();
        $now = Carbon::now();
        $csvHeader = [
            'id',
            '名前',
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

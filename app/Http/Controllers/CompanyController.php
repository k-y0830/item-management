<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Carbon\Carbon;

class CompanyController extends Controller
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
     * 業者一覧
     */
    public function index()
    {
        $company = Company::all();

        return view('company.index', compact('company'));
    }

    /**
     * 業者登録
     */
    public function add(Request $request)
    {
        // POSTリクエストのとき
        if ($request->isMethod('post')) {
            // バリデーション
            $this->validate($request, [
                'name' => 'required|max:100',
            ]);

            // 業者登録
            Company::create([
                'user_id' => Auth::user()->id,
                'name' => $request->name,
                'address' => $request->address,
                'tell' => $request->tell,
            ]);

            return redirect('/company');
        }

        return view('company.add');
    }

    /**
     * 編集ページ表示
     */
    public function edit($id)
    {
        $company = Company::where('id', '=', $id)->first();

        return view('company.edit')->with([
            'company' => $company,
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

        $company = Company::where('id', '=', $id)->first();
        $company->name = $request->name;
        $company->address = $request->address;
        $company->tell = $request->tell;
        $company->save();

        return redirect('/company');
    }

    /**
     * 削除
     */
    public function delete(Request $request, $id)
    {
        $company = Company::where('id', '=', $id)->first();
        $company->delete();

        return redirect('/company');
    }

    /**
     * 検索
     */
    public function search(Request $request)
    {
        $query = Company::query();
        if (!empty($request->input('keyword'))) {
            $search_split = mb_convert_kana($request->input('keyword'), 's');
            $search_split2 = preg_split('/[\s]+/', $search_split);
            foreach ($search_split2 as $keyword) {
                $query->Where('name', 'LIKE', "%$keyword%")
                ->orWhere('address', 'LIKE', "%$keyword%")
                ->orWhere('tell', 'LIKE', "%$keyword%");
            }
        }

        $company = $query->get();

        return view('company.index')->with([
                'company' => $company,
            ]);
    }

    /**
     * CSVエクスポート
     * 参考サイト：https://your-school.jp/laravel-csv-download/293/
     */
    public function export(Request $request)
    {
        $items = Company::all();
        $now = Carbon::now();
        $csvHeader = [
            'id',
            '名前',
            '住所',
            '電話番号',
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

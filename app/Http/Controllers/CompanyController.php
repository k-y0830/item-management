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
    public function index(Request $request)
    {
        $pag_list = [
            0 => '',
            1 => '5',
            2 => '10',
            3 => '100',
            4 => '200',
        ];

        $disp_list = $request->disp_list;

        if(empty($disp_list)) { // disp_list= が空値、またはURLになかった場合
            $disp_list = 5; // デフォルトの表示件数をセット
        }

        $company = Company::paginate($disp_list);

        return view('company.index')->with([
            'company' => $company,
            'pag_list' => $pag_list,
            'disp_list' => $disp_list,
        ]);
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
     * 参考サイト：https://suzumura-tumiage.com/laravel/338/
     */
    public function export()
    {
        // ①HTTPヘッダーの設定
        $headers = [
            'Content-company' => 'text/csv',
            'Content-Disposition' => 'attachment;'
        ];

        $fileName = Carbon::now()->format('YmdHis').'_companyList.csv';

        $callback = function()
        {
            // ②ストリームを作成してファイルに書き込めるようにする
            $stream = fopen('php://output', 'w');
            // ③CSVのヘッダ行の定義
            $head = [
                'id',
                '名前',
                '住所',
                '電話番号',
            ];
            // ④UTF-8からSJISにフォーマットを変更してExcelの文字化け対策
            mb_convert_variables('SJIS', 'UTF-8', $head);
            fputcsv($stream, $head);
            // ⑤データを取得してCSVファイルのデータレコードに顧客情報を挿入
            $companies = Company::orderBy('id', 'asc');

            foreach ($companies->cursor() as $company) {
                $data = [
                    $company->id,
                    $company->name,
                    $company->address,
                    $company->tell,
                ];

                mb_convert_variables('SJIS', 'UTF-8', $data);
                fputcsv($stream, $data);
            }

            fclose($stream);
        };

        return response()->streamDownload($callback, $fileName, $headers);
    }
}

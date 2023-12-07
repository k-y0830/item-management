<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class OrderController extends Controller
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
     * 発注一覧
     * ページネーション：https://qiita.com/dong5588/orders/c1074fb5c608de6ed749
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

        // 発注一覧取得
        $orders = Order::paginate($disp_list);

        // ★company_idが取得できていない？Myadminには登録あり
        // dd($orders);
        return view('order.index')->with([
            'orders' => $orders,
            'pag_list' => $pag_list,
            'disp_list' => $disp_list,
        ]);
    }

    /**
     * 発注登録
     */
    public function add(Request $request)
    {
        $company = Company::all();
        if (count($company) == 0) {
            return view('company.add');
        } else {
            // POSTリクエストのとき
            if ($request->isMethod('post')) {
                // バリデーション
                $this->validate($request, [
                    'company_id' => 'required',
                    'order_item' => 'required',
                    'order' => 'required|integer',
                ]);

                // 発注登録
                Order::create([
                    'user_id' => Auth::user()->id,
                    'company_id' => $request->company_id,
                    'order_item' => $request->order_item,
                    'order' => $request->order,
                ]);

                return redirect('/orders');
            }
        }
        return view('order.add')->with([
            'company' => $company,
        ]);
    }

    /**
     * 編集ページ表示
     */
    public function edit($id)
    {
        $company = Company::all();

        $order = Order::where('id', '=', $id)->first();

        return view('order.edit')->with([
            'order' => $order,
            'company' => $company,
        ]);
    }

    /**
     * 編集登録
     */
    public function editregister(Request $request, $id)
    {
        $company = Company::all();

        $request->validate([
            'oeder' => 'required',
        ]);

        $order = Order::where('id', '=', $id)->first();
        $order->order_stock = $request->order_stock;
        $order->company_id = $request->company_id;
        $order->order = $request->order;
        $order->save();

        return redirect('/orders');
    }

    /**
     * 削除
     */
    public function delete(Request $request, $id)
    {
        $order = Order::where('id', '=', $id)->first();
        $order->delete();

        return redirect('/orders');
    }

    /**
     * 複数検索
     * 参考サイト:https://qiita.com/EasyCoder/orders/83475abb6d6acb3a177f
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

        /* テーブルから全てのレコードを取得する */
        $query = Order::query();

        /* キーワードから検索処理 */
        $keyword = $request->input('keyword');
        if(!empty($keyword)) {
            $search_split = mb_convert_kana($request->input('keyword'), 's');
            $search_split2 = preg_split('/[\s]+/', $search_split);
            foreach ($search_split2 as $keyword) {
                $query->where('order', 'LIKE', "%{$keyword}%")
                ->orwhereHas('company', function ($query) use ($keyword) {
                    $query->where('name', 'LIKE', "%{$keyword}%");
                })->get();
            }
        }

        /* ページネーション */
        $orders = $query->paginate(5);

        return view('order.index', ['orders' => $orders,'pag_list'=>$pag_list,'disp_list'=>$disp_list,]);
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

        $fileName = Carbon::now()->format('YmdHis').'_orderList.csv';

        $callback = function()
        {
            // ②ストリームを作成してファイルに書き込めるようにする
            $stream = fopen('php://output', 'w');
            // ③CSVのヘッダ行の定義
            $head = [
                'id',
                'ユーザー',
                '在庫数',
                '業者',
                '発注数',
            ];
            // ④UTF-8からSJISにフォーマットを変更してExcelの文字化け対策
            mb_convert_variables('SJIS', 'UTF-8', $head);
            fputcsv($stream, $head);
            // ⑤データを取得してCSVファイルのデータレコードに顧客情報を挿入
            $orders = Order::orderBy('id', 'asc');

            foreach ($orders->cursor() as $order) {
                $data = [
                    $order->id,
                    $order->user->name,
                    $order->item_stock,
                    $order->company->name,
                    $order->order,
                ];

                mb_convert_variables('SJIS', 'UTF-8', $data);
                fputcsv($stream, $data);
            }

            fclose($stream);
        };

        return response()->streamDownload($callback, $fileName, $headers);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Type;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        return view('type.index', compact('type'));
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
}

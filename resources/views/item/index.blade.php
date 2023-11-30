@extends('adminlte::page')

@section('title', '商品一覧')

@section('content_header')
    <h1>商品一覧</h1>
@stop

@section('content')
    <div class="row">
            <!-- 検索 -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"></h3>
                        <div class="card-tools">
                            <div class="input-group input-group-sm">
                                <div class="input-group-append">
                                    <form action="{{ url('items/search') }}" method="GET">
                                        @csrf
                                        <input type="search" name="keyword" placeholder="キーワード    複数検索可" class="searchbox">
                                        <button type="submit" class="btn btn-default">検索</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 商品一覧 -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"></h3>
                        <div class="card-tools">
                            <div class="input-group input-group-sm">
                                <div class="input-group-append">
                                    <a href="{{ url('items/add') }}" class="btn btn-default">商品登録</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>名前</th>
                                    <th>種別</th>
                                    <th>詳細</th>
                                    <th>価格</th>
                                    <th>在庫数</th>
                                    <th>&nbsp;</th>
                                    <th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- TODO:IF文追加 -->
                                <!-- @if (!isset($items))
                                    <h3>登録商品なし</h3>
                                @else -->
                                @foreach ($items as $item)
                                    <tr>
                                        <td>{{ $item->id }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->type }}</td>
                                        <td>{{ $item->detail }}</td>
                                        <td>{{ $item->price }}</td>
                                        <td>{{ $item->stock }}</td>
                                        <td>
                                            <a href="{{ url('items/edit').$item->id }}">
                                                <button class="btn btn-default">編集</button>
                                            </a>
                                        </td>
                                        <td>
                                            <form action="{{ url('/items/delete').$item->id }}" method="post" onsubmit="return window.confirm('削除しますか？')">
                                                @csrf
                                                <button type="submit" class="btn btn-default" id="deletebtn">削除</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                <!-- @endif -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
@stop

@section('css')
<style>
    /* TODO:メディアスクリーン */
    .searchbox{
        width: 800px;
        margin-right: 100px;
    }
</style>
@stop

@section('js')
@stop

@extends('adminlte::page')

@section('title', '種別一覧')

@section('content_header')
    <h1>種別一覧</h1>
@stop

@section('content')
    <div class="row">
        <!-- 検索 -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"></h3>
                    <div class="searchcard-tools">
                        <div class="searchinput-group">
                            <div class="searchinput-group-append">
                                <form action="{{ url('type/search') }}" method="GET" class="search-form">
                                    @csrf
                                    <input type="search" name="keyword" placeholder="キーワード    複数検索可" class="search-box">
                                    <button type="submit" class="btn btn-default search-btn">検索</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CSV -->
        <form method="GET" action="{{ url('type/export') }}" class="export-btn">
            <button type="submit" class="btn btn-default">CSVダウンロード</button>
        </form>

        <!-- 種別一覧 -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"></h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm">
                            <div class="input-group-append">
                                <a href="{{ url('type/add') }}" class="btn btn-default">種別登録</a>
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
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($type as $type)
                                <tr>
                                    <td>{{ $type->id }}</td>
                                    <td>{{ $type->name }}</td>
                                    <td>
                                        <a href="{{ url('type/edit').$type->id }}">
                                            <button class="btn btn-default">編集</button>
                                        </a>
                                    </td>
                                    <td>
                                        <form action="{{ url('/type/delete').$type->id }}" method="post" onsubmit="return window.confirm('削除しますか？')">
                                            @csrf
                                            <button type="submit" class="btn btn-default" id="deletebtn">削除</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
<style>
    .searchcard-tools {
        width: 100%;
    }
    .searchinput-group {
        width: 100%;
    }
    .searchinput-group-append {
        width: 100%;
    }
    .search-box {
        display: inline-block;
        width: 75%;
        padding: 0.5em;
        border: 1px solid #999;
        box-sizing: border-box;
        background: #f2f2f2;
        margin: 0.5em;
    }
    .search-btn {
        float: right;
        margin-top: 0.8em;
    }
    .export-btn {
        float: right;
        margin-left: auto;
        padding-bottom: 0.5em;
    }
</style>
@stop

@section('js')
@stop

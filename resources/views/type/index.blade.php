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
                                    <input type="search" name="keyword" placeholder="キーワード" class="search-box">
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
                            @foreach ($type as $val)
                                <tr>
                                    <td>{{ $val->id }}</td>
                                    <td>{{ $val->name }}</td>
                                    <td>
                                        <a href="{{ url('type/edit').$val->id }}">
                                            <button class="btn btn-default">編集</button>
                                        </a>
                                    </td>
                                    <td>
                                        @if (count($val->items) == 0)
                                        <form action="{{ url('/type/delete').$val->id }}" method="post" onsubmit="return window.confirm('削除しますか？')">
                                            @csrf
                                            <button type="submit" class="btn btn-default" id="deletebtn">削除</button>
                                        </form>
                                        @else
                                            <p>※マスタ使用中</p>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

       <!-- 件数表示 -->
       <div class="number-form-group">
                <div class="number-count mb-2 xl:w-66">
                @if (count($type) >0)
                    <p>全{{ $type->total() }}件中 
                        {{  ($type->currentPage() -1) * $type->perPage() + 1}} - 
                        {{ (($type->currentPage() -1) * $type->perPage() + 1) + (count($type) -1)  }}件
                    </p>
                @else
                    <p>データがありません。</p>
                @endif 
                    <form action="{{ url('type') }}" method="get" class="number-select">
                        <label data-te-select-label-ref>表示件数：</label>
                        <select data-te-select-init  id="disp_list" name="disp_list" value="{{ old('disp_list') }}" onchange="submit();">
                            @foreach($pag_list as $key => $val)
                                @if ($val === $disp_list)
                                    <option value="{{ $val }}" selected >{{ $val }}</option>
                                @else
                                    <option value="{{ $val }}">{{ $val }}</option>
                                @endif
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>
            <div class="page">
                {{ $type->appends(request()->query())->links() }}
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
    .number-form-group {
        width: 100%;
        display: flex;
    }
    .number-count {
        width: 100%;
        display: flex;
    }
    .number-count p {
        margin-left: 0.5em;
    }
    .number-select {
        float: right;
        margin-left: auto;
        margin-right: 0.5em;
        padding-bottom: 0.5em;
    }
    .page {
        width: 100%;
        display: flex;
        justify-content: space-around;
    }
</style>
@stop

@section('js')
@stop

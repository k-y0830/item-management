@extends('adminlte::page')

@section('title', '商品登録')

@section('content_header')
    <h1>商品登録</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-10">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                       @foreach ($errors->all() as $error)
                          <li>{{ $error }}</li>
                       @endforeach
                    </ul>
                </div>
            @endif

            <!-- CSVinport -->
            <!-- <div class="card card-primary">
                <form action="{{ url('items/import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label name="csvFile">CSVファイル</label>
                            <input type="file" name="csvFile" class="" id="csvFile">
                            <input type="submit">
                        </div>
                    </div>
                </form>
            </div> -->

            <div class="card card-primary">
                <form method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">名前</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}"placeholder="名前">
                        </div>

                        <!-- 種別一覧でforeach -->
                        <div class="form-group">
                            <label for="type">種別</label><br>
                                <select name="type_id" class="form-control">
                                    <option  value="" selected></option>
                                    @foreach ($type as $val)
                                        @if (!is_null(old('type_id')))
                                            <!-- バリデーションエラー等による再表示時 -->
                                            @if ($val->id == old('type_id'))
                                                <option  value="{{ $val->id }}" selected>{{ $val->name }}</option>
                                            @else
                                                <option  value="{{ $val->id }}">{{ $val->name }}</option>
                                            @endif
                                        @else
                                            <!-- 初期表示時 -->
                                            <option  value="{{ $val->id }}" @if( old($val->id) == $val->id ) selected @endif>{{ $val->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                        </div>

                        <div class="form-group">
                            <label for="detail">詳細</label>
                            <input type="text" class="form-control" id="detail" name="detail" value="{{ old('detail') }}" placeholder="詳細説明">
                        </div>

                        <div class="form-group">
                            <label for="price">価格</label>
                            <input type="text" class="form-control" id="price" name="price" value="{{ old('price') }}"placeholder="価格">
                        </div>

                        <div class="form-group">
                            <label for="stock">在庫数</label>
                            <input type="text" class="form-control" id="stock" name="stock" value="{{ old('stock') }}"placeholder="在庫数">
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">登録</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('css')
@stop

@section('js')
@stop

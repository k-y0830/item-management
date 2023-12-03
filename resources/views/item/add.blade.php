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
            <div class="card card-primary">
                <form action="{{ url('items/inport') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label name="csvFile">CSVファイル</label>
                            <input type="file" name="csvFile" class="" id="csvFile">
                            <input type="submit">
                        </div>
                    </div>
                </form>
            </div>

            <div class="card card-primary">
                <form method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">名前</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="名前">
                        </div>

                        <!-- 種別一覧でforeach -->
                        <div class="form-group">
                            <label for="type">種別</label><br>
                                <select name="type_id" class="form-control">
                                @foreach($type as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                                </select>
                        </div>

                        <div class="form-group">
                            <label for="detail">詳細</label>
                            <input type="text" class="form-control" id="detail" name="detail" placeholder="詳細説明">
                        </div>

                        <div class="form-group">
                            <label for="price">価格</label>
                            <input type="text" class="form-control" id="price" name="price" placeholder="価格">
                        </div>

                        <div class="form-group">
                            <label for="stock">在庫数</label>
                            <input type="text" class="form-control" id="stock" name="stock" placeholder="在庫数">
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

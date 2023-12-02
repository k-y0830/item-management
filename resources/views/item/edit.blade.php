@extends('adminlte::page')

@section('title', '商品編集')

@section('content_header')
    <h1>商品編集</h1>
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

            <div class="card card-primary">
                <form method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">名前</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ $item->name }}">
                        </div>

                        <!-- 種別一覧でforeach -->
                        <div class="form-group">
                            <label for="type">種別</label><br>
                                <select name="type_id" class="form-control">
                                @foreach($type as $type)
                                    <option value="{{ $type->id }}"  @if($type->id==$item->type_id) selected @endif>{{ $type->name }}</option>
                                @endforeach
                                </select>
                        </div>

                        <div class="form-group">
                            <label for="detail">詳細</label>
                            <input type="text" class="form-control" id="detail" name="detail" value="{{ $item->detail }}">
                        </div>

                        <div class="form-group">
                            <label for="detail">価格</label>
                            <input type="text" class="form-control" id="price" name="price" value="{{ $item->price }}">
                        </div>

                        <div class="form-group">
                            <label for="detail">在庫数</label>
                            <input type="text" class="form-control" id="stock" name="stock" value="{{ $item->stock }}">
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

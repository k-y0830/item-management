@extends('adminlte::page')

@section('title', '発注編集')

@section('content_header')
    <h1>発注編集</h1>
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

                        <!-- 業者一覧でforeach -->
                        <div class="form-group">
                            <label for="company">業者</label><br>
                                <select name="company_id" class="form-control">
                                @foreach($company as $val)
                                    <option value="{{ $val->id }}"  @if($val->id==$item->type_id) selected @endif>{{ $val->name }}</option>
                                @endforeach
                                </select>
                        </div>

                        <div class="form-group">
                            <label for="order">発注数</label>
                            <input type="text" class="form-control" id="detail" name="detail" value="{{ old('detail') }}" placeholder="詳細説明">
                        </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">登録</button>
                    </div>
            </div>
        </div>
    </div>
@stop

@section('css')
@stop

@section('js')
@stop

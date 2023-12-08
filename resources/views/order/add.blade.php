@extends('adminlte::page')

@section('title', '発注登録')

@section('content_header')
    <h1>発注登録</h1>
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
                                    @foreach ($company as $val)
                                        @if (!is_null(old('company_id')))
                                            <!-- バリデーションエラー等による再表示時 -->
                                            @if ($val->id == old('company_id'))
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

                        <!-- 商品一覧でforeach -->
                        <div class="form-group">
                            <label for="item">商品</label><br>
                                <select name="item_id" class="form-control">
                                    @foreach ($item as $val)
                                        @if (!is_null(old('item_id')))
                                            <!-- バリデーションエラー等による再表示時 -->
                                            @if ($val->id == old('item_id'))
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
                            <label for="order">発注数</label>
                            <input type="text" class="form-control" id="detail" name="order" value="{{ old('order') }}" placeholder="">
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

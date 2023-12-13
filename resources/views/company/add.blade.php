@extends('adminlte::page')

@section('title', '業者登録')

@section('content_header')
    <h1>業者登録</h1>
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
                            <input type="text" class="form-control" id="name" name="name" placeholder="名前">
                        </div>
                        <div class="form-group">
                            <label for="address">住所</label>
                            <input type="text" class="form-control" id="address" name="address" placeholder="住所">
                        </div>
                        <div class="form-group">
                            <label for="tell">電話番号</label>
                            <input type="text" class="form-control" id="tell" name="tell" placeholder="電話番号">
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

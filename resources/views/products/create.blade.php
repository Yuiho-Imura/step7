@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">商品新規登録</h1>

    <a href="{{ route('products.index') }}" class="btn btn-primary mb-3">商品一覧に戻る</a>

    <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">

        @csrf



        <div class="mb-3">
            <label for="product_name" class="form-label">商品名<span style="color: red;">*</span></label>
            <input id="product_name" type="text" name="product_name" class="form-control" required>
            @if($errors->has('product_name'))
        <p class="text-danger">{{ $errors->first('product_name') }}</p>
    @endif
        </div>

        <div class="mb-3">
            <label for="company_id" class="form-label">メーカー<span style="color: red;">*</span></label>
            <select class="form-select" id="company_id" name="company_id">
                @foreach($companies as $company)
                    <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                @endforeach
            </select>
            @if($errors->has('company_id'))
        <p class="text-danger">{{ $errors->first('company_id') }}</p>
            @endif
        </div>

        <div class="mb-3">
            <label for="price" class="form-label">価格<span style="color: red;">*</span></label>
            <input id="price" type="text" name="price" class="form-control" required>
            @if($errors->has('price'))
        <p class="text-danger">{{ $errors->first('price') }}</p>
    @endif
        </div>

        <div class="mb-3">
            <label for="stock" class="form-label">在庫数<span style="color: red;">*</span></label>
            <input id="stock" type="text" name="stock" class="form-control" required>
            @if($errors->has('stock'))
        <p class="text-danger">{{ $errors->first('stock') }}</p>
    @endif
        </div>

        <div class="mb-3">
            <label for="comment" class="form-label">コメント</label>
            <textarea id="comment" name="comment" class="form-control" rows="3" ></textarea>
        </div>

        <div class="mb-3">
            <label for="img_path" class="form-label">商品画像</label>
            <input id="img_path" type="file" name="img_path" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">登録</button>
    </form>


</div>
@endsection


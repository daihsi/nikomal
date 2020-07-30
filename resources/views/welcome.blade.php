@extends('layouts.app')

@section('content')
@guest
    @include('commons.guest_toppage')
@endguest

    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-11">
                <form>
                    <div class="form-row justify-content-center justify-content-md-end">
                        <div class="form-group col-xl-3 col-lg-4 col-md-5 col-sm-7 col-8">
                          <input class="form-control" type="search" placeholder="キーワード">
                        </div>
                    </div>
                    <div class="form-row justify-content-center justify-content-md-end">
                        <div class="form-group col-lg-2 col-md-3 col-sm-4 col-5 pr-3">
                            <select id="animals-select" class="form-control">
                                <option value="">動物カテゴリー</option>
                                <option value="イヌ">イヌ</option>
                                <option value="ネコ">ネコ</option>
                                <option value="ゾウ">ゾウ</option>
                                <option value="サル">サル</option>
                            </select>
                        </div>
                        <div>
                            <button type="submit" class="btn btn-outline-success"><i class="fas fa-search fa-lg"></i>検索する</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    @include('posts.posts')

@endsection
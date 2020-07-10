@extends('layouts.app')

@section('content')
    <div class="container-fluid">
    @guest
        <div class="row justify-content-center">
            <div class="col-6">
                 <div id="carouselExampleFade" class="carousel slide carousel-fade" data-intrebal=4000 data-touch=true data-ride="carousel">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img class="animalsCarousel" src="{{ asset('storage/images/smile4.jpg') }}" alt="イヌの笑顔"> 
                        </div>
                        <div class="carousel-item">
                            <img class="animalsCarousel" src="{{ asset('storage/images/smile3.jpg') }}" alt="フクロウの笑顔">
                        </div>
                        <div class="carousel-item">
                            <img class="animalsCarousel" src="{{ asset('storage/images/smile7.jpg') }}" alt="イヌの笑顔">
                        </div>
                        <div class="carousel-item">
                            <img class="animalsCarousel" src="{{ asset('storage/images/smile8.jpg') }}" alt="ネコの笑顔">
                        </div>
                        <div class="carousel-item">
                            <img class="animalsCarousel" src="{{ asset('storage/images/smile1.jpg') }}" alt="ゾウの笑顔">
                        </div>
                    </div>
                     <a class="carousel-control-prev" href="#carouselExampleFade" role="button" data-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="carousel-control-next" href="#carouselExampleFade" role="button" data-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
  
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 text-center">
                <h2 class="font-weight-bold mt-4 mb-4">
                    <p>さぁ、動物たちの笑っている表情を</p>
                    <p>投稿共有して一緒に癒されましょう</p>
                </h2>
                <a class="register_btn btn btn btn-lg" href="#">投稿する</a>
                <a class="introduction_btn btn btn btn-lg" href="#">はじめに</a>
            </div>
        </div>
    </div>
    
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-11">
                <div class="border mb-5 mt-5"></div>
            </div>
        </div>
    </div>

    <h3 class="text-center font-weight-bold mt-5 mb-4">みんなの投稿</h3>
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

@endsection
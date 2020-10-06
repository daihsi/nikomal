<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10 col-10">
             <div id="carouselExampleFade" class="carousel slide carousel-fade" data-intrebal=4000 data-touch=true data-ride="carousel">
                <div class="carousel-inner shadow p-2 mb-3 bg-white rounded">
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

<!-- 見出し -->
<div class="text-center">
    <h4 class="font-weight-bold mb-4 top_headline">
        さぁ、動物たちの笑っている表情を<br>
        投稿共有して一緒に癒されましょう
    </h4>
</div>
<div class="text-center">
    <a class="btn btn-outline-success mr-2" href="{{ route('register') }}">{{ __('Register') }}</a>
    <a class="btn btn-outline-success" href="{{ route('login') }}">{{ __('Login') }}</a>
</div>

<!-- ゲストユーザーログイン -->
<form method="POST" action="{{ route('login') }}" accept-charset="UTF-8" class="text-center mt-3">
    @csrf
    <input type="hidden" name="email" value="guest@example.com">
    <input type="hidden" name="password" value="guest123456789">
    <button type="submit" class="btn btn-warning">
        かんたんログイン
    </button>
</form>
<div class="text-center mt-2">
    <small style="color: rgba(0, 0, 0, 0.5); font-size: 11px;">
        ※ かんたんログインはお試しユーザーログイン用です。<br>
          メールアドレス等入力せず、機能をお試しいただけます。
    </small>
</div>

<!-- ボーダー -->
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="border mb-5 mt-5"></div>
        </div>
    </div>
</div>

<!-- 投稿見出し -->
<h5 class="text-center font-weight-bold mb-4 top_headline">みんなの投稿</h5>

@if(Auth::check() && !Gate::allows('admin'))
    <button type="button" class="like_button btn btn 
        {{ Auth::user()->isLike($post->id)? 'like_now_icon fas fa-heart' : 'like_icon far fa-heart' }} 
        fa-lg" data-id="{{ $post->id }}">
    </button>
    <span class="align-self-end post_count">{{ $post->likes->count() }}</span>
@else
    <div class="d-flex align-items-center">
        <i class="far fa-heart fa-lg pr-2" style="color: #BBBBBB;"></i>
        <span class="align-self-end">{{ $post->likes->count() }}</span>
    </div>
@endif
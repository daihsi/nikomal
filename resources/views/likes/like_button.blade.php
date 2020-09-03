@if(Auth::check())
    @if(Auth::user()->isLike($post->id))
        {{-- いいねを外すボタン --}}
        <form method="POST" action="{{ route('posts.unlike', $post->id) }}">
            @method('DELETE')
            @csrf
            <button type="submit" class="btn btn like_now_button fas fa-heart fa-lg"></button>
            <span class="align-self-end">{{ $post->likes->count() }}</span>
        </form>
    @else
        {{-- いいねするボタン --}}
        <form method="POST" action="{{ route('posts.like', $post->id) }}">
            @csrf
            <button type="submit" class="btn btn like_button far fa-heart fa-lg"></button>
            <span class="align-self-end">{{ $post->likes->count() }}</span>
        </form>
    @endif
@else
    <div class="d-flex align-items-center">
        <i class="far fa-heart fa-lg pr-2" style="color: #BBBBBB;"></i>
        <span class="align-self-end">{{ $post->likes->count() }}</span>
    </div>
@endif
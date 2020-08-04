@if(Auth::check())
    @if(Auth::user()->isLike($post->id))
        {{-- いいねを外すボタン --}}
        <form method="POST" action="{{ route('posts.unlike', $post->id) }}">
            @method('DELETE')
            @csrf
            <button type="submit" class="btn btn like_now_button fas fa-heart fa-lg"></button>
            <spnn class="align-self-end">{{ $post->likeUsers->count() }}</spnn>
        </form>
    @else
        {{-- いいねするボタン --}}
        <form method="POST" action="{{ route('posts.like', $post->id) }}">
            @csrf
            <button type="submit" class="btn btn like_button far fa-heart fa-lg"></button>
            <spnn class="align-self-end">{{ $post->likeUsers->count() }}</spnn>
        </form>
    @endif
@else
    <div class="d-flex justify-content-end">
        <i class="far fa-heart fa-lg pr-2" style="color: #BBBBBB;"></i>
        <spnn class="align-self-end">{{ $post->likeUsers->count() }}</spnn>
    </div>
@endif
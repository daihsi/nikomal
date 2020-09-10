<div class="card shadow p-2 mb-5 bg-white rounded show_post_card">
    <div class="card-header">
        @empty($post->user->avatar)
            <a href="{{ route('users.show', $post->user->id) }}" class="text-dark font-weight-bold"><img src="{{ asset('storage/images/default_icon.png') }}" class="rounded-circle mr-2" width="40" height="40">{{ $post->user->name }}</a>
        @else
            <a href="{{ route('users.show', $post->user->id) }}" class="text-dark font-weight-bold"><img src="{{ $post->user->avatar }}" class="rounded-circle mr-2" width="40" height="40">{{ $post->user->name }}</a>
        @endempty
    </div>
    <div class="card-body">
        @foreach($post_images as $post_image)
            <img src="{{ $post_image->image }}" class="rounded" style="width:100%; max-height: 700px;"
                @foreach ($post->postCategorys as $post_category) @php $animals_name = 'animals_name'. $post->id; $$animals_name[] = $post_category->name; @endphp @endforeach alt="{{ implode('・', $$animals_name). 'の笑顔写真' }}">
        @endforeach
    </div>
    <div class="card-footer">
        <p class="card-text">{!! nl2br(e($post->content)) !!}</p>
            @foreach($post->postCategorys as $post_category)
                <a href="{{ route('posts.categorys', $post_category->id) }}">
                    <span class="p_category"><i class="fas fa-hashtag p_hash"></i>{{ $post_category->name }}</span>
                </a>
            @endforeach
        <div class="d-flex justify-content-end mt-3">
            @if(Auth::id() === $post->user_id)
                <a href="{{ route('posts.edit', $post->id) }}" class="d-block justify-content-start"><button class="btn btn-outline-success btn-sm rounded-pill fas fa-edit mt-1">編集</button></a>
                <form method="POST" action="{{ route('posts.destroy', $post->id) }}" class="mr-auto" id="delete_form">
                    @method('DELETE')
                    @csrf
                    <button type="button" class="btn btn-danger btn-sm rounded-pill fas fa-trash-alt mt-1 ml-1 delete_alert">削除</button>
                </form>
            @endif 
            {{-- いいねボタン --}}
            @include('likes.like_button')
        </div>
        <div class="d-flex justify-content-end mt-2">
            <small>{{ $post->created_at->format('Y/m/d H:i') }}</small>
        </div>
    </div>
</div>

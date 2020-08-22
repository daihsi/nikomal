<div class="row justify-content-center">
    <div class="col-xl-9 col-lg-12 col-md-9 col-12">
        <div class="new_comment_box">
            <span class="new_comment_title">コメント入力</span>
            <form method="POST" action="{{ route('posts.comment') }}" accept-charset="UTF-8" class="mt-3">
                @csrf
                <input type="hidden" name="post_id" value="{{ $post->id }}">
                <div class="form-group">
                    <textarea id="comment" name="comment" class="form-control post_comment @error('comment') is-invalid @enderror" autocomplete="comment" placeholder="150字以下で入力してください" rows=8 maxlength="150" autofocus required>{{ old('comment') }}</textarea>
                    @error('comment')
                        <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group mb-2 text-center">
                    <button type="submit" class="btn btn-outline-success mt-2">コメントする</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="row justify-content-center">
    <div class="col-11">
        <div class="comment_heading">コメント一覧</div>
    </div>
</div>
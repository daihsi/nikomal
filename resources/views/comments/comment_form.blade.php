<div class="row justify-content-center">
    <div class="col-xl-9 col-lg-12 col-md-9 col-12">
        <div class="new_comment_box">
            <span class="new_comment_title">コメント入力</span>
            <input type="hidden" name="post_id" id="post_id" value="{{ $post->id }}">
            <div class="form-group mt-3" id="comment_msg_error">
                <textarea id="comment" name="comment" class="form-control post_comment" autocomplete="comment" placeholder="150字以下で入力してください" rows=8 maxlength="150" autofocus required>{{ old('comment') }}</textarea>
            </div>
            <div class="form-group mb-2 text-center">
                <button type="button" class="btn btn-outline-success mt-2 comment_button">コメントする</button>
            </div>
        </div>
    </div>
</div>
<div class="row justify-content-center">
    <div class="col-11">
        <div class="comment_heading">コメント一覧</div>
    </div>
</div>
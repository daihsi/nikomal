<div class="container mb-5">
    <div class="row justify-content-center">
        <div class="col-sm-12 col-11">
            <div class="posts_search_box">
                <form method="GET" action="{{ route('posts.search') }}" accept-charset="UTF-8">
                    <div class="row">
                        <div class="col-md-7">
                            <div class="form-group">
                                <label for="keyword">キーワード</label>
                                <input type="search" name="keyword" value="{{ old('keyword', $keyword ?? null) }}" class="search_text form-control @error('keyword') is-invalid @enderror" placeholder="150字以下で入力してください" maxlength="150" autofocus>

                                @error('keyword')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="animals_name">動物カテゴリー</label>
                                <select name="animals_name[]" id="animals_search" class="search_animals form-control @error('animals_name') is-invalid @enderror" size="5" autofocus multiple>
                                    @foreach(config('animals.animals_optgroup') as $number => $attribute)
                                        <optgroup label="{{ $attribute }}">
                                            @foreach(config('animals.animals'. $number) as $index => $name)
                                                <option value="{{ $index }}"
                                                {{ collect(old('animals_name', $animals_name ?? null))->contains($index) ? 'selected' : '' }}
                                                >{{ $name }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>

                                @error('animals_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-center mb-1">
                        <div class="col-sm-3 col-6">
                            <button type="submit" class="btn btn-success btn-block p_search_button">検索する</button>
                        </div>
                        <div class="col-lg-2 col-sm-3 col-6">
                            <button type="button" class="btn btn-secondary btn-block s_reset_button">リセット</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
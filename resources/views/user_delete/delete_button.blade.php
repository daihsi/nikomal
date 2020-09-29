@can('admin')
    @if(Auth::id() != $user->id && $user->cant('guest_login_user'))
        <form method="POST" action="{{ route('users.destroy', $user->id) }}" accept-charset="UTF-8" class="user_delete_alert">
            @method('DELETE')
            @csrf
            <div>
                <button type="button" class="btn btn-outline-danger rounded-pill fas fa-user-slash">
                    削除
                </button>
            </div>
        </form>
    @endif
@endcan
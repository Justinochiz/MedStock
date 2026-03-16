<form action="{{ route('users.update', $model->id) }}" method="POST" class="d-flex align-items-center gap-1 flex-wrap">
    @csrf
    <select name="role" class="form-select form-select-sm" style="width:90px;">
        <option value="user" @selected($model->role === 'user')>User</option>
        <option value="admin" @selected($model->role === 'admin')>Admin</option>
    </select>
    <select name="is_active" class="form-select form-select-sm" style="width:105px;">
        <option value="1" @selected($model->is_active == 1)>Active</option>
        <option value="0" @selected($model->is_active == 0)>Inactive</option>
    </select>
    <button type="submit" class="btn btn-primary btn-sm">Save</button>
</form>

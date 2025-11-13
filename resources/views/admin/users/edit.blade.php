{{-- resources/views/admin/users/edit.blade.php --}}
@extends('layouts.app')
@section('content')
<div class="container py-4">
    <h3 class="mb-3">Edit User</h3>
    <div class="card p-4">
        <form method="POST" action="{{ route('admin.users.update',$user) }}">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label">Nama</label>
                <input name="name" class="form-control" required value="{{ old('name',$user->name) }}">
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input name="email" type="email" class="form-control" required value="{{ old('email',$user->email) }}">
            </div>
            <div class="mb-3">
                <label class="form-label">Role</label>
                <select name="role" class="form-control" required>
                    <option value="user"  {{ $user->role=='user'?'selected':'' }}>user</option>
                    <option value="admin" {{ $user->role=='admin'?'selected':'' }}>admin</option>
                </select>
            </div>
            <div class="text-end">
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Batal</a>
                <button class="btn btn-dark">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection

{{-- resources/views/admin/users/create.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h3 class="mb-3">Tambah User</h3>

    {{-- Tampilkan error validasi --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card p-4">
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label">Username</label>
                <input name="username" class="form-control" required value="{{ old('username') }}" autocomplete="off">
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input name="email" type="email" class="form-control" required value="{{ old('email') }}">
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input name="password" type="password" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Role</label>
                <select name="role" class="form-control" required>
                    <option value="user" {{ old('role')==='user' ? 'selected' : '' }}>user</option>
                    <option value="admin" {{ old('role')==='admin' ? 'selected' : '' }}>admin</option>
                </select>
            </div>

            <div class="text-end">
                <a href="{{ route('admin.users.index') }}" class="btn btn-dark">Batal</a>
                <button class="btn btn-success">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

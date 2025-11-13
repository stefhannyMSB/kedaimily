{{-- resources/views/admin/users/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h3 class="mb-3 text-success fw-bold">Control User</h3>

    {{-- ALERT --}}
    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if(session('deleted')) <div class="alert alert-success">{{ session('deleted') }}</div> @endif
    @if(session('error'))   <div class="alert alert-danger">{{ session('error') }}</div> @endif

    {{-- FORM CARI & TAMBAH --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <form class="d-flex" method="GET">
            <input type="text" name="q" class="form-control me-2" placeholder="Cari nama/email/role..." value="{{ $q }}">
            <button class="btn btn-outline-success">Cari</button>
        </form>
        <a href="{{ route('admin.users.create') }}" class="btn btn-success">+ Tambah User</a>
    </div>

    {{-- TABEL USER --}}
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-striped mb-0 custom-table-success">
                <thead class="bg-success text-white">
                    <tr>
                        <th class="text-start" style="width: 5%;">No</th>
                        <th class="text-start">Username</th>
                        <th class="text-start">Email</th>
                        <th class="text-start">Role</th>
                        <th class="text-start" style="width: 25%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $index => $u)
                    <tr>
                        {{-- Nomor urut berdasarkan pagination --}}
                        <td>{{ $users->firstItem() + $index }}</td>
                        <td>{{ $u->username }}</td>
                        <td>{{ $u->email }}</td>
                        <td>
                            <span class="badge bg-{{ $u->role=='admin'?'primary':'secondary' }}">
                                {{ ucfirst($u->role) }}
                            </span>
                        </td>
                        <td class="text-end">
                            <form class="d-inline" method="POST" action="{{ route('admin.users.reset',$u) }}">
                                @csrf
                                <button class="btn btn-sm btn-outline-warning"
                                    onclick="return confirm('Reset password user ini?')">Reset Password</button>
                            </form>
                            <a href="{{ route('admin.users.edit',$u) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form class="d-inline" method="POST" action="{{ route('admin.users.destroy',$u) }}">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('Hapus user ini?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">Belum ada data.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-light border-top-0">
            {{ $users->links() }}
        </div>
    </div>
</div>


@endsection

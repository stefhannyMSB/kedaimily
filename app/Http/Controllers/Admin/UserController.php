<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->get('q');

        $users = User::when($q, function ($s) use ($q) {
                $s->where('username', 'like', "%$q%")
                  ->orWhere('email', 'like', "%$q%")
                  ->orWhere('role', 'like', "%$q%");
            })
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'q'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'username' => 'required|string|min:3|max:50|unique:users,username',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role'     => 'required|in:admin,user',
        ]);

        // normalisasi ringan
        $data['username'] = trim($data['username']);
        $data['email']    = Str::lower($data['email']);
        $data['password'] = Hash::make($data['password']);

        User::create($data);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User berhasil dibuat.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'username' => 'required|string|min:3|max:50|unique:users,username,' . $user->id,
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'role'     => 'required|in:admin,user',
        ]);

        $data['username'] = trim($data['username']);
        $data['email']    = Str::lower($data['email']);

        $user->update($data);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Tidak bisa menghapus diri sendiri.');
        }

        $user->delete();

        return back()->with('deleted', 'User dihapus.');
    }

    public function resetPassword(User $user)
    {
        $new = 'password123'; // ganti sesuai kebutuhan atau generate random
        $user->update(['password' => Hash::make($new)]);

        return back()->with('success', "Password {$user->username} direset ke: $new");
    }
}

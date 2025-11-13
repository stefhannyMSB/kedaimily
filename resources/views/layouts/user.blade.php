<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title','KEDAI MILY - User')</title>
  <link href="{{ asset('css/app.css') }}" rel="stylesheet">
  @stack('head')
</head>
<body>
  <div class="d-flex">
    {{-- Sidebar khusus user --}}
    <aside class="sidebar p-3" style="width:260px;background:linear-gradient(#b6f0b6,#9ae69a);min-height:100vh;">
      <div class="fw-bold fs-4 mb-4">KEDAI MILY</div>
      <ul class="list-unstyled">
        <li class="mb-2"><a href="{{ route('user.dashboard') }}">Dashboard</a></li>
        <li class="mb-2"><a href="{{ route('user.menu.index') }}">Menu</a></li>
        
      </ul>
      <form action="{{ route('logout') }}" method="POST" class="mt-auto pt-4">
        @csrf
        <button class="btn btn-outline-danger w-100">Logout</button>
      </form>
    </aside>

    <main class="flex-grow-1 p-4">
      @yield('content')
    </main>
  </div>

  <script src="{{ asset('js/app.js') }}"></script>
  @stack('scripts')
</body>
</html>

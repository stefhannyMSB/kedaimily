@php
    // active helper + role
    $active = function ($patterns = []) {
        foreach ((array)$patterns as $p) {
            if (request()->routeIs($p) || request()->is($p)) return 'active';
        }
        return '';
    };
    $role = auth()->user()->role ?? 'user';
@endphp

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<style>
:root{ --sidebar-w:250px; }

/* ===== SIDEBAR ===== */
#accordionSidebar{
    background: linear-gradient(180deg, #a3f3a0 0%, #72d85d 100%);
    font-family: 'Poppins', sans-serif;
    width: var(--sidebar-w);
    height: 100vh;
    position: fixed; top:0; left:0;
    z-index: 1030;
    display:flex; flex-direction:column;
    padding: 1.5rem 1.25rem;
    box-shadow: 2px 0 10px rgba(0,0,0,.08);
    overflow-y:auto;
    transform: translateX(0);
    transition: transform .25s ease;
}
#accordionSidebar.collapsed{ transform: translateX(calc(-1 * var(--sidebar-w))); }

.sidebar-brand{
    font-size: 1.6rem; font-weight:700; color:#b44545ff;
    text-align:center; letter-spacing:1px;
    margin-bottom: 1.5rem; padding-bottom: 1rem;
    border-bottom: 1px solid rgba(255,255,255,.5);
}
.nav-item{ list-style:none; margin-bottom:.85rem; }
.nav-link{
    display:flex; align-items:center; gap:.75rem;
    padding:.7rem 1rem; border-radius:10px;
    color:#202020; text-decoration:none; font-weight:500;
    transition: all .2s ease;
}
.nav-link i{ width:22px; text-align:center; font-size:1.2rem; }
.nav-link:hover{ background:rgba(255,255,255,.5); transform: translateX(4px); }
.nav-link.active{
    background:#f9d94a; color:#ba1010ff !important; font-weight:600;
    border-left:4px solid #e8c933; box-shadow: inset 0 0 6px rgba(0,0,0,.12);
}

.sidebar-divider{ height:1px; background:rgba(255,255,255,.5); margin:1.25rem 0; }

.logout-container{ margin-top:auto; border-top:1px solid rgba(255,255,255,.5); padding-top:1rem; }
.nav-link.text-danger{
    background:rgba(255,255,255,.3); color:#c62828!important; border-left:4px solid #c62828;
}

/* ===== TOGGLER ===== */
#sidebarToggle{
    position: fixed; top:14px; left:14px; z-index:1100;
    width:46px; height:46px; border:none; border-radius:12px;
    background:rgba(33,33,33,.85); color:#fff; font-size:24px;
    display:none; align-items:center; justify-content:center; cursor:pointer;
}
#sidebarToggle:hover{ background:rgba(0,0,0,.95); }

/* ===== MAIN CONTENT OFFSET ===== */
#mainContent{
    margin-left: var(--sidebar-w);
    padding: 24px;
    min-height: 100vh;
    background:#f7f9fb;
    transition: margin-left .25s ease;
}
/* saat sidebar collapsed (desktop) ATAU pada mobile */
body.sidebar-collapsed #mainContent{ margin-left: 0; }

/* ===== RESPONSIVE ===== */
@media (max-width: 992px){
    #sidebarToggle{ display:flex; }
    #accordionSidebar{ transform: translateX(calc(-1 * var(--sidebar-w))); }
    #accordionSidebar.show{ transform: translateX(0); }
    /* mobile = konten full width */
    #mainContent{ margin-left:0; }
}
</style>

<button id="sidebarToggle" aria-label="Toggle sidebar">
    <i class="bi bi-list"></i>
</button>

<ul class="navbar-nav" id="accordionSidebar">
    <!-- Brand -->
    <a class="sidebar-brand" href="{{ $role==='admin' ? route('dashboard') : route('user.dashboard') }}">
        <i class="bi bi-shop-window me-2"></i>KEDAI MILY
    </a>

    @if ($role === 'admin')
        <li class="nav-item">
            <a class="nav-link {{ $active(['dashboard']) }}" href="{{ route('dashboard') }}">
                </i> Dashboard
            </a>
        </li>
        <li class="nav-item"><a class="nav-link {{ $active(['menu.*']) }}" href="{{ route('menu.index') }}"></i> Data Menu</a></li>
        <li class="nav-item"><a class="nav-link {{ $active(['transaksi.*']) }}" href="{{ route('transaksi.index') }}"></i> Data Transaksi</a></li>
        <li class="nav-item"><a class="nav-link {{ $active(['datapenjualan.*']) }}" href="{{ route('datapenjualan.index') }}"></i> Data Penjualan</a></li>
        <li class="nav-item"><a class="nav-link {{ $active(['peramalan.*']) }}" href="{{ route('peramalan.index') }}"></i> Peramalan (DES)</a></li>
        <div class="sidebar-divider"></div>
        <li class="nav-item"><a class="nav-link {{ $active(['admin.users.*']) }}" href="{{ route('admin.users.index') }}"><i class="bi bi-people"></i> Manajemen User</a></li>
    @else
        <li class="nav-item">
            <a class="nav-link {{ $active(['user.dashboard']) }}" href="{{ route('user.dashboard') }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item"><a class="nav-link {{ $active(['user.menu.*']) }}" href="{{ route('user.menu.index') }}"><i class="bi bi-utensils"></i> Menu</a></li>
        
    @endif

    <div class="logout-container">
        <li class="nav-item">
            <a class="nav-link text-danger" href="#"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
               <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </li>
    </div>
</ul>

<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>

<script>
const sidebar   = document.getElementById('accordionSidebar');
const toggleBtn = document.getElementById('sidebarToggle');

function isMobile(){ return window.innerWidth < 992; }

function toggleSidebar(){
  if (isMobile()){
    sidebar.classList.toggle('show'); // slide in/out
  }else{
    sidebar.classList.toggle('collapsed');           // geser sidebar
    document.body.classList.toggle('sidebar-collapsed'); // konten ikut geser
  }
}
toggleBtn.addEventListener('click', toggleSidebar);

// auto-close di mobile saat klik menu
sidebar.querySelectorAll('a.nav-link').forEach(a=>{
  a.addEventListener('click', ()=>{ if(isMobile()) sidebar.classList.remove('show'); });
});
</script>

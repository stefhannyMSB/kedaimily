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
    z-index: 1040; /* Higher than toggle */
    display:flex; flex-direction:column;
    padding: 1.5rem 1.25rem;
    box-shadow: 4px 0 15px rgba(0,0,0,0.1);
    overflow-y:auto;
    transform: translateX(0);
    transition: transform .3s cubic-bezier(0.4, 0, 0.2, 1);
}
#accordionSidebar.collapsed{ transform: translateX(calc(-1 * var(--sidebar-w))); }

.sidebar-brand{
    font-size: 1.5rem; font-weight:700; color:#b44545ff;
    text-align:center; letter-spacing:1px;
    margin-bottom: 1.5rem; padding-bottom: 1rem;
    border-bottom: 1px solid rgba(255,255,255,.5);
    display: flex; align-items: center; justify-content: center;
}
.nav-item{ list-style:none; margin-bottom:.5rem; }
.nav-link{
    display:flex; align-items:center; gap: 0.85rem;
    padding:.75rem 1rem; border-radius:10px;
    color:#202020; text-decoration:none; font-weight:500;
    transition: all .2s ease;
    white-space: nowrap;
}
.nav-link i{ 
    width:24px; text-align:center; font-size:1.25rem; 
    display: flex; justify-content: center; align-items: center;
}
.nav-link:hover{ background:rgba(255,255,255,.5); transform: translateX(4px); }
.nav-link.active{
    background:#f9d94a; color:#ba1010ff !important; font-weight:600;
    border-left:4px solid #e8c933; box-shadow: inset 0 0 6px rgba(0,0,0,.12);
}

.sidebar-divider{ height:1px; background:rgba(255,255,255,.5); margin:1rem 0; }

.logout-container{ margin-top:auto; border-top:1px solid rgba(255,255,255,.5); padding-top:1rem; }
.nav-link.text-danger{
    background:rgba(255,255,255,.3); color:#c62828!important; border-left:4px solid #c62828;
}

/* ===== TOGGLER (Hamburger) ===== */
#sidebarToggle{
    position: fixed; top:15px; left:15px; z-index:1030;
    width:48px; height:48px; border:none; border-radius:12px;
    background: #fff; 
    color: #333;
    box-shadow: 0 4px 10px rgba(0,0,0,0.15);
    font-size:26px;
    display:none; align-items:center; justify-content:center; cursor:pointer;
    transition: transform .2s ease, background .2s;
}
#sidebarToggle:active { transform: scale(0.95); }

/* ===== OVERLAY ===== */
#sidebarOverlay {
    position: fixed; inset: 0;
    background: rgba(0,0,0,0.5);
    z-index: 1035; /* Below sidebar, above toggle */
    backdrop-filter: blur(2px);
    transition: opacity .3s ease;
}

/* ===== MAIN CONTENT OFFSET ===== */
#mainContent{
    margin-left: var(--sidebar-w);
    padding: 24px;
    min-height: 100vh;
    background:#f7f9fb;
    transition: margin-left .3s cubic-bezier(0.4, 0, 0.2, 1);
}
/* saat sidebar collapsed (desktop) ATAU pada mobile */
body.sidebar-collapsed #mainContent{ margin-left: 0; }

/* ===== RESPONSIVE ===== */
@media (max-width: 991.98px){
    /* Mobile/Tablet variables */
    :root{ --sidebar-w:270px; } /* Slightly wider for ease of reading */

    #sidebarToggle{ display:flex; }
    
    /* Default: Hidden (Slide left) */
    #accordionSidebar{ 
        transform: translateX(-100%); 
        box-shadow: none; /* No shadow when hidden */
    }
    
    /* Show: Slide in */
    #accordionSidebar.show{ 
        transform: translateX(0); 
        box-shadow: 4px 0 25px rgba(0,0,0,0.25);
    }
    
    /* Content always full width */
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
                <i class="bi bi-grid-1x2-fill"></i> Dashboard
            </a>
        </li>
        <li class="nav-item"><a class="nav-link {{ $active(['menu.*']) }}" href="{{ route('menu.index') }}"><i class="bi bi-journal-album"></i> Data Menu</a></li>
        <li class="nav-item"><a class="nav-link {{ $active(['transaksi.*']) }}" href="{{ route('transaksi.index') }}"><i class="bi bi-receipt-cutoff"></i> Data Transaksi</a></li>
        <li class="nav-item"><a class="nav-link {{ $active(['datapenjualan.*']) }}" href="{{ route('datapenjualan.index') }}"><i class="bi bi-bar-chart-line-fill"></i> Data Penjualan</a></li>
        <li class="nav-item"><a class="nav-link {{ $active(['peramalan.*']) }}" href="{{ route('peramalan.index') }}"><i class="bi bi-graph-up-arrow"></i> Peramalan (DES)</a></li>
        <div class="sidebar-divider"></div>
        <li class="nav-item"><a class="nav-link {{ $active(['admin.users.*']) }}" href="{{ route('admin.users.index') }}"><i class="bi bi-people-fill"></i> Manajemen User</a></li>
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
document.addEventListener("DOMContentLoaded", function(){
    const sidebar   = document.getElementById('accordionSidebar');
    const toggleBtn = document.getElementById('sidebarToggle');
    const overlay   = document.getElementById('sidebarOverlay');
    const navLinks  = sidebar.querySelectorAll('a.nav-link, .sidebar-brand');

    function isMobile(){ return window.innerWidth < 992; }

    function openSidebar(){
        sidebar.classList.add('show');
        if(isMobile() && overlay){
            overlay.classList.remove('d-none');
            // Sedikit delay utk animasi opacity jika mau, tapi CSS handle transition
            overlay.style.opacity = '1'; 
        }
    }

    function closeSidebar(){
        if(isMobile()){
            sidebar.classList.remove('show');
            if(overlay) overlay.classList.add('d-none');
        } else {
            // Desktop logic
            sidebar.classList.add('collapsed');
            document.body.classList.add('sidebar-collapsed');
        }
    }

    function toggleSidebar(){
        if (isMobile()){
            if(sidebar.classList.contains('show')) closeSidebar();
            else openSidebar();
        } else {
            sidebar.classList.toggle('collapsed');
            document.body.classList.toggle('sidebar-collapsed');
        }
    }

    if(toggleBtn) toggleBtn.addEventListener('click', toggleSidebar);

    // Klik overlay -> tutup
    if(overlay){
        overlay.addEventListener('click', closeSidebar);
    }

    // Klik menu item di mobile -> tutup
    navLinks.forEach(link => {
        link.addEventListener('click', function(){
            if(isMobile()) closeSidebar();
        });
    });

    // Handle resize
    window.addEventListener('resize', function(){
        if(!isMobile()){
            // Reset overlay if resized to desktop
            if(overlay) overlay.classList.add('d-none');
        }
    });
});
</script>

@php
$active = fn($route) => Request::is($route) ? 'active' : '';
@endphp

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">


<style>
/* ========== SIDEBAR BASE ========== */
#accordionSidebar {
    background: linear-gradient(180deg, #a3f3a0 0%, #72d85d 100%);
    font-family: 'Poppins', sans-serif;
    width: 250px;
    height: 100vh;
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1030;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    padding: 1.5rem 1.25rem;
    box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease-in-out;
    overflow-y: auto;
}

/* Saat disembunyikan */
#accordionSidebar.collapsed {
    transform: translateX(-260px);
}

/* ========== BRAND ========== */
.sidebar-brand {
    font-size: 1.6rem;
    font-weight: 700;
    color: #b44545ff;
    text-align: center;
    letter-spacing: 1px;
    margin-bottom: 2rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.5);
    padding-bottom: 1rem;
}

/* ========== NAVIGATION ========== */
.nav-item {
    list-style: none;
    margin-bottom: 1rem;
}

.nav-link {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1rem;
    color: #202020;
    font-weight: 500;
    text-decoration: none;
    border-radius: 10px;
    transition: all 0.25s ease-in-out;
    background-color: transparent;
}

.nav-link i {
    font-size: 1.2rem;
    width: 22px;
    text-align: center;
}

.nav-link:hover {
    background-color: rgba(255, 255, 255, 0.5);
    color: #000;
    transform: translateX(5px);
}

/* Active link */
.nav-link.active {
    background-color: #f9d94a;
    color: #ba1010ff !important;
    font-weight: 600;
    box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.15);
    border-left: 4px solid #e8c933;
}

/* Divider */
.sidebar-divider {
    height: 1px;
    background-color: rgba(255, 255, 255, 0.5);
    margin: 1.5rem 0;
}

/* Logout di bagian bawah (sticky) */
.logout-container {
    margin-top: auto;
    border-top: 1px solid rgba(255, 255, 255, 0.5);
    padding-top: 1.25rem;
}

.nav-link.text-danger {
    background-color: rgba(255, 255, 255, 0.3);
    color: #c62828 !important;
    border-left: 4px solid #c62828;
    transition: all 0.2s ease-in-out;
}

.nav-link.text-danger:hover {
    background-color: rgba(255, 255, 255, 0.5);
}

/* ========== TOGGLE BUTTON ========== */
#sidebarToggle {
    position: fixed;
    top: 14px;
    left: 14px;
    z-index: 1100;
    background: rgba(33, 33, 33, 0.85);
    color: #fff;
    border: none;
    width: 46px;
    height: 46px;
    border-radius: 12px;
    font-size: 24px;
    display: none;
    justify-content: center;
    align-items: center;
    cursor: pointer;
    transition: background-color 0.25s ease;
}

#sidebarToggle:hover {
    background-color: rgba(0, 0, 0, 0.95);
}

/* ========== MAIN CONTENT ========== */
#mainContent {
    margin-left: 185px;
    transition: margin-left 0.3s ease;
    background-color: #f9fafb;
    min-height: 100vh;
    padding: 2rem;
}

#mainContent.expanded {
    margin-left: 0;
}

/* ========== RESPONSIVE ========== */
@media (max-width: 992px) {
    #sidebarToggle {
        display: flex;
    }

    #accordionSidebar {
        transform: translateX(-260px);
        box-shadow: 4px 0 15px rgba(0, 0, 0, 0.25);
        width: 240px;
    }

    #accordionSidebar.show {
        transform: translateX(0);
    }

    #mainContent {
        margin-left: 0 !important; */
    }
}
</style>

<!-- ========== TOGGLE BUTTON ========== -->
<button id="sidebarToggle" aria-label="Toggle sidebar">
    <i class="bi bi-list"></i>
</button>

<!-- ========== SIDEBAR ========== -->
<ul class="navbar-nav" id="accordionSidebar">
    <!-- Brand -->
    <a class="sidebar-brand" href="{{ url('/') }}">
        <i class="bi bi-shop-window me-2"></i> KEDAI MILY
    </a>

    <!-- Menu Utama -->
    <li class="nav-item">
        <a class="nav-link {{ $active('/') }}" href="{{ url('/') }}">
            Dashboard
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('menu.*') ? 'active' : '' }}" href="{{ route('menu.index') }}">
            Data Menu
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('penjualan.*') ? 'active' : '' }}" href="{{ route('penjualan.index') }}">
            Data penjualan
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('peramalan.*') ? 'active' : '' }}" href="{{ route('peramalan.index') }}">
           Peramalan
        </a>
    </li>
    <!-- Logout (sticky bottom) -->
    <div class="logout-container">
        <li class="nav-item">
            <a class="nav-link text-danger" href="#"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </li>
    </div>
</ul>

<!-- ========== MAIN CONTENT ========== -->
<div id="mainContent">
    {{-- Konten utama halaman di sini --}}
</div>

<!-- ========== SCRIPT ========== -->
<script>
const sidebar = document.getElementById('accordionSidebar');
const toggleBtn = document.getElementById('sidebarToggle');
const mainContent = document.getElementById('mainContent');

toggleBtn.addEventListener('click', () => {
    if (window.innerWidth < 992) {
        sidebar.classList.toggle('show');
    } else {
        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('expanded');
    }
});

sidebar.querySelectorAll('a.nav-link').forEach(link => {
    link.addEventListener('click', () => {
        if (window.innerWidth < 992) {
            sidebar.classList.remove('show');
        }
    });
});
</script>

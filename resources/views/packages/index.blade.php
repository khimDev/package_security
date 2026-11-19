<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Paket | Package Security</title>

    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
</head>

<body>

    <div class="app-container">

        <!-- Mobile Sidebar Overlay (Backdrop Blur) -->
        <div id="sidebarOverlay" class="sidebar-overlay"></div>

        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="brand">
                <div class="brand-info">
                    <img src="{{ asset('images/logo.svg') }}" alt="Logo Package Security" class="brand-logo">
                    <div>
                        <h2>Package Security</h2>
                        <span>Security Management</span>
                    </div>
                </div>
                <button type="button" class="btn-close-sidebar" id="btnCloseSidebar" aria-label="Tutup Menu">&times;</button>
            </div>

            <nav class="navigation">
                <p class="nav-label">MENU UTAMA</p>

                <a href="/" class="nav-link">
                    <span>▦</span>
                    Dashboard
                </a>

                <a href="{{ route('packages.index') }}" class="nav-link active">
                    <span>□</span>
                    Data Paket
                </a>

                <a href="{{ route('packages.history') }}" class="nav-link">
                    <span>↗</span>
                    Riwayat Pengambilan
                </a>

                @if (Auth::user()->is_admin)
                    <a href="{{ route('users.index') }}" class="nav-link">
                        <span>👥</span>
                        Kelola Petugas
                    </a>
                @endif
            </nav>

            <div class="sidebar-footer">
                <span class="status-dot"></span>
                Sistem Aktif
            </div>

            <form action="/logout" method="POST">
                @csrf
                <button type="submit" class="logout-btn">
                    Logout
                </button>
            </form>
        </aside>

        <!-- Main Content -->
        <main class="main-content">

            <header class="topbar">
                <div class="topbar-left">
                    <button type="button" class="btn-hamburger" id="btnToggleSidebar" aria-label="Buka Menu">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                    <div>
                        <h1>Data Paket</h1>
                        <p>Daftar seluruh paket yang belum diambil di pos satpam.</p>
                    </div>
                </div>

                <div class="user-profile">
                    <div class="user-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                    <div>
                        <strong>{{ Auth::user()->is_admin ? 'Administrator' : 'Petugas Satpam' }}</strong>
                        <span>{{ Auth::user()->name }}</span>
                    </div>
                </div>
            </header>

            <!-- Search Bar Section -->
            <section class="filter-section">
                <form action="{{ route('packages.index') }}" method="GET" class="search-form">
                    <div class="search-input-group">
                        <span class="search-icon">🔍</span>
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Cari nama pemilik paket..."
                            autocomplete="off"
                        >
                    </div>

                    <div class="search-buttons">
                        <button type="submit" class="btn-search">
                            Cari
                        </button>

                        @if (request('search'))
                            <a href="{{ route('packages.index') }}" class="btn-reset">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>

                @if (request('search'))
                    <p class="search-info">
                        Menampilkan hasil pencarian untuk: <strong>"{{ request('search') }}"</strong> ({{ $packages->total() }} paket ditemukan)
                    </p>
                @endif
            </section>

            <!-- Package List (Maks 10 per halaman) -->
            <section class="package-section">

                <div class="section-header">
                    <div>
                        <h2>Paket Belum Diambil</h2>
                        <p>Menampilkan maksimal 10 data per halaman.</p>
                    </div>

                    <span class="badge badge-warning">
                        {{ $packages->total() }} Paket Tersimpan
                    </span>
                </div>

                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Pemilik Paket</th>
                                <th>Tanggal Diterima</th>
                                <th>Petugas</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($packages as $package)
                                <tr>
                                    {{-- Nomer urut berlanjut sesuai pagination --}}
                                    <td>{{ $packages->firstItem() ? ($packages->firstItem() + $loop->index) : $loop->iteration }}</td>

                                    <td><strong>{{ $package->owner_name }}</strong></td>

                                    <td>{{ $package->received_at->format('d/m/Y H:i') }} WIB</td>

                                    <td>{{ $package->receiver->name ?? '-' }}</td>

                                    <td>
                                        @php
                                            $status = $package->status;
                                            $badgeClass = match ($status) {
                                                'Picked Up' => 'badge-success',
                                                'Perlu Tindak Lanjut' => 'badge-danger',
                                                default => 'badge-warning',
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">
                                            {{ $status }}
                                        </span>
                                    </td>

                                    <td>
                                        <a href="{{ route('packages.show', $package) }}" class="btn-detail">
                                            Detail ->
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="empty-state">
                                        @if (request('search'))
                                            Tidak ditemukan paket dengan pemilik "<strong>{{ request('search') }}</strong>".
                                        @else
                                            Saat ini belum ada paket yang menunggu diambil.
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Links --}}
                <div class="pagination-wrapper">
                    {{ $packages->links() }}
                </div>

            </section>

        </main>

    </div>

    <!-- Script Drawer Sidebar Mobile -->
    <script>
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const btnToggleSidebar = document.getElementById('btnToggleSidebar');
        const btnCloseSidebar = document.getElementById('btnCloseSidebar');

        function openSidebar(e) {
            if (e) e.stopPropagation();
            if (sidebar) {
                sidebar.classList.add('open');
                sidebar.style.setProperty('left', '0px', 'important');
            }
            if (sidebarOverlay) {
                sidebarOverlay.classList.add('active');
                sidebarOverlay.style.setProperty('display', 'block', 'important');
            }
            document.body.style.overflow = 'hidden';
        }

        function closeSidebar(e) {
            if (e) e.stopPropagation();
            if (sidebar) {
                sidebar.classList.remove('open');
                sidebar.style.removeProperty('left');
            }
            if (sidebarOverlay) {
                sidebarOverlay.classList.remove('active');
                sidebarOverlay.style.removeProperty('display');
            }
            document.body.style.overflow = '';
        }

        if (btnToggleSidebar) {
            btnToggleSidebar.addEventListener('click', openSidebar);
        }
        if (btnCloseSidebar) {
            btnCloseSidebar.addEventListener('click', closeSidebar);
        }
        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', closeSidebar);
        }
    </script>

</body>

</html>

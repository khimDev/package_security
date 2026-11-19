<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pengambilan | Package Security</title>

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
                    <div class="brand-icon">P</div>
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

                <a href="{{ route('packages.index') }}" class="nav-link">
                    <span>□</span>
                    Data Paket
                </a>

                <a href="{{ route('packages.history') }}" class="nav-link active">
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
                        <h1>Riwayat Pengambilan</h1>
                        <p>Daftar seluruh paket yang telah berhasil diambil dalam 30 hari terakhir beserta bukti tanda tangan.</p>
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
                <form action="{{ route('packages.history') }}" method="GET" class="search-form">
                    <div class="search-input-group">
                        <span class="search-icon">🔍</span>
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Cari nama pemilik atau pengambil paket..."
                            autocomplete="off"
                        >
                    </div>

                    <div class="search-buttons">
                        <button type="submit" class="btn-search">
                            Cari
                        </button>

                        @if (request('search'))
                            <a href="{{ route('packages.history') }}" class="btn-reset">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>

                @if (request('search'))
                    <p class="search-info">
                        Menampilkan hasil pencarian untuk: <strong>"{{ request('search') }}"</strong> ({{ $packages->total() }} riwayat ditemukan)
                    </p>
                @endif
            </section>

            <!-- History List (10 per halaman) -->
            <section class="package-section">

                <div class="section-header">
                    <div>
                        <h2>Paket Telah Diambil</h2>
                        <p>Riwayat serah terima paket kepada pemilik / perwakilan.</p>
                    </div>

                    <span class="badge badge-success">
                        {{ $packages->total() }} Paket Sukses Diambil
                    </span>
                </div>

                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Pemilik Paket</th>
                                <th>Nama Pengambil</th>
                                <th>Waktu Pengambilan</th>
                                <th>Petugas Satpam</th>
                                <th>Tanda Tangan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($packages as $package)
                                <tr>
                                    {{-- Nomer urut berlanjut sesuai pagination --}}
                                    <td>{{ $packages->firstItem() ? ($packages->firstItem() + $loop->index) : $loop->iteration }}</td>

                                    <td>{{ $package->owner_name }}</td>

                                    <td><strong>{{ $package->pickup->taker_name ?? '-' }}</strong></td>

                                    <td>{{ $package->pickup?->picked_up_at ? $package->pickup->picked_up_at->format('d/m/Y H:i') . ' WIB' : '-' }}</td>

                                    <td>{{ $package->pickup?->server?->name ?? '-' }}</td>

                                    <td>
                                        @if ($package->pickup && $package->pickup->signature_path)
                                            <a href="{{ asset('storage/' . $package->pickup->signature_path) }}" target="_blank" title="Klik untuk perbesar tanda tangan">
                                                <img
                                                    src="{{ asset('storage/' . $package->pickup->signature_path) }}"
                                                    alt="Tanda Tangan"
                                                    class="table-signature-thumb"
                                                >
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>

                                    <td>
                                        <a href="{{ route('packages.show', $package) }}" class="btn-detail">
                                            Detail ->
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="empty-state">
                                        @if (request('search'))
                                            Tidak ditemukan riwayat pengambilan dengan kata kunci "<strong>{{ request('search') }}</strong>".
                                        @else
                                            Belum ada paket yang telah diambil.
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

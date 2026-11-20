<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pengambilan | Package Security</title>

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
                                            @php
                                                $sigSrc = \Illuminate\Support\Str::startsWith($package->pickup->signature_path, 'data:image/')
                                                    ? $package->pickup->signature_path
                                                    : url('/files/' . $package->pickup->signature_path);
                                                $takerName = $package->pickup->taker_name ?? '-';
                                                $pickupTime = $package->pickup->picked_up_at ? $package->pickup->picked_up_at->format('d/m/Y H:i') . ' WIB' : '-';
                                            @endphp
                                            <div
                                                onclick="openSignatureModal('{{ addslashes($sigSrc) }}', '{{ addslashes($takerName) }}', '{{ addslashes($pickupTime) }}')"
                                                title="Klik untuk melihat pratinjau tanda tangan"
                                                style="display: inline-block; cursor: pointer;"
                                            >
                                                <img
                                                    src="{{ $sigSrc }}"
                                                    alt="Tanda Tangan"
                                                    class="table-signature-thumb"
                                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';"
                                                >
                                                <span class="text-muted" style="display: none; font-size: 11px;">[Tidak tersedia]</span>
                                            </div>
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

    <!-- Modal Preview Tanda Tangan (Lightbox Canvas Putih) -->
    <div id="signatureModal" class="modal-overlay" style="z-index: 99999;">
        <div class="modal-card" style="max-width: 520px; text-align: center; border-radius: 16px; padding: 24px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);">
            <div class="modal-header" style="margin-bottom: 16px; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px; display: flex; justify-content: space-between; align-items: center;">
                <div style="text-align: left;">
                    <h3 style="font-size: 17px; color: #1e293b; margin: 0 0 2px 0;">Bukti Tanda Tangan</h3>
                    <p style="font-size: 12px; color: #64748b; margin: 0;">Pratinjau tanda tangan serah terima paket</p>
                </div>
                <button type="button" class="btn-close" onclick="closeSignatureModal()" aria-label="Tutup">&times;</button>
            </div>

            <!-- Canvas Kotak Putih Bersih dengan Border Halus -->
            <div style="background: #ffffff; border: 2px dashed #cbd5e1; border-radius: 12px; padding: 16px; display: flex; align-items: center; justify-content: center; min-height: 200px; max-height: 320px; box-shadow: inset 0 2px 4px rgba(0,0,0,0.02); margin-bottom: 16px; overflow: hidden;">
                <img id="modalSignatureImg" src="" alt="Pratinjau Tanda Tangan" style="max-width: 100%; max-height: 280px; object-fit: contain; filter: drop-shadow(0 1px 2px rgba(0,0,0,0.08));">
            </div>

            <!-- Info Penerima & Waktu -->
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px 16px; text-align: left; margin-bottom: 20px; display: flex; justify-content: space-between; flex-wrap: wrap; gap: 8px;">
                <div>
                    <span style="font-size: 11px; color: #64748b; display: block; text-transform: uppercase; font-weight: 600;">Nama Pengambil</span>
                    <strong id="modalSignatureRecipient" style="font-size: 14px; color: #1e293b;">-</strong>
                </div>
                <div>
                    <span style="font-size: 11px; color: #64748b; display: block; text-transform: uppercase; font-weight: 600;">Waktu Pengambilan</span>
                    <span id="modalSignatureTime" style="font-size: 13px; color: #334155;">-</span>
                </div>
            </div>

            <button type="button" class="primary-button" onclick="closeSignatureModal()" style="width: 100%; padding: 11px; background: #334155; border-radius: 8px; font-weight: 600;">
                Tutup
            </button>
        </div>
    </div>

    <!-- Script Drawer Sidebar & Modal Lightbox -->
    <script>
        // Drawer Sidebar Mobile
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

        // Modal Lightbox Preview Tanda Tangan
        const signatureModal = document.getElementById('signatureModal');
        const modalSignatureImg = document.getElementById('modalSignatureImg');
        const modalSignatureRecipient = document.getElementById('modalSignatureRecipient');
        const modalSignatureTime = document.getElementById('modalSignatureTime');

        function openSignatureModal(src, recipientName, time) {
            if (modalSignatureImg) modalSignatureImg.src = src;
            if (modalSignatureRecipient) modalSignatureRecipient.textContent = recipientName;
            if (modalSignatureTime) modalSignatureTime.textContent = time;

            if (signatureModal) {
                signatureModal.classList.add('active');
                signatureModal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }
        }

        function closeSignatureModal() {
            if (signatureModal) {
                signatureModal.classList.remove('active');
                signatureModal.style.display = 'none';
                document.body.style.overflow = '';
            }
        }

        // Tutup saat klik di area gelap di luar modal
        if (signatureModal) {
            signatureModal.addEventListener('click', function(e) {
                if (e.target === signatureModal) {
                    closeSignatureModal();
                }
            });
        }

        // Tutup saat menekan tombol Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && signatureModal && signatureModal.classList.contains('active')) {
                closeSignatureModal();
            }
        });
    </script>

</body>

</html>

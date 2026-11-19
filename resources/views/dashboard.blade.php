<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Package Security</title>

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

                <a href="/" class="nav-link active">
                    <span>▦</span>
                    Dashboard
                </a>

                <a href="{{ route('packages.index') }}" class="nav-link">
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
                        <h1>Dashboard</h1>
                        <p>Selamat datang di Package Security.</p>
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

            {{-- Alert Sukses (jika ada paket baru ditambahkan) --}}
            @if (session('succes') || session('success'))
                <div class="alert-success">
                    ✓ {{ session('succes') ?? session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert-danger">
                    ⚠ {{ session('error') }}
                </div>
            @endif

            <section class="welcome-section">
                <div>
                    <h2>Ringkasan Paket</h2>
                    <p>Pantau seluruh paket yang diterima dan diambil.</p>
                </div>

                <button type="button" class="primary-button" id="btnOpenModal">
                    + Tambah Paket
                </button>
            </section>


            <!-- Statistics -->
            <section class="stats-grid">

                <div class="stat-card">
                    <div class="stat-icon blue">□</div>
                    <p>Total Paket</p>
                    <h3>{{ $totalPackages }}</h3>
                    <span>Seluruh paket tercatat</span>
                </div>

                <div class="stat-card">
                    <div class="stat-icon orange">◷</div>
                    <p>Belum Diambil</p>
                    <h3>{{ $pendingPackages }}</h3>
                    <span>Menunggu pengambilan</span>
                </div>

                <div class="stat-card">
                    <div class="stat-icon green">✓</div>
                    <p>Sudah Diambil</p>
                    <h3>{{ $pickedUpPackages }}</h3>
                    <span>Pengambilan berhasil</span>
                </div>

            </section>


            <!-- Package List -->
            <section class="package-section">

                <div class="section-header">
                    <div>
                        <h2>Paket Terbaru</h2>
                        <p>Daftar paket yang baru diterima.</p>
                    </div>

                    <a href="#" class="view-all">Lihat Semua →</a>
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
                                    {{-- Nomer Urut berlanjut sesuai pagination --}}
                                    <td>{{ $packages->firstItem() ? ($packages->firstItem() + $loop->index) : $loop->iteration }}</td>

                                    <td>{{ $package->owner_name }}</td>

                                    <td>
                                        {{ $package->received_at->format('d/m/Y H:i') }}
                                    </td>

                                    <td>{{ $package->receiver->name ?? '-' }}</td>

                                    {{-- Status dengan Class Badge --}}
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
                                        Belum ada paket yang tercatat.
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

    <!-- Modal Pop-up Tambah Paket -->
    <div id="modalTambahPaket" class="modal-overlay">
        <div class="modal-card">
            <div class="modal-header">
                <h3>Tambah Paket Masuk</h3>
                <button type="button" class="btn-close" id="btnCloseModal">&times;</button>
            </div>

            <form action="{{ route('packages.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="owner_name">Nama Pemilik Paket</label>
                    <input
                        type="text"
                        id="owner_name"
                        name="owner_name"
                        placeholder="Masukkan nama pemilik paket"
                        value="{{ old('owner_name') }}"
                        required
                        autocomplete="off"
                    >
                    @error('owner_name')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-cancel" id="btnCancelModal">Batal</button>
                    <button type="submit" class="btn-submit">Simpan Paket</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById('modalTambahPaket');
        const modalCard = document.querySelector('.modal-card');
        const btnOpen = document.getElementById('btnOpenModal');
        const btnClose = document.getElementById('btnCloseModal');
        const btnCancel = document.getElementById('btnCancelModal');
        const inputOwner = document.getElementById('owner_name');

        function openModal() {
            modal.classList.add('active');
            inputOwner.focus();
        }

        function closeModal() {
            modal.classList.remove('active');
        }

        btnOpen.addEventListener('click', openModal);
        btnClose.addEventListener('click', closeModal);
        btnCancel.addEventListener('click', closeModal);

        if (modalCard) {
            modalCard.addEventListener('click', function (e) {
                e.stopPropagation();
            });
        }

        modal.addEventListener('click', function (e) {
            if (e.target === modal) {
                closeModal();
            }
        });

        @if ($errors->any())
            openModal();
        @endif

        const alertSuccess = document.querySelector('.alert-success');
        if (alertSuccess) {
            setTimeout(function () {
                alertSuccess.style.transition = 'opacity 0.5s ease';
                alertSuccess.style.opacity = '0';
                setTimeout(() => alertSuccess.remove(), 500);
            }, 3000);
        }

        // Script Drawer Sidebar Mobile
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

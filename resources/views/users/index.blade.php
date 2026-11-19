<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Petugas | Package Security</title>

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

                <a href="{{ route('packages.history') }}" class="nav-link">
                    <span>↗</span>
                    Riwayat Pengambilan
                </a>

                @if (Auth::user()->is_admin)
                    <a href="{{ route('users.index') }}" class="nav-link active">
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
                        <h1>Kelola Petugas</h1>
                        <p>Pantau dan kelola akun petugas Satpam & Administrator.</p>
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

            {{-- Flash Messages --}}
            @if (session('success') || session('succes'))
                <div class="alert-success">
                    ✓ {{ session('success') ?? session('succes') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert-danger">
                    ⚠ {{ session('error') }}
                </div>
            @endif

            @if (isset($errors) && $errors->any())
                <div class="alert-danger">
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <section class="welcome-section">
                <div>
                    <h2>Daftar Akun Petugas</h2>
                    <p>Total {{ $users->total() }} akun terdaftar dalam sistem.</p>
                </div>

                <button type="button" class="primary-button" id="btnOpenAddModal">
                    + Tambah Petugas
                </button>
            </section>

            <!-- Search Bar Section -->
            <section class="filter-section">
                <form action="{{ route('users.index') }}" method="GET" class="search-form">
                    <div class="search-input-group">
                        <span class="search-icon">🔍</span>
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Cari nama atau username petugas..."
                            autocomplete="off"
                        >
                    </div>

                    <div class="search-buttons">
                        <button type="submit" class="btn-search">
                            Cari
                        </button>

                        @if (request('search'))
                            <a href="{{ route('users.index') }}" class="btn-reset">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </section>

            <!-- Users Table Section -->
            <section class="package-section">
                <div class="section-header">
                    <h3>Data Petugas Satpam</h3>
                </div>

                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>NO</th>
                                <th>NAMA LENGKAP</th>
                                <th>USERNAME</th>
                                <th>ROLE / HAK AKSES</th>
                                <th>AKTIVITAS</th>
                                <th style="text-align: center;">AKSI</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($users as $index => $u)
                                <tr>
                                    <td>{{ $users->firstItem() + $index }}</td>
                                    <td>
                                        <strong>{{ $u->name }}</strong>
                                        @if ($u->id === Auth::id())
                                            <span style="font-size: 11px; color: #2563eb; margin-left: 4px;">(Anda)</span>
                                        @endif
                                    </td>
                                    <td><code>{{ $u->username }}</code></td>
                                    <td>
                                        @if ($u->is_admin)
                                            <span class="badge badge-primary">Administrator</span>
                                        @else
                                            <span class="badge badge-secondary">Petugas Satpam</span>
                                        @endif
                                    </td>
                                    <td style="font-size: 12px; color: #64748b;">
                                        <span>Terima Paket: <strong>{{ $u->received_packages_count }}</strong></span> &bull; 
                                        <span>Penyerahan: <strong>{{ $u->served_pickups_count }}</strong></span>
                                    </td>
                                    <td style="text-align: center;">
                                        <div class="actions-flex" style="justify-content: center;">
                                            <!-- Tombol Reset Password -->
                                            <button
                                                type="button"
                                                class="btn-sm-warning btn-open-reset"
                                                data-id="{{ $u->id }}"
                                                data-name="{{ $u->name }}"
                                                data-username="{{ $u->username }}"
                                                title="Reset Password Petugas"
                                            >
                                                🔑 Reset Pass
                                            </button>

                                            <!-- Tombol Hapus -->
                                            @if ($u->id === Auth::id())
                                                <button type="button" class="btn-sm-secondary" title="Tidak dapat menghapus akun Anda sendiri" disabled>
                                                    ✕ Hapus
                                                </button>
                                            @elseif ($u->received_packages_count > 0 || $u->served_pickups_count > 0)
                                                <button type="button" class="btn-sm-secondary" title="Petugas memiliki riwayat paket dan tidak dapat dihapus" disabled>
                                                    ✕ Terkunci
                                                </button>
                                            @else
                                                <form action="{{ route('users.destroy', $u) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus petugas {{ $u->name }}?');" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-sm-danger" title="Hapus Akun Petugas">
                                                        ✕ Hapus
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="empty-state">
                                        Tidak ada data petugas yang cocok dengan pencarian Anda.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Links --}}
                {{ $users->links('pagination.custom') }}
            </section>

        </main>

    </div>

    <!-- Modal Tambah Petugas -->
    <div class="modal-overlay" id="modalAddUser">
        <div class="modal-card">
            <div class="modal-header">
                <h3>Tambah Akun Petugas Baru</h3>
                <button type="button" class="btn-close" id="btnCloseAddModal">&times;</button>
            </div>

            <form action="{{ route('users.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="name">Nama Petugas Satpam</label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        placeholder="Contoh: Budi Santoso"
                        required
                        autocomplete="off"
                    >
                </div>

                <div class="form-group">
                    <label for="username">Username (untuk Login)</label>
                    <input
                        type="text"
                        name="username"
                        id="username"
                        placeholder="Contoh: satpam_budi"
                        required
                        autocomplete="off"
                    >
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        placeholder="Minimal 4 karakter"
                        required
                    >
                </div>

                <div class="form-group" style="display: flex; align-items: center; gap: 8px;">
                    <input type="checkbox" name="is_admin" id="is_admin" value="1" style="width: auto; cursor: pointer;">
                    <label for="is_admin" style="margin: 0; cursor: pointer; font-size: 13px; font-weight: normal;">
                        Jadikan sebagai Administrator (bisa kelola akun)
                    </label>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-cancel" id="btnCancelAddModal">
                        Batal
                    </button>
                    <button type="submit" class="primary-button">
                        Simpan Akun
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Reset Password -->
    <div class="modal-overlay" id="modalResetPassword">
        <div class="modal-card">
            <div class="modal-header">
                <h3>Reset Password Petugas</h3>
                <button type="button" class="btn-close" id="btnCloseResetModal">&times;</button>
            </div>

            <form id="formResetPassword" method="POST">
                @csrf

                <p style="font-size: 13px; color: #64748b; margin-bottom: 16px;">
                    Mereset password untuk: <strong id="resetTargetName" style="color: #1e293b;"></strong> (<code id="resetTargetUsername"></code>)
                </p>

                <div class="form-group">
                    <label for="new_password">Password Baru</label>
                    <input
                        type="text"
                        name="new_password"
                        id="new_password"
                        placeholder="Kosongkan jika ingin default: satpam123"
                        autocomplete="off"
                    >
                    <span style="font-size: 11px; color: #94a3b8; margin-top: 4px; display: block;">
                        * Jika tidak diisi, otomatis direset menjadi <strong>satpam123</strong>
                    </span>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-cancel" id="btnCancelResetModal">
                        Batal
                    </button>
                    <button type="submit" class="primary-button" style="background: #f59e0b;">
                        Konfirmasi Reset
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Mobile Sidebar Drawer
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const btnToggle = document.getElementById('btnToggleSidebar');
            const btnClose = document.getElementById('btnCloseSidebar');

            function openSidebar() {
                sidebar.classList.add('open');
                overlay.classList.add('active');
            }

            function closeSidebar() {
                sidebar.classList.remove('open');
                overlay.classList.remove('active');
            }

            if (btnToggle) btnToggle.addEventListener('click', openSidebar);
            if (btnClose) btnClose.addEventListener('click', closeSidebar);
            if (overlay) overlay.addEventListener('click', closeSidebar);

            // Modal Tambah Petugas
            const modalAdd = document.getElementById('modalAddUser');
            const btnOpenAdd = document.getElementById('btnOpenAddModal');
            const btnCloseAdd = document.getElementById('btnCloseAddModal');
            const btnCancelAdd = document.getElementById('btnCancelAddModal');

            function openAddModal() {
                if (modalAdd) {
                    modalAdd.classList.add('active');
                    document.getElementById('name').focus();
                }
            }

            function closeAddModal() {
                if (modalAdd) modalAdd.classList.remove('active');
            }

            if (btnOpenAdd) btnOpenAdd.addEventListener('click', openAddModal);
            if (btnCloseAdd) btnCloseAdd.addEventListener('click', closeAddModal);
            if (btnCancelAdd) btnCancelAdd.addEventListener('click', closeAddModal);

            // Modal Reset Password
            const modalReset = document.getElementById('modalResetPassword');
            const formReset = document.getElementById('formResetPassword');
            const targetName = document.getElementById('resetTargetName');
            const targetUsername = document.getElementById('resetTargetUsername');
            const btnCloseReset = document.getElementById('btnCloseResetModal');
            const btnCancelReset = document.getElementById('btnCancelResetModal');

            document.querySelectorAll('.btn-open-reset').forEach(function (button) {
                button.addEventListener('click', function () {
                    const userId = this.dataset.id;
                    const userName = this.dataset.name;
                    const userUsername = this.dataset.username;

                    formReset.action = '/users/' + userId + '/reset-password';
                    targetName.textContent = userName;
                    targetUsername.textContent = userUsername;
                    document.getElementById('new_password').value = '';

                    modalReset.classList.add('active');
                    document.getElementById('new_password').focus();
                });
            });

            function closeResetModal() {
                if (modalReset) modalReset.classList.remove('active');
            }

            if (btnCloseReset) btnCloseReset.addEventListener('click', closeResetModal);
            if (btnCancelReset) btnCancelReset.addEventListener('click', closeResetModal);
        });
    </script>

</body>

</html>

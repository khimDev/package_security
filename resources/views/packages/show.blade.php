<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Paket #{{ $package->id }} | Package Security</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
</head>
<body>

    <div class="detail-container">
        <div class="detail-nav-links">
            <a href="/" class="back-link">← Kembali ke Dashboard</a>
            <a href="{{ route('packages.index') }}" class="back-link">Lihat Semua Paket →</a>
        </div>

        @if (session('success'))
            <div class="alert-success">
                ✓ {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert-danger">
                ⚠ {{ session('error') }}
            </div>
        @endif

        <div class="detail-card">
            <div class="detail-header">
                <div>
                    <h2>Detail Paket #{{ $package->id }}</h2>
                    <span class="detail-subtitle">Informasi lengkap pencatatan paket</span>
                </div>

                @php
                    $status = $package->status;
                    $badgeClass = match($status) {
                        'Picked Up' => 'badge-success',
                        'Perlu Tindak Lanjut' => 'badge-danger',
                        default => 'badge-warning',
                    };
                @endphp
                <span class="badge {{ $badgeClass }}">{{ $status }}</span>
            </div>

            <div class="detail-grid">
                <div class="detail-item">
                    <label>Nama Pemilik Paket</label>
                    <p>{{ $package->owner_name }}</p>
                </div>

                <div class="detail-item">
                    <label>Tanggal Diterima</label>
                    <p>{{ $package->received_at->format('d/m/Y H:i') }} WIB</p>
                </div>

                <div class="detail-item">
                    <label>Petugas Satpam Penerima</label>
                    <p>{{ $package->receiver->name ?? '-' }}</p>
                </div>
            </div>

            {{-- Jika paket sudah diambil --}}
            @if ($package->pickup)
                <hr class="divider">
                <h3 class="section-title">Bukti & Informasi Pengambilan</h3>
                <div class="detail-grid">
                    <div class="detail-item">
                        <label>Nama Pengambil</label>
                        <p><strong>{{ $package->pickup->taker_name }}</strong></p>
                    </div>

                    <div class="detail-item">
                        <label>Waktu Diambil</label>
                        <p>{{ $package->pickup->picked_up_at->format('d/m/Y H:i') }} WIB</p>
                    </div>

                    <div class="detail-item">
                        <label>Petugas yang Melayani</label>
                        <p>{{ $package->pickup->server->name ?? '-' }}</p>
                    </div>

                    {{-- Bukti Tanda Tangan Digital --}}
                    <div class="detail-item">
                        <label>Tanda Tangan Digital Pengambil</label>
                        <div class="signature-preview-box">
                            <img
                                src="{{ asset('storage/' . $package->pickup->signature_path) }}"
                                alt="Tanda Tangan {{ $package->pickup->taker_name }}"
                                class="signature-preview-img"
                            >
                        </div>
                    </div>
                </div>
            @else
                {{-- Jika paket belum diambil --}}
                <div class="action-footer">
                    <a href="{{ route('packages.pickup.create', $package) }}" class="btn-pickup">
                        ✓ Proses Pengambilan Paket
                    </a>
                </div>
            @endif

        </div>
    </div>

</body>
</html>

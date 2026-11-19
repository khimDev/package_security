<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Pengambilan Paket #{{ $package->id }} | Package Security</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
</head>

<body>

    <div class="detail-container">
        <a href="{{ route('packages.show', $package) }}" class="back-link">
            ← Kembali ke Detail Paket
        </a>

        @if (session('error'))
            <div class="alert-danger">
                ⚠ {{ session('error') }}
            </div>
        @endif

        <div class="detail-card">
            <div class="detail-header">
                <div>
                    <h2>Pengambilan Paket #{{ $package->id }}</h2>
                    <span class="detail-subtitle">Verifikasi pengambilan dan bukti tanda tangan digital</span>
                </div>
            </div>

            <!-- Ringkasan Paket -->
            <div class="pickup-package-summary">
                <div class="summary-item">
                    <span>Pemilik Paket</span>
                    <strong>{{ $package->owner_name }}</strong>
                </div>
                <div class="summary-item">
                    <span>Tanggal Masuk</span>
                    <p>{{ $package->received_at->format('d/m/Y H:i') }} WIB</p>
                </div>
                <div class="summary-item">
                    <span>Petugas Jaga</span>
                    <p>{{ Auth::user()->name }} (Sedang Melayani)</p>
                </div>
            </div>

            <hr class="divider">

            <form action="{{ route('packages.pickup.store', $package) }}" method="POST" id="pickupForm">
                @csrf

                <!-- Nama Orang yang Mengambil -->
                <div class="form-group">
                    <label for="taker_name">
                        Nama Pengambil Paket <span class="required-star">*</span>
                    </label>
                    <input
                        type="text"
                        id="taker_name"
                        name="taker_name"
                        placeholder="Masukkan nama orang yang mengambil"
                        value="{{ old('taker_name', $package->owner_name) }}"
                        required
                        autocomplete="off"
                    >
                    <small class="form-hint">
                        Boleh diisi nama perwakilan jika diambilkan oleh orang lain.
                    </small>
                    @error('taker_name')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Area Kanvas Tanda Tangan Digital -->
                <div class="form-group">
                    <label>
                        Tanda Tangan Digital <span class="required-star">*</span>
                    </label>
                    <p class="signature-instruction">
                        ✍️ Bubuhkan tanda tangan di dalam kotak menggunakan jari atau stylus di layar:
                    </p>

                    <div class="canvas-wrapper" id="canvasWrapper">
                        <canvas id="signaturePad" class="signature-canvas"></canvas>
                        <div class="canvas-placeholder" id="canvasPlaceholder">
                            Area Tanda Tangan
                        </div>
                    </div>

                    <div class="canvas-actions">
                        <button type="button" class="btn-clear-canvas" id="btnClearSignature">
                            ↺ Bersihkan / Ulangi Tanda Tangan
                        </button>
                    </div>

                    <input type="hidden" name="signature_data" id="signatureData">

                    <span class="form-error" id="signatureError" style="display: none;">
                        Tanda tangan digital wajib dibubuhkan sebelum konfirmasi.
                    </span>
                    @error('signature_data')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Tombol Konfirmasi -->
                <div class="action-footer">
                    <button type="submit" class="btn-pickup" id="btnSubmitPickup">
                        ✓ Konfirmasi Pengambilan & Simpan Tanda Tangan
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- JavaScript Canvas Signature Pad Responsif Touch & Mouse -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const canvas = document.getElementById('signaturePad');
            const wrapper = document.getElementById('canvasWrapper');
            const placeholder = document.getElementById('canvasPlaceholder');
            const btnClear = document.getElementById('btnClearSignature');
            const form = document.getElementById('pickupForm');
            const signatureInput = document.getElementById('signatureData');
            const signatureError = document.getElementById('signatureError');
            const ctx = canvas.getContext('2d');

            let isDrawing = false;
            let hasDrawn = false;

            // Inisialisasi ukuran kanvas sesuai resolusi layar HP (Retina display support)
            function resizeCanvas() {
                const rect = wrapper.getBoundingClientRect();
                const dpr = window.devicePixelRatio || 1;

                // Simpan konten jika sudah ada coretan
                let tempCanvasData = null;
                if (hasDrawn) {
                    tempCanvasData = canvas.toDataURL();
                }

                canvas.width = rect.width * dpr;
                canvas.height = 200 * dpr;
                canvas.style.width = rect.width + 'px';
                canvas.style.height = '200px';

                ctx.scale(dpr, dpr);
                ctx.lineCap = 'round';
                ctx.lineJoin = 'round';
                ctx.lineWidth = 2.5;
                ctx.strokeStyle = '#0f172a'; // Tinta biru-gelap pekat

                if (tempCanvasData) {
                    const img = new Image();
                    img.onload = function () {
                        ctx.drawImage(img, 0, 0, rect.width, 200);
                    };
                    img.src = tempCanvasData;
                }
            }

            resizeCanvas();
            window.addEventListener('resize', resizeCanvas);

            // Menghitung koordinat sentuhan jari / kursor mouse dengan akurat
            function getCoordinates(e) {
                const rect = canvas.getBoundingClientRect();
                let clientX, clientY;

                if (e.touches && e.touches.length > 0) {
                    clientX = e.touches[0].clientX;
                    clientY = e.touches[0].clientY;
                } else {
                    clientX = e.clientX;
                    clientY = e.clientY;
                }

                return {
                    x: clientX - rect.left,
                    y: clientY - rect.top
                };
            }

            // Memulai goresan
            function startDrawing(e) {
                e.preventDefault();
                isDrawing = true;
                const pos = getCoordinates(e);
                ctx.beginPath();
                ctx.moveTo(pos.x, pos.y);
            }

            // Melanjutkan goresan
            function draw(e) {
                if (!isDrawing) return;
                e.preventDefault();

                const pos = getCoordinates(e);
                ctx.lineTo(pos.x, pos.y);
                ctx.stroke();

                if (!hasDrawn) {
                    hasDrawn = true;
                    if (placeholder) placeholder.style.display = 'none';
                    signatureError.style.display = 'none';
                }
            }

            // Mengakhiri goresan
            function stopDrawing(e) {
                if (!isDrawing) return;
                e.preventDefault();
                isDrawing = false;
            }

            // Event Listeners untuk Touch (Layar HP)
            canvas.addEventListener('touchstart', startDrawing, { passive: false });
            canvas.addEventListener('touchmove', draw, { passive: false });
            canvas.addEventListener('touchend', stopDrawing, { passive: false });
            canvas.addEventListener('touchcancel', stopDrawing, { passive: false });

            // Event Listeners untuk Mouse (Desktop / Laptop)
            canvas.addEventListener('mousedown', startDrawing);
            canvas.addEventListener('mousemove', draw);
            canvas.addEventListener('mouseup', stopDrawing);
            canvas.addEventListener('mouseleave', stopDrawing);

            // Bersihkan Kanvas
            btnClear.addEventListener('click', function () {
                const rect = wrapper.getBoundingClientRect();
                ctx.clearRect(0, 0, rect.width, 200);
                hasDrawn = false;
                signatureInput.value = '';
                if (placeholder) placeholder.style.display = 'block';
                signatureError.style.display = 'none';
            });

            // Validasi sebelum submit form
            form.addEventListener('submit', function (e) {
                if (!hasDrawn) {
                    e.preventDefault();
                    signatureError.style.display = 'block';
                    wrapper.classList.add('canvas-error');
                    wrapper.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    return false;
                }

                // Simpan hasil kanvas sebagai data PNG Base64
                signatureInput.value = canvas.toDataURL('image/png');
            });
        });
    </script>

</body>

</html>

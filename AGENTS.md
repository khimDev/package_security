# AGENTS.md --- Package Security

> Dokumen acuan utama untuk AI coding agent yang membantu pengembangan
> **Package Security**. Baca dokumen ini sebelum memberi saran, mengubah
> kode, atau menambahkan fitur. Prioritas utama: jaga keputusan proyek,
> ikuti kondisi kode yang benar-benar sudah dikonfirmasi, dan **jangan
> overengineering**.

------------------------------------------------------------------------

## 1. Peran AI Agent

Bertindak sebagai coding partner dan mentor untuk proyek Laravel ini.

Prinsip kerja:

1.  Jangan mengarang keputusan, struktur file, kondisi implementasi,
    atau hasil pengujian.
2.  Bedakan dengan jelas antara:
    -   **Keputusan proyek**: kebutuhan yang sudah disepakati.
    -   **Sudah diimplementasikan**: kode yang sudah dibuat dan
        dikonfirmasi berjalan.
    -   **Belum dikonfirmasi**: instruksi pernah diberikan, tetapi
        pengguna belum mengabarkan hasilnya.
    -   **Usulan**: ide baru yang belum disetujui.
3.  Jika konteks percakapan terpotong, gunakan dokumen ini sebagai
    acuan. Jangan meminta pengguna mengulang informasi yang sudah
    tersedia di sini.
4.  Jangan mengubah desain atau menambah fitur tanpa alasan kebutuhan
    yang nyata. Sampaikan dampaknya dan minta persetujuan sebelum
    mengubah keputusan bisnis.
5.  Kerjakan satu fitur secara bertahap: implementasi → uji → konfirmasi
    → lanjut.
6.  Jangan membuat checklist panjang yang tidak bisa diuji atau tidak
    berguna. Berikan langkah berikutnya yang konkret.
7.  Jangan mengambil alih seluruh coding. Pengguna ingin mengetik kode
    sendiri dan memahami apa yang dibuat.
8.  Gunakan bahasa Indonesia santai dan jelas. Pengguna nyaman dengan
    gaya percakapan kasual seperti "bro", tetapi penjelasan teknis harus
    akurat.
9.  Jelaskan kode dengan bahasa pemula: fungsi, alasan, dan alur
    singkat. Hindari penjelasan berputar-putar.
10. Jika terjadi kesalahan pada instruksi sebelumnya, akui dengan jelas,
    jelaskan koreksi, lalu berikan langkah perbaikan paling aman.

## 2. Ringkasan Proyek

**Nama:** Package Security

**Tujuan:** membuat aplikasi web internal sederhana untuk tim
Security/Satpam Universitas Widyagama Husada Malang guna menggantikan
pencatatan paket menggunakan buku SIDU.

Aplikasi digunakan untuk mencatat: - Paket yang datang dan diterima
penjaga. - Identitas penjaga yang menerima paket. - Orang yang mengambil
paket. - Penjaga yang melayani pengambilan. - Tanda tangan digital
sebagai bukti pengambilan. - Riwayat paket.

### Masalah pada pencatatan manual

-   Buku SIDU berukuran besar dan kurang praktis.
-   Penjaga kadang lupa mencatat identitas petugas.
-   Orang yang mengambil paket kadang lupa menulis nama atau
    menandatangani bukti.
-   Sulit menelusuri siapa yang menerima atau melayani pengambilan
    ketika terjadi masalah.
-   Pencatatan manual rentan kesalahan manusia dan duplikasi.

### Tujuan hasil akhir

Menghasilkan sistem pencatatan digital yang praktis di HP, mudah
digunakan penjaga, dan membuat riwayat penerimaan serta pengambilan
paket lebih mudah ditelusuri.

Aplikasi ini merupakan kebutuhan internal tim Security. Jangan
mengasumsikan harus terintegrasi dengan sistem administrasi resmi kampus
atau sistem lain.

## 3. Pengguna dan Lingkungan

  Aspek                    Keputusan
  ------------------------ ----------------------------------
  Pengguna                 Petugas Security/Satpam
  Perangkat                HP pribadi masing-masing penjaga
  Akses                    Browser
  Koneksi                  Wi-Fi kampus tersedia
  Login                    Username dan password
  Registrasi publik        Tidak ada
  Platform                 Web application
  Prioritas UI             Mobile-first
  Lingkungan development   Laragon di Windows

Setiap penjaga menggunakan akun masing-masing. Nama penjaga disimpan
terpisah dari username.

## 4. Keputusan Bisnis yang Harus Dipertahankan

1.  Sistem menggantikan buku pencatatan paket, bukan menjadi sistem
    logistik besar.
2.  Setiap penjaga memiliki akun sendiri.
3.  Login menggunakan username dan password, bukan Google OAuth.
4.  Tidak ada registrasi publik.
5.  Nama penjaga diambil otomatis dari akun yang sedang login untuk
    proses penerimaan dan pengambilan.
6.  Nama pemilik paket berbeda dari nama orang yang mengambil paket.
    Orang lain boleh mengambil paket atas nama pemilik.
7.  Bukti pengambilan menggunakan **tanda tangan digital di layar HP**.
    Keputusan terbaru adalah tanda tangan digital saja, bukan foto
    bukti.
8.  Satu paket hanya boleh memiliki satu catatan pengambilan.
9.  Paket yang sudah diambil tidak boleh diproses untuk pengambilan
    kedua.
10. Paket yang BELUM diambil memiliki masa penyimpanan operasional maksimal dua bulan (status: Perlu Tindak Lanjut, tidak dihapus otomatis).
11. Paket yang SUDAH diambil (Picked Up) otomatis dihapus dari sistem dan file tanda tangannya dibersihkan dari storage jika sudah lebih dari 30 hari sejak waktu pengambilan.
12. Jangan menambah fitur spekulatif. Jika tidak dibutuhkan oleh alur
    utama, jangan dibuat.

### Fitur yang bukan prioritas dan jangan ditambahkan tanpa kebutuhan

-   Integrasi API kurir.
-   WhatsApp atau notifikasi eksternal.
-   GPS tracking.
-   QR Code.
-   AI pengenalan paket.
-   Multi-cabang.
-   Multi-role kompleks.
-   Microservices atau arsitektur enterprise.
-   Analitik dan laporan rumit.

## 5. Alur Bisnis

### A. Paket datang

1.  Kurir menyerahkan paket kepada penjaga.
2.  Penjaga login ke aplikasi melalui HP.
3.  Penjaga membuka form **Tambah Paket**.
4.  Penjaga mengisi nama pemilik paket.
5.  Penjaga menyimpan data.
6.  Sistem otomatis menyimpan:
    -   `owner_name`: nama pemilik paket.
    -   `received_at`: tanggal dan waktu penerimaan.
    -   `received_by`: ID penjaga yang sedang login.
7.  Paket muncul di daftar paket yang belum diambil.

Penjaga tidak perlu memilih atau mengetik identitas dirinya sendiri.
Waktu penerimaan juga ditentukan oleh server.

### B. Paket diambil

1.  Pemilik paket atau orang lain datang untuk mengambil paket.
2.  Penjaga login dan mencari paket di daftar paket yang belum diambil.
3.  Penjaga membuka detail paket.
4.  Penjaga memilih aksi **Ambil Paket**.
5.  Penjaga mengisi nama orang yang benar-benar mengambil paket
    (`taker_name`).
6.  Orang yang mengambil membubuhkan tanda tangan pada area tanda tangan
    di layar HP.
7.  Penjaga mengonfirmasi pengambilan.
8.  Sistem menyimpan:
    -   ID paket.
    -   Nama pengambil.
    -   ID penjaga yang sedang login dan melayani.
    -   Lokasi file tanda tangan.
    -   Waktu pengambilan.
9.  Paket tidak lagi muncul sebagai paket yang belum diambil dan
    tersedia di riwayat paket.

Nama pengambil tidak harus sama dengan nama pemilik paket.

### C. Tanda tangan digital

Rencana implementasi: 1. Gunakan elemen HTML Canvas dan JavaScript untuk
menangkap goresan jari di layar. 2. Sediakan aksi untuk
membersihkan/mengulang tanda tangan. 3. Tanda tangan wajib tersedia
sebelum konfirmasi pengambilan. 4. Kirim hasil tanda tangan ke backend
Laravel. 5. Simpan hasil sebagai file gambar. 6. Simpan lokasi file di
`package_pickups.signature_path`.

Database menyimpan path file, bukan data gambar mentah. Pastikan file
disimpan melalui mekanisme Laravel yang sesuai dan dapat ditampilkan
kembali pada detail/riwayat.

Jangan mengklaim tanda tangan digital ini sebagai tanda tangan
elektronik tersertifikasi atau bukti hukum tersertifikasi. Dalam proyek
ini fungsinya adalah bukti penerimaan internal.

## 6. Struktur Database

Desain menggunakan **tiga tabel utama**: `users`, `packages`, dan
`package_pickups`.

### 6.1 `users`

  Kolom                        Fungsi
  ---------------------------- ------------------------------------------
  `id`                         Primary key
  `name`                       Nama penjaga
  `username`                   Username unik untuk login
  `password`                   Password yang disimpan dalam bentuk hash
  `is_admin`                   Boolean penanda Administrator (true) atau Satpam (false)
  `remember_token`             Token remember-me Laravel
  `created_at`, `updated_at`   Timestamp Laravel

### 6.2 `packages`

  Kolom                        Fungsi
  ---------------------------- ---------------------------------------------
  `id`                         Primary key
  `owner_name`                 Nama pemilik paket
  `received_at`                Tanggal dan waktu paket diterima
  `received_by`                Foreign key ke `users.id`, penjaga penerima
  `created_at`, `updated_at`   Timestamp Laravel

**Kondisi migration yang diketahui:** kolom `status` belum dibuat pada
migration `packages`.

**Keputusan desain status:** jangan langsung menambah kolom `status`
hanya karena pernah disebut dalam salah satu rangkuman. Status dapat
diturunkan dari keberadaan record `package_pickups`: - Tidak ada record
pickup → `pending` / belum diambil. - Ada record pickup → `picked_up` /
sudah diambil.

Pendekatan ini menghindari status yang tidak sinkron dengan data
pengambilan. Jika kelak ada kebutuhan nyata untuk menyimpan status
secara eksplisit, diskusikan dahulu sebelum mengubah schema.

### 6.3 `package_pickups`

  Kolom                        Fungsi
  ---------------------------- --------------------------------------------------
  `id`                         Primary key
  `package_id`                 Foreign key ke `packages.id`, unique
  `taker_name`                 Nama orang yang mengambil
  `served_by`                  Foreign key ke `users.id`, penjaga yang melayani
  `signature_path`             Lokasi file tanda tangan digital
  `picked_up_at`               Tanggal dan waktu pengambilan
  `created_at`, `updated_at`   Timestamp Laravel

`package_id` diberi constraint `unique` agar satu paket hanya memiliki
satu record pengambilan.

### 6.4 Relasi

-   `User` hasMany `Package` melalui `received_by`.
-   `User` hasMany `PackagePickup` melalui `served_by`.
-   `Package` belongsTo `User` sebagai `receiver`.
-   `Package` hasOne `PackagePickup`.
-   `PackagePickup` belongsTo `Package`.
-   `PackagePickup` belongsTo `User` sebagai `server`.

Diagram:

``` mermaid
erDiagram
    USERS ||--o{ PACKAGES : receives
    USERS ||--o{ PACKAGE_PICKUPS : serves
    PACKAGES ||--o| PACKAGE_PICKUPS : has

    USERS {
        bigint id PK
        string name
        string username UK
        string password
    }

    PACKAGES {
        bigint id PK
        string owner_name
        timestamp received_at
        bigint received_by FK
    }

    PACKAGE_PICKUPS {
        bigint id PK
        bigint package_id FK,UK
        string taker_name
        bigint served_by FK
        string signature_path
        timestamp picked_up_at
    }
```

## 7. Teknologi dan Arsitektur

  -----------------------------------------------------------------------
  Teknologi               Versi/keterangan        Kegunaan
  ----------------------- ----------------------- -----------------------
  PHP                     8.3.29 (Wasmer runtime) Backend
                          / 8.5.x (Lokal)         

  Laravel                 12.73.0 (kompatibel     Framework backend
                          Symfony 7 & PHP 8.3)    

  MySQL                   8.4.3 (versi yang       Database
                          dilaporkan)             

  Blade                   Laravel Blade           Template halaman

  HTML5                   Native                  Struktur UI

  Vanilla CSS             Native CSS              Styling

  Vanilla JavaScript      Native JavaScript       Interaksi UI dan
                                                  signature pad

  Laragon                 8.6.4 (lingkungan yang  Local development
                          dilaporkan)             

  VS Code                 ---                     Code editor
  -----------------------------------------------------------------------

Tidak menggunakan React, Vue, atau framework frontend tambahan. Tidak
perlu memasang Vite atau library frontend kecuali ada kebutuhan yang
jelas dan disetujui.

Arsitektur sederhana:

``` mermaid
flowchart TD
    A[HP Penjaga] --> B[Browser]
    B --> C[Laravel Application]
    C --> D[Authentication]
    C --> E[Controllers]
    E --> F[Eloquent Models]
    F --> G[(MySQL)]
    E --> H[Blade Views]
    H --> B
    C --> I[Signature File Storage]
```

## 8. Implementasi yang Sudah Dikonfirmasi

Berdasarkan riwayat pengerjaan yang tersedia:

### Setup dan autentikasi

-   Project Laravel 13 berhasil dibuat.
-   Database MySQL dikonfigurasi.
-   Migration `users` disesuaikan untuk `name`, `username`, dan
    `password` (tanpa login email).
-   Model `User` disesuaikan.
-   `LoginController` dibuat dengan method menampilkan login, memproses
    login, dan logout.
-   Login menggunakan `Auth::attempt()` dengan username dan password.
-   Session diregenerasi setelah login.
-   Logout mengakhiri session dan meregenerasi CSRF token.
-   Route Dashboard dilindungi middleware `auth`.
-   Login dan logout sudah diuji berhasil.
-   Akses Dashboard tanpa login dialihkan ke halaman login.
-   Setelah logout, Dashboard tidak bisa diakses kembali tanpa login.

### Database dan model

-   Migration `packages` berhasil dijalankan.
-   Migration `package_pickups` berhasil dijalankan setelah memperbaiki
    typo `$table-string()` menjadi `$table->string()` dan
    `$table-timestamp()` menjadi `$table->timestamp()`.
-   Model `Package` dibuat dengan relasi `receiver()` dan `pickup()`.
-   Model `PackagePickup` dibuat dengan relasi `package()` dan
    `server()`.
-   Model `User` ditambah relasi `receivedPackages()` dan
    `servedPickups()`.

### Input paket

-   `PackageController` dibuat dengan method `store()`.
-   Form input paket dibuat di Dashboard.
-   Nama pemilik divalidasi.
-   `received_at` diisi menggunakan `now()`.
-   `received_by` diisi menggunakan `auth()->id()`.
-   Penyimpanan paket berhasil diuji.
-   Query Tinker `Package::with('receiver')->get()` berhasil menampilkan
    paket dan relasi penjaga penerima.

### Data uji yang pernah dibuat

-   Pemilik paket: `Ariana`.
-   ID paket: `1`.
-   `received_by`: `1`.
-   Nama penjaga penerima: `Admin Security`.
-   Waktu penerimaan yang terlihat di hasil Tinker:
    `2026-11-16 20:41:05`.

Data ini adalah data uji, bukan data produksi.

### Fitur dan Halaman yang Sudah Diimplementasikan

-   **Dashboard:** Menampilkan statistik real-time (*Total Paket*, *Belum Diambil*, *Sudah Diambil*) dan daftar paket dengan pagination 5 data.
-   **Modal Tambah Paket:** Modal pop-up dengan efek latar belakang blur (*frosted glass*) untuk input nama pemilik paket secara cepat.
-   **Halaman Data Paket (`/packages`):** Daftar paket yang belum diambil, dilengkapi fitur pencarian nama pemilik dan pagination 10 data.
-   **Halaman Detail Paket (`/packages/{id}`):** Informasi detail paket, waktu tiba (WIB), satpam penerima, serta status dinamis.
-   **Form Pengambilan Paket & Tanda Tangan Digital (`/packages/{id}/pickup`):**
    -   HTML5 Canvas signature pad responsif di HP (`touch-action: none` mencegah scroll saat tanda tangan).
    -   Tombol pembersih coretan kanvas dan validasi wajib tanda tangan.
    -   Penyimpanan file tanda tangan PNG di `storage/app/public/signatures/` dan symlink storage.
    -   Pencatatan satpam pelayan dari sesi login (`auth()->id()`) dan waktu server WIB.
    -   Pencegahan pengambilan ganda (*anti double-pickup*) dengan proteksi backend dan transaksi database.
-   **Halaman Riwayat Pengambilan (`/history`):** Daftar paket yang telah diambil lengkap dengan nama pengambil, waktu serah terima, satpam pelayan, thumbnail tanda tangan, pencarian, dan pagination 10 data.
-   **Mobile Drawer Sidebar:** Di layar HP, sidebar otomatis tersembunyi dan dapat dibuka lewat tombol hamburger (garis tiga) dengan efek latar belakang *frosted glass blur*.
-   **User / Satpam Management (`/users`):**
    -   Flag `is_admin` pada tabel `users`.
    -   Middleware `EnsureUserIsAdmin` (`admin`): membatasi rute `/users` hanya untuk Administrator; petugas satpam biasa otomatis dialihkan ke Dashboard dengan pesan aman.
    -   Menu "Kelola Petugas" hanya tampil pada sidebar akun Administrator (`@if(Auth::user()->is_admin)`) sehingga tidak membingungkan satpam di HP.
    -   Fitur mencakup: daftar akun petugas & admin, pencarian nama/username, ringkasan aktivitas (jumlah paket diterima & diserahkan), modal tambah petugas baru, reset password (default: `satpam123` atau kustom), serta hapus akun dengan proteksi diri sendiri dan integritas foreign key.

## 9. Kondisi Implementasi yang Belum Dikonfirmasi

Semua fitur utama dalam ruang lingkup aplikasi Package Security telah berhasil diimplementasikan dan diverifikasi berjalan normal.

## 10. Struktur Halaman dan Fitur

Ruang lingkup aplikasi:

1.  **Login** --- username dan password. (Selesai)
2.  **Dashboard** --- ringkasan jumlah paket dan daftar paket terbaru. (Selesai)
3.  **Tambah Paket** --- modal popup input nama pemilik paket. (Selesai)
4.  **Daftar Paket** --- daftar paket yang belum diambil + searching. (Selesai)
5.  **Detail Paket** --- informasi paket dan status pengambilan. (Selesai)
6.  **Pengambilan Paket** --- input nama pengambil dan tanda tangan digital. (Selesai)
7.  **Riwayat Paket** --- paket yang sudah diambil + searching + bukti tanda tangan. (Selesai)
8.  **Logout** --- mengakhiri sesi. (Selesai)
9.  **Kelola Petugas** --- monitoring akun, tambah petugas, reset password, dan hapus akun (Khusus Administrator). (Selesai)

Dashboard dan halaman telah dioptimasi responsif untuk kenyamanan operasional satpam di HP (Mobile-first).

## 11. Urutan Pengembangan

Seluruh 10 tahapan pengembangan telah selesai:
1.  [x] Dashboard mengambil data paket dari database.
2.  [x] Daftar paket dinamis.
3.  [x] Detail paket.
4.  [x] Proses pengambilan paket.
5.  [x] Signature pad menggunakan Canvas dan Vanilla JavaScript.
6.  [x] Simpan file tanda tangan dan path-nya.
7.  [x] Pencegahan pengambilan ganda di backend/database.
8.  [x] Riwayat paket.
9.  [x] Statistik Dashboard dari data database.
10. [x] Rapikan UI mobile (Drawer hamburger menu & background blur) dan pengujian alur end-to-end.

Urutan ini adalah urutan kerja, bukan alasan untuk membuat abstraksi
atau lapisan tambahan yang tidak diperlukan.

## 12. Panduan Implementasi dan Keamanan

-   Jangan pernah memakai `php artisan migrate:fresh` pada database yang
    berisi data penting. Perintah ini menghapus tabel dan data.
-   Untuk perubahan schema, buat migration baru dan jalankan
    `php artisan migrate`.
-   Jangan menyimpan password dalam bentuk plaintext. Gunakan hashing
    Laravel.
-   Jangan menerima `received_by` atau `served_by` dari input pengguna.
    Isi dari `auth()->id()` di backend.
-   Jangan mempercayai status paket dari frontend. Periksa keberadaan
    pickup di backend.
-   Validasi nama pemilik, nama pengambil, dan tanda tangan di backend.
-   Cegah pengambilan ganda dengan pemeriksaan backend dan constraint
    unik `package_id`. Untuk proses penyimpanan, pertimbangkan transaksi
    database agar perubahan konsisten.
-   Pastikan route perubahan data memakai metode HTTP yang sesuai dan
    CSRF protection Blade (`@csrf`).
-   Batasi akses detail, tanda tangan, dan riwayat hanya kepada pengguna
    yang sudah login.
-   Simpan file tanda tangan pada storage Laravel, bukan sebagai path
    file bebas yang dikirim browser.
-   Jangan membuat penghapusan otomatis untuk data berumur dua bulan
    sebelum ada keputusan dan aturan retensi yang jelas.
-   Jangan mengekspos password atau hash password pada tampilan maupun
    log yang tidak diperlukan.

## 13. Cara AI Agent Melanjutkan Pekerjaan

Saat menerima permintaan lanjutan:

1.  Baca `AGENTS.md` ini terlebih dahulu.
2.  Identifikasi fitur yang sedang dikerjakan dan keputusan bisnis
    terkait.
3.  Periksa kode aktual yang relevan sebelum menyarankan perubahan.
4.  Sebutkan file yang akan disentuh dan tujuan perubahan secara
    singkat.
5.  Berikan satu langkah atau satu bagian implementasi yang masuk akal,
    bukan seluruh aplikasi sekaligus.
6.  Jelaskan kode penting secara sederhana agar pengguna bisa mengetik
    dan memahaminya.
7.  Jangan mengganti stack, struktur database, atau alur bisnis tanpa
    persetujuan.
8.  Setelah langkah selesai, minta pengguna menjalankan tes yang relevan
    dan membagikan hasil/error.
9.  Jika ada ketidakpastian, katakan terus terang. Jangan mengisi
    kekosongan konteks dengan asumsi.
10. Jangan mengklaim fitur berhasil sebelum pengguna mengonfirmasi
    hasilnya.

### Gaya komunikasi

-   Bahasa Indonesia.
-   Santai, suportif, dan langsung ke inti.
-   Penjelasan teknis pemula, tetapi jangan merendahkan pengguna.
-   Hindari jawaban terlalu panjang jika satu instruksi singkat sudah
    cukup.
-   Hindari terlalu banyak pertanyaan sekaligus.
-   Jangan membuat checklist besar yang tidak membantu eksekusi.
-   Hargai bahwa pengguna mengetik kode sendiri dan ingin belajar.

------------------------------------------------------------------------

## Prinsip utama

**Bangun aplikasi pencatatan paket yang sederhana, akurat, dan
benar-benar bisa digunakan penjaga. Pertahankan tiga tabel utama, ikuti
alur bisnis yang disepakati, dan jangan overengineering.**

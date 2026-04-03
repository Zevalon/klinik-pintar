# Klinik Pintar

Klinik Pintar adalah aplikasi manajemen klinik berbasis web untuk membantu operasional harian, mulai dari pendaftaran pasien, antrian per poli, pemeriksaan, farmasi, kasir, inventory, hingga laporan operasional.

Paket ini sudah dirapikan agar aman dipublikasikan ke repository GitHub publik. File konfigurasi lokal yang bersifat sensitif, seperti kredensial database, tidak disertakan di dalam repository.

## Fitur utama

- Manajemen pasien
- Antrian per poli
- Pemeriksaan dan kunjungan
- Rekam medis rinci
- Farmasi dan resep
- Kasir dan billing
- Inventory obat dan stok
- Pengeluaran cabang
- Profil user mandiri
- Dashboard sesuai role
- Monitor antrian publik

## Update penting pada versi ini

### Dashboard berbeda untuk tiap role
Setiap role mendapatkan dashboard yang lebih relevan dengan pekerjaannya.

- **Super Admin / Owner / Admin Cabang**: ringkasan operasional cabang
- **Front Office**: registrasi pasien dan antrian
- **Dokter**: pasien aktif, pemeriksaan, dan kontrol lanjutan
- **Perawat**: antrian klinis dan vital sign
- **Farmasi**: resep dan stok obat
- **Kasir**: transaksi, tagihan, dan pendapatan
- **Inventory**: stok, mutasi, dan kebutuhan restok

### Rekam medis yang lebih lengkap
- Profil medis pasien
- Catatan medis per kunjungan
- Monitoring pasien kontrol rutin
- Timeline riwayat medis pasien

### Pengelolaan poli per cabang
- Tambah poli
- Ubah poli
- Nonaktifkan poli
- Aktifkan kembali poli

### Profil user mandiri
- Edit data diri sendiri
- Ganti password
- Upload, ganti, dan hapus foto profil
- Avatar default berdasarkan role

### Penyegaran tampilan
- UI lebih modern dan konsisten
- Perbaikan kontras teks dan background
- Pembersihan label atau keterangan yang tidak perlu
- Format Rupiah yang konsisten
- Format nomor telepon yang rapi

## Perbandingan singkat

### Sebelum
- Dashboard belum dibedakan per role
- Rekam medis masih terbatas
- Belum ada menu poli per cabang
- Profil user masih sederhana
- Format data belum seragam
- Tampilan beberapa halaman masih belum konsisten

### Sesudah
- Dashboard lebih fokus sesuai role
- Rekam medis lebih rinci
- Pengelolaan poli per cabang tersedia
- User bisa mengelola profil sendiri
- Format uang dan telepon lebih rapi
- Tampilan aplikasi lebih bersih dan modern

## Persyaratan sistem

- PHP 8.0 atau lebih baru
- MySQL atau MariaDB
- Apache atau Nginx
- Composer bila diperlukan

## Struktur konfigurasi untuk repo publik

Repository ini **tidak menyimpan file rahasia** seperti kredensial database produksi atau lokal. Untuk itu, repository hanya menyertakan file contoh:

- `.env.example`
- `application/config/database.example.php`

File berikut tetap dianggap lokal dan tidak boleh ikut commit:

- `.env`
- `.env.local`
- `application/config/database.php`

## Cara setup lokal

### 1. Clone repository

```bash
git clone https://github.com/Zevalon/klinik-pintar.git
cd klinik-pintar
```

### 2. Buat konfigurasi database

Ada dua cara yang bisa dipakai.

#### Opsi A — disarankan untuk repo publik
Salin file `.env.example` menjadi `.env`, lalu isi nilainya.

```bash
cp .env.example .env
```

Isi variabel berikut di file `.env`:

```env
APP_BASE_URL=http://localhost/klinik-pintar
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=klinik_pintar
DB_USER=root
DB_PASS=
DB_CHARSET=utf8mb4
```

#### Opsi B — konfigurasi lokal berbasis PHP
Salin file contoh berikut:

```bash
cp application/config/database.example.php application/config/database.php
```

Lalu isi nilainya sesuai server lokal yang digunakan.

> Catatan: file `application/config/database.php` bersifat lokal dan jangan di-commit ke GitHub.

### 3. Import database

Gunakan salah satu file berikut:

- `sql/klinik_pintar.sql` untuk struktur dan data contoh
- `sql/klinik_pintar_schema_only.sql` untuk struktur saja

### 4. Jalankan patch tambahan bila diperlukan

Patch modul baru tersedia di folder `sql`:

- `sql/patch_medical_records_module.sql`
- `sql/patch_user_profile_module.sql`

Sebagian modul juga sudah memiliki mekanisme pengecekan struktur tabel saat fitur dibuka. Walau begitu, tetap lebih aman menjalankan patch di lingkungan development atau staging sebelum dipakai di production.

## Catatan untuk repository publik

- Jangan pernah commit file `.env`.
- Jangan pernah commit `application/config/database.php`.
- Ganti password user demo setelah import database contoh.
- Periksa kembali file upload, log, dan cache sebelum membuat commit.

## Ringkasan perubahan

Daftar perubahan lebih ringkas tersedia di file berikut:

- `CHANGELOG.md`

## Lisensi

Mengikuti lisensi yang sudah ada di repository ini.

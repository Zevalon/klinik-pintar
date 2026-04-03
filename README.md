# Klinik Pintar

Klinik Pintar adalah aplikasi manajemen klinik berbasis web untuk membantu operasional harian mulai dari pendaftaran pasien, antrian per poli, pemeriksaan, farmasi, kasir, inventory, hingga laporan operasional.

Versi pada paket ini sudah disiapkan agar mudah diajukan sebagai update ke repository GitHub `Zevalon/klinik-pintar` dengan struktur proyek yang tetap mengikuti repository saat ini.

## Gambaran singkat

Aplikasi ini cocok untuk alur layanan klinik atau fasilitas kesehatan tingkat pertama dengan proses utama:

1. pasien didaftarkan
2. pasien masuk antrian sesuai poli
3. pasien diperiksa oleh tenaga medis
4. resep diteruskan ke farmasi
5. pembayaran diselesaikan di kasir
6. data operasional dapat dipantau dari dashboard dan laporan

## Yang baru di update ini

Update ini tidak hanya merapikan tampilan, tetapi juga menambah modul dan alur kerja yang lebih matang.

### 1. CRUD poli per cabang
- menu data poli
- tambah, ubah, nonaktifkan, dan aktifkan kembali poli
- pengelolaan poli mengikuti cabang aktif
- penghapusan dibuat aman dengan pendekatan soft delete

### 2. Rekam medis rinci
- profil medis pasien
- catatan medis per kunjungan
- monitoring pasien kondisi khusus atau kontrol rutin
- timeline riwayat medis pasien

### 3. Profil user mandiri
- setiap user dapat mengubah data diri sendiri
- ganti password
- upload, ganti, dan hapus foto profil
- avatar default berdasarkan role bila belum ada foto pribadi

### 4. Dashboard berbeda untuk tiap role
- super admin, owner, admin cabang
- front office
- dokter
- perawat
- farmasi
- kasir
- inventory

Setiap dashboard difokuskan pada pekerjaan utama role tersebut agar tampilan lebih relevan dan tidak terlalu ramai.

### 5. Penyegaran tampilan aplikasi
- tampilan admin lebih modern dan konsisten
- perbaikan kontras warna agar teks tetap terbaca
- pembersihan teks yang tidak perlu agar tampilan lebih rapi
- avatar role berbasis ikon Font Awesome yang rapi dan konsisten

### 6. Format data lebih rapi
- seluruh nominal uang ditampilkan dalam format Rupiah
- input dan tampilan nomor telepon dirapikan ke format `XXXX-XXXX-XXXX`

## Perbandingan sebelum dan sesudah

### Sebelum
- dashboard masih umum dan belum dibedakan per role
- belum ada modul rekam medis rinci yang terstruktur
- belum ada pengelolaan poli per cabang dari menu khusus
- user belum punya halaman profil mandiri yang lengkap
- format uang dan nomor telepon belum konsisten
- tampilan beberapa halaman masih belum seragam

### Sesudah
- dashboard menjadi spesifik sesuai peran pengguna
- rekam medis lebih lengkap dan mendukung pemantauan pasien rutin
- ada menu khusus untuk mengelola poli per cabang
- user dapat mengelola profil dan foto profil sendiri
- tampilan lebih bersih, lebih modern, dan lebih konsisten
- format Rupiah dan telepon menjadi seragam di berbagai halaman

## Modul utama

- Dashboard
- Pasien
- Antrian
- Pemeriksaan
- Rekam Medis
- Farmasi
- Kasir
- Inventory
- Pengeluaran Cabang
- Profil Saya
- Monitor Antrian Publik

## Kebutuhan sistem

- PHP 8.0 atau lebih baru
- MySQL atau MariaDB
- Web server Apache atau Nginx
- Composer untuk dependency PHP bila diperlukan

## Cara menjalankan

### 1. Clone repository

```bash
git clone https://github.com/Zevalon/klinik-pintar.git
cd klinik-pintar
```

### 2. Salin konfigurasi database

Buat file `application/config/database.php` dengan menyalin dari file contoh:

```bash
cp application/config/database.example.php application/config/database.php
```

Lalu isi host, port, nama database, username, dan password sesuai lingkungan server.

### 3. Import database

Gunakan salah satu file berikut:

- `sql/klinik_pintar.sql` untuk data lengkap
- `sql/klinik_pintar_schema_only.sql` untuk struktur database saja

### 4. Jalankan patch tambahan bila diperlukan

Patch tersedia untuk modul baru berikut:

- `sql/patch_medical_records_module.sql`
- `sql/patch_user_profile_module.sql`

Catatan:
- sebagian modul juga sudah dibuat cukup aman untuk melakukan pengecekan struktur tabel saat fitur dibuka
- tetap disarankan menjalankan patch SQL pada lingkungan staging atau development sebelum deploy ke production

## Catatan implementasi update

- dashboard kini disesuaikan berdasarkan role aktif user
- tampilan avatar default tidak lagi memakai vector acak, tetapi ikon role yang konsisten
- perubahan difokuskan agar tetap menyatu dengan struktur proyek yang sudah ada
- file lokal seperti `application/config/database.php` tidak disertakan sebagai file commit karena memang bersifat khusus untuk masing-masing server

## File pendukung untuk proses pull request

Untuk mempermudah review, paket ini juga menyertakan:

- `RINGKASAN_PERUBAHAN.md`
- `PULL_REQUEST_BODY.md`

## Lisensi

Mengikuti lisensi yang sudah ada di repository ini.

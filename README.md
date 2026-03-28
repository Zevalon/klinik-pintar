# Klinik Pintar

Aplikasi manajemen klinik berbasis web untuk membantu operasional layanan klinik mulai dari pendaftaran pasien, antrian per poli, pemeriksaan dokter, validasi resep farmasi, pembayaran kasir, inventory obat, laporan operasional, hingga monitor antrian publik.

## Fitur Utama

- Manajemen data pasien
- Pendaftaran kunjungan dan antrian per poli
- Pemeriksaan pasien oleh dokter
- Validasi dan penyesuaian resep oleh farmasi
- Pembayaran tunai di kasir
- Cetak dan unduh invoice PDF
- Manajemen inventory obat
- Laporan keuangan
- Laporan stok masuk dan keluar
- Pengeluaran cabang
- Monitor antrian publik dengan suara panggilan

## Alur Operasional

1. Pasien didaftarkan ke sistem
2. Pasien dibuatkan antrian ke poli tujuan
3. Saat giliran tiba, pasien masuk ke proses pemeriksaan
4. Dokter mengisi diagnosa, tindakan, dan resep obat
5. Resep masuk ke farmasi untuk divalidasi atau disesuaikan
6. Setelah validasi farmasi selesai, data masuk ke kasir
7. Kasir memproses pembayaran tunai
8. Setelah pembayaran lunas, invoice PDF dapat diunduh

## Modul Aplikasi

### 1. Pasien
Digunakan untuk mengelola data identitas pasien dan nomor rekam medis.

### 2. Antrian
Digunakan untuk membuat antrian pasien berdasarkan poli tujuan dan memantau status pelayanan.

### 3. Pemeriksaan
Digunakan oleh dokter atau petugas pemeriksaan untuk mengisi:
- vital sign
- diagnosa
- catatan medis
- tindakan / layanan
- resep obat

### 4. Farmasi
Digunakan untuk memvalidasi resep dari dokter sebelum diteruskan ke kasir. Pada tahap ini farmasi dapat:
- menyesuaikan obat
- mengganti obat jika stok tidak tersedia
- mengubah qty
- memperbarui aturan pakai

### 5. Kasir
Digunakan untuk:
- melihat tagihan pasien yang siap dibayar
- memproses pembayaran tunai
- menghitung kekurangan atau kembalian
- menandai transaksi sebagai lunas
- mengunduh invoice PDF

### 6. Inventory
Digunakan untuk mengelola stok obat dan pergerakan barang.

### 7. Laporan Keuangan
Digunakan untuk melihat laporan pendapatan dan pengeluaran berdasarkan rentang tanggal.

### 8. Laporan Stok
Digunakan untuk melihat laporan barang masuk dan barang keluar berdasarkan rentang tanggal.

### 9. Pengeluaran Cabang
Digunakan untuk mencatat pengeluaran operasional cabang, seperti pembelian obat, alat, atau kebutuhan operasional lainnya.

### 10. Monitor Antrian Publik
Halaman publik tanpa login untuk menampilkan:
- nomor antrian yang sedang dipanggil
- nomor antrian aktif pada setiap poli
- suara panggilan otomatis

## Teknologi yang Digunakan

- PHP
- MySQL / MariaDB
- HTML, CSS, JavaScript
- Library PDF sesuai bawaan project
- Web Speech API untuk pengumuman suara pada monitor antrian

## Struktur Singkat Folder

```bash
application/
public/
system/
storage/
sql/
index.php
.htaccess
composer.json
README.md
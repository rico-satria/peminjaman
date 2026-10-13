# Peminjaman Barang SMK SMTI Padang

Aplikasi dinamis PHP–MySQL untuk peminjaman aset sementara dan jangka panjang.

## Pemisahan jenis peminjaman

Sidebar memisahkan seluruh alur menjadi **Peminjaman Sementara** dan **Peminjaman Jangka Panjang**. Setiap kelompok mempunyai katalog, formulir pengajuan, riwayat, persetujuan, dan serah terima sendiri. Kolom `assets.loan_scope` menentukan apakah aset tersedia untuk `sementara`, `jangka_panjang`, `keduanya`, atau `tidak_dipinjamkan`. Instalasi lama dimigrasikan otomatis tanpa menghapus transaksi.

## Konfigurasi

Salin `config.example.php` menjadi `config.php`, lalu isi koneksi database sesuai lingkungan server. `config.php` sengaja tidak disimpan di Git agar password database tidak ikut terunggah.

Versi 1.2 Taste Edition meningkatkan kualitas visual dan aksesibilitas tanpa mengubah alur transaksi: hierarki tipografi lebih tegas, warna institusional yang konsisten, kepadatan dashboard yang lebih efisien, fokus keyboard, respons mobile dengan backdrop, serta dukungan reduced motion.

## Instalasi XAMPP

1. Ekstrak folder ke `C:\xampp\htdocs\peminjaman-barang-smti`.
2. Jalankan Apache dan MySQL dari XAMPP Control Panel.
3. Buka `http://localhost/peminjaman-barang-smti/install.php`.
4. Klik **Instal / Reset Data Contoh**.
5. Masuk melalui `http://localhost/peminjaman-barang-smti/`.

Jika MySQL Anda memakai kata sandi, sesuaikan `config.php` terlebih dahulu.

## Akun contoh

| Peran | Username | Password |
|---|---|---|
| Admin | admin | Admin123! |
| Petugas Sarpras | sarpras | Sarpras123! |
| Waka Sarpras | waka | Waka123! |
| Kepala Tata Usaha | katu | TU123! |
| Guru | guru | Guru123! |
| Tendik | tendik | Guru123! |

## Catatan keamanan

- Kata sandi disimpan menggunakan `password_hash`.
- Query input menggunakan prepared statement.
- Formulir transaksi dilindungi token CSRF.
- Aplikasi memakai nama sesi khusus agar tidak bertabrakan dengan aplikasi PHP lain di localhost.
- Ganti seluruh kata sandi contoh sebelum digunakan secara nyata.
- Setelah instalasi selesai, hapus atau ganti nama `install.php`.

# Website Portofolio Laravel

Website portofolio pribadi dengan **panel admin** — semua data (teks, foto, warna,
dan animasi) bisa diubah tanpa menyentuh kode.

- Framework: Laravel 13 · PHP 8.4
- Frontend: Tailwind CSS v4 + JavaScript vanilla (tanpa framework JS)
- **Backend data: KroomBase** (PostgreSQL + REST API) — semua isi portofolio
  (profil, proyek, keahlian, pengalaman, pendidikan, layanan, testimoni,
  pengaturan, pesan) tersimpan di sana, begitu juga foto di bucket-nya
- Database lokal (SQLite): hanya untuk akun panel, sesi login, dan cache

> README bawaan Laravel disimpan sebagai `README-laravel-asli.md`.

---

## 1. Cara Menjalankan

```bash
cd ~/Sites/portfolio

# kalau belum pernah: siapkan database + data contoh
php artisan migrate --seed

# jalankan server
php artisan serve
```

Buka **http://127.0.0.1:8000**

### Ingin URL cantik seperti `portfolio.test`?

Herd di komputer ini memantau folder `~/Herd`, bukan `~/Sites`. Jadi salah satu:

```bash
# opsi A — daftarkan folder ini ke Herd (setelah ini buka http://portfolio.test)
cd ~/Sites/portfolio && herd link portfolio

# opsi B — pindahkan proyek ke folder pantauan Herd
mv ~/Sites/portfolio ~/Herd/portfolio    # lalu buka http://portfolio.test
```

---

## 2. Masuk ke Panel Admin

Halaman masuk **sengaja tidak ada di `/login`**. Alamatnya acak dan disimpan di
`.env`. Buka alamat itu (lihat nilainya dengan `grep PORTFOLIO_LOGIN_PATH .env`),
lalu masuk dengan:

| | |
|---|---|
| Email | `admin@portfolio.test` |
| Password | `password` |

> **Penting:** ganti password ini sebelum website dipakai online — paling mudah
> lewat menu **Kelola Akun → Edit** di panel.

### Dua peran akun

| Peran | Bisa apa |
|---|---|
| **Admin** 👑 | Semua yang bisa editor **plus** membuka menu *Kelola Akun* (tambah akun, ubah peran, nonaktifkan, hapus) |
| **Editor** ✏️ | Mengubah seluruh konten: profil, proyek, keahlian, pengalaman, pendidikan, layanan, testimoni, pengaturan tampilan, pesan masuk |

Akun Anda sendiri dibuat sebagai **admin**. Menu *Kelola Akun* hanya muncul untuk
admin; kalau editor memaksa membukanya, jawabannya **403**.

**Pengaman yang sudah aktif:**

- Admin tidak bisa menghapus atau menurunkan akunnya sendiri (tidak bisa terkunci).
- Admin aktif terakhir selalu dipertahankan — tidak bisa dihapus, dinonaktifkan,
  atau diturunkan perannya.
- Akun nonaktif tetap tidak bisa login walau passwordnya benar.
- Email akun wajib unik.

### "Jalur tikus" — menyembunyikan pintu masuk

Website publik terbuka untuk semua orang, tetapi **pintu masuk admin tidak
didaftarkan di alamat umum**. Dua nilai di `.env` mengaturnya:

```
PORTFOLIO_LOGIN_PATH=gerbang-6e9df5
PORTFOLIO_ADMIN_PATH=ruang-kendali-00de46
```

Akibatnya:

| Alamat | Hasil |
|---|---|
| `/<PORTFOLIO_LOGIN_PATH>` | halaman masuk (hanya ini yang bisa dipakai login) |
| `/<PORTFOLIO_ADMIN_PATH>` | panel admin |
| `/login`, `/admin`, `/admin/users` | **404** — tidak ada apa-apa |
| `/`, `/projects`, halaman proyek | **200** — bebas dibuka siapa saja |

Cara mengganti alamatnya:

```
cd ~/Sites/portfolio
# ubah nilainya di .env lalu:
php artisan optimize:clear
```

**Hal yang wajib dijaga:**

- Jangan pernah menautkan alamat login dari halaman publik. Footer situs sudah
  bersih dari tautan "Panel Admin" karena satu tautan saja membuat jalur tikus
  tidak ada gunanya.
- `public/robots.txt` juga sengaja tidak menyebut jalur ini.
- Simpan alamatnya di catatan pribadi Anda (password manager), bukan di README
  yang ikut ter-commit ke Git.

> Ini **keamanan lewat kerahasiaan alamat** (security by obscurity), bukan
> pengganti password yang kuat. Pakai keduanya: alamat rahasia **dan** password
> yang panjang. Kalau nanti website dipasang online, alamat acak ini juga tidak
> akan muncul di log server orang lain — tapi jangan sampai dibocorkan sendiri.

---

## 3. Apa Saja yang Bisa Diubah

| Menu di panel | Isinya |
|---|---|
| **Dashboard** | Ringkasan isi + pesan terbaru |
| **Profil Saya** | Nama, foto profil, bio, "Tentang Saya", tombol hero, sosial media, unggah CV |
| **Proyek** | Tambah/edit/hapus, thumbnail, galeri foto, teknologi, tautan demo & repo, tandai unggulan |
| **Keahlian** | Nama, kategori, ikon, tingkat penguasaan (bilah progres) |
| **Pengalaman** | Riwayat kerja, jenis pekerjaan, periode, poin pencapaian |
| **Pendidikan** | Jenjang, jurusan, tahun, IPK/predikat |
| **Layanan** | Jasa yang ditawarkan + daftar fitur |
| **Testimoni** | Ulasan klien, bintang, foto |
| **Pesan Masuk** | Semua pesan dari formulir kontak |
| **Pengaturan Tampilan** | Warna, tema, latar, animasi, judul tiap bagian, SEO, favicon |
| **Kelola Akun** (khusus admin) | Tambah/ubah/hapus akun, atur peran (Admin/Editor), aktif-nonaktifkan, lihat waktu login terakhir |

### Tentang Pengaturan Tampilan

- **Warna utama & gradasi** — menentukan warna seluruh website.
- **Mode tema** — Gelap, Terang, atau "bisa diganti pengunjung" (muncul tombol 🌙/☀️;
  pilihan pengunjung diingat browser).
- **Gaya latar** — Aurora bergerak, grid teknologi, titik-titik, atau polos.
- **Animasi** — bisa dinyalakan/dimatikan sekaligus, plus kecepatan animasi,
  efek mengetik, cahaya mengikuti kursor, efek 3D pada kartu, dan muncul saat discroll.
- **Bagian halaman** — matikan bagian yang tidak dipakai (misal Testimoni) dan ubah judulnya.
- **Reset ke default** — mengembalikan semua pengaturan ke nilai awal.

---

## 4. Animasi & Interaksi yang Terpasang

| Efek | Muncul di |
|---|---|
| Muncul saat discroll | Semua bagian |
| Efek mengetik | Judul di bagian hero |
| Cahaya mengikuti kursor | Seluruh halaman |
| Aurora / grid bergerak | Latar belakang |
| Kartu 3D mengikuti kursor | Kartu layanan & proyek |
| Kilau saat disorot | Kartu layanan |
| Angka berjalan | Statistik & bilah keahlian |
| Bilah progres terisi | Keahlian |
| Filter kategori | Keahlian & proyek |
| Slider otomatis | Testimoni |
| Lightbox | Galeri proyek |
| Tombol kembali ke atas | Semua halaman |
| Salin ke clipboard + notifikasi | Email & telepon |
| Efek mengambang & cincin berputar | Foto profil |
| Baris dinamis | Form sosial media di admin |
| Pratinjau gambar sebelum unggah | Semua form unggah |
| Modal konfirmasi hapus | Semua tombol hapus di admin |

Semua animasi **otomatis mati** kalau pengunjung mengaktifkan "reduce motion"
di perangkatnya, dan tentu bisa dimatikan dari panel admin.

---

## 5. Perintah yang Sering Dipakai

```bash
npm run build              # wajib setelah mengubah CSS/JS
npm run dev                # mode pengembangan (rebuild otomatis)

php artisan migrate --seed # siapkan akun panel + data awal
php artisan db:seed        # tambah data contoh lagi
php artisan migrate:fresh --seed   # database dari nol + data contoh
php artisan test           # jalankan pengujian otomatis
php artisan optimize:clear # bersihkan cache
```

---

## 6. Struktur Penting

```
app/
  Http/Controllers/
    PortfolioController.php     → halaman publik
    AuthController.php          → login/logout
    DashboardController.php     → dashboard admin
    Admin/                      → CRUD tiap jenis data + UserController (kelola akun)
  Http/Middleware/
    EnsureUserHasRole.php       → pembatas akses berdasarkan peran
  Kroombase/                    → lapisan data ke KroomBase
    Client.php                  → panggilan REST + upload foto ke bucket
    Model.php                   → model ala-Eloquent (fill/save/update/delete)
    QueryBuilder.php            → where/orderBy/paginate di sisi aplikasi
    Paginator.php               → paginasi untuk tampilan
  Models/                       → Profile, Project, Skill, Experience, ..., User
  Support/
    helpers.php                 → media_url(), period_label(), initials()
    PortfolioData.php           → data bersama halaman publik
    StoresFiles.php             → unggah/hapus foto ke bucket KroomBase
    Media.php                   → tautan bertanda tangan untuk menampilkan foto

config/portfolio.php            → daftar pengaturan tampilan (form admin dibuat otomatis)
config/kroombase.php            → kredensial & pengaturan backend KroomBase

database/migrations/            → struktur tabel
database/seeders/PortfolioSeeder.php → data contoh

resources/
  css/app.css                   → tema + semua animasi
  js/app.js                     → seluruh interaksi
  views/
    layouts/portfolio.blade.php → kerangka halaman publik
    layouts/admin.blade.php     → kerangka panel admin
    portfolio/                  → halaman + partial tiap bagian
    admin/                      → halaman panel admin

routes/web.php                  → semua rute
tests/Feature/                  → pengujian otomatis
```

**Mau menambah pengaturan baru?** Cukup tambahkan satu entri di
`config/portfolio.php` bagian `settings`, lalu jalankan `php artisan optimize:clear`.
Form di panel admin otomatis menampilkan fieldnya — tidak perlu mengedit Blade.
Pakai di tampilan dengan: `$settings['kunci_baru'] ?? 'default'`.

---

## 7. Sebelum Online (Produksi)

1. **Ganti password admin** (lihat bagian 2).
2. Di `.env`: set `APP_ENV=production`, `APP_DEBUG=false`, dan `APP_URL` sesuai domain.
3. Jalankan `php artisan optimize` lalu `npm run build`.
4. Arahkan document root web server ke folder `public/` (bukan folder proyek).
5. Pastikan folder `storage/` dan `bootstrap/cache/` bisa ditulis.

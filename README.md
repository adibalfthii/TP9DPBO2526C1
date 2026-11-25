# Tugas Praktikum 9 DPBO - Sistem Rekrutmen (Arsitektur MVP)

Nama: Muh. Adib Al-Fathi

NIM: 2500418

Kelas: C1

Janji

Saya Muh. Adib Al-Fathi dengan NIM 2500418 mengerjakan Tugas Praktikum 9 dalam mata kuliah Desain dan Pemrograman Berorientasi Objek untuk keberkahan-Nya maka saya tidak melakukan kecurangan seperti yang telah dispesifikasikan. Aamiin.

## 1. Desain Program

Pada tugas praktikum ini, saya melengkapi fitur CRUD pada program starter **Daftar Kandidat** sekaligus menambahkan satu modul baru yaitu **Daftar Lowongan Pekerjaan** yang masih satu tema dengan sistem rekrutmen/HR. Seluruh kode dibangun menggunakan PHP Native dan database MySQL (`mvp_kandidat_db`) dengan pola arsitektur **Model–View–Presenter (MVP) *Strict Passive View***.

### A. Penerapan Arsitektur MVP (*Strict Passive View*)
Supaya pemisahan antar komponen jelas dan sesuai aturan modul, pembagian tugas tiap bagian dibuat seperti berikut:
1. **Model (`models/`):** Mengurus koneksi database lewat class `DB.php` (PDO) dan menjalankan query SQL menggunakan *prepared statement*. Hasil query dari database dipetakan menjadi objek entity (`Kandidat` dan `Lowongan`) sebelum dikembalikan ke Presenter.
2. **View (`views/` & `template/`):** Dibuat pasif sepenuhnya (*Strict Passive View*). File di dalam `views/` tidak melakukan `require_once` ke Model maupun Entity, serta tidak punya perulangan `foreach` ataupun fungsi `count()`. Tugas View hanya membaca file `.html` di folder `template/` lalu mengganti token *placeholder* seperti `{{TABLE_ROWS}}` dan `{{TOTAL_DATA}}` dengan string yang sudah jadi.
3. **Presenter (`presenters/`):** Menjadi pusat logika yang menghubungkan Model dan View. Presenter mengambil array objek dari Model, menghitung jumlah datanya, merakit baris tabel HTML `<tr>...</tr>` lewat helper `buildRowHtml()`, dan menyiapkan data *prefill* saat form edit dibuka.
4. **Interface Contract:** Komunikasi antar komponen menggunakan kontrak interface (`KontrakModel`, `KontrakModelLowongan`, `KontrakPresenter`, `KontrakPresenterLowongan`, dan `KontrakView`) sehingga tiap lapisan tidak bergantung langsung pada implementasi konkretnya.

### B. Desain Database (`mvp_kandidat_db`)
Database terdiri dari 2 tabel utama yang digunakan oleh masing-masing modul:

1. **Tabel `kandidat`** *(Modul Starter — CRUD Lengkap)*
   * `id` (`INT`, Primary Key, Auto Increment) — ID unik kandidat
   * `nama` (`VARCHAR(100)`) — Nama lengkap pelamar
   * `posisi` (`VARCHAR(100)`) — Posisi pekerjaan yang dilamar
   * `domisili` (`VARCHAR(100)`) — Kota tempat tinggal pelamar
   * `skorTes` (`INT`) — Nilai hasil tes seleksi (skala 0–100)
   * `pengalamanTahun` (`INT`) — Lama pengalaman kerja dalam satuan tahun

2. **Tabel `lowongan`** *(Modul Tambahan — CRUD Lengkap)*
   * `id` (`INT`, Primary Key, Auto Increment) — ID unik lowongan
   * `judulPosisi` (`VARCHAR(100)`) — Nama posisi pekerjaan yang dibuka
   * `departemen` (`VARCHAR(100)`) — Divisi atau departemen yang membutuhkan
   * `lokasi` (`VARCHAR(100)`) — Lokasi dan sistem kerja (Onsite/Hybrid/Remote)
   * `kuota` (`INT`) — Jumlah kebutuhan kandidat
   * `status` (`VARCHAR(30)`) — Status lowongan (`Dibuka` atau `Ditutup`)

### C. Struktur Direktori Repositori
```text
TP9DPBO2526C1_Adib/
├── dokumentasi/                           # Rekaman layar (screen record) uji coba CRUD
├── project/                               # Kode utama aplikasi dengan arsitektur MVP
│   ├── models/
│   │   ├── DB.php                         # Wrapper koneksi & eksekusi query PDO
│   │   ├── Kandidat.php                   # Class entity untuk data Kandidat
│   │   ├── KontrakModel.php               # Interface kontrak Model Kandidat
│   │   ├── KontrakModelLowongan.php       # Interface kontrak Model Lowongan
│   │   ├── Lowongan.php                   # Class entity untuk data Lowongan
│   │   ├── TabelKandidat.php              # Query CRUD tabel kandidat
│   │   └── TabelLowongan.php              # Query CRUD tabel lowongan
│   ├── presenters/
│   │   ├── KontrakPresenter.php           # Interface kontrak Presenter Kandidat
│   │   ├── KontrakPresenterLowongan.php   # Interface kontrak Presenter Lowongan
│   │   ├── PresenterKandidat.php          # Pengelola logika & perakitan HTML tabel kandidat
│   │   └── PresenterLowongan.php          # Pengelola logika & perakitan HTML tabel lowongan
│   ├── template/
│   │   ├── form.html                      # Template HTML form tambah/edit kandidat
│   │   ├── form_lowongan.html             # Template HTML form tambah/edit lowongan
│   │   ├── skin.html                      # Template HTML halaman daftar kandidat
│   │   └── skin_lowongan.html             # Template HTML halaman daftar lowongan
│   ├── views/
│   │   ├── KontrakView.php                # Interface kontrak View (Strict Passive View)
│   │   ├── ViewKandidat.php               # Renderer template HTML modul kandidat
│   │   └── ViewLowongan.php               # Renderer template HTML modul lowongan
│   ├── index.php                          # Entry point & pengatur alur request GET/POST
│   └── mvp_db.sql                         # Salinan file SQL database
├── mvp_db.sql                             # File database terbaru (tabel kandidat & lowongan)
└── README.md                              # Penjelasan desain, alur program, dan dokumentasi
```

## 2. Penjelasan Alur Program

Seluruh interaksi pengguna masuk melalui satu pintu utama di `project/index.php`. Di bagian awal, `index.php` membaca parameter `page` (`kandidat` sebagai halaman default, atau `lowongan` saat tab Data Lowongan diklik), lalu menginisialisasi pasangan objek **Model**, **View**, dan **Presenter** yang sesuai.

### A. Alur Menampilkan Data (`GET` — Read)
1. Saat pengguna membuka halaman utama (`index.php?page=kandidat` atau `index.php?page=lowongan`), `index.php` memanggil method `tampilkanKandidat()` atau `tampilkanLowongan()` pada Presenter.
2. Presenter meminta seluruh data ke Model melalui `getAllKandidat()` atau `getAllLowongan()`.
3. Model menjalankan query `SELECT` ke MySQL dan mengubah setiap baris hasil query menjadi array berisi objek `Kandidat` atau `Lowongan`.
4. Presenter menghitung jumlah total data dengan `count()`, lalu melakukan perulangan `foreach` untuk merakit baris HTML `<tr>...</tr>` menggunakan helper `buildRowHtml()`.
5. Presenter mengirimkan string `rowsHtml` dan angka `totalData` ke method `renderDaftar()` milik View.
6. View membaca file `skin.html` (atau `skin_lowongan.html`), mengganti token `{{TABLE_ROWS}}` dan `{{TOTAL_DATA}}`, lalu menampilkan halaman ke browser.

### B. Alur Menampilkan Form Tambah & Edit (`GET` — Create & Update Form)
1. Saat tombol **+ Tambah** diklik (`screen=add`), `index.php` memanggil `tampilkanFormKandidat()` atau `tampilkanFormLowongan()` tanpa parameter ID. Presenter langsung menyuruh View merender form kosong dengan `action="tambah"`.
2. Saat tombol **Edit** diklik (`screen=edit&id=...`), Presenter mengambil data lama terlebih dahulu lewat `getKandidatById($id)` atau `getLowonganById($id)`. Data tersebut dimasukkan ke array `$formData` lalu dikirim ke `renderForm()` agar kolom input otomatis terisi data lama (*prefill*) dengan `action="ubah"`.  ### C. Alur Simpan, Ubah, dan Hapus Data (`POST` — Create, Update, Delete) 1. Ketika form disubmit atau tombol **Hapus** ditekan, browser mengirim request `POST` beserta input tersembunyi `action` (`tambah`, `ubah`, atau `hapus`) ke `index.php`. 2. `index.php` meneruskan input `$_POST` ke method Presenter yang sesuai (`tambah...`, `ubah...`, atau `hapus...`).
3. Presenter memanggil method query pada Model (`add...`, `update...`, atau `delete...`) yang mengeksekusi perintah `INSERT`, `UPDATE`, atau `DELETE` menggunakan *prepared statement* PDO.
4. Setelah query berhasil dijalankan, Presenter memanggil `header("Location: index.php?page=...")` agar pengguna langsung diarahkan kembali ke halaman daftar tabel terbaru.

## 3. Dokumentasi Screen Record

**record:**
https://github.com/user-attachments/assets/cabe5ba5-2f8a-43d1-881b-a806e78fda76

**tampilan data kandidat:**
<img width="941" height="441" alt="tampilan_data kandidat" src="https://github.com/user-attachments/assets/f8356e6d-1e8c-4def-92f9-fd7225305224" />

**add data kandidat:**
<img width="898" height="368" alt="add_data kandidat" src="https://github.com/user-attachments/assets/8a615a12-db83-494a-ac0c-d78cb2ca431b" />

**edit data kandidat:**
<img width="806" height="371" alt="edit_data kandidat" src="https://github.com/user-attachments/assets/978c7f67-197d-4152-b6aa-235cbb26c1cc" />

**delete data kandidat:**
<img width="893" height="427" alt="delete_data kandidat" src="https://github.com/user-attachments/assets/19a295c4-8413-4824-9e9f-fa4c13917569" />

**tampilan data lowongan:**
<img width="904" height="471" alt="tampilan_data lowongan" src="https://github.com/user-attachments/assets/6b8e0369-2efe-400b-92ea-e8943c9624e8" />

**add data lowongan:**
<img width="848" height="351" alt="add_data lowongan" src="https://github.com/user-attachments/assets/49e04e5f-aa86-4a9b-9a21-6a8efc33ae97" />

**edit data lowongan:**
<img width="736" height="334" alt="edit_data lowongan" src="https://github.com/user-attachments/assets/b212a26e-aa26-41ea-819a-4901dee3d71f" />

**delete data lowongan:**
<img width="848" height="453" alt="delete_data lowongan" src="https://github.com/user-attachments/assets/372ad0a6-2960-47ba-a651-021f3c0b0e0e" />










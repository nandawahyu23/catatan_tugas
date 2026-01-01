📘 DESKRIPSI APLIKASI

Catatan Tugas Kuliah merupakan aplikasi berbasis web yang dikembangkan untuk membantu pengguna dalam mengelola data tugas perkuliahan secara sistematis 
📚. Aplikasi ini memungkinkan pengguna untuk mencatat tugas, menentukan tenggat waktu ⏰, serta memantau status pengerjaan tugas dengan lebih terstruktur.
Aplikasi ini dibangun menggunakan arsitektur RESTful API 🌐 sehingga proses pertukaran data dilakukan melalui layanan API yang terpisah dari tampilan antarmuka. 
Untuk menjaga keamanan data 🔐,sistem menerapkan autentikasi berbasis token yang memastikan hanya pengguna terdaftar yang dapat mengakses data tugas.

----------------------------------------------------------------

✨ FITUR SISTEM

- 🔐 Autentikasi pengguna melalui proses login dan registrasi
- 📝 Pengelolaan data tugas perkuliahan
- ➕➖✏️ Penambahan, pengubahan, dan penghapusan data tugas
- 📋 Penampilan daftar tugas sesuai akun pengguna
- 🌐 Penyediaan layanan RESTful API dengan proses CRUD

----------------------------------------------------------------

🛠️ TEKNOLOGI YANG DIGUNAKAN

Frontend   : HTML, CSS, JavaScript 🎨  
Backend    : PHP Native ⚙️  
Database   : MySQL 🗄️  
Web Server : Apache 🌍  
Arsitektur : RESTful API (JSON) 🔄  

----------------------------------------------------------------

📂 STRUKTUR DIREKTORI APLIKASI

catatan_tugas
├── 📁 api
│   ├── ⚙️ config.php
│   ├── 🔑 login.php
│   ├── 🧾 register.php
│   └── 📌 tugas.php
├── 📁 public
│   ├── 🌐 index.html
│   ├── 📄 tugas.html
│   └── 🎨 style.css
└── 📁 sql
    └── 🗃️ database.sql

----------------------------------------------------------------

🔄 ALUR KERJA SISTEM

👤 Proses Pengguna:
1. Pengguna mengakses aplikasi melalui browser 🌐
2. Pengguna melakukan login ke dalam sistem 🔑
3. Sistem mengirim permintaan autentikasi ke API
4. API memverifikasi data pengguna dan menghasilkan token 🔐
5. Token digunakan untuk mengakses data tugas
6. Data tugas ditampilkan pada halaman web 📋

⚙️ Proses API:
1. API menerima request dari frontend
2. Sistem melakukan validasi token
3. API menjalankan proses CRUD pada database 🗄️
4. Server mengirimkan response dalam format JSON 🔄

----------------------------------------------------------------

📡 DOKUMENTASI RESTFUL API

🔗 Base URL:
http://localhost/catatan_tugas/api

📌 Endpoint API:
- POST   /login.php        🔑
- POST   /register.php     🧾
- GET    /tugas.php        📋
- POST   /tugas.php        ➕
- PUT    /tugas.php?id={id} ✏️
- DELETE /tugas.php?id={id} ❌

----------------------------------------------------------------

📊 CONTOH RESPONSE API (JSON)

[
  {
    "id": 2,
    "mata_kuliah": "Pemrograman Web",
    "judul": "Pengembangan API",
    "deskripsi": "Implementasi RESTful API dengan token",
    "deadline": "2025-12-31",
    "status": "proses"
  }
]

----------------------------------------------------------------

✅ KESIMPULAN

Aplikasi Catatan Tugas Kuliah telah menerapkan konsep CRUD dan RESTful API secara lengkap 🔄.
Sistem dilengkapi dengan mekanisme autentikasi berbasis token 🔐 untuk menjaga keamanan data.
Pengujian API dilakukan menggunakan Postman pada lingkungan localhost 🧪 dan hasil pengujian menunjukkan bahwa seluruh endpoint berfungsi sesuai dengan konsep RESTful API.

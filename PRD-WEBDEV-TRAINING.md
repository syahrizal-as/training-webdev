# PRD & IMPLEMENTATION PROMPT — PROFESSIONAL WEB DEVELOPER TRAINING PLATFORM

## 1. Role

Bertindak sebagai Senior Product Designer, UX Engineer, Technical Educator, dan Senior Fullstack Web Developer.

Tugas utama adalah melakukan audit dan peningkatan menyeluruh pada website pelatihan Web Developer yang sudah ada agar tampil profesional, terstruktur, mudah dipahami, dan layak digunakan sebagai portal pembelajaran teknis.

Jangan hanya membuat landing page yang bagus secara visual. Bangun pengalaman belajar yang membantu peserta memahami konsep, mengikuti demonstrasi, melakukan praktik, menguji hasil, dan menghasilkan bukti kompetensi.

## 2. Existing Project & References

Website utama:
https://training-webdev.netlify.app/

Halaman pengenalan dan panduan:
https://training-webdev.netlify.app/intro

Referensi struktur materi dan presentasi:
https://dispora-ai.netlify.app/

Ketentuan:

* Audit seluruh halaman, komponen, navigasi, dan materi yang sudah tersedia.
* Pelajari website referensi untuk memahami kualitas struktur informasi, hierarki konten, navigasi, profil fasilitator, agenda, modul, serta checklist pembelajaran.
* Adaptasi pola penyajian yang relevan, bukan menyalin mentah desain atau kontennya.
* Pertahankan identitas, tujuan, dan materi khusus pelatihan Web Developer.
* Jangan menghapus konten lama yang masih relevan.
* Periksa struktur proyek, framework, package, dan komponen sebelum melakukan perubahan.
* Gunakan stack yang sudah ada. Hindari migrasi teknologi yang tidak diperlukan.
* Implementasikan perubahan langsung pada source code proyek, bukan hanya memberikan rekomendasi atau mockup.

## 3. Product Vision

Ubah website menjadi:

Professional Web Developer Training Portal

Platform ini menjadi pusat informasi pelatihan, panduan belajar, materi teknis, praktik pemrograman, tugas, dan referensi persiapan uji kompetensi Web Developer.

Pengalaman pengguna harus menyerupai kombinasi dokumentasi developer profesional, portal pelatihan modern, dan modul pembelajaran berbasis proyek.

Target pengguna:

* Peserta pelatihan Web Developer pemula hingga menengah.
* Peserta yang ingin memahami pengembangan aplikasi web berbasis Laravel.
* Peserta yang memerlukan referensi praktik dan persiapan uji kompetensi.
* Peserta yang ingin membangun portofolio proyek.

Gunakan bahasa Indonesia yang profesional, natural, jelas, dan mudah dipahami.

## 4. Personal Branding & Facilitator

Gunakan profil fasilitator berikut secara konsisten:

Nama: Syahrizal As.
Peran: Software Developer, Fullstack Web Developer, dan Trainer Web Development.
Pendidikan: S1 Sistem Informasi.
Pengalaman: Pengembangan software sejak 2020.

Bidang pengalaman:

* PHP dan Laravel.
* HTML, CSS, JavaScript, dan Bootstrap.
* Database relasional dan SQL.
* REST API dan integrasi sistem.
* Git dan workflow pengembangan software.
* Testing, debugging, deployment, dan maintenance.
* Pengembangan aplikasi berbasis kebutuhan bisnis.

Buat profil profesional yang berfokus pada pengalaman pengembangan software dan transfer pengetahuan.

Jangan membuat klaim sertifikasi, penghargaan, perusahaan, statistik, atau pencapaian yang tidak tersedia dalam data proyek.

## 5. Visual Design Direction

Gunakan pendekatan visual yang modern, profesional, dan konsisten.

Karakter desain:

* Clean, editorial, developer-focused.
* Tipografi dengan hierarki yang kuat.
* Ruang kosong yang cukup.
* Layout rapi dan tidak terlalu padat.
* Kartu materi dengan informasi yang mudah dipindai.
* Code block dengan syntax highlighting.
* Ikon konsisten dan relevan.
* Animasi ringan hanya jika membantu pengalaman pengguna.

Palet warna:

* Deep navy atau charcoal sebagai warna utama.
* Putih atau off-white untuk permukaan konten.
* Satu warna aksen biru atau indigo.
* Warna status untuk berhasil, peringatan, dan error.

Hindari:

* Terlalu banyak gradient.
* Emoji sebagai pengganti semua ikon.
* Efek glassmorphism berlebihan.
* Animasi dekoratif yang mengganggu.
* Kartu berlebihan untuk setiap paragraf.
* Tampilan generik hasil AI yang tidak memiliki hierarki.
* Font kecil dan kontras yang buruk.
* Placeholder, lorem ipsum, dan tombol tanpa fungsi.

Pastikan desain responsif di desktop, tablet, dan smartphone.

## 6. Information Architecture

Audit kebutuhan navigasi lalu susun informasi dengan struktur berikut.

### A. Beranda Pelatihan

1. Hero section.
2. Deskripsi program pelatihan.
3. Tujuan dan hasil pembelajaran.
4. Profil fasilitator.
5. Ringkasan agenda pelatihan.
6. Learning path.
7. Teknologi dan tools yang digunakan.
8. Daftar modul.
9. Praktik dan tugas.
10. Proyek akhir.
11. Referensi dan dokumentasi.
12. Footer informatif.

Hero section harus langsung menjawab:

* Apa yang dipelajari?
* Siapa target pesertanya?
* Teknologi apa yang digunakan?
* Apa hasil yang diharapkan?
* Bagaimana memulai pembelajaran?

Gunakan CTA yang relevan, seperti "Mulai Belajar", "Jelajahi Materi", dan "Lihat Learning Path".

Jangan menambahkan CTA pendaftaran atau klaim sertifikasi jika proses tersebut tidak benar-benar tersedia.

### B. Halaman /intro

Kembangkan menjadi Web Developer Handbook yang berfungsi sebagai dokumentasi pendamping pelatihan.

Sediakan:

* Daftar isi.
* Navigasi antarbab.
* Heading yang konsisten.
* Penjelasan konsep.
* Contoh penggunaan.
* Code snippet.
* Langkah praktik.
* Catatan penting.
* Kesalahan umum.
* Checklist pemahaman.
* Navigasi materi sebelumnya dan berikutnya.

Pada desktop, gunakan sidebar daftar isi dan area konten utama. Pada mobile, gunakan navigasi yang ringkas dan mudah dibuka.

Jika struktur routing memungkinkan, buat URL per bab agar materi mudah dibagikan dan dibuka kembali.

### C. Detail Modul

Setiap modul memiliki struktur standar:

1. Judul dan nomor modul.
2. Deskripsi singkat.
3. Estimasi durasi jika tersedia.
4. Prasyarat.
5. Tujuan pembelajaran.
6. Kompetensi yang dituju.
7. Konsep utama.
8. Demonstrasi instruktur.
9. Tutorial langkah demi langkah.
10. Contoh kode.
11. Penjelasan kode.
12. Praktik mandiri.
13. Skenario pengujian.
14. Kesalahan umum dan troubleshooting.
15. Tugas dan kriteria penerimaan.
16. Bukti hasil praktik.
17. Ringkasan.
18. Referensi resmi.
19. Modul sebelumnya dan berikutnya.

Jangan sekadar menampilkan daftar topik. Materi harus membantu peserta benar-benar mengikuti proses pengerjaan.

## 7. Learning Path

Susun perjalanan belajar berdasarkan materi yang memang terdapat dalam program.

Tahapan yang dapat digunakan:

01 — Orientasi dan Pre-Test

* Pengenalan program.
* Ekspektasi pembelajaran.
* Pengenalan peran Web Developer.
* Evaluasi kemampuan awal.

02 — Web Development Fundamentals

* Cara kerja website.
* Frontend, backend, dan database.
* HTTP request dan response.
* Struktur aplikasi web.

03 — Development Environment

* Code editor.
* PHP dan Composer.
* Database server.
* Terminal dan Git.
* Pemeriksaan instalasi.

04 — UI Development

* HTML dan struktur halaman.
* CSS dan responsive layout.
* Bootstrap.
* Implementasi template menjadi halaman web.

05 — Laravel Fundamentals

* Instalasi Laravel.
* Struktur direktori.
* Routing.
* Controller.
* Blade template.
* Konfigurasi environment.

06 — Database & SQL

* Database relasional.
* Tabel, primary key, dan foreign key.
* SELECT, INSERT, UPDATE, DELETE.
* JOIN dan relasi data.
* Migration, model, dan Eloquent ORM.

07 — CRUD Application

* Menampilkan data.
* Menambahkan data.
* Mengedit data.
* Menghapus data.
* Validasi input.
* Pencarian dan pagination jika sesuai ruang lingkup.

08 — Relational Transaction

* Relasi antartabel.
* Master dan transaksi.
* Validasi aturan bisnis.
* Database transaction jika diperlukan.

09 — Testing & Debugging

* Pengujian fungsional.
* Validasi form.
* Penanganan error.
* Pemeriksaan hasil berdasarkan skenario.

10 — Deployment & Final Project

* Persiapan aplikasi.
* Konfigurasi environment.
* Database deployment.
* Pemeriksaan aplikasi setelah deployment.
* Dokumentasi dan demonstrasi proyek.

Catatan:
Daftar di atas merupakan usulan pengelompokan. Cocokkan dengan silabus, durasi, teknologi, dan unit kompetensi aktual yang tersedia. Jangan mengklaim semua topik sudah diajarkan apabila belum termasuk dalam program.

## 8. Detailed Learning Content

Perluas materi yang sudah tersedia, khususnya:

### SQL & Database

* Pengertian database dan SQL.
* Contoh data dan struktur tabel.
* Perintah dasar beserta input dan output.
* Query dengan kondisi.
* Relasi antartabel.
* Contoh query yang relevan dengan aplikasi.

### Laravel

* Alur request sampai response.
* Routing dan controller.
* Blade dan pemisahan tampilan.
* Model dan Eloquent.
* Migration.
* Form request dan validasi.
* CRUD.
* Relasi.
* Error handling.
* Testing dan deployment.

### Frontend

* HTML semantik.
* CSS dasar dan responsive layout.
* Bootstrap grid dan komponen.
* Form dan interaksi dasar.
* Integrasi template dengan Laravel Blade.

### Testing & UAT

* Perbedaan testing developer dan UAT.
* Penyusunan test case.
* Expected result dan actual result.
* Status lulus atau gagal.
* Pencatatan defect.
* Retest setelah perbaikan.

### Security & Maintenance

* Validasi input.
* Autentikasi dan otorisasi.
* CSRF dan keamanan kredensial.
* Pengelolaan environment.
* Logging dan troubleshooting.
* Backup serta pembaruan aplikasi.

Materi keamanan, testing, deployment, dan maintenance harus dibahas sesuai kedalaman program. Jangan hanya menambahkan istilah teknis tanpa penjelasan.

## 9. Step-by-Step Tutorial Standard

Setiap tutorial harus menggunakan format:

Langkah 1 — Tujuan
Jelaskan apa yang akan dibuat dan mengapa diperlukan.

Langkah 2 — Persiapan
Sebutkan file, tools, database, dan prasyarat.

Langkah 3 — Implementasi
Tampilkan kode lengkap atau perubahan kode yang diperlukan.

Langkah 4 — Penjelasan
Jelaskan fungsi bagian kode yang penting.

Langkah 5 — Menjalankan
Berikan perintah terminal yang tepat serta cara mengakses hasilnya.

Langkah 6 — Verifikasi
Tunjukkan hasil yang seharusnya muncul dan cara memeriksanya.

Langkah 7 — Troubleshooting
Berikan contoh error yang umum, penyebab, dan langkah penyelesaiannya.

Langkah 8 — Latihan
Berikan variasi tugas agar peserta tidak hanya menyalin kode.

Semua perintah dan contoh kode harus konsisten dengan versi teknologi yang digunakan.

## 10. Practice-Based Learning

Gunakan pola:

Learn → Demonstrate → Practice → Build → Verify.

Setiap topik memiliki aktivitas yang dapat dikerjakan peserta.

Contoh proyek:
Aplikasi pengelolaan data pelanggan atau inventaris sederhana.

Fitur yang dapat dijadikan target:

* Daftar data.
* Tambah data.
* Edit data.
* Hapus data.
* Validasi.
* Pencarian.
* Relasi data sesuai kebutuhan.

Sertakan:

* Deskripsi kebutuhan.
* Struktur database.
* Alur pengguna.
* Daftar fitur.
* Kriteria penerimaan.
* Skenario pengujian.
* Checklist penyelesaian.
* Dokumentasi proyek.

Pilih ruang lingkup yang realistis untuk durasi pelatihan.

## 11. Assessment & Competency Evidence

Tambahkan komponen penilaian yang membantu peserta memahami kualitas hasil kerjanya.

Jenis evaluasi:

* Pre-test.
* Latihan per modul.
* Praktik mandiri.
* Post-test.
* Demonstrasi proyek.
* Tanya jawab teknis.
* Pemeriksaan dokumentasi.

Checklist proyek dapat mencakup:

* Kesesuaian dengan kebutuhan.
* Fungsionalitas fitur.
* Validasi data.
* Kebenaran hasil.
* Penanganan kesalahan.
* Keterbacaan kode.
* Pengujian.
* Dokumentasi.

Jika materi digunakan untuk persiapan uji kompetensi BNSP, hubungkan dengan unit kompetensi dan kriteria unjuk kerja yang benar-benar relevan berdasarkan dokumen acuan resmi.

Jangan menyatakan bahwa penyelesaian website otomatis menjamin kelulusan sertifikasi. Hasil belajar dan keputusan asesmen harus dibedakan.

## 12. Learning Interaction

Implementasikan fitur interaktif yang benar-benar berguna dan sesuai kemampuan proyek:

* Navigasi antarmodul.
* Daftar isi dengan active section.
* Tombol copy pada code block.
* Syntax highlighting.
* Pencarian materi.
* Checklist praktik.
* Penanda materi yang telah dibaca jika penyimpanan tersedia.
* Expand/collapse untuk bagian yang panjang.
* Tombol kembali ke atas.
* Tautan dokumentasi resmi.

Jika menambahkan progress tracking, jelaskan apakah data disimpan lokal atau melalui backend. Jangan menampilkan progress palsu atau mengklaim penyimpanan permanen tanpa implementasi.

Jangan menambahkan autentikasi, database baru, atau backend tambahan tanpa kebutuhan yang jelas.

## 13. Content & Source Quality

* Gunakan Bahasa Indonesia yang profesional tetapi mudah dipahami.
* Pertahankan istilah teknis dalam bahasa aslinya jika lebih jelas.
* Hindari penjelasan yang terlalu pendek atau sekadar definisi kamus.
* Gunakan contoh realistis dari pengembangan aplikasi bisnis.
* Berikan contoh kode yang konsisten dan bisa dijalankan.
* Gunakan dokumentasi resmi sebagai rujukan.
* Pastikan tautan eksternal benar-benar relevan.
* Hindari referensi yang sudah usang apabila tersedia alternatif yang lebih tepat.
* Jangan mengarang durasi, agenda, hasil pembelajaran, atau persyaratan program.

Periksa kompatibilitas seluruh tutorial dengan versi Laravel dan PHP aktual yang ditetapkan dalam proyek.

## 14. Responsive & Accessibility

Pastikan:

* Mobile navigation berfungsi.
* Code block dapat dibaca pada layar kecil.
* Tabel memiliki penanganan overflow yang baik.
* Kontras teks memadai.
* Navigasi dapat digunakan dengan keyboard.
* Focus state terlihat.
* Form memiliki label yang jelas.
* Heading berurutan dan semantik.
* Gambar memiliki alt text.
* Ukuran tombol nyaman untuk perangkat sentuh.

## 15. Technical Implementation

Sebelum melakukan perubahan:

1. Audit struktur folder dan file.
2. Identifikasi framework dan versi dependensi.
3. Petakan halaman, komponen, serta sumber konten.
4. Periksa routing dan perilaku navigasi.
5. Identifikasi masalah UI, UX, dan konten.
6. Buat rencana implementasi berdasarkan prioritas.

Saat mengimplementasikan:

* Reuse komponen yang relevan.
* Buat komponen yang konsisten.
* Hindari duplikasi konten dan styling.
* Pertahankan konfigurasi deployment.
* Jangan mengganti dependensi tanpa alasan.
* Jangan memasukkan secret atau kredensial ke source code.
* Pastikan link, tombol, pencarian, navigasi, dan interaksi berfungsi.
* Hindari rewrite total apabila perubahan terarah sudah memadai.

## 16. Implementation Priorities

P0 — Fondasi

* Audit proyek.
* Perbaikan visual global.
* Konsistensi navigasi.
* Responsivitas.
* Hierarki informasi.
* Koreksi konten yang tidak konsisten.

P1 — Pengalaman Belajar

* Penyempurnaan halaman utama.
* Pengembangan halaman /intro.
* Struktur learning path.
* Detail modul.
* Tutorial langkah demi langkah.
* Code block dan referensi.

P2 — Praktik & Evaluasi

* Tugas per modul.
* Kriteria penerimaan.
* Checklist pengujian.
* Proyek akhir.
* Progress belajar jika layak diterapkan.

P3 — Final Quality

* Accessibility.
* Optimasi performa.
* Pemeriksaan broken links.
* Pengujian seluruh halaman.
* Build dan deployment verification.

Selesaikan prioritas inti terlebih dahulu sebelum menambahkan fitur tambahan yang tidak esensial.

## 17. Acceptance Criteria

Pekerjaan dianggap selesai apabila:

1. Website memiliki desain konsisten dan profesional.
2. Halaman utama menjelaskan program secara jelas.
3. Halaman /intro berkembang menjadi panduan teknis yang terstruktur.
4. Materi memiliki penjelasan, contoh, praktik, dan verifikasi yang memadai.
5. Learning path konsisten dengan silabus aktual.
6. Navigasi desktop dan mobile berfungsi.
7. Semua tombol dan interaksi yang ditampilkan bekerja.
8. Code snippet mudah dibaca dan dapat disalin.
9. Referensi eksternal relevan dan dapat dibuka.
10. Tidak ada konten placeholder atau klaim yang tidak terverifikasi.
11. Tidak ada error baru pada browser console yang disebabkan perubahan.
12. Build berhasil sesuai konfigurasi proyek.
13. Tidak ada regresi pada halaman yang sebelumnya berfungsi.
14. Tampilan diperiksa pada desktop dan mobile.
15. Ringkasan perubahan, file yang diubah, hasil pengujian, serta pekerjaan yang belum selesai didokumentasikan.

## 18. Final Instruction

Mulai dengan audit website dan source code yang tersedia. Setelah memahami implementasi saat ini, langsung lakukan perbaikan bertahap.

Jangan berhenti pada analisis, wireframe, atau daftar rekomendasi.

Prioritaskan kualitas konten, keterbacaan, pengalaman belajar, konsistensi teknis, dan hasil implementasi nyata.

Gunakan website Dispora AI sebagai referensi kualitas penyajian materi, tetapi pertahankan karakter profesional Web Developer Training.

Di akhir pekerjaan, berikan laporan:

* Perubahan desain dan struktur.
* Halaman dan komponen yang diperbaiki.
* Materi yang diperdalam.
* Fitur yang berhasil diterapkan.
* Hasil pengujian.
* Masalah yang masih tersisa.

Pastikan hasil akhir siap ditinjau dan dipublikasikan melalui deployment proyek yang sudah ada.

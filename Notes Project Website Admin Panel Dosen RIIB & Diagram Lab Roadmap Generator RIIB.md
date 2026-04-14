Membuat database back-end dengan MySQL yang memiliki fitur CRUD menggunakan Laravel. Data tersebut nantinya akan diconsume oleh Proyek Dashboard



Membuat dashboard dan table display website untuk Project Master Data Dosen RIIB ini.



Data Dosen RIIB meliputi:

\- Data profil dosen KK (Kelompok Keahlian) RIIB yaitu: nama, prodi, kode dosen, jabatan fungsional (JAD), KK, Sub-KK

\- Data catatan publikasi (sudah/belum)

\- Data linieritas: topik pengajaran + penelitian (dari hibah internal/eksternal) + pengmas

\- Data matriks: prodi s1, prodi s2, judul tesis, prodi homebase, publikasi, dst

\- Data pengajaran: mengandung muatan riset

\- Data materi kegiatan + dokumen SK: contoh workshop menulis buku (ada slide materi), sharing session membahas menulis latex (ada slide materi)



Next on: Mulai menyicil laravel untuk data profil dosen KK (Finished)



Notes Tambahan dari Pak Nizar:



1. Tambahkan fitur penomoran serta Jumlah Dosen.
2. Fitur Sorting pada Nama, Prodi, dan Sub-KK Dosen.
3. KK Dosen dihapus.
4. Foto dosen (1 kolom sendiri).
5. Status Publikasi diambil berdasarkan urutan berikut: 1. Sudah/Belumnya publikasi dalam 2 tahun ini | 2. Beliau sebagai penulis pertama dan bukan merupakan Seminar/eProceeding | 3. Beliau sudah/belum pada masa periode TMT nya.
6. Diberi Indikator di 1 Kolom (Status) | Merah: Sudah lewat TMT tapi belum mengurus Publikasi | Kuning: Masih dalam masa TMT dan belum punya publikasi dan ketika sudah melewati TMT dan punya publikasi | Hijau: Jika belum TMT namun sudah mempunyai Publikasi
7. Buat Dummy JSON periode TMT dan Status Publikasi (sementara) untuk testing fungsi otomatisasi kolom Status
8. Tambahkan kolom baru khusus Indikator, status publikasi hanya Sudah/Belum
9. menambahkan warna pada pdf



Cara menjalankan project ini (website) di Localhost:



php artisan cache:clear

php artisan optimize:clear

php artisan config:clear

php artisan view:clear

php artisan migrate:fresh --seed

php artisan key:generate

npm run build

npm run laravel



php artisan queue:work



API Brevo: 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



Membuat project Draw.io Generator menggunakan Draw.io API dan MxGraph API. mempelajari website-website berikut, atau dapat mencarinya pada sumber lainnya. kemudian membangun dan mengembangkan project ini. berikut kumpulan website referensinya:



* https://github.com/jgraph/drawio-integration
* https://www.drawio.com/integrations
* https://www.google.com/search?q=mxGraph%27s+API\&oq=mxGraph%27s+API\&gs\_lcrp=EgZjaHJvbWUyBggAEEUYOTIHCAEQIRiPAjIHCAIQIRiPAtIBBzYwMmowajSoAgCwAgE\&sourceid=chrome\&ie=UTF-8
* https://docs.nasdanika.org/core/drawio/index.html
* https://stackoverflow.com/questions/70381781/how-to-read-write-compressed-mxgraph-diagram-with-java/70393846#70393846
* https://stackoverflow.com/questions/70543817/is-there-draw-io-api-to-manipulate-diagrams



Notes dari Pak Nizar untuk Project Diagram Workflow Penelitian Generator:



1. Muat contoh Diagram nanti bikin isi form menyesuaikan template admin
2. Tipe/bentuk diganti dengan warna saja. semua pakai Rectangle (persegi panjang)
3. opsi Tags (pisah dengan koma) diganti Periode Semester
4. opsi Deskripsi / Tujuan diganti judul penelitian
5. opsi Nama Diagram diganti menjadi "Nama Dosen"
6. Dari admin panel dosen riib ke diagram generator langsung masuk sebagai kredensial admin dan langsung mengarah ke menu admin panel. tambahkan fitur delete langsung pada kolom Aksi pada admin panel diagram generator RIIB. itu hover bar atas yang bertuliskan 4 menu (Workspace, Library, Docs, Admin Panel) responsif sesuai yang di klik, jangan selalu workspace yang ada background abu-abunya ketika di klik. karena kalo di klik "Admin Panel", efek hovernya "Workspace" masih menyala (berupa background text berwarna abu-abu). Card Total Dosen, Data Kepangkatan Tercatat, Status Publikasi dihapus di menu Data Kepangkatan. Tabel kepangkatan dosen bisa di adjust berapa row yang dimunculkan, mulai dari 10, 20, 50, 100, dan All. Disebelah card dropdown filter status TMT, Tambahkan fitur download as PDF berdasarkan status tmt yang di filter. Jadi isi tabel yang ada di PDF tersebut hanya status tmt yang dipilih (difilter). Itu kok tabel "Aktivitas Kepangkatan Terbaru" tidak sesuai dengan menu Data Kepangkatan? tolong sesuaikan ya isi tabelnya. Data Profil Dosen tambahkan kolom "Lab", kemudian urutan kolom tabelnya menjadi "Foto Dosen, Kode Dosen, Nama Dosen, NIP, NIDN, Program Studi, Sub-Kelompok Keahlian, Lab, Aksi".




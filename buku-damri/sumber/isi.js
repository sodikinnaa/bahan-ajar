// Naskah buku Pengetahuan Seputar DAMRI.
// Blok isi subbab: string (paragraf, boleh **tebal** dan _miring_),
// { daftar: [...] }, { langkah: [...] }, { tabel: { kolom, baris, lebar } },
// { kotak: { judul, isi } }, dan { lengkapi: '...' } untuk data yang masih
// perlu dilengkapi cabang.

const KATA_PENGANTAR = [
  'Puji syukur kami panjatkan ke hadirat Tuhan Yang Maha Esa atas rahmat dan karunia-Nya sehingga buku Pengetahuan Seputar DAMRI ini dapat tersusun dan hadir di tengah para pembaca.',
  'Sejak 25 November 1946, DAMRI telah menjadi bagian dari perjalanan bangsa Indonesia. Berawal dari Djawatan Angkoetan Motor Republik Indonesia, kini Perum DAMRI hadir sebagai Badan Usaha Milik Negara yang melayani masyarakat di bidang transportasi jalan. Perjalanan panjang tersebut menyimpan banyak pengetahuan yang layak dikenal, baik oleh insan DAMRI maupun oleh masyarakat yang setiap hari memanfaatkan layanannya.',
  'Buku ini disusun oleh DAMRI Cabang Bandar Lampung sebagai sumber informasi yang ringkas dan mudah dipahami. Bagi karyawan, buku ini menjadi bekal untuk mengenal perusahaan tempat mereka berkarya: sejarahnya, nilai-nilai yang dipegang, serta layanan yang diberikan. Bagi masyarakat luas, buku ini diharapkan membantu mengenal DAMRI lebih dekat, termasuk layanan yang tersedia di Provinsi Lampung.',
  'Kami berupaya menyajikan informasi yang bersumber dari dokumen resmi perusahaan, peraturan perundang-undangan, dan sumber lain yang dapat dipertanggungjawabkan.',
  'Terima kasih kami sampaikan kepada jajaran manajemen serta seluruh karyawan DAMRI Cabang Bandar Lampung yang telah mendukung penyusunan buku ini. Kami menyadari buku ini masih memiliki kekurangan. Oleh karena itu, kritik dan saran yang membangun sangat kami harapkan demi penyempurnaan edisi berikutnya.',
  'Semoga buku ini bermanfaat dan menambah kebanggaan kita terhadap DAMRI sebagai transportasi milik bangsa.',
];

const PENANDA_TANGAN = {
  tempatTanggal: 'Bandar Lampung, September 2026',
  jabatan: 'Manager SDM',
  unit: 'Perum DAMRI Cabang Bandar Lampung',
  nama: '(Nama Lengkap)',
};

const BAB_MENGENAL = {
  no: 'I', judul: 'Mengenal DAMRI',
  pembuka: 'Hampir setiap orang Indonesia pernah melihat bus DAMRI. Namun, belum banyak yang tahu arti namanya dan perjalanan panjang di baliknya.',
  sub: [
    {
      judul: 'Arti Nama DAMRI',
      isi: [
        'DAMRI adalah singkatan dari **Djawatan Angkoetan Motor Republik Indonesia**. Nama ini ditulis dengan ejaan lama yang berlaku pada 1946, tahun ketika DAMRI didirikan. Dalam ejaan sekarang, nama tersebut berarti _Jawatan Angkutan Motor Republik Indonesia_.',
        { tabel: {
          kolom: ['Kata', 'Arti'],
          lebar: [1.2, 3.3],
          baris: [
            ['Djawatan', 'Jawatan, yaitu instansi atau lembaga pemerintah.'],
            ['Angkoetan', 'Angkutan, yaitu kegiatan memindahkan orang dan barang.'],
            ['Motor', 'Kendaraan bermotor, seperti bus dan truk.'],
            ['Republik Indonesia', 'Menunjukkan bahwa lembaga ini milik negara Republik Indonesia.'],
          ],
        } },
        'Sejak itu status dan bentuk badan hukum DAMRI beberapa kali berubah, tetapi nama DAMRI tetap dipakai hingga sekarang dan dikenal luas oleh masyarakat.',
        { kotak: {
          judul: 'Tahukah Anda?',
          isi: 'Tanggal 25 November, hari lahirnya DAMRI pada 1946, diperingati setiap tahun sebagai hari jadi perusahaan. Pada 25 November 2026, DAMRI genap berusia 80 tahun.',
        } },
      ],
    },
    {
      judul: 'Status dan Bidang Usaha',
      isi: [
        'DAMRI berbentuk **Perusahaan Umum (Perum)**, yaitu Badan Usaha Milik Negara yang seluruh modalnya dimiliki negara dan tidak terbagi atas saham. Perum bertujuan menyediakan barang atau jasa yang bermutu bagi kemanfaatan umum, sekaligus mengejar keuntungan berdasarkan prinsip pengelolaan perusahaan yang sehat.',
        'Dasar hukum Perum DAMRI saat ini adalah **Peraturan Pemerintah Nomor 38 Tahun 2018** tentang Perusahaan Umum (Perum) DAMRI. Bidang usaha utamanya adalah angkutan orang dan barang dengan kendaraan bermotor di jalan, beserta usaha lain yang mendukungnya.',
        'Sebagai perusahaan milik negara, DAMRI menjalankan dua peran sekaligus. Di satu sisi, DAMRI melayani penumpang secara komersial. Di sisi lain, DAMRI menjalankan penugasan pemerintah, misalnya angkutan perintis yang menjangkau daerah terpencil dan perbatasan.',
        'Pada September 2026, pemerintah menegaskan bahwa DAMRI tetap berstatus Perum bersama sejumlah BUMN lain. Pembinaan BUMN saat ini dijalankan oleh Badan Pengaturan BUMN.',
        { kotak: {
          judul: 'Kantor Pusat Perum DAMRI',
          isi: 'Jl. Matraman Raya No. 25, Palmeriam, Matraman, Jakarta Timur 13140.',
        } },
      ],
    },
    {
      judul: 'Logo dan Identitas Perusahaan',
      isi: [
        'Logo DAMRI yang dipakai sekarang diperkenalkan pada 2018, bersamaan dengan pembaruan citra (_rebranding_) perusahaan. Peluncurannya dilakukan pada Rapat Kerja DAMRI di Sentul, Jawa Barat.',
        'Logo baru ini memadukan dua unsur utama:',
        { daftar: [
          '**Tiga garis biru yang bersilangan**, melambangkan sayap Garuda dan bermakna keamanan serta keteraturan.',
          '**Lingkaran kuning-oranye**, melambangkan roda kendaraan yang terus berputar menjangkau seluruh Nusantara hingga kawasan ASEAN.',
        ] },
        'Warna biru juga mencerminkan ketenangan, kepercayaan, rasa aman, teknologi, dan kebersihan. Nilai-nilai tersebut ingin dihadirkan DAMRI dalam setiap layanannya.',
        'Identitas DAMRI tidak hanya terlihat pada logo, tetapi juga pada warna armada, seragam awak, tampilan loket, dan cara karyawan melayani penumpang. Karena itu, setiap karyawan wajib memakai logo dan atribut perusahaan sesuai ketentuan.',
        { lengkapi: 'Sesuaikan uraian makna logo dengan pedoman identitas visual resmi Perum DAMRI.' },
      ],
    },
  ],
};

const BAB_SEJARAH = {
  no: 'II', judul: 'Sejarah Perjalanan DAMRI',
  pembuka: 'Perjalanan DAMRI berawal dari masa pendudukan Jepang dan terus berkembang seiring perjalanan bangsa Indonesia.',
  sub: [
    {
      judul: 'Masa Pendudukan Jepang',
      isi: [
        'Cikal bakal DAMRI dapat ditelusuri hingga masa pendudukan Jepang di Indonesia (1942–1945). Pada masa itu terdapat dua usaha angkutan yang terpisah:',
        { daftar: [
          '_Jawa Unyu Zigyosha_, yang melayani angkutan barang dengan truk dan gerobak.',
          '_Zidosha Sokyoku_, yang melayani angkutan penumpang dengan bus.',
        ] },
        'Ejaan kedua nama ini sedikit berbeda di berbagai sumber, misalnya _Jawa Unyu Zidousha_ dan _Zidousha Sokyoku_, tetapi yang dimaksud adalah dua usaha angkutan yang sama.',
      ],
    },
    {
      judul: 'Lahirnya DAMRI Tahun 1946',
      isi: [
        'Setelah Indonesia merdeka pada 17 Agustus 1945, kedua usaha angkutan tersebut diambil alih oleh Kementerian Perhubungan Republik Indonesia. Angkutan barang berganti nama menjadi **Djawatan Pengangkoetan**, sedangkan angkutan penumpang menjadi **Djawatan Angkoetan Darat**.',
        'Pada **25 November 1946**, melalui **Maklumat Menteri Perhubungan RI Nomor 01/DAM/46**, kedua djawatan itu digabungkan menjadi satu lembaga bernama **Djawatan Angkoetan Motor Republik Indonesia (DAMRI)**. Tugasnya adalah menyelenggarakan angkutan jalan dengan bus, truk, dan kendaraan bermotor lainnya.',
        'DAMRI lahir di tengah masa perjuangan mempertahankan kemerdekaan. Sejak awal, keberadaannya tidak dapat dipisahkan dari kebutuhan bangsa akan sarana angkutan yang menghubungkan satu daerah dengan daerah lain.',
      ],
    },
    {
      judul: 'Menjadi Perusahaan Umum',
      isi: [
        'Bentuk DAMRI berubah beberapa kali mengikuti kebijakan pemerintah dalam mengelola usaha milik negara:',
        { daftar: [
          '**1961**: melalui Peraturan Pemerintah Nomor 233 Tahun 1961, dibentuk Badan Pimpinan Umum Perusahaan Negara (BPUPN) Angkutan Motor "DAMRI".',
          '**1965**: BPUPN dibubarkan dan DAMRI menjadi Perusahaan Negara (PN) Angkutan Motor DAMRI.',
          '**1982**: melalui Peraturan Pemerintah Nomor 30 Tahun 1982, PN Angkutan Motor DAMRI diubah menjadi Perusahaan Umum (Perum) DAMRI.',
          '**1984**: Peraturan Pemerintah Nomor 31 Tahun 1984 menyempurnakan ketentuan tentang Perum DAMRI.',
          '**2002**: Peraturan Pemerintah Nomor 31 Tahun 2002 kembali memperbarui dasar hukum Perum DAMRI.',
        ] },
        'Perubahan menjadi Perum menandai peran ganda DAMRI yang masih dijalankan hingga kini: melayani kepentingan umum sekaligus dikelola sebagai perusahaan yang sehat.',
      ],
    },
    {
      judul: 'DAMRI di Era Modern',
      isi: [
        'Memasuki abad ke-21, DAMRI terus memperbarui diri, baik dari sisi aturan, layanan, maupun armada.',
        { daftar: [
          '**2018**: Peraturan Pemerintah Nomor 38 Tahun 2018 tentang Perum DAMRI ditetapkan pada 6 Agustus 2018 dan menggantikan aturan tahun 2002. Pada tahun yang sama, DAMRI meluncurkan logo barunya.',
          '**2023**: melalui Peraturan Pemerintah Nomor 30 Tahun 2023, Perum PPD (Pengangkutan Penumpang Djakarta) digabungkan ke dalam Perum DAMRI. Sekitar 600 bus dan 1.808 pekerja PPD beralih ke DAMRI tanpa pemutusan hubungan kerja.',
          '**2023**: DAMRI mengoperasikan bus tingkat _Imperial Suites_ untuk rute Jakarta–Surabaya–Malang.',
          '**2026**: DAMRI mengoperasikan 316 bus listrik untuk layanan Transjakarta dan membuka layanan lintas tiga negara Pontianak–Kuching–Bandar Seri Begawan.',
          '**2026**: DAMRI meraih Transportasi Indonesia Award 2026 untuk kategori konektivitas nasional dan layanan transportasi publik.',
        ] },
      ],
    },
    {
      judul: 'Linimasa DAMRI',
      isi: [
        'Ringkasan perjalanan DAMRI dari masa ke masa:',
        { tabel: {
          kolom: ['Tahun', 'Peristiwa'],
          lebar: [0.9, 3.6],
          baris: [
            ['1942–1945', 'Jawa Unyu Zigyosha (barang) dan Zidosha Sokyoku (penumpang) beroperasi.'],
            ['1945', 'Keduanya menjadi Djawatan Pengangkoetan dan Djawatan Angkoetan Darat.'],
            ['1946', 'DAMRI lahir pada 25 November melalui Maklumat Menteri Perhubungan No. 01/DAM/46.'],
            ['1961', 'Menjadi BPUPN Angkutan Motor "DAMRI" (PP No. 233/1961).'],
            ['1965', 'Menjadi Perusahaan Negara (PN) Angkutan Motor DAMRI.'],
            ['1982', 'Menjadi Perusahaan Umum (Perum) DAMRI (PP No. 30/1982).'],
            ['1984', 'Penyempurnaan aturan Perum DAMRI (PP No. 31/1984).'],
            ['2002', 'Dasar hukum baru Perum DAMRI (PP No. 31/2002).'],
            ['2018', 'PP No. 38/2018 tentang Perum DAMRI dan logo baru.'],
            ['2023', 'Penggabungan Perum PPD ke dalam Perum DAMRI (PP No. 30/2023).'],
            ['2026', 'DAMRI genap berusia 80 tahun pada 25 November.'],
          ],
        } },
      ],
    },
  ],
};

const VISI_MISI = {
  judul: 'Visi dan Misi',
  isi: [
    'Visi dan misi menjadi arah bagi seluruh insan DAMRI dalam bekerja.',
    { kotak: {
      judul: 'Visi',
      isi: 'Menjadi perusahaan transportasi jalan kelas dunia yang berkinerja unggul dan berkelanjutan dengan memberikan pelayanan yang berkualitas bagi pelanggan untuk mendukung konektivitas nasional.',
    } },
    '**Misi** Perum DAMRI adalah sebagai berikut.',
    { langkah: [
      'Menyediakan alat produksi yang andal, modern, dan berbasis teknologi mutakhir untuk mendukung konektivitas transportasi.',
      'Memberikan pelayanan yang berkualitas prima, berkeselamatan, dan berorientasi kepada pelanggan.',
      'Mengembangkan _human capital_ yang profesional dan inovatif untuk mengoptimalkan profit guna meningkatkan nilai tambah kepada pemangku kepentingan.',
      'Menjalankan prinsip-prinsip tata kelola perusahaan yang baik (_Good Corporate Governance_) dalam aktivitas usaha perusahaan.',
    ] },
    'Bagi karyawan cabang, visi dan misi ini diwujudkan dalam pekerjaan sehari-hari: armada yang terawat, perjalanan yang selamat dan tepat waktu, pelayanan yang ramah, serta pengelolaan keuangan dan administrasi yang tertib.',
  ],
};

const BAB_ORGANISASI = {
  no: 'IV', judul: 'Organisasi dan Wilayah Operasi',
  pembuka: 'Dari kantor pusat di Jakarta hingga cabang di daerah, DAMRI bekerja sebagai satu organisasi.',
  sub: [
    {
      judul: 'Struktur Organisasi Kantor Pusat',
      isi: [
        'Menurut Peraturan Pemerintah Nomor 38 Tahun 2018, organ Perum DAMRI terdiri atas **Menteri**, **Dewan Pengawas**, dan **Direksi**. Dewan Pengawas bertugas mengawasi dan memberi nasihat kepada Direksi, dengan dukungan Komite Audit dan Sekretaris Dewan Pengawas. Direksi bertanggung jawab atas pengurusan perusahaan sehari-hari.',
        { bagan: 'pusat', keterangan: 'Gambar 4.1 Struktur organisasi kantor pusat Perum DAMRI tingkat Direksi' },
        'Direksi Perum DAMRI terdiri atas jabatan-jabatan berikut:',
        { tabel: {
          kolom: ['Jabatan', 'Lingkup tugas utama'],
          lebar: [2, 2.5],
          baris: [
            ['Direktur Utama', 'Memimpin dan mengoordinasikan seluruh Direksi.'],
            ['Direktur Komersial dan Pengembangan Usaha', 'Penjualan, layanan pelanggan, dan pengembangan bisnis baru.'],
            ['Direktur Keuangan dan Manajemen Risiko', 'Pengelolaan keuangan, akuntansi, dan risiko perusahaan.'],
            ['Direktur Teknik dan Fasilitas', 'Armada, perawatan kendaraan, dan sarana prasarana.'],
            ['Direktur SDM dan Umum', 'Pengelolaan dan pengembangan karyawan serta urusan umum.'],
          ],
        } },
        'Di bawah Direksi terdapat unit-unit kerja setingkat divisi. Pimpinannya kini disebut _Vice President_ (VP), sebelumnya Kepala Divisi. Unit-unit tersebut antara lain:',
        { daftar: [
          'Sekretariat Perusahaan (_Corporate Secretary_) dan Satuan Pengawas Internal, yang berada langsung di bawah Direktur Utama.',
          'Komersial dan Pemasaran, Pengembangan Bisnis, serta Strategi Korporasi.',
          'Operasional dan Keselamatan, Kualitas dan Fasilitas Pelayanan, serta Pengadaan.',
          'Human Capital, _General Service_, dan Teknologi Informasi.',
        ] },
        'Susunan dan nama unit kerja ditetapkan melalui keputusan Direksi. Perubahan nomenklatur struktur organisasi yang terakhir ditetapkan pada 1 November 2023.',
        { lengkapi: 'Sesuaikan daftar unit dan pembagiannya per direktorat dengan keputusan Direksi tentang struktur organisasi yang berlaku.' },
      ],
    },
    {
      judul: 'Jaringan Kantor Cabang',
      isi: [
        'Untuk menjangkau seluruh wilayah Indonesia, DAMRI membentuk kantor Divisi Regional dan kantor cabang. Menurut profil perusahaan, DAMRI memiliki **4 Divisi Regional** dan **44 kantor cabang**. Divisi Regional mengoordinasikan cabang-cabang di wilayahnya, sedangkan setiap cabang dipimpin oleh seorang General Manager yang bertanggung jawab atas layanan di daerahnya.',
        'Cabang merupakan ujung tombak pelayanan. Di cabanglah bus disiapkan, awak ditugaskan, tiket dijual, dan penumpang dilayani secara langsung.',
        { kotak: {
          judul: 'Tahukah Anda?',
          isi: 'Untuk musim mudik Lebaran 2026, DAMRI menyiapkan 1.800 bus di seluruh Indonesia.',
        } },
        { lengkapi: 'Lengkapi: Divisi Regional yang membawahi DAMRI Cabang Bandar Lampung.' },
      ],
    },
    {
      judul: 'Struktur Organisasi Kantor Cabang',
      isi: [
        'Kantor cabang dipimpin oleh seorang **General Manager (GM)**. Dalam menjalankan tugasnya, GM dibantu tiga manager yang masing-masing membawahi asisten manager dan staf. Gambaran umumnya sebagai berikut.',
        { bagan: 'cabang', keterangan: 'Gambar 4.2 Gambaran umum struktur organisasi kantor cabang DAMRI' },
        { tabel: {
          kolom: ['Jabatan', 'Tugas pokok'],
          lebar: [1.7, 2.8],
          baris: [
            ['General Manager', 'Memimpin cabang dan bertanggung jawab atas seluruh kegiatan usaha, operasional, dan kinerja cabang.'],
            ['Manager Usaha', 'Pemasaran, pengembangan usaha, penyelenggaraan layanan angkutan, serta keselamatan dan mutu pelayanan.'],
            ['Manager Keuangan, SDM dan Umum', 'Akuntansi, anggaran, perbendaharaan, perpajakan, pengelolaan SDM, organisasi, dan administrasi umum.'],
            ['Manager Teknik', 'Perawatan dan perbaikan armada, kesiapan kendaraan, serta fasilitas bengkel.'],
            ['Asisten Manager', 'Memimpin sub-bagian di bawah masing-masing manager.'],
            ['Staf dan pelaksana', 'Staf administrasi, petugas loket, pengemudi dan awak bus, serta mekanik.'],
          ],
        } },
        { lengkapi: 'Sesuaikan bagan dengan surat keputusan struktur organisasi DAMRI Cabang Bandar Lampung yang berlaku. Nama pejabat dapat ditambahkan bila diperlukan.' },
      ],
    },
  ],
};

const NILAI_AKHLAK = {
  judul: 'Nilai Utama AKHLAK',
  isi: [
    'Sejak 2020, seluruh insan BUMN, termasuk karyawan Perum DAMRI, memegang nilai-nilai utama (_core values_) yang sama, yaitu **AKHLAK**. Nilai ini ditetapkan melalui Surat Edaran Menteri BUMN Nomor SE-7/MBU/07/2020 tanggal 1 Juli 2020 dan berlaku bagi Direksi, Dewan Pengawas, hingga seluruh pegawai.',
    { tabel: {
      kolom: ['Nilai', 'Makna', 'Panduan perilaku'],
      lebar: [1.05, 1.6, 2.6],
      baris: [
        ['**Amanah**', 'Memegang teguh kepercayaan yang diberikan.', 'Memenuhi janji dan komitmen; bertanggung jawab atas tugas, keputusan, dan tindakan; berpegang teguh pada nilai moral dan etika.'],
        ['**Kompeten**', 'Terus belajar dan mengembangkan kapabilitas.', 'Meningkatkan kompetensi diri; membantu orang lain belajar; menyelesaikan tugas dengan kualitas terbaik.'],
        ['**Harmonis**', 'Saling peduli dan menghargai perbedaan.', 'Menghargai setiap orang apa pun latar belakangnya; suka menolong; membangun lingkungan kerja yang kondusif.'],
        ['**Loyal**', 'Berdedikasi dan mengutamakan kepentingan bangsa dan negara.', 'Menjaga nama baik sesama karyawan, pimpinan, BUMN, dan negara; rela berkorban untuk tujuan yang lebih besar; patuh kepada pimpinan sepanjang tidak bertentangan dengan hukum dan etika.'],
        ['**Adaptif**', 'Terus berinovasi dan antusias menghadapi perubahan.', 'Cepat menyesuaikan diri untuk menjadi lebih baik; terus melakukan perbaikan mengikuti perkembangan teknologi; bertindak proaktif.'],
        ['**Kolaboratif**', 'Membangun kerja sama yang sinergis.', 'Memberi kesempatan kepada berbagai pihak untuk berkontribusi; terbuka dalam bekerja sama untuk menghasilkan nilai tambah; menggerakkan berbagai sumber daya untuk tujuan bersama.'],
      ],
    } },
    'Dalam pekerjaan sehari-hari di DAMRI, nilai-nilai tersebut dapat diwujudkan antara lain sebagai berikut.',
    { daftar: [
      '**Amanah**: pengemudi berangkat sesuai jadwal dan petugas menyerahkan barang penumpang yang tertinggal kepada pemiliknya.',
      '**Kompeten**: awak bus mengikuti pelatihan mengemudi aman dan pelayanan penumpang secara berkala.',
      '**Harmonis**: melayani setiap penumpang dengan ramah tanpa membeda-bedakan.',
      '**Loyal**: menjaga nama baik perusahaan, termasuk saat bermedia sosial.',
      '**Adaptif**: membantu penumpang memakai layanan tiket digital.',
      '**Kolaboratif**: bagian operasional, teknik, dan pelayanan saling berbagi informasi agar perjalanan berjalan lancar.',
    ] },
  ],
};

const ETIKA_KERJA = {
  judul: 'Etika Kerja Insan DAMRI',
  isi: [
    'Etika kerja adalah pedoman sikap dan perilaku yang menjaga kepercayaan penumpang, mitra, dan negara kepada DAMRI. Rincian resminya dimuat dalam pedoman perilaku (_code of conduct_) perusahaan. Secara umum, setiap insan DAMRI diharapkan memegang prinsip berikut.',
    { daftar: [
      '**Mengutamakan keselamatan.** Tidak ada jadwal yang lebih penting daripada keselamatan penumpang, awak, dan pengguna jalan lain.',
      '**Jujur dan berintegritas.** Tidak menerima atau meminta imbalan di luar ketentuan, tidak menarik penumpang tanpa tiket, dan melaporkan setiap penyimpangan.',
      '**Melayani dengan sepenuh hati.** Bersikap sopan, ramah, dan sigap membantu, terutama kepada lansia, penyandang disabilitas, ibu hamil, dan anak-anak.',
      '**Menjaga aset perusahaan.** Merawat armada, peralatan, dan fasilitas seperti milik sendiri.',
      '**Menjaga kerahasiaan.** Melindungi data penumpang dan informasi internal perusahaan.',
      '**Bijak bermedia sosial.** Tidak menyebarkan informasi internal atau konten yang dapat merusak nama baik perusahaan.',
      '**Menjaga penampilan dan disiplin.** Mengenakan seragam dan atribut dengan rapi serta hadir tepat waktu.',
    ] },
    { kotak: {
      judul: 'Ingat',
      isi: 'Satu tindakan kecil seorang karyawan di jalan atau di loket dapat membentuk kesan penumpang terhadap seluruh DAMRI.',
    } },
  ],
};

const BAB_LAYANAN = {
  no: 'V', judul: 'Layanan DAMRI',
  pembuka: 'Dari bandara di kota besar hingga desa di perbatasan negara, DAMRI hadir melalui beragam layanan angkutan.',
  sub: [
    {
      judul: 'Tujuh Segmen Layanan',
      isi: [
        'Peraturan Pemerintah Nomor 38 Tahun 2018 menetapkan kegiatan usaha Perum DAMRI, antara lain angkutan orang dan barang untuk umum, penugasan pemerintah pusat dan daerah seperti angkutan perintis dan angkutan perkotaan, penyewaan kendaraan, keagenan, serta pengiriman paket dan barang. Dalam praktiknya, layanan DAMRI dikelompokkan ke dalam tujuh segmen.',
        { tabel: {
          kolom: ['Segmen', 'Contoh layanan'],
          lebar: [1.3, 3.2],
          baris: [
            ['Bandara', 'Bus dari dan menuju bandar udara, bus apron, dan bus antarterminal.'],
            ['Antarkota', 'Trayek antarkota antarprovinsi (AKAP) dan antarkota dalam provinsi (AKDP).'],
            ['Perkotaan', 'Bus kota, termasuk bus listrik Transjakarta dan Teman Bus.'],
            ['Perintis', 'Trayek bersubsidi ke daerah terpencil, tertinggal, dan perbatasan.'],
            ['Lintas batas negara', 'Trayek ke Malaysia, Brunei Darussalam, dan Timor Leste.'],
            ['Logistik', 'Pengiriman paket dan barang.'],
            ['Pariwisata', 'Sewa bus dan angkutan ke kawasan wisata.'],
          ],
        } },
        'Sebagian layanan bersifat komersial, artinya dijalankan dengan tarif yang ditetapkan perusahaan. Sebagian lainnya merupakan **penugasan pemerintah**, yaitu layanan yang disubsidi agar masyarakat di daerah tertentu tetap mendapat angkutan dengan tarif terjangkau.',
      ],
    },
    {
      judul: 'Angkutan Bandara',
      isi: [
        'Angkutan bandara menghubungkan bandar udara dengan pusat kota dan kota-kota di sekitarnya. Layanan ini menjadi salah satu yang paling dikenal masyarakat, terutama di bandara-bandara besar seperti Soekarno-Hatta. Selain bus dari dan menuju bandara, DAMRI juga melayani bus apron yang mengantar penumpang dari terminal ke pesawat, serta bus antarterminal.',
        'Sejak 1 Februari 2024, layanan DAMRI di Bandara Soekarno-Hatta dan Trans Jawa sepenuhnya memakai pembayaran nontunai, misalnya QRIS, uang elektronik, serta kartu debit dan kredit.',
      ],
    },
    {
      judul: 'Angkutan Antarkota',
      isi: [
        'Angkutan antarkota terdiri atas trayek **antarkota antarprovinsi (AKAP)** yang melintasi batas provinsi dan trayek **antarkota dalam provinsi (AKDP)**. DAMRI menyediakan beberapa kelas layanan, di antaranya Bisnis, Eksekutif, dan Royal Class.',
        { daftar: [
          'Sejak Desember 2023, DAMRI mengoperasikan bus tingkat _Imperial Suites_ untuk rute Jakarta–Surabaya–Malang.',
          'Sejak 6 Maret 2026, DAMRI membuka rute Jakarta–Denpasar yang berangkat sekali seminggu dari masing-masing kota.',
          'Dari Lampung, DAMRI melayani trayek ke berbagai kota di Pulau Jawa dan Sumatra. Rinciannya dibahas pada Bab VI.',
        ] },
      ],
    },
    {
      judul: 'Angkutan Perkotaan',
      isi: [
        'Di kota-kota besar, DAMRI menjalankan angkutan massal yang sebagian besar merupakan penugasan atau kerja sama dengan pemerintah.',
        { daftar: [
          '**Transjakarta**: DAMRI menjadi operator bus listrik Transjakarta dengan armada terbanyak, yaitu 316 bus listrik pada 2026.',
          '**Teman Bus**: program _Buy The Service_ Kementerian Perhubungan sejak Juni 2020. Di Bandung, DAMRI menjadi salah satu operatornya.',
          '**Metro Jabar Trans**: sejak 1 Agustus 2026, DAMRI mengelola layanan ini dengan 60 bus pada empat koridor.',
        ] },
        { kotak: {
          judul: 'Tahukah Anda?',
          isi: 'Pada KTT G20 di Bali, November 2022, DAMRI bersama Kementerian Perhubungan dan PT INKA mengoperasikan 24 bus listrik untuk melayani para delegasi.',
        } },
      ],
    },
    {
      judul: 'Angkutan Perintis',
      isi: [
        'Angkutan perintis adalah angkutan yang melayani daerah terpencil, tertinggal, dan perbatasan yang belum dilayani angkutan umum secara komersial. DAMRI menjalankan angkutan perintis sejak 2001 atas penugasan Kementerian Perhubungan melalui Direktorat Jenderal Perhubungan Darat. Kontrak subsidinya ditandatangani oleh Balai Pengelola Transportasi Darat (BPTD) di setiap wilayah.',
        'Pada 2025, angkutan perintis DAMRI melayani 298 trayek di 36 provinsi, yaitu seluruh provinsi kecuali DKI Jakarta dan DI Yogyakarta. Trayek terbanyak berada di Papua, Nusa Tenggara Timur, dan Papua Barat. Karena disubsidi pemerintah, tarifnya jauh lebih murah daripada angkutan komersial.',
        { kotak: {
          judul: 'Mengapa perintis penting?',
          isi: 'Bagi banyak warga di pelosok, bus perintis menjadi sarana untuk pergi ke pasar, sekolah, puskesmas, dan kantor pemerintahan. Di sinilah peran DAMRI sebagai perusahaan milik negara paling terasa.',
        } },
      ],
    },
    {
      judul: 'Angkutan Lintas Batas Negara',
      isi: [
        'DAMRI juga menghubungkan Indonesia dengan negara tetangga melalui pos lintas batas negara. Penumpang layanan ini wajib membawa paspor.',
        { tabel: {
          kolom: ['Trayek', 'Keterangan'],
          lebar: [2.2, 2.3],
          baris: [
            ['Pontianak–Entikong–Kuching (Malaysia)', 'Kembali beroperasi pada 2022 setelah pandemi.'],
            ['Pontianak–Bandar Seri Begawan (Brunei Darussalam)', 'Kembali beroperasi Maret 2023; melintasi tiga negara.'],
            ['Kupang–Mota\'ain–Dili (Timor Leste)', 'Mulai beroperasi 30 Maret 2023.'],
            ['Singkawang–Aruk–Kuching (Malaysia)', 'Mulai beroperasi 1 Desember 2023.'],
          ],
        } },
      ],
    },
    {
      judul: 'Pariwisata dan Logistik',
      isi: [
        '**Pariwisata.** Masyarakat dan instansi dapat menyewa bus DAMRI untuk perjalanan wisata atau kegiatan rombongan. DAMRI juga mendapat penugasan pemerintah untuk melayani angkutan menuju Kawasan Strategis Pariwisata Nasional, misalnya kawasan Danau Toba.',
        '**Logistik.** Melalui layanan angkutan barang, DAMRI mengirim paket dan barang antarkota dengan memanfaatkan jaringan trayeknya. Layanan ini juga dikembangkan melalui kerja sama dengan mitra logistik.',
      ],
    },
  ],
};

const BAB_KESELAMATAN = {
  no: 'VII', judul: 'Keselamatan dan Pelayanan',
  pembuka: 'Selamat sampai tujuan adalah janji pertama DAMRI kepada setiap penumpang.',
  sub: [
    {
      judul: 'Budaya Keselamatan',
      isi: [
        'Keselamatan angkutan umum diatur dalam Undang-Undang Nomor 22 Tahun 2009 tentang Lalu Lintas dan Angkutan Jalan. Perusahaan angkutan umum juga wajib menerapkan **Sistem Manajemen Keselamatan (SMK)** sesuai Peraturan Menteri Perhubungan Nomor PM 85 Tahun 2018. SMK tersebut memuat sepuluh unsur:',
        { langkah: [
          'Komitmen dan kebijakan keselamatan.',
          'Pengorganisasian.',
          'Manajemen bahaya dan risiko.',
          'Fasilitas pemeliharaan dan perbaikan kendaraan.',
          'Dokumentasi dan data.',
          'Peningkatan kompetensi dan pelatihan.',
          'Tanggap darurat.',
          'Pelaporan kecelakaan internal.',
          'Monitoring dan evaluasi.',
          'Pengukuran kinerja.',
        ] },
        'Dalam praktik sehari-hari, keselamatan dijaga melalui pemeriksaan kondisi kendaraan sebelum berangkat, pemeriksaan kesehatan awak sebelum bertugas, penugasan dua pengemudi untuk perjalanan jarak jauh, serta uji berkala kendaraan. Menjelang Lebaran serta Natal dan Tahun Baru, petugas perhubungan juga melakukan _ramp check_ untuk memeriksa rem, ban, lampu, dan kelengkapan dokumen bus.',
        { kotak: {
          judul: 'Aturan waktu kerja pengemudi',
          isi: [
            'Menurut Pasal 90 UU Nomor 22 Tahun 2009, pengemudi kendaraan umum bekerja paling lama 8 jam sehari. Setelah mengemudi 4 jam berturut-turut, pengemudi wajib beristirahat paling singkat 30 menit.',
          ],
        } },
        'Penumpang juga berperan menjaga keselamatan:',
        { daftar: [
          'Kenali letak pintu darurat, alat pemecah kaca, dan alat pemadam api ringan.',
          'Gunakan sabuk keselamatan bila tersedia.',
          'Jangan membawa barang berbahaya atau mudah terbakar.',
          'Laporkan kepada awak bila melihat hal yang membahayakan.',
        ] },
      ],
    },
    {
      judul: 'Standar Pelayanan Minimal',
      isi: [
        'Standar Pelayanan Minimal (SPM) adalah ukuran terendah pelayanan yang wajib dipenuhi perusahaan angkutan umum. SPM angkutan orang dalam trayek diatur dalam Peraturan Menteri Perhubungan Nomor PM 98 Tahun 2013 yang diubah dengan PM 29 Tahun 2015. SPM mencakup enam aspek:',
        { tabel: {
          kolom: ['Aspek', 'Contoh penerapan'],
          lebar: [1.2, 3.3],
          baris: [
            ['Keamanan', 'Identitas awak yang jelas dan penerangan yang cukup di dalam bus.'],
            ['Keselamatan', 'Kendaraan laik jalan serta tersedianya alat pemecah kaca, alat pemadam api, dan pintu darurat.'],
            ['Kenyamanan', 'Kebersihan bus, pendingin udara atau ventilasi, dan kapasitas penumpang sesuai ketentuan.'],
            ['Keterjangkauan', 'Tarif yang wajar dan kemudahan menjangkau titik keberangkatan.'],
            ['Kesetaraan', 'Kursi prioritas bagi penyandang disabilitas, lansia, ibu hamil, dan anak-anak.'],
            ['Keteraturan', 'Jadwal yang pasti serta informasi perjalanan yang jelas.'],
          ],
        } },
        'Setiap karyawan, dari pengemudi hingga petugas loket, ikut bertanggung jawab memastikan standar ini terpenuhi setiap hari.',
      ],
    },
    {
      judul: 'Pemesanan Tiket dan Pengaduan',
      isi: [
        'Tiket DAMRI dapat dibeli melalui beberapa kanal resmi:',
        { daftar: [
          '**DAMRI Apps**, tersedia di Android dan iOS.',
          '**Situs resmi** damri.co.id.',
          '**Loket resmi** DAMRI di pool, terminal, dan titik keberangkatan.',
          '**Mitra penjualan** resmi, misalnya Traveloka, redBus, dan tiket.com.',
        ] },
        'Cara memesan tiket melalui DAMRI Apps:',
        { langkah: [
          'Unduh dan buka DAMRI Apps, lalu daftar atau masuk ke akun.',
          'Pilih jenis layanan, kota asal, kota tujuan, dan tanggal keberangkatan.',
          'Pilih jadwal, kelas, dan nomor kursi.',
          'Isi data penumpang dengan benar.',
          'Lakukan pembayaran melalui metode yang tersedia.',
          'Simpan tiket elektronik dan tunjukkan kepada petugas saat keberangkatan.',
        ] },
        'Untuk informasi, saran, dan pengaduan, masyarakat dapat menghubungi kanal resmi berikut.',
        { tabel: {
          kolom: ['Kanal', 'Kontak'],
          lebar: [1.6, 2.9],
          baris: [
            ['Call center Halo DAMRI', '1500-825'],
            ['WhatsApp', '0811-2110-0825'],
            ['Surel', 'cs@damri.co.id'],
            ['Situs web', 'damri.co.id'],
            ['Media sosial', '@damriindonesia'],
          ],
        } },
        { kotak: {
          judul: 'Waspada penipuan',
          isi: 'Beli tiket hanya melalui kanal resmi dan jangan mentransfer uang ke rekening pribadi yang mengatasnamakan DAMRI.',
        } },
      ],
    },
  ],
};

const BAB_LAMPUNG = {
  no: 'VI', judul: 'DAMRI Cabang Bandar Lampung',
  pembuka: 'Dari gerbang Pulau Sumatra, DAMRI menghubungkan Lampung dengan Pulau Jawa, kota-kota di Sumatra, dan desa-desa di pedalaman.',
  sub: [
    {
      judul: 'Profil Cabang',
      isi: [
        'DAMRI Cabang Bandar Lampung, yang dalam berbagai pemberitaan juga disebut DAMRI Cabang Lampung, adalah unit Perum DAMRI yang melayani angkutan penumpang dan barang dari dan ke Provinsi Lampung. Letak Lampung sebagai pintu masuk Pulau Sumatra membuat cabang ini berperan penting dalam menghubungkan Sumatra dan Jawa melalui penyeberangan Bakauheni–Merak.',
        'Cabang ini dipimpin oleh seorang General Manager yang dibantu Manager Usaha, Manager Keuangan, SDM dan Umum, serta Manager Teknik. Susunan organisasinya dijelaskan pada subbab 4.3.',
        { tabel: {
          kolom: ['Lokasi', 'Alamat'],
          lebar: [1.5, 3],
          baris: [
            ['Kantor dan Pool Rajabasa', 'Jl. Kapten Abdul Haq, Rajabasa, Bandar Lampung'],
            ['Loket dan Pool Stasiun Tanjung Karang', 'Jl. Kotaraja No. 1, Gunung Sari, Enggal, Bandar Lampung'],
          ],
        } },
        { lengkapi: 'Lengkapi: tahun berdiri cabang, nomor alamat kantor, dan nomor telepon resmi cabang sesuai arsip perusahaan.' },
      ],
    },
    {
      judul: 'Trayek dan Layanan di Lampung',
      isi: [
        'Layanan DAMRI Cabang Bandar Lampung dapat dikelompokkan sebagai berikut. Daftar trayek dan tarif dapat berubah sewaktu-waktu, sehingga informasi terbaru sebaiknya dicek melalui kanal resmi DAMRI.',
        { tabel: {
          kolom: ['Jenis layanan', 'Contoh tujuan'],
          lebar: [1.4, 3.1],
          baris: [
            ['Antarkota antarprovinsi ke Jawa', 'Jakarta (Gambir, Tanjung Priok, Pulo Gebang, Kemayoran), Tangerang dan Serang, Sukabumi, Bandung, Tasikmalaya, dan Yogyakarta.'],
            ['Antarkota antarprovinsi di Sumatra', 'Bengkulu.'],
            ['Dari kabupaten ke Jakarta', 'Kotabumi, Metro, Bandar Jaya, Talang Padang, Tulang Bawang Barat, dan Kalianda.'],
            ['Antarkota dalam provinsi', 'Tanjung Karang–Terminal Eksekutif Bakauheni melalui jalan tol.'],
            ['Angkutan perintis', 'Trayek bersubsidi yang menghubungkan pusat kota dengan desa, misalnya di Lampung Selatan, Lampung Barat, Pringsewu, dan Tulang Bawang Barat.'],
            ['Angkutan khusus', 'Antar-jemput jemaah haji menuju Bandara Radin Inten II dan angkutan logistik.'],
          ],
        } },
        'Bus ke Pulau Jawa menyeberangi Selat Sunda dengan kapal melalui Pelabuhan Bakauheni dan Merak. Layanan tersedia dalam beberapa kelas, yaitu **Bisnis**, **Eksekutif**, dan **Royal Class**. Royal Class memakai susunan kursi 1-2 dengan 24 kursi sehingga penumpang lebih leluasa.',
        'Trayek perintis di Lampung terus bertambah. Pada 2023 tercatat trayek seperti Bandar Jaya–Kalirejo, Kebun Tebu–Liwa, Natar–Margomulyo, Rajabasa–Kejabung, Negara Batin–Panaragan, dan Pringsewu–Sendang Agung. Pada awal 2026, trayek baru di Lampung Barat dibuka, antara lain Liwa–Lumbok Seminung yang sekaligus membuka akses wisata Danau Ranau, dengan tarif mulai Rp15.000.',
        { kotak: {
          judul: 'Tahukah Anda?',
          isi: 'Pada musim haji 2025, DAMRI mengerahkan 13 bus untuk mengantar 7.120 jemaah haji Lampung menuju Bandara Radin Inten II selama 1–29 Mei 2025.',
        } },
      ],
    },
    {
      judul: 'Sarana dan Fasilitas',
      isi: [
        'Pada Agustus 2024, DAMRI Cabang Bandar Lampung mengoperasikan 146 unit armada dengan rincian sebagai berikut.',
        { tabel: {
          kolom: ['Jenis layanan', 'Jumlah armada'],
          lebar: [3, 1.5],
          baris: [
            ['Antarkota antarprovinsi (AKAP)', '86 unit'],
            ['Antarkota dalam provinsi (AKDP)', '31 unit'],
            ['Perintis', '26 unit'],
            ['Logistik', '3 unit'],
            ['**Jumlah**', '**146 unit**'],
          ],
        } },
        'Pada 6 Agustus 2024, Penjabat Gubernur Lampung dan Direktur Utama Perum DAMRI meresmikan 26 bus baru kelas Eksekutif dan Royal Class, bersama ruang tunggu baru di Loket Stasiun Tanjung Karang.',
        'Selain pool Rajabasa dan loket Stasiun Tanjung Karang, penumpang dapat naik dari beberapa titik keberangkatan lain, antara lain Terminal Rajabasa, Sukarame, dan kawasan ITERA di Bandar Lampung, serta pool atau loket di Kotabumi, Metro, Bandar Jaya, Talang Padang, dan Tulang Bawang Barat.',
        { kotak: {
          judul: 'Angkutan Lebaran',
          isi: 'Setiap musim mudik Lebaran, cabang menambah kesiapan armada. Pada Lebaran 2024 disiapkan 98 bus, naik dari sekitar 90 bus pada tahun sebelumnya. Pada angkutan Lebaran 2023, DAMRI Lampung melayani 53.584 penumpang.',
        } },
        { lengkapi: 'Lengkapi: jumlah armada, karyawan, dan titik layanan terbaru bila ada perubahan.' },
      ],
    },
  ],
};

const BAB_INSAN = {
  no: 'VIII', judul: 'Insan DAMRI',
  pembuka: 'Bus yang baik hanya bisa melayani dengan baik bila dijalankan oleh orang-orang yang kompeten dan bangga pada pekerjaannya.',
  sub: [
    {
      judul: 'Pengembangan Kompetensi',
      isi: [
        'Layanan DAMRI digerakkan oleh banyak peran: pengemudi, kondektur dan awak bus, mekanik, petugas loket dan pelayanan, hingga staf administrasi di kantor. Setiap peran menuntut kompetensi yang berbeda, dan semuanya perlu terus diperbarui seiring perkembangan teknologi dan kebutuhan penumpang.',
        { tabel: {
          kolom: ['Peran', 'Contoh kompetensi utama'],
          lebar: [1.3, 3.2],
          baris: [
            ['Pengemudi', 'SIM Umum sesuai jenis kendaraan, mengemudi defensif, pengenalan rute, dan penanganan keadaan darurat.'],
            ['Awak dan petugas pelayanan', 'Pelayanan prima, komunikasi dengan penumpang, pertolongan pertama, dan sistem tiket digital.'],
            ['Mekanik', 'Perawatan berkala, pemeriksaan kelaikan jalan, dan teknologi kendaraan terbaru.'],
            ['Staf kantor', 'Administrasi, keuangan, pengelolaan SDM, dan pemanfaatan sistem informasi.'],
          ],
        } },
        'Pengembangan kompetensi dilakukan melalui berbagai cara, antara lain pelatihan di dalam dan di luar perusahaan, sertifikasi profesi, pembelajaran langsung di tempat kerja, pendampingan oleh karyawan senior, serta pembelajaran mandiri secara daring.',
        { kotak: {
          judul: 'Tahukah Anda?',
          isi: 'Menurut Undang-Undang Nomor 22 Tahun 2009 tentang Lalu Lintas dan Angkutan Jalan, pengemudi kendaraan bermotor umum wajib memiliki SIM Umum. Pengemudi bus besar memerlukan SIM B I Umum.',
        } },
      ],
    },
    {
      judul: 'Hak dan Kewajiban Karyawan',
      isi: [
        'Hubungan kerja di Perum DAMRI mengikuti peraturan perundang-undangan di bidang ketenagakerjaan serta Peraturan Perusahaan atau Perjanjian Kerja Bersama yang berlaku. Karyawan dapat menanyakan rinciannya kepada Bagian SDM. Secara garis besar, hak dan kewajiban karyawan adalah sebagai berikut.',
        { tabel: {
          kolom: ['Hak karyawan', 'Kewajiban karyawan'],
          baris: [
            ['Upah dan penghasilan sesuai ketentuan perusahaan.', 'Melaksanakan tugas sesuai jabatan dengan penuh tanggung jawab.'],
            ['Jaminan sosial ketenagakerjaan dan kesehatan.', 'Mematuhi peraturan perusahaan, tata tertib, dan perintah kerja yang sah.'],
            ['Waktu istirahat dan cuti, termasuk cuti tahunan.', 'Mengutamakan keselamatan diri, rekan kerja, dan penumpang.'],
            ['Keselamatan dan kesehatan kerja.', 'Menjaga aset, nama baik, dan kerahasiaan perusahaan.'],
            ['Kesempatan mengembangkan kompetensi dan karier.', 'Menerapkan nilai AKHLAK dan etika kerja.'],
            ['Perlakuan yang adil tanpa diskriminasi.', 'Tidak menerima gratifikasi dan menghindari benturan kepentingan.'],
          ],
        } },
      ],
    },
    {
      judul: 'Menjadi Duta DAMRI',
      isi: [
        'Setiap karyawan adalah wajah DAMRI. Penumpang tidak membaca laporan tahunan perusahaan; mereka menilai DAMRI dari sapaan petugas loket, cara pengemudi membawa kendaraan, dan kebersihan bus yang mereka naiki.',
        'Beberapa kebiasaan sederhana yang membuat karyawan menjadi duta yang baik bagi perusahaan:',
        { daftar: [
          'Menyapa penumpang dengan senyum, salam, dan sapa.',
          'Berpenampilan rapi dengan seragam dan tanda pengenal lengkap.',
          'Menjaga kebersihan dan kerapian armada serta ruang tunggu.',
          'Menanggapi pertanyaan dan keluhan dengan sabar, lalu meneruskannya ke saluran resmi bila perlu.',
          'Menceritakan sejarah dan layanan DAMRI dengan bangga dan benar.',
          'Menunjukkan perilaku yang baik di jalan, di tempat kerja, maupun di media sosial.',
        ] },
        { kotak: {
          judul: 'Pesan untuk insan DAMRI',
          isi: 'DAMRI telah melayani sejak 1946. Setiap karyawan hari ini ikut menulis bab berikutnya dari sejarah panjang tersebut.',
        } },
      ],
    },
  ],
};

const BAB = [
  BAB_MENGENAL,
  BAB_SEJARAH,
  { no: 'III', judul: 'Visi, Misi, dan Budaya Perusahaan',
    pembuka: 'Visi, misi, dan nilai-nilai perusahaan menjadi pegangan bersama seluruh insan DAMRI.',
    sub: [VISI_MISI, NILAI_AKHLAK, ETIKA_KERJA] },
  BAB_ORGANISASI,
  BAB_LAYANAN,
  BAB_LAMPUNG,
  BAB_KESELAMATAN,
  BAB_INSAN,
];

const PENUTUP = [
  'Demikian buku Pengetahuan Seputar DAMRI ini kami susun. Melalui buku ini, pembaca diajak mengenal DAMRI dari berbagai sisi: sejarah panjangnya sejak 1946, nilai-nilai yang menjadi pegangan, ragam layanan yang diberikan, hingga peran DAMRI Cabang Bandar Lampung dalam melayani masyarakat Lampung.',
  'Bagi karyawan, mengenal perusahaan adalah dasar untuk bekerja dengan bangga dan penuh tanggung jawab. Karyawan yang memahami sejarah dan nilai perusahaannya akan lebih siap menjadi duta DAMRI di mana pun ia bertugas. Bagi masyarakat, kami berharap buku ini menambah wawasan dan mempererat kedekatan dengan DAMRI.',
  'Informasi dalam buku ini disusun berdasarkan data yang tersedia pada saat penyusunan. Layanan dan kebijakan perusahaan dapat berkembang sewaktu-waktu. Untuk informasi terbaru, pembaca dapat menghubungi kantor DAMRI Cabang Bandar Lampung atau mengunjungi situs resmi Perum DAMRI di damri.co.id.',
  'Saran dan masukan untuk penyempurnaan buku ini dapat disampaikan kepada Bagian SDM DAMRI Cabang Bandar Lampung. Terima kasih telah membaca, dan selamat melanjutkan perjalanan bersama DAMRI.',
];

const PUSTAKA = [
  // Peraturan
  'Peraturan Pemerintah Nomor 233 Tahun 1961 tentang pembentukan Badan Pimpinan Umum Perusahaan Negara Angkutan Motor "DAMRI".',
  'Peraturan Pemerintah Nomor 30 Tahun 1982 tentang pengalihan bentuk Perusahaan Negara Angkutan Motor "DAMRI" menjadi Perusahaan Umum.',
  'Peraturan Pemerintah Nomor 31 Tahun 1984 tentang Perusahaan Umum (Perum) DAMRI.',
  'Peraturan Pemerintah Nomor 31 Tahun 2002 tentang Perusahaan Umum (Perum) DAMRI.',
  'Peraturan Pemerintah Nomor 38 Tahun 2018 tentang Perusahaan Umum (Perum) DAMRI.',
  'Peraturan Pemerintah Nomor 30 Tahun 2023 tentang Penggabungan Perusahaan Umum Pengangkutan Penumpang Djakarta ke dalam Perusahaan Umum DAMRI.',
  'Undang-Undang Nomor 22 Tahun 2009 tentang Lalu Lintas dan Angkutan Jalan.',
  'Peraturan Menteri Perhubungan Nomor PM 98 Tahun 2013 tentang Standar Pelayanan Minimal Angkutan Orang dengan Kendaraan Bermotor Umum dalam Trayek, sebagaimana diubah dengan PM 29 Tahun 2015.',
  'Peraturan Menteri Perhubungan Nomor PM 85 Tahun 2018 tentang Sistem Manajemen Keselamatan Perusahaan Angkutan Umum.',
  'Surat Edaran Menteri BUMN Nomor SE-7/MBU/07/2020 tentang Nilai-Nilai Utama Sumber Daya Manusia Badan Usaha Milik Negara.',
  // Sumber resmi perusahaan dan pemerintah
  'Perum DAMRI. _Sejarah Perusahaan_. https://damri.co.id/id/sejarah-perusahaan',
  'Perum DAMRI. _Visi dan Misi_. https://damri.co.id/id/vision',
  'Perum DAMRI. _Struktur DAMRI_. https://www.damri.co.id/struktur-damri',
  'Perum DAMRI. _Kontak Kami_. https://damri.co.id/id/kontak-kami',
  'Perum DAMRI. _Launching Logo Baru DAMRI_. https://compro.damri.co.id/artikel/launching-logo-baru-damri.html',
  'Pemerintah Provinsi Lampung. (2024). _Peningkatan Layanan Transportasi, Pj Gubernur Lampung Resmikan Ruang Tunggu dan 26 Bus DAMRI_. https://lampungprov.go.id/detail-post/peningkatan-layanan-transportasi-pj-gubernur-lampung-resmikan-ruang-tunggu-dan-26-bus-damri',
  // Pemberitaan dan kajian
  'CNBC Indonesia. (2023). _Jokowi Resmi Bubarkan Perum PPD, Lebur ke Perum DAMRI_. https://www.cnbcindonesia.com/news/20230608101107-4-444117/jokowi-resmi-bubarkan-perum-ppd-lebur-ke-perum-damri',
  'Kompas.com. (2023). _Usai Merger, Perum DAMRI Ambil Alih Seluruh Aset PPD Termasuk Armada_. https://money.kompas.com/read/2023/06/19/185314526/usai-merger-perum-damri-ambil-alih-seluruh-aset-ppd-termasuk-armada',
  'Kompas.com. (2026). _DAMRI Sebut 316 Bus Listrik Telah Beroperasi di Jakarta_. https://otomotif.kompas.com/read/2026/05/29/184100815/damri-sebut-316-bus-listrik-telah-beroperasi-di-jakarta',
  'Tempo. (2026). _DAMRI Buka Rute Jakarta-Bali, Berapa Tarifnya?_ https://www.tempo.co/hiburan/damri-buka-rute-jakarta-bali-berapa-tarifnya--2116291',
  'IDN Times Jabar. (2026). _Metro Jabar Trans Kini Dikelola DAMRI_. https://jabar.idntimes.com/news/jawa-barat/metro-jabar-trans-kini-dikelola-damri-tarif-dipastikan-tetap-rp4-900-00-nqtvm-rhz1zj',
  'Masyarakat Transportasi Indonesia. _Angkutan Jalan Perintis Menggapai Pelosok Mensejahterakan Negeri_. https://mti.or.id/en/angkutan-jalan-perintis-menggapai-pelosok-mensejahterakan-negeri/',
  'Okezone. (2026). _Bulog hingga Perhutani Tetap Jadi Perum, Wacana Ubah Status Jadi Persero Dihentikan_. https://economy.okezone.com/read/2026/09/17/320/3242742/bulog-hingga-perhutani-tetap-jadi-perum-wacana-ubah-status-jadi-persero-dihentikan',
  'Antara Lampung. (2026). Berita kesiapan 1.800 bus DAMRI untuk angkutan Lebaran 2026. https://lampung.antaranews.com/berita/817503/damri-siapkan-1800-bus-untuk-layani-proyeksi-27-juta-pemudik',
  'Transportasi Media. (2026). _Perum DAMRI Raih Penghargaan Bergengsi di Ajang Transportasi Indonesia Award 2026_. https://transportasimedia.com/detail/21293/perum-damri-raih-penghargaan-bergengsi-di-ajang-transportasi-indonesia-award-2026',
  'Jurnal Atrabis. Kajian makna logo Perum DAMRI. https://jurnal.plb.ac.id/index.php/atrabis/article/download/230/135',
];

// Bagan struktur organisasi, digambar oleh bagan.js.
const BAGAN = {
  pusat: {
    pengawas: 'Dewan Pengawas',
    puncak: 'Direktur Utama',
    staf: ['Sekretariat Perusahaan', 'Satuan Pengawas Internal'],
    kolom: [
      { nama: 'Direktur Komersial dan Pengembangan Usaha' },
      { nama: 'Direktur Keuangan dan Manajemen Risiko' },
      { nama: 'Direktur Teknik dan Fasilitas' },
      { nama: 'Direktur SDM dan Umum' },
    ],
    catatan: 'Garis putus-putus: fungsi pengawasan dan pemberian nasihat.',
  },
  cabang: {
    puncak: 'General Manager',
    kolom: [
      { nama: 'Manager Usaha', anak: ['Asisten Manager Usaha', 'Staf Usaha', 'Petugas Loket', 'Pengemudi dan Awak Bus'] },
      { nama: 'Manager Keuangan, SDM dan Umum', anak: ['Asisten Manager', 'Staf Keuangan, SDM dan Umum'] },
      { nama: 'Manager Teknik', anak: ['Asisten Manager Teknik Perbaikan', 'Mekanik'] },
    ],
  },
};

module.exports = { KATA_PENGANTAR, PENANDA_TANGAN, BAB, PENUTUP, PUSTAKA, BAGAN };

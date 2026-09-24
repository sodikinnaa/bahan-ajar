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

const BAB = [];

const PENUTUP = [
  'Demikian buku Pengetahuan Seputar DAMRI ini kami susun. Melalui buku ini, pembaca diajak mengenal DAMRI dari berbagai sisi: sejarah panjangnya sejak 1946, nilai-nilai yang menjadi pegangan, ragam layanan yang diberikan, hingga peran DAMRI Cabang Bandar Lampung dalam melayani masyarakat Lampung.',
  'Bagi karyawan, mengenal perusahaan adalah dasar untuk bekerja dengan bangga dan penuh tanggung jawab. Karyawan yang memahami sejarah dan nilai perusahaannya akan lebih siap menjadi duta DAMRI di mana pun ia bertugas. Bagi masyarakat, kami berharap buku ini menambah wawasan dan mempererat kedekatan dengan DAMRI.',
  'Informasi dalam buku ini disusun berdasarkan data yang tersedia pada saat penyusunan. Layanan dan kebijakan perusahaan dapat berkembang sewaktu-waktu. Untuk informasi terbaru, pembaca dapat menghubungi kantor DAMRI Cabang Bandar Lampung atau mengunjungi situs resmi Perum DAMRI di damri.co.id.',
  'Saran dan masukan untuk penyempurnaan buku ini dapat disampaikan kepada Bagian SDM DAMRI Cabang Bandar Lampung. Terima kasih telah membaca, dan selamat melanjutkan perjalanan bersama DAMRI.',
];

const PUSTAKA = [];

module.exports = { KATA_PENGANTAR, PENANDA_TANGAN, BAB, PENUTUP, PUSTAKA };

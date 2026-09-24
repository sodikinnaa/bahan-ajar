// Menyusun Pengetahuan-Seputar-DAMRI.docx.
// Urutan lengkap ada di build.sh; nomor halaman daftar isi dibaca dari
// halaman.json yang dihasilkan halaman.py.
const fs = require('fs');
const path = require('path');
const JSZip = require('jszip');
const {
  Document, Packer, Paragraph, TextRun, ImageRun, Header, Footer, Tab, Bookmark,
  AlignmentType, PageNumber, NumberFormat, TabStopType, LeaderType, BorderStyle,
  HorizontalPositionRelativeFrom, VerticalPositionRelativeFrom, TextWrappingType, LineRuleType,
} = require('docx');

const AKAR = path.resolve(__dirname, '..');
const ASET = path.join(AKAR, 'aset');
const KELUARAN = path.join(AKAR, 'Pengetahuan-Seputar-DAMRI.docx');
const HALAMAN = path.join(__dirname, 'halaman.json');

const WARNA = {
  navy: '0B3A78', kuning: 'F2A516', kuningTua: 'B45309', teks: '1F2937',
  abu: '6B7280', garis: 'C9D3E3', biruMuda: 'BFD7F5',
};
const FONT = 'Arial';
const mm = (v) => Math.round(v * 56.6929); // milimeter ke twip
const px = (v) => v * 96 / 25.4;          // milimeter ke piksel gambar

const A5 = { width: 8391, height: 11906 };
const MARGIN = {
  top: mm(25), bottom: mm(22), left: mm(20), right: mm(20),
  header: mm(10), footer: mm(10),
};
const LEBAR_ISI = A5.width - MARGIN.left - MARGIN.right;
// Jarak baris proporsional (240 = 1 spasi). lineRule wajib ditulis: tanpa itu
// LibreOffice membacanya sebagai jarak pasti dan memotong gambar.
const spasi = (line) => ({ line, lineRule: LineRuleType.AUTO });

// ---------- Isi buku ----------

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

// Kerangka bab: [judul subbab, panduan isi untuk penulis].
const BAB = [
  { no: 'I', judul: 'Mengenal DAMRI', sub: [
    ['Arti Nama DAMRI', 'Kepanjangan DAMRI dari masa ke masa dan makna di balik nama tersebut.'],
    ['Status dan Bidang Usaha', 'Perum DAMRI sebagai Badan Usaha Milik Negara di bidang transportasi jalan.'],
    ['Logo dan Identitas Perusahaan', 'Makna logo DAMRI dan identitas visual yang digunakan saat ini.'],
  ] },
  { no: 'II', judul: 'Sejarah Perjalanan DAMRI', sub: [
    ['Masa Pendudukan Jepang', 'Dua usaha angkutan barang dan penumpang yang menjadi cikal bakal DAMRI.'],
    ['Lahirnya DAMRI Tahun 1946', 'Maklumat Menteri Perhubungan RI No. 01/DAM/46 tanggal 25 November 1946.'],
    ['Menjadi Perusahaan Umum', 'Perum DAMRI berdasarkan PP No. 30 Tahun 1982 dan PP No. 31 Tahun 1984.'],
    ['DAMRI di Era Modern', 'PP No. 38 Tahun 2018, pembaruan logo, dan penggabungan Perum PPD pada 2023.'],
  ] },
  { no: 'III', judul: 'Visi, Misi, dan Budaya Perusahaan', sub: [
    ['Visi dan Misi', 'Visi dan misi Perum DAMRI sesuai dokumen resmi perusahaan terbaru.'],
    ['Nilai Utama AKHLAK', 'Amanah, Kompeten, Harmonis, Loyal, Adaptif, dan Kolaboratif.'],
    ['Etika Kerja Insan DAMRI', 'Pedoman perilaku yang berlaku bagi seluruh karyawan.'],
  ] },
  { no: 'IV', judul: 'Organisasi dan Wilayah Operasi', sub: [
    ['Struktur Organisasi', 'Dewan Pengawas, Direksi, dan unit kerja di kantor pusat.'],
    ['Jaringan Kantor Cabang', 'Sebaran kantor cabang DAMRI di seluruh Indonesia.'],
  ] },
  { no: 'V', judul: 'Layanan DAMRI', sub: [
    ['Angkutan Bandara', 'Layanan bus dari dan menuju bandar udara.'],
    ['Angkutan Antarkota', 'Trayek antarkota dalam provinsi dan antarprovinsi.'],
    ['Angkutan Perkotaan', 'Bus kota dan angkutan massal di wilayah perkotaan.'],
    ['Angkutan Perintis', 'Penugasan pemerintah untuk menjangkau daerah terpencil dan perbatasan.'],
    ['Angkutan Lintas Batas Negara', 'Trayek yang menghubungkan Indonesia dengan negara tetangga.'],
    ['Pariwisata dan Logistik', 'Sewa bus pariwisata serta angkutan barang.'],
  ] },
  { no: 'VI', judul: 'DAMRI Cabang Bandar Lampung', sub: [
    ['Profil Cabang', 'Sejarah singkat, alamat kantor, dan wilayah kerja cabang.'],
    ['Trayek dan Layanan di Lampung', 'Daftar trayek dan layanan yang dijalankan cabang Bandar Lampung.'],
    ['Sarana dan Fasilitas', 'Armada, pool, loket, dan fasilitas pendukung lainnya.'],
  ] },
  { no: 'VII', judul: 'Keselamatan dan Pelayanan', sub: [
    ['Budaya Keselamatan', 'Prinsip keselamatan bagi pengemudi, awak, dan penumpang.'],
    ['Standar Pelayanan Minimal', 'Standar pelayanan yang wajib dipenuhi dalam setiap perjalanan.'],
    ['Pemesanan Tiket dan Pengaduan', 'Cara memesan tiket, kanal informasi resmi, dan saluran pengaduan.'],
  ] },
  { no: 'VIII', judul: 'Insan DAMRI', sub: [
    ['Pengembangan Kompetensi', 'Program pelatihan dan jenjang karier karyawan.'],
    ['Hak dan Kewajiban Karyawan', 'Ketentuan pokok yang perlu diketahui setiap karyawan.'],
    ['Menjadi Duta DAMRI', 'Peran karyawan dalam menjaga nama baik perusahaan.'],
  ] },
];

const PENUTUP = [
  'Demikian buku Pengetahuan Seputar DAMRI ini kami susun. Melalui buku ini, pembaca diajak mengenal DAMRI dari berbagai sisi: sejarah panjangnya sejak 1946, nilai-nilai yang menjadi pegangan, ragam layanan yang diberikan, hingga peran DAMRI Cabang Bandar Lampung dalam melayani masyarakat Lampung.',
  'Bagi karyawan, mengenal perusahaan adalah dasar untuk bekerja dengan bangga dan penuh tanggung jawab. Karyawan yang memahami sejarah dan nilai perusahaannya akan lebih siap menjadi duta DAMRI di mana pun ia bertugas. Bagi masyarakat, kami berharap buku ini menambah wawasan dan mempererat kedekatan dengan DAMRI.',
  'Informasi dalam buku ini disusun berdasarkan data yang tersedia pada saat penyusunan. Layanan dan kebijakan perusahaan dapat berkembang sewaktu-waktu. Untuk informasi terbaru, pembaca dapat menghubungi kantor DAMRI Cabang Bandar Lampung atau mengunjungi situs resmi Perum DAMRI di damri.co.id.',
  'Saran dan masukan untuk penyempurnaan buku ini dapat disampaikan kepada Bagian SDM DAMRI Cabang Bandar Lampung. Terima kasih telah membaca, dan selamat melanjutkan perjalanan bersama DAMRI.',
];

// ---------- Gambar ----------

function ukuranPng(buf) {
  return { w: buf.readUInt32BE(16), h: buf.readUInt32BE(20) };
}

const LOGO = fs.readFileSync(path.join(ASET, 'logo-damri.png'));
const LATAR_SAMPUL = fs.readFileSync(path.join(ASET, 'sampul-latar.png'));

// Logo diukur dari tingginya agar logo resmi dengan rasio apa pun tetap pas.
function logo(tinggiMm, lebarMaksMm) {
  const { w, h } = ukuranPng(LOGO);
  let tinggi = tinggiMm;
  let lebar = tinggiMm * w / h;
  if (lebar > lebarMaksMm) { lebar = lebarMaksMm; tinggi = lebar * h / w; }
  return new ImageRun({
    type: 'png', data: LOGO,
    transformation: { width: px(lebar), height: px(tinggi) },
    altText: { name: 'Logo DAMRI', title: 'Logo DAMRI', description: 'Logo Perum DAMRI' },
  });
}

// ---------- Bagian umum ----------

const entri = []; // isi daftar isi, urut sesuai kemunculan
let nomorPenanda = 0;

function judul(level, teks, { kicker, pisahHalaman = true } = {}) {
  const penanda = `_Toc${String(++nomorPenanda).padStart(6, '0')}`;
  entri.push({ penanda, level, teks: kicker ? `${kicker} ${teks}` : teks });
  const runs = kicker
    ? [
      new TextRun({ text: kicker, size: 20, color: WARNA.kuningTua, characterSpacing: 60 }),
      new TextRun({ text: teks, break: 1 }),
    ]
    : [new TextRun(teks)];
  return new Paragraph({
    style: level === 1 ? 'Heading1' : 'Heading2',
    pageBreakBefore: level === 1 && pisahHalaman,
    children: [new Bookmark({ id: penanda, children: runs })],
  });
}

const paragraf = (teks) => new Paragraph({ children: [new TextRun(teks)] });

// ---------- Sampul ----------

function sampul() {
  const latar = new ImageRun({
    type: 'png', data: LATAR_SAMPUL,
    transformation: { width: 560, height: 794 },
    floating: {
      horizontalPosition: { relative: HorizontalPositionRelativeFrom.PAGE, offset: 0 },
      verticalPosition: { relative: VerticalPositionRelativeFrom.PAGE, offset: 0 },
      behindDocument: true,
      wrap: { type: TextWrappingType.NONE },
    },
    altText: { name: 'Latar sampul', title: 'Latar sampul', description: 'Latar sampul buku' },
  });
  const teks = (text, opsi) => new TextRun({ text, font: FONT, ...opsi });
  const baris = (opsi, children) => new Paragraph({ alignment: AlignmentType.LEFT, ...opsi, children });
  return [
    baris({ spacing: { after: 0 } }, [latar, logo(14, 60)]),
    baris({ spacing: { before: mm(37), after: mm(3) } },
      [teks('BUKU PENGETAHUAN PERUSAHAAN', { size: 17, bold: true, color: WARNA.kuning, characterSpacing: 50 })]),
    baris({ spacing: { after: 0, ...spasi(250) } },
      [teks('Pengetahuan', { size: 60, bold: true, color: 'FFFFFF' })]),
    baris({ spacing: { after: mm(6), ...spasi(250) } },
      [teks('Seputar DAMRI', { size: 60, bold: true, color: 'FFFFFF' })]),
    baris({ spacing: { after: 0, ...spasi(300) }, indent: { right: mm(38) } },
      [teks('Sejarah, nilai, dan layanan Perum DAMRI untuk karyawan dan masyarakat luas.', { size: 22, color: WARNA.biruMuda })]),
    baris({ spacing: { before: mm(62), after: mm(1) } },
      [teks('Disusun oleh', { size: 17, color: WARNA.abu })]),
    baris({ spacing: { after: mm(1) } },
      [teks('Perum DAMRI Cabang Bandar Lampung', { size: 24, bold: true, color: WARNA.navy })]),
    baris({ spacing: { after: 0 } },
      [teks('Bandar Lampung, 2026', { size: 18, color: WARNA.abu })]),
  ];
}

// ---------- Header dan footer ----------

const header = new Header({
  children: [new Paragraph({
    alignment: AlignmentType.LEFT,
    spacing: { after: 0, ...spasi(240) },
    tabStops: [{ type: TabStopType.RIGHT, position: LEBAR_ISI }],
    border: { bottom: { style: BorderStyle.SINGLE, size: 8, color: WARNA.navy, space: 4 } },
    children: [
      logo(7, 35),
      new TextRun({
        children: [new Tab(), 'PENGETAHUAN SEPUTAR DAMRI'],
        size: 15, bold: true, color: WARNA.navy, characterSpacing: 30,
      }),
    ],
  })],
});

const footer = new Footer({
  children: [new Paragraph({
    alignment: AlignmentType.LEFT,
    spacing: { before: 0, after: 0, ...spasi(240) },
    tabStops: [{ type: TabStopType.RIGHT, position: LEBAR_ISI }],
    border: { top: { style: BorderStyle.SINGLE, size: 4, color: WARNA.garis, space: 6 } },
    children: [
      new TextRun({ text: 'Perum DAMRI Cabang Bandar Lampung', size: 15, color: WARNA.abu }),
      new TextRun({ children: [new Tab(), PageNumber.CURRENT], size: 18, bold: true, color: WARNA.navy }),
    ],
  })],
});

// ---------- Halaman awal ----------

function kataPengantar() {
  const ttd = (text, opsi = {}) => new Paragraph({
    alignment: AlignmentType.LEFT,
    indent: { left: Math.round(LEBAR_ISI * 0.35) },
    spacing: { after: 0 },
    children: [new TextRun({ text, ...opsi })],
  });
  return [
    judul(1, 'KATA PENGANTAR', { pisahHalaman: false }),
    ...KATA_PENGANTAR.map(paragraf),
    new Paragraph({ spacing: { after: 0 }, children: [] }),
    ttd(PENANDA_TANGAN.tempatTanggal),
    ttd(PENANDA_TANGAN.jabatan),
    ttd(PENANDA_TANGAN.unit),
    new Paragraph({ spacing: { before: mm(16), after: 0 }, children: [] }),
    ttd(PENANDA_TANGAN.nama, { bold: true, underline: {} }),
  ];
}

function daftarIsi() {
  return [
    judul(1, 'DAFTAR ISI'),
    new Paragraph({ children: [new TextRun('@@DAFTAR_ISI@@')] }),
  ];
}

// ---------- Isi ----------

function kerangkaBab() {
  const hasil = [];
  BAB.forEach((bab, i) => {
    hasil.push(judul(1, bab.judul.toUpperCase(), { kicker: `BAB ${bab.no}`, pisahHalaman: i > 0 }));
    bab.sub.forEach(([sub, panduan], j) => {
      hasil.push(judul(2, `${i + 1}.${j + 1} ${sub}`));
      hasil.push(new Paragraph({ style: 'Panduan', children: [new TextRun(panduan)] }));
    });
  });
  return hasil;
}

function penutup() {
  return [judul(1, 'PENUTUP'), ...PENUTUP.map(paragraf)];
}

// ---------- Dokumen ----------

const halamanAwal = kataPengantar().concat(daftarIsi());
const isi = kerangkaBab().concat(penutup());

const doc = new Document({
  title: 'Pengetahuan Seputar DAMRI',
  creator: 'Perum DAMRI Cabang Bandar Lampung',
  styles: {
    default: {
      document: {
        run: { font: FONT, size: 21, color: WARNA.teks, language: { value: 'id-ID' } },
        paragraph: { alignment: AlignmentType.JUSTIFIED, spacing: { after: 140, ...spasi(324) } },
      },
    },
    paragraphStyles: [
      {
        id: 'Heading1', name: 'heading 1', basedOn: 'Normal', next: 'Normal', quickFormat: true,
        run: { font: FONT, size: 28, bold: true, color: WARNA.navy },
        paragraph: {
          alignment: AlignmentType.CENTER, outlineLevel: 0, keepNext: true,
          spacing: { before: mm(4), after: mm(8), ...spasi(300) },
        },
      },
      {
        id: 'Heading2', name: 'heading 2', basedOn: 'Normal', next: 'Normal', quickFormat: true,
        run: { font: FONT, size: 23, bold: true, color: WARNA.navy },
        paragraph: {
          alignment: AlignmentType.LEFT, outlineLevel: 1, keepNext: true,
          spacing: { before: 240, after: 80, ...spasi(276) },
        },
      },
      {
        id: 'Panduan', name: 'Panduan Penulisan', basedOn: 'Normal', next: 'Normal', quickFormat: true,
        run: { size: 19, italics: true, color: WARNA.abu },
        paragraph: {
          alignment: AlignmentType.LEFT, indent: { left: mm(3) },
          border: { left: { style: BorderStyle.SINGLE, size: 18, color: WARNA.kuning, space: 6 } },
          spacing: { after: 120, ...spasi(276) },
        },
      },
      {
        id: 'TOC1', name: 'toc 1', basedOn: 'Normal', next: 'Normal',
        run: { bold: true, color: WARNA.navy },
        paragraph: {
          alignment: AlignmentType.LEFT,
          tabStops: [{ type: TabStopType.RIGHT, position: LEBAR_ISI, leader: LeaderType.DOT }],
          spacing: { before: 140, after: 20, ...spasi(276) },
        },
      },
      {
        id: 'TOC2', name: 'toc 2', basedOn: 'Normal', next: 'Normal',
        paragraph: {
          alignment: AlignmentType.LEFT, indent: { left: mm(6) },
          tabStops: [{ type: TabStopType.RIGHT, position: LEBAR_ISI, leader: LeaderType.DOT }],
          spacing: { before: 0, after: 20, ...spasi(276) },
        },
      },
    ],
  },
  sections: [
    {
      properties: {
        page: {
          size: A5,
          margin: { top: mm(16), bottom: mm(10), left: mm(16), right: mm(16), header: 0, footer: 0 },
        },
      },
      children: sampul(),
    },
    {
      properties: {
        page: { size: A5, margin: MARGIN, pageNumbers: { start: 1, formatType: NumberFormat.LOWER_ROMAN } },
      },
      headers: { default: header },
      footers: { default: footer },
      children: halamanAwal,
    },
    {
      // Header dan footer tidak ditulis ulang: Word memakai milik bagian sebelumnya.
      properties: {
        page: { size: A5, margin: MARGIN, pageNumbers: { start: 1, formatType: NumberFormat.DECIMAL } },
      },
      children: isi,
    },
  ],
});

// ---------- Daftar isi ----------

const esc = (s) => s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');

function xmlDaftarIsi() {
  const halaman = fs.existsSync(HALAMAN) ? JSON.parse(fs.readFileSync(HALAMAN, 'utf8')) : {};
  const r = (isi) => `<w:r>${isi}</w:r>`;
  const fld = (jenis) => r(`<w:fldChar w:fldCharType="${jenis}"/>`);
  const instr = (teks) => r(`<w:instrText xml:space="preserve"> ${teks} </w:instrText>`);
  const baris = entri.map((e, i) => {
    const awal = i === 0 ? fld('begin') + instr('TOC \\o "1-2" \\h \\z \\u') + fld('separate') : '';
    const nomor = halaman[e.penanda] || '?';
    return `<w:p><w:pPr><w:pStyle w:val="TOC${e.level}"/></w:pPr>${awal}`
      + `<w:hyperlink w:anchor="${e.penanda}" w:history="1">`
      + r(`<w:t xml:space="preserve">${esc(e.teks)}</w:t>`) + r('<w:tab/>')
      + fld('begin') + instr(`PAGEREF ${e.penanda} \\h`) + fld('separate')
      + r(`<w:t>${nomor}</w:t>`) + fld('end')
      + '</w:hyperlink></w:p>';
  });
  return baris.join('') + `<w:p><w:pPr><w:pStyle w:val="TOC2"/></w:pPr>${fld('end')}</w:p>`;
}

async function main() {
  const zip = await JSZip.loadAsync(await Packer.toBuffer(doc));
  const berkas = 'word/document.xml';
  const xml = await zip.file(berkas).async('string');
  const pola = /<w:p>(?:(?!<\/w:p>).)*@@DAFTAR_ISI@@(?:(?!<\/w:p>).)*<\/w:p>/s;
  if (!pola.test(xml)) throw new Error('Penanda daftar isi tidak ditemukan');
  // docx memberi semua bookmark w:id yang sama; beri nomor unik berurutan.
  // Bookmark di sini tidak bertumpuk, jadi tiap End milik Start sebelumnya.
  let id = 0;
  const hasil = xml.replace(pola, xmlDaftarIsi())
    .replace(/<w:bookmark(Start|End)([^>]*?) w:id="\d+"/g, (_, jenis, atribut) => {
      if (jenis === 'Start') id += 1;
      return `<w:bookmark${jenis}${atribut} w:id="${id}"`;
    });
  zip.file(berkas, hasil);
  fs.writeFileSync(KELUARAN, await zip.generateAsync({ type: 'nodebuffer', compression: 'DEFLATE' }));
  console.log(`Tersimpan: ${path.relative(process.cwd(), KELUARAN)}`);
}

main().catch((err) => { console.error(err); process.exit(1); });

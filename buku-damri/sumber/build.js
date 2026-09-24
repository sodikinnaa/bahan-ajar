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
  Table, TableRow, TableCell, WidthType, ShadingType, LevelFormat,
} = require('docx');

const AKAR = path.resolve(__dirname, '..');
const ASET = path.join(AKAR, 'aset');
const KELUARAN = path.join(AKAR, 'Pengetahuan-Seputar-DAMRI.docx');
const HALAMAN = path.join(__dirname, 'halaman.json');

const WARNA = {
  navy: '0B3A78', kuning: 'F2A516', kuningTua: 'B45309', teks: '1F2937',
  abu: '6B7280', garis: 'C9D3E3', biruMuda: 'BFD7F5', latarKotak: 'EAF2FB', latarBaris: 'F4F7FB',
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

// Naskah buku ada di isi.js; berkas ini hanya mengatur tata letak.
const { KATA_PENGANTAR, PENANDA_TANGAN, BAB, PENUTUP, PUSTAKA } = require('./isi');

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

// Teks naskah boleh memuat **tebal** dan _miring_.
function runs(teks, opsi = {}) {
  return teks.split(/(\*\*[^*]+\*\*|_[^_]+_)/).filter(Boolean).map((bagian) => {
    if (bagian.startsWith('**')) return new TextRun({ text: bagian.slice(2, -2), bold: true, ...opsi });
    if (bagian.startsWith('_')) return new TextRun({ text: bagian.slice(1, -1), italics: true, ...opsi });
    return new TextRun({ text: bagian, ...opsi });
  });
}

const paragraf = (teks) => new Paragraph({ children: runs(teks) });

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

let nomorDaftar = 0; // tiap daftar bernomor mulai lagi dari 1

const garisTipis = { style: BorderStyle.SINGLE, size: 4, color: WARNA.garis };
const tanpaGaris = { style: BorderStyle.NONE, size: 0, color: 'FFFFFF' };

function lebarKolom(perbandingan) {
  const total = perbandingan.reduce((a, b) => a + b, 0);
  const lebar = perbandingan.map((p) => Math.floor(LEBAR_ISI * p / total));
  lebar[lebar.length - 1] += LEBAR_ISI - lebar.reduce((a, b) => a + b, 0);
  return lebar;
}

// Tabel pendek dijaga agar tidak terpotong di pergantian halaman; tabel
// panjang boleh berlanjut dengan baris judul yang diulang.
const TABEL_PENDEK = 600; // jumlah karakter isi

function tabel({ kolom, baris, lebar }) {
  const ukuran = lebarKolom(lebar || kolom.map(() => 1));
  const utuh = baris.flat().join('').length <= TABEL_PENDEK;
  const sel = (teks, i, opsi) => new TableCell({
    width: { size: ukuran[i], type: WidthType.DXA },
    margins: { top: 50, bottom: 50, left: 90, right: 90 },
    shading: opsi.latar ? { type: ShadingType.CLEAR, color: 'auto', fill: opsi.latar } : undefined,
    children: [new Paragraph({
      alignment: AlignmentType.LEFT,
      spacing: { after: 0, ...spasi(264) },
      keepNext: opsi.lanjut,
      children: runs(teks, { size: 18, ...opsi.teks }),
    })],
  });
  return new Table({
    width: { size: LEBAR_ISI, type: WidthType.DXA },
    columnWidths: ukuran,
    borders: {
      top: garisTipis, bottom: garisTipis, left: tanpaGaris, right: tanpaGaris,
      insideHorizontal: garisTipis, insideVertical: tanpaGaris,
    },
    rows: [
      new TableRow({
        tableHeader: true, cantSplit: true,
        children: kolom.map((k, i) => sel(k, i, {
          latar: WARNA.navy, teks: { bold: true, color: 'FFFFFF' }, lanjut: true,
        })),
      }),
      ...baris.map((isi, r) => new TableRow({
        cantSplit: true,
        children: isi.map((k, i) => sel(k, i, {
          latar: r % 2 ? WARNA.latarBaris : undefined, lanjut: utuh && r < baris.length - 1,
        })),
      })),
    ],
  });
}

// Kotak informasi berlatar biru muda, misalnya "Tahukah Anda?".
function kotak({ judul: kepala, isi }) {
  return new Table({
    width: { size: LEBAR_ISI, type: WidthType.DXA },
    columnWidths: [LEBAR_ISI],
    borders: {
      top: tanpaGaris, bottom: tanpaGaris, right: tanpaGaris,
      left: { style: BorderStyle.SINGLE, size: 24, color: WARNA.navy },
      insideHorizontal: tanpaGaris, insideVertical: tanpaGaris,
    },
    rows: [new TableRow({
      cantSplit: true,
      children: [new TableCell({
        width: { size: LEBAR_ISI, type: WidthType.DXA },
        margins: { top: 100, bottom: 100, left: 160, right: 140 },
        shading: { type: ShadingType.CLEAR, color: 'auto', fill: WARNA.latarKotak },
        children: [
          new Paragraph({
            alignment: AlignmentType.LEFT, spacing: { after: 40, ...spasi(264) },
            children: [new TextRun({ text: kepala, bold: true, size: 19, color: WARNA.navy })],
          }),
          ...[].concat(isi).map((teks) => new Paragraph({
            alignment: AlignmentType.LEFT, spacing: { after: 40, ...spasi(276) },
            children: runs(teks, { size: 19 }),
          })),
        ],
      })],
    })],
  });
}

// Jarak kosong setelah tabel atau kotak agar tidak menempel ke paragraf berikutnya.
const jeda = () => new Paragraph({ spacing: { after: 0, ...spasi(200) }, children: [] });

// Butir terakhir sebuah daftar diberi jarak paragraf biasa.
const jarakButir = (i, daftar) => ({ after: i === daftar.length - 1 ? 140 : 60 });

// akhirBab: blok terakhir sebelum bab baru. Jeda di posisi itu bisa terdorong
// sendirian ke halaman berikutnya dan menghasilkan halaman kosong.
function blok(b, { akhirBab = false } = {}) {
  const penutupBlok = akhirBab ? [] : [jeda()];
  if (typeof b === 'string') {
    // Kalimat pengantar yang berakhir titik dua tetap satu halaman dengan isinya.
    return [new Paragraph({ keepNext: b.endsWith(':'), children: runs(b) })];
  }
  if (b.daftar) {
    return b.daftar.map((teks, i, daftar) => new Paragraph({
      numbering: { reference: 'titik', level: 0 },
      spacing: jarakButir(i, daftar),
      children: runs(teks),
    }));
  }
  if (b.langkah) {
    nomorDaftar += 1;
    return b.langkah.map((teks, i, daftar) => new Paragraph({
      numbering: { reference: 'angka', level: 0, instance: nomorDaftar },
      spacing: jarakButir(i, daftar),
      children: runs(teks),
    }));
  }
  if (b.tabel) return [tabel(b.tabel), ...penutupBlok];
  if (b.kotak) return [kotak(b.kotak), ...penutupBlok];
  if (b.lengkapi) return [new Paragraph({ style: 'Panduan', children: runs(b.lengkapi) })];
  throw new Error(`Blok tidak dikenal: ${JSON.stringify(b)}`);
}

function isiBab() {
  const hasil = [];
  BAB.forEach((bab, i) => {
    hasil.push(judul(1, bab.judul.toUpperCase(), { kicker: `BAB ${bab.no}`, pisahHalaman: i > 0 }));
    if (bab.pembuka) {
      hasil.push(new Paragraph({ style: 'Pembuka', children: runs(bab.pembuka) }));
    }
    bab.sub.forEach((sub, j) => {
      hasil.push(judul(2, `${i + 1}.${j + 1} ${sub.judul}`));
      const subTerakhir = j === bab.sub.length - 1;
      sub.isi.forEach((b, k) => {
        hasil.push(...blok(b, { akhirBab: subTerakhir && k === sub.isi.length - 1 }));
      });
    });
  });
  return hasil;
}

function penutup() {
  return [judul(1, 'PENUTUP'), ...PENUTUP.map(paragraf)];
}

function daftarPustaka() {
  return [
    judul(1, 'DAFTAR PUSTAKA'),
    ...PUSTAKA.map((teks) => new Paragraph({
      alignment: AlignmentType.LEFT,
      indent: { left: mm(8), hanging: mm(8) },
      spacing: { after: 100, ...spasi(276) },
      children: runs(teks, { size: 19 }),
    })),
  ];
}

// ---------- Dokumen ----------

const halamanAwal = kataPengantar().concat(daftarIsi());
const isi = isiBab().concat(penutup(), daftarPustaka());

const doc = new Document({
  title: 'Pengetahuan Seputar DAMRI',
  creator: 'Perum DAMRI Cabang Bandar Lampung',
  numbering: {
    config: [
      {
        reference: 'titik',
        levels: [{
          level: 0, format: LevelFormat.BULLET, text: '\u2022', alignment: AlignmentType.LEFT,
          style: { paragraph: { indent: { left: mm(6), hanging: mm(4) } } },
        }],
      },
      {
        reference: 'angka',
        levels: [{
          level: 0, format: LevelFormat.DECIMAL, text: '%1.', alignment: AlignmentType.LEFT,
          style: { paragraph: { indent: { left: mm(7), hanging: mm(6) } } },
        }],
      },
    ],
  },
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
        id: 'Pembuka', name: 'Pembuka Bab', basedOn: 'Normal', next: 'Normal', quickFormat: true,
        run: { size: 21, italics: true, color: WARNA.abu },
        paragraph: { alignment: AlignmentType.CENTER, spacing: { after: 240, ...spasi(300) } },
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

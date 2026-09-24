// Menggambar bagan struktur organisasi dari data BAGAN di isi.js menjadi PNG
// di ../aset/bagan-<nama>.png. Dirender dengan Chromium (Playwright) agar
// teks tajam saat dicetak.
const path = require('path');
const { chromium } = require('playwright');
const { BAGAN } = require('./isi');

const ASET = path.resolve(__dirname, '..', 'aset');
const LEBAR = 540;  // px CSS; gambar dipasang selebar area isi (108 mm)
const SKALA = 4;    // piksel gambar per px CSS, sekitar 470 dpi di kertas

const esc = (s) => s.replace(/&/g, '&amp;').replace(/</g, '&lt;');

const kotak = (teks, kelas = '') => `<div class="kotak ${kelas}">${esc(teks)}</div>`;

// Unit di bawah pimpinan kolom, disusun menurun dengan rel di kiri.
function daftarTurun(anak) {
  if (!anak || !anak.length) return '';
  return `<div class="turun">${anak.map((a) => `<div class="butir">${kotak(a, 'kecil')}</div>`).join('')}</div>`;
}

function html({ puncak, pengawas, staf = [], kolom, catatan }) {
  const kiri = staf.filter((_, i) => i % 2 === 0);
  const kanan = staf.filter((_, i) => i % 2 === 1);
  const sisi = (daftar, arah) => daftar.map((s) => `<div class="lengan ${arah}">${kotak(s, 'staf')}</div>`).join('');
  return `<!doctype html><html><head><meta charset="utf-8"><style>
  * { box-sizing: border-box; margin: 0; }
  body { font-family: Arial, 'Liberation Sans', sans-serif; background: #fff; }
  .bagan { width: ${LEBAR}px; padding: 8px 4px 10px; color: #1F2937; }
  .tengah { display: flex; flex-direction: column; align-items: center; }
  .kotak { border-radius: 5px; padding: 6px 8px; text-align: center; font-size: 12px; line-height: 1.25; }
  .puncak { background: #0B3A78; color: #fff; font-weight: bold; font-size: 13.5px; min-width: 190px; padding: 8px 14px; }
  .pengawas { background: #fff; border: 1.5px dashed #0B3A78; color: #0B3A78; font-weight: bold; min-width: 150px; }
  .putus { width: 0; height: 16px; border-left: 1.5px dashed #0B3A78; }
  .staf { background: #EAF2FB; border: 1px solid #9DB8DC; color: #0B3A78; }
  .pimpinan { background: #1D5BA8; color: #fff; font-weight: bold; }
  .kecil { background: #fff; border: 1px solid #C9D3E3; font-size: 11px; padding: 4px 6px; text-align: left; }
  /* Batang dari puncak turun ke baris kolom, dengan unit staf di kiri-kanan. */
  .batang { display: grid; grid-template-columns: 1fr 1.5px 1fr; width: 100%; }
  .batang .garis { background: #0B3A78; }
  .batang .sisi { display: flex; flex-direction: column; gap: 6px; padding: 8px 0; }
  .batang .sisi.kiri { align-items: flex-end; }
  .lengan { display: flex; align-items: center; }
  .lengan.kiri::after, .lengan.kanan::before { content: ''; width: 14px; border-top: 1.5px solid #0B3A78; }
  .lengan .kotak { max-width: 170px; }
  .jeda { height: 12px; }
  /* Baris kolom dengan garis penghubung di atasnya. */
  .baris { display: flex; width: 100%; }
  .kolom { flex: 1; position: relative; padding: 12px 3px 0; display: flex; flex-direction: column; align-items: stretch; }
  .kolom::before { content: ''; position: absolute; top: 0; left: 0; right: 0; border-top: 1.5px solid #0B3A78; }
  .kolom:first-child::before { left: 50%; }
  .kolom:last-child::before { right: 50%; }
  .baris.tunggal .kolom::before { display: none; }
  .kolom::after { content: ''; position: absolute; top: 0; left: 50%; height: 12px; border-left: 1.5px solid #0B3A78; }
  .kolom > .kotak { min-height: 46px; display: flex; align-items: center; justify-content: center; }
  .turun { margin-left: 12px; }
  .butir { position: relative; padding: 5px 0 0 9px; }
  .butir::before { content: ''; position: absolute; left: 0; top: 0; bottom: 0; border-left: 1.5px solid #9DB8DC; }
  .butir:last-child::before { bottom: auto; height: calc(50% + 2.5px); }
  .butir::after { content: ''; position: absolute; left: 0; top: calc(50% + 2.5px); width: 9px; border-top: 1.5px solid #9DB8DC; }
  .catatan { margin-top: 10px; font-size: 10px; color: #6B7280; text-align: center; }
  </style></head><body><div class="bagan">
    <div class="tengah">
      ${pengawas ? kotak(pengawas, 'pengawas') + '<div class="putus"></div>' : ''}
      ${kotak(puncak, 'puncak')}
    </div>
    ${staf.length ? `<div class="batang"><div class="sisi kiri">${sisi(kiri, 'kiri')}</div><div class="garis"></div><div class="sisi">${sisi(kanan, 'kanan')}</div></div>`
      : '<div class="batang"><div></div><div class="garis jeda"></div><div></div></div>'}
    <div class="baris${kolom.length === 1 ? ' tunggal' : ''}">
      ${kolom.map((k) => `<div class="kolom">${kotak(k.nama, 'pimpinan')}${daftarTurun(k.anak)}</div>`).join('')}
    </div>
    ${catatan ? `<p class="catatan">${esc(catatan)}</p>` : ''}
  </div></body></html>`;
}

async function main() {
  const peramban = await chromium.launch();
  const halaman = await peramban.newPage({ deviceScaleFactor: SKALA, viewport: { width: LEBAR + 20, height: 400 } });
  for (const [nama, data] of Object.entries(BAGAN)) {
    await halaman.setContent(html(data));
    // Samakan tinggi kotak pimpinan dalam satu baris.
    await halaman.evaluate(() => {
      const kotak = [...document.querySelectorAll('.kolom > .kotak')];
      const tinggi = Math.max(...kotak.map((k) => k.offsetHeight));
      kotak.forEach((k) => { k.style.height = `${tinggi}px`; });
    });
    const tujuan = path.join(ASET, `bagan-${nama}.png`);
    await halaman.locator('.bagan').screenshot({ path: tujuan });
    console.log(`Tersimpan: ${path.relative(process.cwd(), tujuan)}`);
  }
  await peramban.close();
}

main().catch((err) => { console.error(err); process.exit(1); });

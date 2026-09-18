# bahan-ajar

Bahan ajar Noodu Academy dalam format slide HTML. Satu file HTML = satu deck,
memakai template bersama di `assets/`.

```
assets/
  noodu-slides.css    template tampilan
  noodu-slides.js     header, footer, nomor halaman otomatis
  noodu-logo.png      logo
  noo-*.png           10 pose maskot Noo
build-publish-with-ai/
  m01-coding-environment.html
```

## Membuka dan mencetak

Buka file HTML langsung di browser. Untuk menghasilkan PDF, pakai
**Print → Save as PDF** dengan ukuran kertas **13.333 × 7.5 inci**
(setara 960 × 540 pt, 16:9) dan margin nol. Background graphics harus aktif.

## Menulis deck baru

Salin kerangka di bawah, lalu isi slide. Header, footer, progress bar, dan
nomor halaman disusun otomatis oleh `noodu-slides.js`.

```html
<body data-meta="Modul 2 / Vibe Coding"
      data-brand="Noodu Academy / Build &amp; Publish with AI"
      data-assets="../assets">

  <section class="slide" data-label="Peta belajar">
    <h1>Judul berupa kalimat, bukan topik.</h1>
    <div class="cols"> ... </div>
  </section>

  <script src="../assets/noodu-slides.js"></script>
</body>
```

### Komponen

| Kelas | Dipakai untuk |
| --- | --- |
| `slide` `slide--cover` `slide--dark` | slide biasa, sampul, penutup |
| `cols` `cols--wide-left` `cols--wide-right` | tata letak dua kolom |
| `code` + `code__label` | blok kode gelap berlabel |
| `tbl` | tabel perbandingan |
| `steps` `bullets` `checklist` | langkah bernomor, poin, kotak centang |
| `callout` `--green` `--orange` `--dark` | alasan konsep, kondisi gagal, catatan |
| `noo-says` | kotak “NOO bilang” untuk peringatan teknis |
| `noo-buddy` `--green` `--orange` `--left` | sapaan “Teman belajarmu · Noo” |

Pose maskot: `wave`, `blocks`, `tablet`, `laptop`, `book`, `thinking`,
`pointing`, `cheer`, `thumbsup`, `scroll`.

### Gaya penulisan

- Judul slide berupa kalimat pernyataan pendek, sering paralel —
  *“Customer melihat. Admin mengelola.”*
- Satu slide membahas satu konsep.
- Kode di satu sisi, alasannya di sisi lain.
- `noo-says` untuk jebakan yang sering terjadi, bukan pengulangan isi slide.
- Alur tiap pertemuan: sampul → peta belajar → titik berangkat → konsep inti →
  praktik terbimbing → praktik mandiri → challenge → debugging → checkpoint →
  pertemuan berikutnya.

## Course: Build & Publish with AI

Dari project di lokal menjadi project online.

| # | Modul | Fokus | Deck |
| --- | --- | --- | --- |
| 1 | Coding Environment | VPS, SSH, VS Code Server | [m01](build-publish-with-ai/m01-coding-environment.html) |
| 2 | Vibe Coding | Antigravity CLI, AI dalam alur kerja | belum |
| 3 | Run Your Project | Source code, dependency, menjalankan di VPS | belum |
| 4 | Understanding Port | Port, port forwarding, akses internet | belum |
| 5 | Cloudflare | Domain, DNS, Cloudflare | belum |
| 6 | Connect Domain with AI | API, Cloudflare API, AI sebagai interface | belum |
| 7 | Publish | Publikasi, verifikasi server sampai domain | belum |
| 8 | Practical Exam | Workflow build & publish secara mandiri | belum |
| 9 | Final Project | Build → Run → Publish project sendiri | belum |

# Noodu Brand Kit

Semua aset visual Noodu Academy: logo, ikon, maskot NOO, palet warna,
font, dan template slide.

Aset diekstrak langsung dari deck resmi `Noodu-M14-Pertemuan-1`. Di dalam
PDF itu hanya ada dua gambar: satu logo dan satu sprite sheet berisi
sepuluh pose maskot. Keduanya diambil pada resolusi penuh beserta lapisan
transparansinya, lalu dipotong per pose dan dirapikan sampai batas piksel
yang benar-benar terlihat.

## Isi

```
logo/
  noodu-logo.png            1838 x 721, transparan, resolusi penuh
  noodu-logo-960.png        untuk cetak
  noodu-logo-480.png        untuk web dan slide
  noodu-logo-240.png        untuk ukuran kecil
  ikon/
    noo-icon.png            ikon sumber, 276 x 276
    noo-icon-512..16.png    ukuran siap pakai
    favicon.ico             multi-ukuran, 16 sampai 256
    pratinjau-ikon.png

maskot/
  pose-maskot.png           lembar semua pose beserta namanya
  sumber/
    noo-<pose>.png          resolusi penuh, sekitar 320 x 380
    noo-sprite-sheet.png    lembar asli 1983 x 793, 5 x 2
  web/
    noo-<pose>.png          tinggi 300 px, sekitar 50 KB, untuk web

warna/
  palette.png               lembar swatch
  palette.css               variabel CSS
  palette.json              untuk dibaca program

font/
  fonts.css                 tiga font tertanam base64, siap pakai

template/
  noodu-slides.css          template slide lengkap
  contoh-komponen.html      contoh semua komponen
```

## Sepuluh pose maskot

`wave` `blocks` `tablet` `laptop` `book`
`thinking` `pointing` `cheer` `thumbsup` `scroll`

Pemakaian di deck: `pointing` untuk kotak **NOO bilang**, pose lain untuk
kotak **Teman belajarmu · NOO**, dan `wave` untuk kartu sampul.

Ambil dari `maskot/sumber/` kalau untuk cetak atau perlu dipotong ulang,
dan dari `maskot/web/` kalau untuk halaman web atau slide.

## Warna

Warna template diambil dari deck resmi. Warna maskot disampel dari
pikselnya sendiri: tiap pose dikuantisasi lalu warna dominannya dihitung,
jadi angkanya bukan hasil kira-kira dari layar.

| Peran | Hex |
| --- | --- |
| Teks utama | `#0d203f` |
| Aksen utama | `#3366ff` |
| Aksen sampul | `#f2722b` |
| Nomor modul | `#49c08b` |
| Latar slide | `#fafaf6` |
| Badan maskot | `#1d64d8` |
| Tas maskot | `#fa882b` |

Selengkapnya ada di `warna/palette.css`.

## Font

`font/fonts.css` memuat tiga font sebagai data URI, jadi tidak perlu
koneksi internet dan tidak perlu memasang apa pun:

| Peran | Font | Bobot |
| --- | --- | --- |
| Judul slide | Poppins | 600, 700 |
| Badan teks | Inter | 400, 500, 600, 700 |
| Blok kode | DejaVu Sans Mono | 400, 700 |

Deck resmi menanam URW Gothic Demi untuk judul dan Nimbus Sans untuk
badan teks. Keduanya tidak bisa disertakan ulang di sini, jadi dipilih
pengganti terdekat: beberapa kandidat dirender berdampingan dengan deck
asli, lalu dibandingkan bentuk hurufnya dan diukur lebarnya. Poppins 600
meleset kurang dari satu persen dari lebar aslinya.

Kalau nanti file font Noodu yang asli tersedia, tukar saja isi
`fonts.css` — nama variabelnya sudah disiapkan di `noodu-slides.css`.

## Cara pakai template

```html
<link rel="stylesheet" href="font/fonts.css">
<link rel="stylesheet" href="template/noodu-slides.css">

<section class="slide">
  <header class="topbar">
    <img class="topbar__logo" src="logo/noodu-logo-480.png" alt="noodu">
    <span class="topbar__rule"></span>
    <span class="topbar__meta">Minggu 14 / Pertemuan 1</span>
    <span class="topbar__label">PETA BELAJAR</span>
  </header>

  <h1>Judul berupa kalimat, bukan topik.</h1>

  <div class="cols"> ... </div>

  <footer class="footer">
    <span>Noodu Academy</span>
    <span class="footer__track"><span class="footer__bar" style="width:20%"></span></span>
    <span class="footer__num">02 / 25</span>
  </footer>
</section>
```

Buka `template/contoh-komponen.html` untuk melihat semua komponen yang
tersedia: sampul, daftar, tabel, blok kode, callout, NOO bilang, sapaan
NOO, dan slide gelap.

## Ukuran slide

Kanvas 1280 x 720 px. Untuk PDF: **Print → Save as PDF**, kertas
**13.333 x 7.5 inci**, margin nol, background graphics aktif. Hasilnya
960 x 540 pt, sama dengan deck resmi.

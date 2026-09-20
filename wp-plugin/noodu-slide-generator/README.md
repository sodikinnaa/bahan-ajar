# Noodu Slide Generator

WordPress plugin yang membuat deck bahan ajar bergaya Noodu Academy lewat
OpenAI atau endpoint apa pun yang kompatibel dengannya.

## Pasang

1. **Plugins → Add New → Upload Plugin**, pilih `noodu-slide-generator.zip`
2. **Install Now**, lalu **Activate**
3. **Noodu → Settings**, isi Base URL dan API key, lalu simpan

Aktivasi otomatis membuat dua tabel dan folder
`wp-content/uploads/noodu-slides/`. Tidak ada langkah manual lain.

> Zip harus punya folder `noodu-slide-generator/` di akarnya. Kalau ada
> folder pembungkus tambahan, WordPress menolak dengan pesan
> *"No valid plugins were found."*

## Pakai

**Lewat admin** — Noodu → Dashboard. Isi nama project, tekan *Fetch
available models*, pilih model, tulis prompt, lalu Generate. Hasilnya
muncul di Noodu → Projects.

**Lewat halaman** — taruh shortcode di page atau post mana pun:

```
[noodu_generator]
```

Yang bisa memakainya: user dengan kapabilitas `edit_posts` (Author ke
atas). Admin melihat semua project, user lain hanya miliknya sendiri.

## Tampilan deck

Deck memakai template Noodu Academy yang sama dengan deck buatan tangan:
logo di topbar, judul geometris, blok kode gelap berlabel, callout, kotak
"NOO bilang" dan "Teman belajarmu · NOO" dengan maskot, lalu footer dengan
progress bar dan nomor halaman.

Semua aset ikut di dalam plugin (`assets/brand/`) dan ditanam sebagai data
URI ke dalam deck, jadi file hasilnya berdiri sendiri: dipindah, dikirim
lewat email, atau dibuka tanpa internet tetap tampil sama.

| Aset | Isi |
| --- | --- |
| `noodu-logo.png` | logo topbar |
| `noo-*.png` | 10 pose maskot: wave, blocks, tablet, laptop, book, thinking, pointing, cheer, thumbsup, scroll |
| `noodu-slides.css` | template slide, ukuran huruf diambil dari deck rujukan |
| `fonts.css` | Poppins (judul), Inter (teks), DejaVu Sans Mono (kode), tertanam |

Kanvas 1280 × 720 px, dicetak 960 × 540 pt (16:9).

## Bentuk JSON yang diminta dari model

```json
{
  "meta": {
    "module": "Minggu 14 / Pertemuan 1",
    "brand": "Noodu Academy / Catalogue, Search & Filter",
    "badges": ["MINGGU 14", "PERTEMUAN 1"],
    "number": "14.1",
    "project": "Noodu Store",
    "tagline": "Bangun etalase, mulai ceritamu.",
    "note": "Private Thematics · Bulan 4 · 90 menit"
  },
  "slides": [
    {
      "type": "cover",
      "label": "MULAI DI SINI",
      "title": "Cari produknya. Temukan yang pas.",
      "lead": "Satu paragraf pembuka.",
      "mascot": "wave"
    },
    {
      "type": "normal",
      "label": "PETA BELAJAR",
      "title": "Misi: toko mudah dijelajahi.",
      "layout": "two-col",
      "left": "<div class=\"callout callout--green\">…</div>",
      "right": "<table class=\"tbl\">…</table>",
      "noo_says": "Jebakan yang sering terjadi.",
      "buddy": { "text": "Ini misi kita.", "pose": "tablet", "tone": "green" }
    }
  ]
}
```

Judul cover dipecah otomatis per kalimat, satu kalimat satu baris.
Topbar, footer, nomor halaman, dan progress bar disusun oleh plugin —
model tidak perlu menuliskannya.

## Output

Plugin selalu menulis deck HTML. Kalau server punya Chromium dan `exec()`
aktif, deck itu langsung dirender jadi PDF 960 × 540 pt (16:9).

Kalau tidak — dan ini normal di shared hosting — yang diunduh adalah HTML.
Buka di browser, **Print → Save as PDF**, ukuran kertas **13.333 × 7.5
inci**, margin nol, background graphics aktif. Hasilnya identik.

Noodu → Settings menunjukkan mana yang tersedia di server Anda.

## Referensi PDF

Upload PDF di form generate, teksnya diekstrak dengan `pdftotext` dan
ikut dikirim ke model sebagai bahan. Butuh `poppler-utils`:

```bash
sudo apt-get install poppler-utils
```

Tanpa itu, salin saja isinya ke dalam prompt.

## REST API

Semua endpoint di bawah `/wp-json/noodu/v1/` dan butuh header
`X-WP-Nonce` serta cookie login.

| Method | Endpoint | Guna |
| --- | --- | --- |
| GET | `/models` | daftar model dari endpoint yang dikonfigurasi |
| POST | `/generate` | buat deck baru (`project_name`, `model`, `prompt`, `reference_file`) |
| GET | `/projects` | daftar project |
| GET | `/projects/{id}` | detail project beserta revisinya |
| DELETE | `/projects/{id}` | hapus project dan filenya |
| POST | `/projects/{id}/revise` | revisi deck (`revision_prompt`) |

## Struktur

```
noodu-slide-generator/
  noodu-slide-generator.php   header plugin + bootstrap
  uninstall.php               bersih-bersih saat plugin dihapus
  includes/
    class-noodu-plugin.php    menu, settings, REST
    class-database.php        akses tabel
    class-openai-client.php   panggilan API + parsing JSON
    class-file-uploader.php   upload dan ekstraksi PDF
    class-pdf-generator.php   HTML deck → PDF
  admin/                      dashboard, projects, settings
  public/shortcode.php        form frontend
  assets/                     CSS dan JS
```

## Tabel

`wp_noodu_projects` menyimpan nama, kode, prompt, model, status, nama
file, jumlah slide, dan JSON slide. `wp_noodu_revisions` menyimpan tiap
permintaan revisi. Keduanya dihapus saat plugin di-delete lewat
`uninstall.php` — deaktivasi tidak menghapus apa pun.

## Kalau bermasalah

**"No valid plugins were found"** — struktur zip salah, lihat catatan di
bagian Pasang.

**Fetch models gagal** — cek Base URL memuat `/v1` dan API key valid.
Pesan error dari server ditampilkan apa adanya di layar.

**Model tidak mengembalikan JSON valid** — coba model lain, atau buat
prompt lebih spesifik soal jumlah dan isi slide.

**Yang terunduh HTML, bukan PDF** — server tidak punya Chromium. Print ke
PDF dari browser seperti di bagian Output.

## Lisensi

GPL v2 atau setelahnya.

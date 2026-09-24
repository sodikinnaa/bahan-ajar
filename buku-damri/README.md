# Buku Pengetahuan Seputar DAMRI

Buku informasi tentang Perum DAMRI untuk karyawan dan masyarakat umum,
disusun oleh Perum DAMRI Cabang Bandar Lampung. Ukuran halaman A5.

| Berkas | Isi |
| --- | --- |
| `Pengetahuan-Seputar-DAMRI.docx` | Naskah Word yang bisa diedit |
| `Pengetahuan-Seputar-DAMRI.pdf` | Versi siap baca atau cetak |
| `aset/` | Logo dan latar sampul |
| `sumber/` | Skrip penyusun `.docx` |

Isi saat ini: sampul, kata pengantar, daftar isi, kerangka Bab I–VIII, dan
penutup. Setiap subbab masih berisi panduan singkat bergaris kuning yang
diganti dengan isi sebenarnya.

## Halaman awal dan nomor halaman

- Sampul tanpa header, footer, dan nomor.
- Kata pengantar dan daftar isi memakai angka Romawi kecil (i, ii, iii, …).
- Bab I sampai penutup memakai angka biasa, mulai dari 1.
- Header setiap halaman memuat logo DAMRI. Footer memuat nama cabang dan
  nomor halaman.

## Mengedit di Word

Setelah isi bab ditambahkan, perbarui daftar isi: klik kanan daftar isi, pilih
**Update Field → Update entire table**. Judul bab memakai style *Heading 1*
dan subbab memakai *Heading 2*, jadi keduanya otomatis masuk daftar isi.

## Logo

`aset/logo-damri.png` saat ini **logo sementara**. Ganti dengan file logo resmi
DAMRI (PNG, latar transparan) bernama sama, lalu susun ulang. Ukurannya
menyesuaikan tinggi logo, jadi rasio lebar-tinggi apa pun tetap pas.

## Menyusun ulang dari skrip

Butuh Node.js, Python 3 dengan Pillow dan PyMuPDF, serta LibreOffice Writer.

```
cd sumber
npm install
python3 aset.py   # latar sampul, dan logo sementara bila logo belum ada
bash build.sh     # .docx → hitung nomor halaman daftar isi → .docx → .pdf
```

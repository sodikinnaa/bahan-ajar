"""Menghitung nomor halaman setiap judul untuk daftar isi.

Dokumen dirender ke PDF dengan LibreOffice, lalu setiap judul dicari di
halaman hasil render dan nomor halamannya dibaca dari footer. Hasilnya
ditulis ke halaman.json dan dipakai build.js. Butuh PyMuPDF.
"""
import html
import json
import re
import subprocess
import sys
import tempfile
import zipfile
from pathlib import Path

import pymupdf

SUMBER = Path(__file__).resolve().parent
DOCX = SUMBER.parent / "Pengetahuan-Seputar-DAMRI.docx"
HALAMAN = SUMBER / "halaman.json"
UKURAN_JUDUL_MIN = 11  # pt; teks isi dan daftar isi lebih kecil dari ini
TINGGI_FOOTER = 18 * 72 / 25.4  # pt dari tepi bawah


def ke_pdf(docx, folder):
    profil = Path(folder, "profil").as_uri()
    subprocess.run(
        ["soffice", f"-env:UserInstallation={profil}", "--headless",
         "--convert-to", "pdf", "--outdir", folder, str(docx)],
        check=True, capture_output=True,
    )
    return Path(folder, docx.with_suffix(".pdf").name)


def normal(teks):
    return re.sub(r"\s+", " ", teks).strip().upper()


def judul_dokumen(docx):
    """Daftar (penanda, teks yang tampil di halaman) sesuai urutan dokumen."""
    xml = zipfile.ZipFile(docx).read("word/document.xml").decode("utf8")
    hasil = []
    for p in re.findall(r"<w:p>.*?</w:p>", xml, re.S):
        if not re.search(r'<w:pStyle w:val="Heading[12]"/>', p):
            continue
        penanda = re.search(r'<w:bookmarkStart [^>]*w:name="(_Toc\d+)"', p)
        if not penanda:
            continue
        # Judul bab ditulis "BAB I" <w:br/> "JUDUL"; yang dicari cukup baris judulnya.
        bagian = p.split("<w:br/>")[-1]
        teks = "".join(html.unescape(t) for t in re.findall(r"<w:t[^>]*>([^<]*)</w:t>", bagian))
        hasil.append((penanda.group(1), normal(teks)))
    return hasil


def isi_halaman(pdf):
    """Untuk setiap halaman: (label nomor di footer, teks berukuran judul)."""
    halaman = []
    for page in pymupdf.open(pdf):
        batas_footer = page.rect.height - TINGGI_FOOTER
        label, besar = None, []
        for blok in page.get_text("dict")["blocks"]:
            for baris in blok.get("lines", []):
                for span in baris["spans"]:
                    teks = span["text"].strip()
                    if not teks:
                        continue
                    if span["bbox"][1] > batas_footer and re.fullmatch(r"[ivxlcdm]+|\d+", teks):
                        label = teks
                    elif span["size"] >= UKURAN_JUDUL_MIN:
                        besar.append(teks)
        halaman.append((label, normal(" ".join(besar))))
    return halaman


def main():
    with tempfile.TemporaryDirectory() as folder:
        halaman = isi_halaman(ke_pdf(DOCX, folder))

    hasil, mulai = {}, 0
    for penanda, teks in judul_dokumen(DOCX):
        for i in range(mulai, len(halaman)):
            label, besar = halaman[i]
            if label and teks in besar:
                hasil[penanda], mulai = label, i
                break
        else:
            sys.exit(f"Judul tidak ditemukan di PDF: {teks}")

    HALAMAN.write_text(json.dumps(hasil, indent=2) + "\n")
    print(f"{len(hasil)} judul, {len(halaman)} halaman -> {HALAMAN.name}")


if __name__ == "__main__":
    main()

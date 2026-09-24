"""Membuat gambar pendukung buku: latar sampul dan logo sementara.

Jalankan: python3 aset.py  (butuh Pillow)
Hasil disimpan ke ../aset/. Logo sementara cukup diganti dengan file logo
resmi bernama sama (logo-damri.png), lalu jalankan ulang build.js.
"""
from pathlib import Path

from PIL import Image, ImageDraw, ImageFont

ASET = Path(__file__).resolve().parent.parent / "aset"
FONT_BOLD_ITALIC = "/usr/share/fonts/truetype/liberation/LiberationSans-BoldItalic.ttf"

NAVY = (11, 58, 120)
NAVY_TUA = (7, 38, 80)
NAVY_MUDA = (16, 72, 146)
KUNING = (242, 165, 22)
PUTIH = (255, 255, 255)

DPI = 300
PX_PER_MM = DPI / 25.4
SKALA = 2  # gambar dua kali lebih besar lalu diperkecil agar tepi halus


def mm(nilai):
    return round(nilai * PX_PER_MM * SKALA)


def latar_sampul():
    lebar, tinggi = mm(148), mm(210)
    img = Image.new("RGB", (lebar, tinggi), PUTIH)
    d = ImageDraw.Draw(img)

    # Blok biru utama.
    d.rectangle([0, mm(44), lebar, mm(160)], fill=NAVY)

    # Garis miring di sisi kanan, kesan bergerak.
    for i, warna in enumerate([NAVY_MUDA, (22, 84, 166), (30, 98, 186)]):
        x0 = mm(96 + i * 17)
        d.polygon(
            [(x0, mm(44)), (x0 + mm(10), mm(44)), (x0 - mm(30), mm(146)), (x0 - mm(40), mm(146))],
            fill=warna,
        )

    # Jalan dengan marka putus-putus.
    d.rectangle([0, mm(146), lebar, mm(160)], fill=NAVY_TUA)
    x = mm(6)
    while x < lebar:
        d.rectangle([x, mm(152.4), x + mm(10), mm(153.6)], fill=PUTIH)
        x += mm(16)

    # Aksen kuning di bawah jalan.
    d.rectangle([0, mm(160), lebar, mm(163)], fill=KUNING)

    img = img.resize((lebar // SKALA, tinggi // SKALA), Image.LANCZOS)
    img.save(ASET / "sampul-latar.png", dpi=(DPI, DPI), optimize=True)


def logo_sementara():
    lebar, tinggi = 900 * SKALA, 300 * SKALA
    img = Image.new("RGBA", (lebar, tinggi), (0, 0, 0, 0))
    d = ImageDraw.Draw(img)
    d.rounded_rectangle([0, 0, lebar - 1, tinggi - 1], radius=48 * SKALA, fill=NAVY + (255,))
    d.rectangle([60 * SKALA, 236 * SKALA, 840 * SKALA, 250 * SKALA], fill=KUNING + (255,))
    font = ImageFont.truetype(FONT_BOLD_ITALIC, 190 * SKALA)
    kotak = d.textbbox((0, 0), "DAMRI", font=font)
    tx = (lebar - (kotak[2] - kotak[0])) / 2 - kotak[0]
    ty = (224 * SKALA - (kotak[3] - kotak[1])) / 2 - kotak[1] + 6 * SKALA
    d.text((tx, ty), "DAMRI", font=font, fill=PUTIH)
    img = img.resize((lebar // SKALA, tinggi // SKALA), Image.LANCZOS)
    img.save(ASET / "logo-damri.png", optimize=True)


if __name__ == "__main__":
    ASET.mkdir(exist_ok=True)
    latar_sampul()
    # Jangan menimpa logo resmi yang sudah dipasang.
    if not (ASET / "logo-damri.png").exists():
        logo_sementara()

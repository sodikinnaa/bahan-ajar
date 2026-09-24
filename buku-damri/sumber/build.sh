#!/usr/bin/env bash
# Menyusun ulang buku: bagan organisasi, .docx, nomor halaman daftar isi, lalu .pdf.
set -euo pipefail
cd "$(dirname "$0")"
node bagan.js
node build.js
python3 halaman.py
node build.js
profil=$(mktemp -d)
soffice "-env:UserInstallation=file://$profil" --headless \
  --convert-to pdf --outdir .. ../Pengetahuan-Seputar-DAMRI.docx >/dev/null
rm -rf "$profil"
echo "Tersimpan: ../Pengetahuan-Seputar-DAMRI.pdf"

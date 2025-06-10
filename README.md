Final Project MBD 2025
Final project untuk MBD adalah pembuatan website mengenai sistem yang berguna di lingkungan ITS
tetapi belum diprovide di myITS. 
Ketentuan pembuatan website:
1. Website yang dibangun boleh menggunakan database selain PostgreSQL
2. Website memuat seluruh query dari masing-masing anggota
3. Website memiliki 3 tipe user yang memiliki hak akses berbeda
4. Desain tabel di CDM terdiri dari 5-6 tabel, di mana terdapat minimal 1 tabel transaksi dan ada relasi
one‐to‐many dan many‐to‐many.
5. Lakukan pengisian data dengan jumlah row minimal dengan isian domain data yang valid:
a. Tabel master : minimal 20 baris data
b. Tabel transaksi :minimal 60baris data
6. Setiap anggota kelompok 2 query searching melibatkan join, 2 query view, 2 query trigger dan 2
query function/procedure


Notes : untuk admin bisa masukinn berikut sebagai contoh
run generate_pass.php terlebih dahulu untuk mendapatkan hasil hashing dari password admin
INSERT INTO Admin (id_admin, username, password, nama_lengkap, email) VALUES ('5025231177', 'haqi', 'run hash dari generate_pass.php', 'Muhammad Baihaqi Dawanis', 'dawanisbaihaqi@gmail.com');

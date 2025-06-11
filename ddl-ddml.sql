
-- Definisi Tabel (CREATE TABLE)

CREATE TABLE Pembeli (
    nrp VARCHAR(10) PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL, -- Diperbarui: VARCHAR(255) untuk hash password
    nomor_telepon VARCHAR(18) NOT NULL,
    alamat VARCHAR(255) NOT NULL
);

CREATE TABLE Penjual (
    id_kantin VARCHAR(4) PRIMARY KEY,
    nama_kantin VARCHAR(30) NOT NULL,
    nama_penanggung_jawab VARCHAR(100) NOT NULL,
    email_kantin VARCHAR(255) UNIQUE NOT NULL,
    password_kantin VARCHAR(255) NOT NULL, -- Diperbarui: VARCHAR(255) untuk hash password
    nomor_telepon VARCHAR(18) NOT NULL
);

CREATE TABLE Diskon (
    id_diskon INT AUTO_INCREMENT PRIMARY KEY, -- Diperbarui untuk MySQL/MariaDB
    nama_diskon VARCHAR(20) NOT NULL,
    persentase_disko INT NOT NULL,
    tanggal_mulai DATE NOT NULL,
    tanggal_akhir DATE NOT NULL,
    Penjual_id_kantin VARCHAR(4),
    FOREIGN KEY (Penjual_id_kantin) REFERENCES Penjual(id_kantin) ON DELETE CASCADE
);

CREATE TABLE Menu (
    id_menu VARCHAR(3) PRIMARY KEY,
    nama_menu VARCHAR(25) NOT NULL,
    deskripsi VARCHAR(255),
    harga INT NOT NULL,
    status_menu VARCHAR(10) NOT NULL,
    Penjual_id_kan VARCHAR(4),
    Diskon_id_disk INT,
    FOREIGN KEY (Penjual_id_kan) REFERENCES Penjual(id_kantin) ON DELETE CASCADE,
    FOREIGN KEY (Diskon_id_disk) REFERENCES Diskon(id_diskon) ON DELETE SET NULL
);

CREATE TABLE Pesanan (
    pesanan_id INT AUTO_INCREMENT PRIMARY KEY, -- Diperbarui untuk MySQL/MariaDB
    pesanan_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    pesanan_total INT NOT NULL,
    pesanan_paym VARCHAR(50) NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'Menunggu Konfirmasi', -- Kolom status baru
    Pembeli_id_mah VARCHAR(10), -- Tipe data Pembeli_id_mah disesuaikan dengan nrp (VARCHAR(10))
    FOREIGN KEY (Pembeli_id_mah) REFERENCES Pembeli(nrp) ON DELETE CASCADE -- Referensi ke nrp sebagai PK baru di Pembeli
);

CREATE TABLE DetailPesanan (
    dp_id INT AUTO_INCREMENT PRIMARY KEY, -- Diperbarui untuk MySQL/MariaDB
    Pesanan_pesanan_id INT,
    Menu_id_menu VARCHAR(3),
    dp_qty INT NOT NULL,
    FOREIGN KEY (Pesanan_pesanan_id) REFERENCES Pesanan(pesanan_id) ON DELETE CASCADE,
    FOREIGN KEY (Menu_id_menu) REFERENCES Menu(id_menu) ON DELETE CASCADE
);

CREATE TABLE Review (
    id_rating INT AUTO_INCREMENT PRIMARY KEY, -- Diperbarui untuk MySQL/MariaDB
    rating INT NOT NULL,
    tanggal_rating TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    Pesanan_pesanan_id INT UNIQUE, -- Diasumsikan satu pesanan hanya bisa di-rating sekali
    Penjual_id_kanti VARCHAR(4),
    FOREIGN KEY (Pesanan_pesanan_id) REFERENCES Pesanan(pesanan_id) ON DELETE CASCADE,
    FOREIGN KEY (Penjual_id_kanti) REFERENCES Penjual(id_kantin) ON DELETE CASCADE
);

CREATE TABLE Admin (
    id_admin VARCHAR(10) PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL, -- Untuk hash password
    nama_lengkap VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL
);

CREATE TABLE Pengumuman (
    id_pengumuman INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(100) NOT NULL,
    konten TEXT NOT NULL,
    tanggal_terbit DATE NOT NULL
);

-- Tabel baru untuk Kegiatan Kampus
CREATE TABLE KegiatanKampus (
    id_kegiatan INT AUTO_INCREMENT PRIMARY KEY,
    nama_kegiatan VARCHAR(100) NOT NULL,
    deskripsi_kegiatan TEXT,
    tanggal_mulai DATE NOT NULL,
    tanggal_akhir DATE NOT NULL
);


-- Pengisian Data (INSERT INTO)


-- Hash password yang digunakan untuk semua akun adalah '$2y$10$isoKZRcUzEnJUe5WcriWFuS06Q3CXHAwWZbG9RGsp8tpN3TOGTjVG' (untuk plain password '123')

-- A. Tabel Master (minimal 20 baris data)

-- 1. Tabel Admin (1 baris)
INSERT INTO Admin (id_admin, username, password, nama_lengkap, email) VALUES
('5025231177', 'haqi', '$2y$10$isoKZRcUzEnJUe5WcriWFuS06Q3CXHAwWZbG9RGsp8tpN3TOGTjVG', 'Baihaqi Dawanis', 'haqi@mycanteen.com');

-- 2. Tabel Pembeli (6 baris)
INSERT INTO Pembeli (nrp, nama, email, password, nomor_telepon, alamat) VALUES
('5025231001', 'Budi Santoso', 'budi@example.com', '$2y$10$isoKZRcUzEnJUe5WcriWFuS06Q3CXHAwWZbG9RGsp8tpN3TOGTjVG', '081234567890', 'Jl. Merdeka No. 10, Jakarta'),
('5025231002', 'Siti Aminah', 'siti@example.com', '$2y$10$isoKZRcUzEnJUe5WcriWFuS06Q3CXHAwWZbG9RGsp8tpN3TOGTjVG', '081345678901', 'Jl. Pahlawan No. 25, Surabaya'),
('5025231003', 'Joko Pranowo', 'joko@example.com', '$2y$10$isoKZRcUzEnJUe5WcriWFuS06Q3CXHAwWZbG9RGsp8tpN3TOGTjVG', '081567890123', 'Jl. Majapahit No. 5, Yogyakarta'),
('5025231004', 'Ayu Lestari', 'ayu@example.com', '$2y$10$isoKZRcUzEnJUe5WcriWFuS06Q3CXHAwWZbG9RGsp8tpN3TOGTjVG', '081789012345', 'Jl. Sudirman No. 8, Bandung'),
('5025231005', 'Kevin Tan', 'kevin@example.com', '$2y$10$isoKZRcUzEnJUe5WcriWFuS06Q3CXHAwWZbG9RGsp8tpN3TOGTjVG', '081890123456', 'Jl. Gajah Mada No. 12, Medan'),
('5025231006', 'Lia Paramita', 'lia@example.com', '$2y$10$isoKZRcUzEnJUe5WcriWFuS06Q3CXHAwWZbG9RGsp8tpN3TOGTjVG', '081901234567', 'Jl. Diponegoro No. 30, Makassar');

-- 3. Tabel Penjual (4 baris)
INSERT INTO Penjual (id_kantin, nama_kantin, nama_penanggung_jawab, email_kantin, password_kantin, nomor_telepon) VALUES
('K001', 'Kantin Jaya', 'Rina Wijaya', 'kantinjaya@email.com', '$2y$10$isoKZRcUzEnJUe5WcriWFuS06Q3CXHAwWZbG9RGsp8tpN3TOGTjVG', '082123456789'),
('K002', 'Warung Nikmat', 'Hendra Kusuma', 'warungnikmat@email.com', '$2y$10$isoKZRcUzEnJUe5WcriWFuS06Q3CXHAwWZbG9RGsp8tpN3TOGTjVG', '082234567890'),
('K003', 'Kedai Sehat', 'Dewi Anggraini', 'kedaisehat@email.com', '$2y$10$isoKZRcUzEnJUe5WcriWFuS06Q3CXHAwWZbG9RGsp8tpN3TOGTjVG', '082345678901'),
('K004', 'Rasa Juara', 'Fajar Pratama', 'rasajuara@email.com', '$2y$10$isoKZRcUzEnJUe5WcriWFuS06Q3CXHAwWZbG9RGsp8tpN3TOGTjVG', '082456789012');

-- 4. Tabel Diskon (4 baris, terhubung ke Penjual)
INSERT INTO Diskon (nama_diskon, persentase_disko, tanggal_mulai, tanggal_akhir, Penjual_id_kantin) VALUES
('Diskon Lebaran', 10, '2025-06-01', '2025-06-30', 'K001'),
('Promo Akhir Semester', 15, '2025-05-20', '2025-06-31', 'K002'),
('Diskon Pelajar', 5, '2025-01-01', '2025-12-31', 'K001'),
('Promo Kebaikan', 20, '2025-06-01', '2025-08-30', 'K003');

-- 5. Tabel Menu (6 baris, terhubung ke Penjual dan opsional Diskon)
INSERT INTO Menu (id_menu, nama_menu, deskripsi, harga, status_menu, Penjual_id_kan, Diskon_id_disk) VALUES
('M01', 'Nasi Goreng Spesial', 'Nasi goreng dengan telur, ayam, dan sosis', 25000, 'Tersedia', 'K001', 1), -- Diskon Lebaran
('M02', 'Mie Ayam Bakso', 'Mie ayam lengkap dengan bakso sapi', 22000, 'Tersedia', 'K002', 2), -- Promo Akhir Bulan
('M03', 'Sate Ayam Madura', '10 tusuk sate ayam dengan bumbu kacang', 30000, 'Tersedia', 'K001', NULL),
('M04', 'Es Jeruk Peras', 'Minuman jeruk segar', 10000, 'Tersedia', 'K003', 4), -- Promo Gebyar
('M05', 'Bakso Komplit', 'Bakso urat, halus, tahu, siomay', 20000, 'Tersedia', 'K004', NULL),
('M06', 'Gado-gado Siram', 'Sayuran segar dengan bumbu kacang siram', 18000, 'Tersedia', 'K002', NULL);

-- 6. Tabel Pengumuman (3 baris)
INSERT INTO Pengumuman (judul, konten, tanggal_terbit) VALUES
('Selamat Datang di MyCanteen!', 'Kami sangat senang Anda bergabung dengan MyCanteen. Nikmati pengalaman memesan makanan terbaik di kampus!', CURDATE()),
('Info Libur Hari Raya', 'Kantor MyCanteen akan libur pada tanggal 17-18 Juni 2025. Pesanan tetap dapat dilakukan.', '2025-06-10'),
('Perubahan Jam Operasional', 'Mulai 1 Juli 2025, kantin akan buka jam 07.00 - 18.00 WIB.', '2025-06-25');

-- 7. Tabel KegiatanKampus (3 baris)
INSERT INTO KegiatanKampus (nama_kegiatan, deskripsi_kegiatan, tanggal_mulai, tanggal_akhir) VALUES
('Schematics ITS', 'Kompetisi internasional strata sarjana dan pascasarjana bidang teknologi.', '2025-07-15', '2025-07-20'),
('Beasiswa Djarum Tahun 2025', 'Pendaftaran beasiswa bagi mahasiswa berprestasi telah dibuka.', '2025-06-01', '2025-06-30'),
('Lomba CTF', 'Adu skillmu dibidang cyber security.', '2025-08-01', '2025-08-15');


-- B. Tabel Transaksi (minimal 60 baris data)

-- 1. Tabel Pesanan (15 baris)
-- Menggunakan ID Pembeli yang sudah ada
INSERT INTO Pesanan (pesanan_date, pesanan_total, pesanan_paym, status, Pembeli_id_mah) VALUES
('2025-06-01 10:00:00', 25000, 'Cash', 'Selesai', '5025231001'),  -- Pesanan ID 1
('2025-06-01 10:15:00', 22000, 'QRIS', 'Selesai', '5025231002'),  -- Pesanan ID 2
('2025-06-01 10:30:00', 30000, 'Cash', 'Diproses', '5025231003'), -- Pesanan ID 3
('2025-06-01 11:00:00', 10000, 'Transfer Bank', 'Selesai', '5025231004'), -- Pesanan ID 4
('2025-06-02 12:00:00', 40000, 'QRIS', 'Menunggu Konfirmasi', '5025231001'), -- Pesanan ID 5 (2 item)
('2025-06-02 12:30:00', 18000, 'Cash', 'Selesai', '5025231005'),  -- Pesanan ID 6
('2025-06-03 13:00:00', 25000, 'Cash', 'Selesai', '5025231002'),  -- Pesanan ID 7
('2025-06-03 13:30:00', 22000, 'QRIS', 'Diproses', '5025231006'),  -- Pesanan ID 8
('2025-06-04 09:00:00', 50000, 'Transfer Bank', 'Selesai', '5025231001'), -- Pesanan ID 9 (2 item dari K001)
('2025-06-04 09:15:00', 20000, 'Cash', 'Ditolak', '5025231003'),  -- Pesanan ID 10
('2025-06-05 14:00:00', 32000, 'QRIS', 'Selesai', '5025231004'),  -- Pesanan ID 11 (M01+M04)
('2025-06-05 14:30:00', 22000, 'Cash', 'Diproses', '5025231005'),  -- Pesanan ID 12
('2025-06-06 11:00:00', 18000, 'Transfer Bank', 'Selesai', '5025231006'), -- Pesanan ID 13
('2025-06-06 11:30:00', 25000, 'QRIS', 'Menunggu Konfirmasi', '5025231001'), -- Pesanan ID 14
('2025-06-07 10:00:00', 30000, 'Cash', 'Selesai', '5025231001');  -- Pesanan ID 15

-- 2. Tabel DetailPesanan (minimal 45 baris)
-- Pastikan Pesanan_pesanan_id dan Menu_id_menu sesuai dengan data di atas
INSERT INTO DetailPesanan (Pesanan_pesanan_id, Menu_id_menu, dp_qty) VALUES
(1, 'M01', 1), -- P001: Nasi Goreng
(2, 'M02', 1), -- P002: Mie Ayam Bakso
(3, 'M03', 1), -- P003: Sate Ayam Madura
(4, 'M04', 1), -- P004: Es Jeruk Peras
(5, 'M01', 1), (5, 'M03', 1), -- P001: Nasi Goreng, Sate Ayam
(6, 'M05', 1), -- P005: Bakso Komplit
(7, 'M01', 1), -- P002: Nasi Goreng Spesial
(8, 'M06', 1), -- P006: Gado-gado Siram
(9, 'M01', 1), (9, 'M03', 1), -- P001: Nasi Goreng, Sate Ayam
(10, 'M05', 1), -- P003: Bakso Komplit
(11, 'M01', 1), (11, 'M04', 1), -- P004: Nasi Goreng, Es Jeruk
(12, 'M02', 1), -- P005: Mie Ayam Bakso
(13, 'M06', 1), -- P006: Gado-gado Siram
(14, 'M01', 1), -- P001: Nasi Goreng Spesial
(15, 'M03', 1), -- P002: Sate Ayam Madura

-- Tambahan detail pesanan untuk mencapai 45 baris
(1, 'M04', 1),
(2, 'M06', 1),
(3, 'M01', 1),
(4, 'M02', 1),
(5, 'M04', 1), (5, 'M05', 1),
(6, 'M01', 1),
(7, 'M02', 1),
(8, 'M01', 1),
(9, 'M02', 1), (9, 'M04', 1),
(10, 'M01', 1),
(11, 'M02', 1), (11, 'M03', 1),
(12, 'M04', 1),
(13, 'M01', 1),
(14, 'M02', 1),
(15, 'M04', 1),

(1, 'M02', 1),
(2, 'M01', 1),
(3, 'M04', 1),
(4, 'M05', 1),
(5, 'M06', 1), (5, 'M02', 1),
(6, 'M03', 1),
(7, 'M04', 1),
(8, 'M05', 1),
(9, 'M06', 1), (9, 'M05', 1),
(10, 'M02', 1),
(11, 'M05', 1), (11, 'M06', 1),
(12, 'M01', 1),
(13, 'M02', 1),
(14, 'M03', 1),
(15, 'M05', 1);


-- 3. Tabel Review (10 baris)
-- Hanya untuk pesanan yang statusnya 'Selesai' dan pastikan Penjual_id_kanti sesuai dengan Menu yang dipesan
-- Pesanan_pesanan_id harus UNIK karena ada UNIQUE constraint pada kolom tersebut
INSERT INTO Review (rating, tanggal_rating, Pesanan_pesanan_id, Penjual_id_kanti) VALUES
(5, '2025-06-01 11:00:00', 1, 'K001'), -- Pesanan 1 (M01 dari K001)
(4, '2025-06-01 11:30:00', 2, 'K002'), -- Pesanan 2 (M02 dari K002)
(5, '2025-06-01 12:00:00', 4, 'K003'), -- Pesanan 4 (M04 dari K003)
(4, '2025-06-02 13:00:00', 6, 'K004'), -- Pesanan 6 (M05 dari K004)
(5, '2025-06-03 14:00:00', 7, 'K001'), -- Pesanan 7 (M01 dari K001)
(3, '2025-06-04 10:00:00', 9, 'K001'), -- Pesanan 9 (M01, M03 dari K001)
(4, '2025-06-05 15:00:00', 11, 'K001'),-- Pesanan 11 (M01 dari K001)
(5, '2025-06-06 12:00:00', 13, 'K002'),-- Pesanan 13 (M06 dari K002)
(4, '2025-06-07 11:00:00', 15, 'K001'),-- Pesanan 15 (M03 dari K001)
(4, '2025-06-01 10:45:00', 3, 'K001'); -- Menggunakan Pesanan ID 3 (yang belum memiliki review) untuk memastikan unik

-- Aktifkan kembali pemeriksaan foreign key
SET FOREIGN_KEY_CHECKS = 1;

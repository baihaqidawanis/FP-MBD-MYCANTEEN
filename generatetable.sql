CREATE TABLE Pembeli (
    nrp VARCHAR(10) PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nomor_telepon VARCHAR(18) NOT NULL,
    alamat VARCHAR(255) NOT NULL
);

CREATE TABLE Penjual (
    id_kantin VARCHAR(4) PRIMARY KEY,
    nama_kantin VARCHAR(30) NOT NULL,
    nama_penanggung_jawab VARCHAR(100) NOT NULL,
    email_kantin VARCHAR(255) UNIQUE NOT NULL,
    password_kantin VARCHAR(255) NOT NULL,
    nomor_telepon VARCHAR(18) NOT NULL
);

CREATE TABLE Diskon (
    id_diskon INT AUTO_INCREMENT PRIMARY KEY,
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
    pesanan_id INT AUTO_INCREMENT PRIMARY KEY,
    pesanan_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    pesanan_total INT NOT NULL,
    pesanan_paym VARCHAR(50) NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'Menunggu Konfirmasi',
    Pembeli_id_mah VARCHAR(10),
    FOREIGN KEY (Pembeli_id_mah) REFERENCES Pembeli(nrp) ON DELETE CASCADE
);

CREATE TABLE DetailPesanan (
    dp_id INT AUTO_INCREMENT PRIMARY KEY,
    Pesanan_pesanan_id INT,
    Menu_id_menu VARCHAR(3),
    dp_qty INT NOT NULL,
    FOREIGN KEY (Pesanan_pesanan_id) REFERENCES Pesanan(pesanan_id) ON DELETE CASCADE,
    FOREIGN KEY (Menu_id_menu) REFERENCES Menu(id_menu) ON DELETE CASCADE
);

CREATE TABLE Review (
    id_rating INT AUTO_INCREMENT PRIMARY KEY,
    rating INT NOT NULL,
    tanggal_rating TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    Pesanan_pesanan_id INT UNIQUE,
    Penjual_id_kanti VARCHAR(4),
    FOREIGN KEY (Pesanan_pesanan_id) REFERENCES Pesanan(pesanan_id) ON DELETE CASCADE,
    FOREIGN KEY (Penjual_id_kanti) REFERENCES Penjual(id_kantin) ON DELETE CASCADE
);

CREATE TABLE Admin (
    id_admin VARCHAR(10) PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
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

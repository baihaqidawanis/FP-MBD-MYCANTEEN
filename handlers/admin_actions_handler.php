<?php
// handlers/admin_actions_handler.php
// Di-include oleh index.php, jadi $conn, $_SESSION sudah tersedia.

$user_delete_success = $user_delete_error = '';
$announcement_success = $announcement_error = '';
$kegiatan_success = $kegiatan_error = ''; // Variabel baru untuk kegiatan kampus

// Logika Hapus Akun (Pembeli/Penjual)
if (isset($_POST['delete_user_submit']) && isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin') {
    $user_id_to_delete = trim($_POST['user_id_to_delete']);
    $user_role_to_delete = trim($_POST['user_role_to_delete']);

    if (empty($user_id_to_delete) || empty($user_role_to_delete)) {
        $user_delete_error = "ID Pengguna dan Peran wajib diisi.";
    } else {
        $delete_query = "";
        $stmt_delete = null;

        if ($user_role_to_delete === 'pembeli') {
            $delete_query = "DELETE FROM Pembeli WHERE nrp = ?";
            $stmt_delete = mysqli_prepare($conn, $delete_query);
            mysqli_stmt_bind_param($stmt_delete, "s", $user_id_to_delete);
        } elseif ($user_role_to_delete === 'penjual') {
            $delete_query = "DELETE FROM Penjual WHERE id_kantin = ?";
            $stmt_delete = mysqli_prepare($conn, $delete_query);
            mysqli_stmt_bind_param($stmt_delete, "s", $user_id_to_delete);
        } else {
            $user_delete_error = "Peran pengguna tidak valid.";
        }

        if ($stmt_delete) {
            if (mysqli_stmt_execute($stmt_delete)) {
                if (mysqli_stmt_affected_rows($stmt_delete) > 0) {
                    $user_delete_success = "Akun " . htmlspecialchars($user_role_to_delete) . " dengan ID " . htmlspecialchars($user_id_to_delete) . " berhasil dihapus.";
                } else {
                    $user_delete_error = "Akun tidak ditemukan atau sudah dihapus.";
                }
            } else {
                $user_delete_error = "Gagal menghapus akun. Error: " . mysqli_stmt_error($stmt_delete);
            }
            mysqli_stmt_close($stmt_delete);
        }
    }
}

// Logika Tambah Pengumuman
if (isset($_POST['add_announcement_submit']) && isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin') {
    $judul = trim($_POST['judul_announcement']);
    $konten = trim($_POST['konten_announcement']);
    $tanggal_terbit = trim($_POST['tanggal_terbit_announcement']);

    if (empty($judul) || empty($konten) || empty($tanggal_terbit)) {
        $announcement_error = "Judul, Konten, dan Tanggal Terbit pengumuman wajib diisi.";
    } else {
        $insert_query = "INSERT INTO Pengumuman (judul, konten, tanggal_terbit) VALUES (?, ?, ?)";
        $stmt_insert = mysqli_prepare($conn, $insert_query);
        mysqli_stmt_bind_param($stmt_insert, "sss", $judul, $konten, $tanggal_terbit);

        if (mysqli_stmt_execute($stmt_insert)) {
            $announcement_success = "Pengumuman berhasil ditambahkan!";
        } else {
            $announcement_error = "Gagal menambahkan pengumuman. Error: " . mysqli_stmt_error($stmt_insert);
        }
        mysqli_stmt_close($stmt_insert);
    }
}

// Logika Edit Pengumuman
if (isset($_POST['update_announcement_submit']) && isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin') {
    $id_pengumuman = (int) $_POST['id_pengumuman_update'];
    $judul = trim($_POST['judul_announcement_update']);
    $konten = trim($_POST['konten_announcement_update']);
    $tanggal_terbit = trim($_POST['tanggal_terbit_announcement_update']);

    if (empty($id_pengumuman) || empty($judul) || empty($konten) || empty($tanggal_terbit)) {
        $announcement_error = "Semua field pengumuman wajib diisi untuk update.";
    } else {
        $update_query = "UPDATE Pengumuman SET judul = ?, konten = ?, tanggal_terbit = ? WHERE id_pengumuman = ?";
        $stmt_update = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($stmt_update, "sssi", $judul, $konten, $tanggal_terbit, $id_pengumuman);

        if (mysqli_stmt_execute($stmt_update)) {
            if (mysqli_stmt_affected_rows($stmt_update) > 0) {
                $announcement_success = "Pengumuman berhasil diperbarui!";
            } else {
                $announcement_error = "Pengumuman tidak ditemukan atau tidak ada perubahan data.";
            }
        } else {
            $announcement_error = "Gagal memperbarui pengumuman. Error: " . mysqli_stmt_error($stmt_update);
        }
        mysqli_stmt_close($stmt_update);
    }
}

// Logika Hapus Pengumuman
if (isset($_POST['delete_announcement_submit']) && isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin') {
    $id_pengumuman = (int) $_POST['id_pengumuman_delete'];

    if (empty($id_pengumuman)) {
        $announcement_error = "ID Pengumuman tidak valid.";
    } else {
        $delete_query = "DELETE FROM Pengumuman WHERE id_pengumuman = ?";
        $stmt_delete = mysqli_prepare($conn, $delete_query);
        mysqli_stmt_bind_param($stmt_delete, "i", $id_pengumuman);

        if (mysqli_stmt_execute($stmt_delete)) {
            if (mysqli_stmt_affected_rows($stmt_delete) > 0) {
                $announcement_success = "Pengumuman berhasil dihapus!";
            } else {
                $announcement_error = "Pengumuman tidak ditemukan atau sudah dihapus.";
            }
        } else {
            $announcement_error = "Gagal menghapus pengumuman. Error: " . mysqli_stmt_error($stmt_delete);
        }
        mysqli_stmt_close($stmt_delete);
    }
}

// Logika Tambah Kegiatan Kampus
if (isset($_POST['add_kegiatan_submit']) && isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin') {
    $nama_kegiatan = trim($_POST['nama_kegiatan']);
    $deskripsi_kegiatan = trim($_POST['deskripsi_kegiatan']);
    $tanggal_mulai = trim($_POST['tanggal_mulai_kegiatan']);
    $tanggal_akhir = trim($_POST['tanggal_akhir_kegiatan']);

    if (empty($nama_kegiatan) || empty($tanggal_mulai) || empty($tanggal_akhir)) {
        $kegiatan_error = "Nama Kegiatan, Tanggal Mulai, dan Tanggal Akhir wajib diisi.";
    } elseif (strtotime($tanggal_mulai) > strtotime($tanggal_akhir)) {
        $kegiatan_error = "Tanggal Mulai tidak boleh lebih dari Tanggal Akhir.";
    } else {
        $insert_query = "INSERT INTO KegiatanKampus (nama_kegiatan, deskripsi_kegiatan, tanggal_mulai, tanggal_akhir) VALUES (?, ?, ?, ?)";
        $stmt_insert = mysqli_prepare($conn, $insert_query);
        mysqli_stmt_bind_param($stmt_insert, "ssss", $nama_kegiatan, $deskripsi_kegiatan, $tanggal_mulai, $tanggal_akhir);

        if (mysqli_stmt_execute($stmt_insert)) {
            $kegiatan_success = "Kegiatan kampus berhasil ditambahkan!";
        } else {
            $kegiatan_error = "Gagal menambahkan kegiatan kampus. Error: " . mysqli_stmt_error($stmt_insert);
        }
        mysqli_stmt_close($stmt_insert);
    }
}

// Logika Edit Kegiatan Kampus
if (isset($_POST['update_kegiatan_submit']) && isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin') {
    $id_kegiatan = (int) $_POST['id_kegiatan_update'];
    $nama_kegiatan = trim($_POST['nama_kegiatan_update']);
    $deskripsi_kegiatan = trim($_POST['deskripsi_kegiatan_update']);
    $tanggal_mulai = trim($_POST['tanggal_mulai_kegiatan_update']);
    $tanggal_akhir = trim($_POST['tanggal_akhir_kegiatan_update']);

    if (empty($id_kegiatan) || empty($nama_kegiatan) || empty($tanggal_mulai) || empty($tanggal_akhir)) {
        $kegiatan_error = "Semua field kegiatan kampus wajib diisi untuk update.";
    } elseif (strtotime($tanggal_mulai) > strtotime($tanggal_akhir)) {
        $kegiatan_error = "Tanggal Mulai tidak boleh lebih dari Tanggal Akhir.";
    } else {
        $update_query = "UPDATE KegiatanKampus SET nama_kegiatan = ?, deskripsi_kegiatan = ?, tanggal_mulai = ?, tanggal_akhir = ? WHERE id_kegiatan = ?";
        $stmt_update = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($stmt_update, "ssssi", $nama_kegiatan, $deskripsi_kegiatan, $tanggal_mulai, $tanggal_akhir, $id_kegiatan);

        if (mysqli_stmt_execute($stmt_update)) {
            if (mysqli_stmt_affected_rows($stmt_update) > 0) {
                $kegiatan_success = "Kegiatan kampus berhasil diperbarui!";
            } else {
                $kegiatan_error = "Kegiatan kampus tidak ditemukan atau tidak ada perubahan data.";
            }
        } else {
            $kegiatan_error = "Gagal memperbarui kegiatan kampus. Error: " . mysqli_stmt_error($stmt_update);
        }
        mysqli_stmt_close($stmt_update);
    }
}

// Logika Hapus Kegiatan Kampus
if (isset($_POST['delete_kegiatan_submit']) && isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin') {
    $id_kegiatan = (int) $_POST['id_kegiatan_delete'];

    if (empty($id_kegiatan)) {
        $kegiatan_error = "ID Kegiatan tidak valid.";
    } else {
        $delete_query = "DELETE FROM KegiatanKampus WHERE id_kegiatan = ?";
        $stmt_delete = mysqli_prepare($conn, $delete_query);
        mysqli_stmt_bind_param($stmt_delete, "i", $id_kegiatan);

        if (mysqli_stmt_execute($stmt_delete)) {
            if (mysqli_stmt_affected_rows($stmt_delete) > 0) {
                $kegiatan_success = "Kegiatan kampus berhasil dihapus!";
            } else {
                $kegiatan_error = "Kegiatan kampus tidak ditemukan atau sudah dihapus.";
            }
        } else {
            $kegiatan_error = "Gagal menghapus kegiatan kampus. Error: " . mysqli_stmt_error($stmt_delete);
        }
        mysqli_stmt_close($stmt_delete);
    }
}
?>
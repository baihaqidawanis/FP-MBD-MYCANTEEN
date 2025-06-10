<?php
// handlers/seller_handlers.php
// File ini akan di-include di index.php, jadi $conn, $_SESSION, $menu_error, $menu_success, $diskon_error, $diskon_success, $order_update_error, $order_update_success, dll. sudah tersedia.

// Logika Tambah Menu (Penjual)
if (isset($_POST['add_menu_submit']) && isset($_SESSION['user_id']) && $_SESSION['role'] === 'penjual') {
    $id_menu = trim($_POST['id_menu']);
    $nama_menu = trim($_POST['nama_menu']);
    $deskripsi = trim($_POST['deskripsi']);
    $harga = (int) $_POST['harga'];
    $status_menu = trim($_POST['status_menu']);
    $penjual_id_kan = $_SESSION['user_id']; // ID Kantin dari sesi
    $diskon_id_disk = empty($_POST['diskon_id_disk']) ? null : (int) $_POST['diskon_id_disk']; // Opsional

    if (empty($id_menu) || empty($nama_menu) || empty($harga) || empty($status_menu)) {
        $menu_error = "ID Menu, Nama Menu, Harga, dan Status Menu wajib diisi.";
    } elseif (strlen($id_menu) > 3) {
        $menu_error = "ID Menu maksimal 3 karakter.";
    } elseif (strlen($nama_menu) > 25) {
        $menu_error = "Nama Menu maksimal 25 karakter.";
    } elseif (strlen($deskripsi) > 255) {
        $menu_error = "Deskripsi maksimal 255 karakter.";
    } elseif (!in_array($status_menu, ['Tersedia', 'Habis', 'Pre-Order'])) {
        $menu_error = "Status Menu tidak valid.";
    } else {
        // Cek duplikasi ID Menu
        $check_duplicate_query = "SELECT id_menu FROM Menu WHERE id_menu = ?";
        $stmt_check = mysqli_prepare($conn, $check_duplicate_query);
        mysqli_stmt_bind_param($stmt_check, "s", $id_menu);
        mysqli_stmt_execute($stmt_check);
        $result_check = mysqli_stmt_get_result($stmt_check);
        if (mysqli_num_rows($result_check) > 0) {
            $menu_error = "ID Menu ini sudah digunakan. Silakan gunakan ID lain.";
        } else {
            $insert_query = "INSERT INTO Menu (id_menu, nama_menu, deskripsi, harga, status_menu, Penjual_id_kan, Diskon_id_disk) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt_insert = mysqli_prepare($conn, $insert_query);

            // Perbaikan untuk bind_param dengan nilai NULL
            $bind_types = "sssisss"; // Default types for all 7 parameters
            $bind_params = [];
            $bind_params[] = $id_menu;
            $bind_params[] = $nama_menu;
            $bind_params[] = $deskripsi;
            $bind_params[] = $harga;
            $bind_params[] = $status_menu;
            $bind_params[] = $penjual_id_kan;
            $bind_params[] = $diskon_id_disk;

            // Memperbaiki penggunaan call_user_func_array untuk bind_param
            call_user_func_array('mysqli_stmt_bind_param', array_merge([$stmt_insert, $bind_types], $bind_params));

            if (mysqli_stmt_execute($stmt_insert)) {
                $menu_success = "Menu berhasil ditambahkan!";
            } else {
                $menu_error = "Gagal menambahkan menu. Error: " . mysqli_stmt_error($stmt_insert);
            }
        }
    }
}

// Logika Edit Menu (Penjual)
if (isset($_POST['update_menu_submit']) && isset($_SESSION['user_id']) && $_SESSION['role'] === 'penjual') {
    $id_menu_update = trim($_POST['id_menu_update']); // ID menu yang akan diupdate
    $nama_menu_update = trim($_POST['nama_menu_update']);
    $deskripsi_update = trim($_POST['deskripsi_update']);
    $harga_update = (int) $_POST['harga_update'];
    $status_menu_update = trim($_POST['status_menu_update']);
    $penjual_id_kan_update = $_SESSION['user_id']; // ID Kantin dari sesi
    $diskon_id_disk_update = empty($_POST['diskon_id_disk_update']) ? null : (int) $_POST['diskon_id_disk_update']; // Opsional

    if (empty($id_menu_update) || empty($nama_menu_update) || empty($harga_update) || empty($status_menu_update)) {
        $menu_error = "ID Menu, Nama Menu, Harga, dan Status Menu wajib diisi untuk update.";
    } elseif (strlen($nama_menu_update) > 25) {
        $menu_error = "Nama Menu maksimal 25 karakter.";
    } elseif (strlen($deskripsi_update) > 255) {
        $menu_error = "Deskripsi maksimal 255 karakter.";
    } elseif (!in_array($status_menu_update, ['Tersedia', 'Habis', 'Pre-Order'])) {
        $menu_error = "Status Menu tidak valid.";
    } else {
        // Pastikan penjual hanya bisa mengupdate menu miliknya
        $check_ownership_query = "SELECT id_menu FROM Menu WHERE id_menu = ? AND Penjual_id_kan = ?";
        $stmt_check = mysqli_prepare($conn, $check_ownership_query);
        mysqli_stmt_bind_param($stmt_check, "ss", $id_menu_update, $penjual_id_kan_update);
        mysqli_stmt_execute($stmt_check);
        $result_check = mysqli_stmt_get_result($stmt_check);

        if (mysqli_num_rows($result_check) > 0) {
            $update_query = "UPDATE Menu SET nama_menu = ?, deskripsi = ?, harga = ?, status_menu = ?, Diskon_id_disk = ? WHERE id_menu = ? AND Penjual_id_kan = ?";
            $stmt_update = mysqli_prepare($conn, $update_query);

            $bind_types = "ssisiss";
            $bind_params = [];
            $bind_params[] = $nama_menu_update;
            $bind_params[] = $deskripsi_update;
            $bind_params[] = $harga_update;
            $bind_params[] = $status_menu_update;
            $bind_params[] = $diskon_id_disk_update; // This can be NULL, and 'i' type for NULL is generally fine.
            $bind_params[] = $id_menu_update;
            $bind_params[] = $penjual_id_kan_update;

            call_user_func_array('mysqli_stmt_bind_param', array_merge([$stmt_update, $bind_types], $bind_params));

            if (mysqli_stmt_execute($stmt_update)) {
                $menu_success = "Menu berhasil diperbarui!";
            } else {
                $menu_error = "Gagal memperbarui menu. Error: " . mysqli_stmt_error($stmt_update);
            }
        } else {
            $menu_error = "Anda tidak memiliki izin untuk mengedit menu ini atau menu tidak ditemukan.";
        }
    }
}

// Logika Hapus Menu (Penjual)
if (isset($_POST['delete_menu_submit']) && isset($_SESSION['user_id']) && $_SESSION['role'] === 'penjual') {
    $id_menu_delete = trim($_POST['id_menu_delete']);
    $penjual_id_kan_delete = $_SESSION['user_id'];

    // Pastikan penjual hanya bisa menghapus menu miliknya
    $check_ownership_query = "SELECT id_menu FROM Menu WHERE id_menu = ? AND Penjual_id_kan = ?";
    $stmt_check = mysqli_prepare($conn, $check_ownership_query);
    mysqli_stmt_bind_param($stmt_check, "ss", $id_menu_delete, $penjual_id_kan_delete);
    mysqli_stmt_execute($stmt_check);
    $result_check = mysqli_stmt_get_result($stmt_check);

    if (mysqli_num_rows($result_check) > 0) {
        // Cek apakah menu sedang ada di DetailPesanan sebelum menghapus
        $check_detail_pesanan = "SELECT dp_id FROM DetailPesanan WHERE Menu_id_menu = ?";
        $stmt_check_dp = mysqli_prepare($conn, $check_detail_pesanan);
        mysqli_stmt_bind_param($stmt_check_dp, "s", $id_menu_delete);
        mysqli_stmt_execute($stmt_check_dp);
        $result_check_dp = mysqli_stmt_get_result($stmt_check_dp);

        if (mysqli_num_rows($result_check_dp) > 0) {
            $menu_delete_error = "Menu tidak dapat dihapus karena sudah terkait dengan data pesanan. Anda bisa mengubah statusnya menjadi 'Habis' atau 'Tidak Tersedia'.";
        } else {
            $delete_query = "DELETE FROM Menu WHERE id_menu = ? AND Penjual_id_kan = ?";
            $stmt_delete = mysqli_prepare($conn, $delete_query);
            mysqli_stmt_bind_param($stmt_delete, "ss", $id_menu_delete, $penjual_id_kan_delete);

            if (mysqli_stmt_execute($stmt_delete)) {
                $menu_delete_success = "Menu berhasil dihapus!";
            } else {
                $menu_delete_error = "Gagal menghapus menu. Error: " . mysqli_stmt_error($stmt_delete);
            }
        }
    } else {
        $menu_delete_error = "Anda tidak memiliki izin untuk menghapus menu ini atau menu tidak ditemukan.";
    }
}


// Logika Kelola Diskon (Penjual) - Tambah Diskon
if (isset($_POST['add_diskon_submit']) && isset($_SESSION['user_id']) && $_SESSION['role'] === 'penjual') {
    $nama_diskon = trim($_POST['nama_diskon']);
    $persentase_disko = (int) $_POST['persentase_disko'];
    $tanggal_mulai = trim($_POST['tanggal_mulai']);
    $tanggal_akhir = trim($_POST['tanggal_akhir']);
    $penjual_id_kantin = $_SESSION['user_id']; // ID Kantin dari sesi

    if (empty($nama_diskon) || empty($persentase_disko) || empty($tanggal_mulai) || empty($tanggal_akhir)) {
        $diskon_error = "Nama Diskon, Persentase, Tanggal Mulai, dan Tanggal Akhir wajib diisi.";
    } elseif ($persentase_disko <= 0 || $persentase_disko > 100) {
        $diskon_error = "Persentase Diskon harus antara 1-100.";
    } elseif (strtotime($tanggal_mulai) > strtotime($tanggal_akhir)) {
        $diskon_error = "Tanggal Mulai tidak boleh lebih dari Tanggal Akhir.";
    } else {
        $insert_query = "INSERT INTO Diskon (nama_diskon, persentase_disko, tanggal_mulai, tanggal_akhir, Penjual_id_kantin) VALUES (?, ?, ?, ?, ?)";
        $stmt_insert = mysqli_prepare($conn, $insert_query);
        mysqli_stmt_bind_param($stmt_insert, "sisss", $nama_diskon, $persentase_disko, $tanggal_mulai, $tanggal_akhir, $penjual_id_kantin);

        if (mysqli_stmt_execute($stmt_insert)) {
            $diskon_success = "Diskon berhasil ditambahkan!";
        } else {
            $diskon_error = "Gagal menambahkan diskon. Error: " . mysqli_stmt_error($stmt_insert);
        }
    }
}

// Logika Edit Diskon (Penjual)
if (isset($_POST['update_diskon_submit']) && isset($_SESSION['user_id']) && $_SESSION['role'] === 'penjual') {
    $id_diskon_update = (int) $_POST['id_diskon_update'];
    $nama_diskon_update = trim($_POST['nama_diskon_update']);
    $persentase_disko_update = (int) $_POST['persentase_disko_update'];
    $tanggal_mulai_update = trim($_POST['tanggal_mulai_update']);
    $tanggal_akhir_update = trim($_POST['tanggal_akhir_update']);
    $penjual_id_kantin_update = $_SESSION['user_id'];

    if (empty($nama_diskon_update) || empty($persentase_disko_update) || empty($tanggal_mulai_update) || empty($tanggal_akhir_update)) {
        $diskon_error = "Nama Diskon, Persentase, Tanggal Mulai, dan Tanggal Akhir wajib diisi untuk update.";
    } elseif ($persentase_disko_update <= 0 || $persentase_disko_update > 100) {
        $diskon_error = "Persentase Diskon harus antara 1-100.";
    } elseif (strtotime($tanggal_mulai_update) > strtotime($tanggal_akhir_update)) {
        $diskon_error = "Tanggal Mulai tidak boleh lebih dari Tanggal Akhir.";
    } else {
        // Pastikan penjual hanya bisa mengupdate diskon miliknya
        $check_ownership_query = "SELECT id_diskon FROM Diskon WHERE id_diskon = ? AND Penjual_id_kantin = ?";
        $stmt_check = mysqli_prepare($conn, $check_ownership_query);
        mysqli_stmt_bind_param($stmt_check, "is", $id_diskon_update, $penjual_id_kantin_update);
        mysqli_stmt_execute($stmt_check);
        $result_check = mysqli_stmt_get_result($stmt_check);

        if (mysqli_num_rows($result_check) > 0) {
            $update_query = "UPDATE Diskon SET nama_diskon = ?, persentase_disko = ?, tanggal_mulai = ?, tanggal_akhir = ? WHERE id_diskon = ? AND Penjual_id_kantin = ?";
            $stmt_update = mysqli_prepare($conn, $update_query);
            mysqli_stmt_bind_param($stmt_update, "sissis", $nama_diskon_update, $persentase_disko_update, $tanggal_mulai_update, $tanggal_akhir_update, $id_diskon_update, $penjual_id_kantin_update);

            if (mysqli_stmt_execute($stmt_update)) {
                $diskon_success = "Diskon berhasil diperbarui!";
            } else {
                $diskon_error = "Gagal memperbarui diskon. Error: " . mysqli_stmt_error($stmt_update);
            }
        } else {
            $diskon_error = "Anda tidak memiliki izin untuk mengedit diskon ini atau diskon tidak ditemukan.";
        }
    }
}

// Logika Hapus Diskon (Penjual)
if (isset($_POST['delete_diskon_submit']) && isset($_SESSION['user_id']) && $_SESSION['role'] === 'penjual') {
    $id_diskon_delete = (int) $_POST['id_diskon_delete'];
    $penjual_id_kantin_delete = $_SESSION['user_id'];

    // Pastikan penjual hanya bisa menghapus diskon miliknya
    $check_ownership_query = "SELECT id_diskon FROM Diskon WHERE id_diskon = ? AND Penjual_id_kantin = ?";
    $stmt_check = mysqli_prepare($conn, $check_ownership_query);
    mysqli_stmt_bind_param($stmt_check, "is", $id_diskon_delete, $penjual_id_kantin_delete);
    mysqli_stmt_execute($stmt_check);
    $result_check = mysqli_stmt_get_result($stmt_check);

    if (mysqli_num_rows($result_check) > 0) {
        // Set Diskon_id_disk di tabel Menu ke NULL jika merujuk ke diskon yang akan dihapus
        $update_menu_diskon_query = "UPDATE Menu SET Diskon_id_disk = NULL WHERE Diskon_id_disk = ? AND Penjual_id_kan = ?";
        $stmt_update_menu = mysqli_prepare($conn, $update_menu_diskon_query);
        mysqli_stmt_bind_param($stmt_update_menu, "is", $id_diskon_delete, $penjual_id_kantin_delete);
        mysqli_stmt_execute($stmt_update_menu);

        $delete_query = "DELETE FROM Diskon WHERE id_diskon = ? AND Penjual_id_kantin = ?";
        $stmt_delete = mysqli_prepare($conn, $delete_query);
        mysqli_stmt_bind_param($stmt_delete, "is", $id_diskon_delete, $penjual_id_kantin_delete);

        if (mysqli_stmt_execute($stmt_delete)) {
            $diskon_delete_success = "Diskon berhasil dihapus!";
        } else {
            $diskon_delete_error = "Gagal menghapus diskon. Error: " . mysqli_stmt_error($stmt_delete);
        }
    } else {
        $diskon_delete_error = "Anda tidak memiliki izin untuk menghapus diskon ini atau diskon tidak ditemukan.";
    }
}


// Logika Update Status Pesanan (Penjual)
if (isset($_POST['update_status_submit']) && isset($_SESSION['user_id']) && $_SESSION['role'] === 'penjual') {
    $pesanan_id = (int) $_POST['pesanan_id'];
    $new_status = trim($_POST['new_status']);
    $seller_id = $_SESSION['user_id'];

    // Pastikan penjual hanya bisa mengupdate pesanan yang terkait dengan menu-nya
    $check_ownership_query = "
        SELECT DISTINCT p.pesanan_id
        FROM Pesanan p
        JOIN DetailPesanan dp ON p.pesanan_id = dp.Pesanan_pesanan_id
        JOIN Menu m ON dp.Menu_id_menu = m.id_menu
        WHERE p.pesanan_id = ? AND m.Penjual_id_kan = ? LIMIT 1
    ";
    $stmt_check = mysqli_prepare($conn, $check_ownership_query);
    mysqli_stmt_bind_param($stmt_check, "is", $pesanan_id, $seller_id);
    mysqli_stmt_execute($stmt_check);
    $result_check = mysqli_stmt_get_result($stmt_check);

    if (mysqli_num_rows($result_check) > 0) {
        $update_query = "UPDATE Pesanan SET status = ? WHERE pesanan_id = ?";
        $stmt_update = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($stmt_update, "si", $new_status, $pesanan_id);

        if (mysqli_stmt_execute($stmt_update)) {
            $order_update_success = "Status pesanan berhasil diperbarui!";
        } else {
            $order_update_error = "Gagal memperbarui status pesanan. Error: " . mysqli_stmt_error($stmt_update);
        }
    } else {
        $order_update_error = "Anda tidak memiliki izin untuk memperbarui pesanan ini atau pesanan tidak ditemukan.";
    }
}
?>
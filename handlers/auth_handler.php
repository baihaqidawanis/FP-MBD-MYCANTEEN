<?php
// handlers/auth_handler.php
// File ini akan di-include di index.php, jadi $conn, $login_error, $register_error, $register_success sudah tersedia.

// Logika Login
if (isset($_POST['login'])) {
    $username_input = $_POST['username'];
    $password_input = $_POST['password'];

    $user_found = false;

    // Coba login sebagai Pembeli (menggunakan NRP sebagai username)
    $query_pembeli = "SELECT nrp AS id, nama AS display_name, password AS hashed_password, 'pembeli' AS role FROM Pembeli WHERE nrp = ?";
    $stmt_pembeli = mysqli_prepare($conn, $query_pembeli);
    mysqli_stmt_bind_param($stmt_pembeli, "s", $username_input);
    mysqli_stmt_execute($stmt_pembeli);
    $result_pembeli = mysqli_stmt_get_result($stmt_pembeli);
    $user_pembeli = mysqli_fetch_assoc($result_pembeli);

    if ($user_pembeli && password_verify($password_input, $user_pembeli['hashed_password'])) {
        $_SESSION['user_id'] = $user_pembeli['id']; // user_id sekarang menyimpan NRP
        $_SESSION['username'] = $user_pembeli['display_name']; // Tampilkan nama
        $_SESSION['role'] = $user_pembeli['role'];
        $user_found = true;
    }

    // Jika bukan Pembeli, coba login sebagai Penjual (menggunakan email_kantin sebagai username)
    if (!$user_found) {
        $query_penjual = "SELECT id_kantin AS id, nama_kantin AS display_name, password_kantin AS hashed_password, 'penjual' AS role FROM Penjual WHERE email_kantin = ?";
        $stmt_penjual = mysqli_prepare($conn, $query_penjual);
        mysqli_stmt_bind_param($stmt_penjual, "s", $username_input);
        mysqli_stmt_execute($stmt_penjual);
        $result_penjual = mysqli_stmt_get_result($stmt_penjual);
        $user_penjual = mysqli_fetch_assoc($result_penjual);

        if ($user_penjual && password_verify($password_input, $user_penjual['hashed_password'])) {
            $_SESSION['user_id'] = $user_penjual['id'];
            $_SESSION['username'] = $user_penjual['display_name']; // Tampilkan nama kantin
            $_SESSION['role'] = $user_penjual['role'];
            $user_found = true;
        }
    }

    // Jika bukan Penjual, coba login sebagai Admin (menggunakan username sebagai username)
    if (!$user_found) {
        $query_admin = "SELECT id_admin AS id, nama_lengkap AS display_name, password AS hashed_password, 'admin' AS role FROM Admin WHERE username = ?";
        $stmt_admin = mysqli_prepare($conn, $query_admin);
        mysqli_stmt_bind_param($stmt_admin, "s", $username_input);
        mysqli_stmt_execute($stmt_admin);
        $result_admin = mysqli_stmt_get_result($stmt_admin);
        $user_admin = mysqli_fetch_assoc($result_admin);

        if ($user_admin && password_verify($password_input, $user_admin['hashed_password'])) {
            $_SESSION['user_id'] = $user_admin['id'];
            $_SESSION['username'] = $user_admin['display_name']; // Tampilkan nama lengkap admin
            $_SESSION['role'] = $user_admin['role'];
            $user_found = true;
        }
    }


    if ($user_found) {
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        $login_error = "Username atau password salah!";
    }
}

// Logika Registrasi Pembeli
if (isset($_POST['register_pembeli_submit'])) {
    $nama = trim($_POST['nama']);
    $nrp = trim($_POST['nrp']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $nomor_telepon = trim($_POST['nomor_telepon']);
    $alamat = trim($_POST['alamat']);

    // Validasi input
    if (empty($nama) || empty($nrp) || empty($email) || empty($password) || empty($nomor_telepon) || empty($alamat)) {
        $register_error = "Nama, NRP, Email, Password, Nomor Telepon, dan Alamat wajib diisi.";
    } elseif (strlen($nrp) > 10) {
        $register_error = "NRP maksimal 10 karakter.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $register_error = "Format email tidak valid.";
    } else {
        // Cek duplikasi NRP atau Email di Pembeli, dan email di Penjual atau Admin (untuk konsistensi email)
        $check_duplicate_query = "
            SELECT nrp FROM Pembeli WHERE nrp = ? OR email = ?
            UNION ALL
            SELECT id_kantin FROM Penjual WHERE email_kantin = ?
            UNION ALL
            SELECT id_admin FROM Admin WHERE email = ?
        ";
        $stmt_check = mysqli_prepare($conn, $check_duplicate_query);
        mysqli_stmt_bind_param($stmt_check, "ssss", $nrp, $email, $email, $email); // Bind email 3 kali
        mysqli_stmt_execute($stmt_check);
        $result_check = mysqli_stmt_get_result($stmt_check);

        if (mysqli_num_rows($result_check) > 0) {
            $register_error = "NRP atau Email ini sudah terdaftar. Silakan gunakan yang lain.";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Lakukan pendaftaran
            $insert_query = "INSERT INTO Pembeli (nama, nrp, email, password, nomor_telepon, alamat) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt_insert = mysqli_prepare($conn, $insert_query);
            mysqli_stmt_bind_param($stmt_insert, "ssssss", $nama, $nrp, $email, $hashed_password, $nomor_telepon, $alamat);

            if (mysqli_stmt_execute($stmt_insert)) {
                $register_success = "Pendaftaran pembeli berhasil! Silakan login.";
                $mode = 'login'; // Atur mode untuk form di views/login_register_form.php
            } else {
                $register_error = "Pendaftaran pembeli gagal. Error: " . mysqli_stmt_error($stmt_insert);
            }
        }
    }
}

// Logika Registrasi Penjual
if (isset($_POST['register_penjual_submit'])) {
    $id_kantin = trim($_POST['id_kantin']);
    $nama_kantin = trim($_POST['nama_kantin']);
    $nama_penanggung_jawab = trim($_POST['nama_penanggung_jawab']);
    $email_kantin = trim($_POST['email_kantin']);
    $password_kantin = $_POST['password_kantin'];
    $nomor_telepon = trim($_POST['nomor_telepon']);

    // Validasi input
    if (empty($id_kantin) || empty($nama_kantin) || empty($nama_penanggung_jawab) || empty($email_kantin) || empty($password_kantin) || empty($nomor_telepon)) {
        $register_error = "ID Kantin, Nama Kantin, Nama Penanggung Jawab, Email Kantin, Password, dan Nomor Telepon wajib diisi.";
    } elseif (strlen($id_kantin) > 4) {
        $register_error = "ID Kantin maksimal 4 karakter.";
    } elseif (!filter_var($email_kantin, FILTER_VALIDATE_EMAIL)) {
        $register_error = "Format email kantin tidak valid.";
    } else {
        // Cek duplikasi ID Kantin atau Email Kantin di Penjual, dan email di Pembeli atau Admin
        $check_duplicate_query = "
            SELECT id_kantin FROM Penjual WHERE id_kantin = ? OR email_kantin = ?
            UNION ALL
            SELECT nrp FROM Pembeli WHERE email = ?
            UNION ALL
            SELECT id_admin FROM Admin WHERE email = ?
        ";
        $stmt_check = mysqli_prepare($conn, $check_duplicate_query);
        mysqli_stmt_bind_param($stmt_check, "ssss", $id_kantin, $email_kantin, $email_kantin, $email_kantin); // Bind email 3 kali
        mysqli_stmt_execute($stmt_check);
        $result_check = mysqli_stmt_get_result($stmt_check);
        if (mysqli_num_rows($result_check) > 0) {
            $register_error = "ID Kantin atau Email Kantin ini sudah terdaftar. Silakan gunakan yang lain.";
        } else {
            // Hash password
            $hashed_password_kantin = password_hash($password_kantin, PASSWORD_DEFAULT);

            // Lakukan pendaftaran
            $insert_query = "INSERT INTO Penjual (id_kantin, nama_kantin, nama_penanggung_jawab, email_kantin, password_kantin, nomor_telepon) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt_insert = mysqli_prepare($conn, $insert_query);
            mysqli_stmt_bind_param($stmt_insert, "ssssss", $id_kantin, $nama_kantin, $nama_penanggung_jawab, $email_kantin, $hashed_password_kantin, $nomor_telepon);

            if (mysqli_stmt_execute($stmt_insert)) {
                $register_success = "Pendaftaran penjual berhasil! Silakan login.";
                $mode = 'login'; // Atur mode untuk form di views/login_register_form.php
            } else {
                $register_error = "Pendaftaran penjual gagal. Error: " . mysqli_stmt_error($stmt_insert);
            }
        }
    }
}

?>
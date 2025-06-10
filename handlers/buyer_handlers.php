<?php
// handlers/buyer_handlers.php
// File ini akan di-include di index.php, jadi $conn, $_SESSION, $order_success, $order_error, $review_success, $review_error sudah tersedia.

// Logika Melakukan Pemesanan (Pembeli)
if (isset($_POST['place_order_submit']) && isset($_SESSION['user_id']) && $_SESSION['role'] === 'pembeli') {
    $menu_id = trim($_POST['menu_id']);
    $quantity = (int) $_POST['quantity'];
    $payment_method = trim($_POST['payment_method']);
    $buyer_id = $_SESSION['user_id']; // NRP Pembeli dari sesi

    if (empty($menu_id) || $quantity <= 0 || empty($payment_method)) {
        $order_error = "ID Menu, Kuantitas, dan Metode Pembayaran wajib diisi.";
    } else {
        // Ambil detail menu dan diskon
        $query_menu_detail = "
            SELECT
                m.harga,
                m.Penjual_id_kan,
                COALESCE(d.persentase_disko, 0) AS persentase_disko
            FROM
                Menu m
            LEFT JOIN
                Diskon d ON m.Diskon_id_disk = d.id_diskon
            WHERE
                m.id_menu = ? AND m.status_menu = 'Tersedia' LIMIT 1
        "; // Perubahan: status_menu di sini
        $stmt_menu = mysqli_prepare($conn, $query_menu_detail);
        mysqli_stmt_bind_param($stmt_menu, "s", $menu_id);
        mysqli_stmt_execute($stmt_menu);
        $result_menu = mysqli_stmt_get_result($stmt_menu);
        $menu_item = mysqli_fetch_assoc($result_menu);

        if ($menu_item) {
            $harga_satuan = $menu_item['harga'];
            $persentase_diskon = $menu_item['persentase_disko'];
            $seller_id_for_order = $menu_item['Penjual_id_kan'];

            $total_harga = $harga_satuan * $quantity;
            if ($persentase_diskon > 0) {
                $total_harga -= ($total_harga * $persentase_diskon / 100);
            }

            // Mulai transaksi
            mysqli_autocommit($conn, FALSE);
            $transaction_ok = true;

            // Insert ke tabel Pesanan
            $insert_pesanan_query = "INSERT INTO Pesanan (pesanan_date, pesanan_total, pesanan_paym, Pembeli_id_mah, status) VALUES (NOW(), ?, ?, ?, 'Menunggu Konfirmasi')";
            $stmt_pesanan = mysqli_prepare($conn, $insert_pesanan_query);
            mysqli_stmt_bind_param($stmt_pesanan, "iss", $total_harga, $payment_method, $buyer_id);

            if (!mysqli_stmt_execute($stmt_pesanan)) {
                $order_error = "Gagal membuat pesanan. Error: " . mysqli_stmt_error($stmt_pesanan);
                $transaction_ok = false;
            } else {
                $new_pesanan_id = mysqli_insert_id($conn);

                // Insert ke tabel DetailPesanan
                $insert_detail_query = "INSERT INTO DetailPesanan (Pesanan_pesanan_id, Menu_id_menu, dp_qty) VALUES (?, ?, ?)";
                $stmt_detail = mysqli_prepare($conn, $insert_detail_query);
                mysqli_stmt_bind_param($stmt_detail, "isi", $new_pesanan_id, $menu_id, $quantity);

                if (!mysqli_stmt_execute($stmt_detail)) {
                    $order_error = "Gagal detail pesanan. Error: " . mysqli_stmt_error($stmt_detail);
                    $transaction_ok = false;
                }
            }

            if ($transaction_ok) {
                mysqli_commit($conn);
                $order_success = "Pesanan Anda berhasil dibuat! ID Pesanan: " . $new_pesanan_id;
            } else {
                mysqli_rollback($conn);
                if (empty($order_error))
                    $order_error = "Terjadi kesalahan saat memproses pesanan Anda.";
            }
            mysqli_autocommit($conn, TRUE); // Kembalikan ke autocommit
        } else {
            $order_error = "Menu tidak ditemukan atau tidak tersedia.";
        }
    }
}

// Logika Melakukan Review (Pembeli)
if (isset($_POST['submit_review']) && isset($_SESSION['user_id']) && $_SESSION['role'] === 'pembeli') {
    $pesanan_id_review = (int) $_POST['pesanan_id_review'];
    $rating = (int) $_POST['rating'];
    $buyer_id_review = $_SESSION['user_id'];

    if ($rating < 1 || $rating > 5) {
        $review_error = "Rating harus antara 1 sampai 5.";
    } else {
        // Ambil id_kantin dari pesanan untuk FK Review
        $query_order_seller = "
            SELECT DISTINCT m.Penjual_id_kan AS id_kantin
            FROM Pesanan p
            JOIN DetailPesanan dp ON p.pesanan_id = dp.Pesanan_pesanan_id
            JOIN Menu m ON dp.Menu_id_menu = m.id_menu
            WHERE p.pesanan_id = ? AND p.Pembeli_id_mah = ? LIMIT 1
        ";
        $stmt_order_seller = mysqli_prepare($conn, $query_order_seller);
        mysqli_stmt_bind_param($stmt_order_seller, "is", $pesanan_id_review, $buyer_id_review);
        mysqli_stmt_execute($stmt_order_seller);
        $result_order_seller = mysqli_stmt_get_result($stmt_order_seller);
        $order_seller_info = mysqli_fetch_assoc($result_order_seller);

        if ($order_seller_info) {
            $penjual_id_kanti = $order_seller_info['id_kantin'];

            // Cek apakah pesanan sudah di-review (kolom Pesanan_pesanan_id di Review UNIQUE)
            $check_review_query = "SELECT id_rating FROM Review WHERE Pesanan_pesanan_id = ?";
            $stmt_check_review = mysqli_prepare($conn, $check_review_query);
            mysqli_stmt_bind_param($stmt_check_review, "i", $pesanan_id_review);
            mysqli_stmt_execute($stmt_check_review);
            $result_check_review = mysqli_stmt_get_result($stmt_check_review);

            if (mysqli_num_rows($result_check_review) > 0) {
                $review_error = "Pesanan ini sudah pernah Anda ulas.";
            } else {
                $insert_review_query = "INSERT INTO Review (rating, tanggal_rating, Pesanan_pesanan_id, Penjual_id_kanti) VALUES (?, NOW(), ?, ?)";
                $stmt_insert_review = mysqli_prepare($conn, $insert_review_query);
                mysqli_stmt_bind_param($stmt_insert_review, "iis", $rating, $pesanan_id_review, $penjual_id_kanti);

                if (mysqli_stmt_execute($stmt_insert_review)) {
                    $review_success = "Ulasan berhasil ditambahkan!";
                } else {
                    $review_error = "Gagal menambahkan ulasan. Error: " . mysqli_stmt_error($stmt_insert_review);
                }
            }
        } else {
            $review_error = "Pesanan tidak ditemukan atau Anda tidak memiliki akses untuk mengulasnya.";
        }
    }
}
?>
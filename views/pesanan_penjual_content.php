<?php
// views/pesanan_penjual_content.php
// Di-include oleh index.php, jadi variabel seperti $conn, $_SESSION['user_id'], $order_update_success, $order_update_error sudah tersedia.
?>

<div class="bg-white p-8 rounded-xl shadow-md">
    <h4 class="text-lg font-bold text-gray-800 mb-4">Daftar Pesanan Masuk (Penjual)</h4>
    <?php if (isset($order_update_success) && !empty($order_update_success)): ?>
        <p class="text-green-500 mb-4"><?php echo $order_update_success; ?></p>
    <?php endif; ?>
    <?php if (isset($order_update_error) && !empty($order_update_error)): ?>
        <p class="text-red-500 mb-4"><?php echo $order_update_error; ?></p>
    <?php endif; ?>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white rounded-lg shadow-sm">
            <thead class="bg-gray-200">
                <tr>
                    <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">ID Pesanan</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">Tanggal</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">Pembeli</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">Total</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">Status</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $seller_id = $_SESSION['user_id'];
                // Query untuk mendapatkan pesanan yang relevan untuk penjual ini
                $query_seller_orders = "
                    SELECT DISTINCT
                        p.pesanan_id,
                        p.pesanan_date,
                        p.pesanan_total,
                        p.pesanan_paym,
                        p.status,
                        b.nama AS nama_pembeli
                    FROM
                        Pesanan p
                    JOIN
                        DetailPesanan dp ON p.pesanan_id = dp.Pesanan_pesanan_id
                    JOIN
                        Menu m ON dp.Menu_id_menu = m.id_menu
                    JOIN
                        Pembeli b ON p.Pembeli_id_mah = b.nrp
                    WHERE
                        m.Penjual_id_kan = ?
                    ORDER BY p.pesanan_date DESC;
                ";
                $stmt_seller_orders = mysqli_prepare($conn, $query_seller_orders);
                mysqli_stmt_bind_param($stmt_seller_orders, "s", $seller_id);
                mysqli_stmt_execute($stmt_seller_orders);
                $result_seller_orders = mysqli_stmt_get_result($stmt_seller_orders);

                if ($result_seller_orders && mysqli_num_rows($result_seller_orders) > 0) {
                    while ($order = fetch_assoc_db($result_seller_orders)) {
                        echo '<tr class="border-b border-gray-200 hover:bg-gray-50">';
                        echo '<td class="py-3 px-4 text-sm text-gray-700">' . htmlspecialchars($order['pesanan_id']) . '</td>';
                        echo '<td class="py-3 px-4 text-sm text-gray-700">' . htmlspecialchars($order['pesanan_date']) . '</td>';
                        echo '<td class="py-3 px-4 text-sm text-gray-700">' . htmlspecialchars($order['nama_pembeli']) . '</td>';
                        echo '<td class="py-3 px-4 text-sm text-gray-700">Rp ' . number_format($order['pesanan_total'], 0, ',', '.') . '</td>';
                        echo '<td class="py-3 px-4 text-sm text-gray-700"><span class="px-2 py-1 rounded-full text-xs font-semibold ' .
                            ($order['status'] === 'Ditolak' ? 'bg-red-100 text-red-800' :
                                ($order['status'] === 'Diproses' ? 'bg-yellow-100 text-yellow-800' :
                                    ($order['status'] === 'Selesai' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'))) . '"> ' . htmlspecialchars($order['status']) . '</span></td>';
                        echo '<td class="py-3 px-4 text-sm text-gray-700">';
                        echo '<form method="POST" action="?page=pesanan">';
                        echo '<input type="hidden" name="pesanan_id" value="' . htmlspecialchars($order['pesanan_id']) . '">';
                        echo '<select name="new_status" class="px-2 py-1 border rounded-md mr-2 text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">';
                        echo '<option value="Menunggu Konfirmasi" ' . ($order['status'] === 'Menunggu Konfirmasi' ? 'selected' : '') . '>Menunggu Konfirmasi</option>';
                        echo '<option value="Diproses" ' . ($order['status'] === 'Diproses' ? 'selected' : '') . '>Diproses</option>';
                        echo '<option value="Selesai" ' . ($order['status'] === 'Selesai' ? 'selected' : '') . '>Selesai</option>';
                        echo '<option value="Ditolak" ' . ($order['status'] === 'Ditolak' ? 'selected' : '') . '>Ditolak</option>';
                        echo '</select>';
                        echo '<button type="submit" name="update_status_submit" class="bg-blue-500 text-white px-3 py-1 rounded-md hover:bg-blue-600 transition-colors duration-200">Update</button>';
                        echo '</form>';
                        echo '</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="6" class="py-4 text-center text-gray-500">Tidak ada pesanan masuk.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
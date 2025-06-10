<?php
// views/my_orders_content.php
// Di-include oleh index.php, jadi variabel seperti $conn, $_SESSION['user_id'], $review_success, $review_error sudah tersedia.
?>

<div class="bg-white p-8 rounded-xl shadow-md">
    <h4 class="text-lg font-bold text-gray-800 mb-4">Daftar Pesanan Saya</h4>
    <?php if (isset($review_success) && !empty($review_success)): ?>
        <p class="text-green-500 mb-4"><?php echo $review_success; ?></p>
    <?php endif; ?>
    <?php if (isset($review_error) && !empty($review_error)): ?>
        <p class="text-red-500 mb-4"><?php echo $review_error; ?></p>
    <?php endif; ?>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white rounded-lg shadow-sm">
            <thead class="bg-gray-200">
                <tr>
                    <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">ID Pesanan</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">Tanggal</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">Total</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">Status</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $buyer_id = $_SESSION['user_id'];
                $query_buyer_orders = "SELECT pesanan_id, pesanan_date, pesanan_total, status FROM Pesanan WHERE Pembeli_id_mah = ? ORDER BY pesanan_date DESC";
                $stmt_buyer_orders = mysqli_prepare($conn, $query_buyer_orders);
                mysqli_stmt_bind_param($stmt_buyer_orders, "s", $buyer_id);
                mysqli_stmt_execute($stmt_buyer_orders);
                $result_buyer_orders = mysqli_stmt_get_result($stmt_buyer_orders);

                if ($result_buyer_orders && mysqli_num_rows($result_buyer_orders) > 0) {
                    while ($order = fetch_assoc_db($result_buyer_orders)) {
                        echo '<tr class="border-b border-gray-200 hover:bg-gray-50">';
                        echo '<td class="py-3 px-4 text-sm text-gray-700">' . htmlspecialchars($order['pesanan_id']) . '</td>';
                        echo '<td class="py-3 px-4 text-sm text-gray-700">' . htmlspecialchars($order['pesanan_date']) . '</td>';
                        echo '<td class="py-3 px-4 text-sm text-gray-700">Rp ' . number_format($order['pesanan_total'], 0, ',', '.') . '</td>';
                        echo '<td class="py-3 px-4 text-sm text-gray-700"><span class="px-2 py-1 rounded-full text-xs font-semibold ' .
                            ($order['status'] === 'Ditolak' ? 'bg-red-100 text-red-800' :
                                ($order['status'] === 'Diproses' ? 'bg-yellow-100 text-yellow-800' :
                                    ($order['status'] === 'Selesai' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'))) . '"> ' . htmlspecialchars($order['status']) . '</span></td>';
                        echo '<td class="py-3 px-4 text-sm text-gray-700">';

                        // Tombol Review hanya jika status Selesai dan belum di-review
                        if ($order['status'] === 'Selesai') {
                            $check_review_query = "SELECT id_rating FROM Review WHERE Pesanan_pesanan_id = ?";
                            $stmt_check_review = mysqli_prepare($conn, $check_review_query);
                            mysqli_stmt_bind_param($stmt_check_review, "i", $order['pesanan_id']);
                            mysqli_stmt_execute($stmt_check_review);
                            $result_check_review = mysqli_stmt_get_result($stmt_check_review);

                            if (mysqli_num_rows($result_check_review) == 0) {
                                echo '<form method="POST" action="?page=my_orders" class="inline-block">';
                                echo '<input type="hidden" name="pesanan_id_review" value="' . htmlspecialchars($order['pesanan_id']) . '">';
                                echo '<select name="rating" class="px-2 py-1 border rounded-md mr-2 text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">';
                                for ($i = 1; $i <= 5; $i++) {
                                    echo '<option value="' . $i . '">' . $i . ' Bintang</option>';
                                }
                                echo '</select>';
                                echo '<button type="submit" name="submit_review" class="bg-yellow-500 text-white px-3 py-1 rounded-md hover:bg-yellow-600 transition-colors duration-200">Ulas</button>';
                                echo '</form>';
                            } else {
                                echo '<span class="text-gray-500">Sudah Diulas</span>';
                            }
                        } else {
                            echo '<span class="text-gray-500">Aksi Tidak Tersedia</span>';
                        }
                        echo '</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="5" class="py-4 text-center text-gray-500">Anda belum memiliki pesanan.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
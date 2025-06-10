<?php
// views/seller_reviews_content.php
// Di-include oleh index.php, jadi $conn, $_SESSION['user_id'] sudah tersedia.
?>

<div class="bg-white p-8 rounded-xl shadow-md">
    <h4 class="text-lg font-bold text-gray-800 mb-4">Ulasan Masuk untuk Kantin Anda</h4>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white rounded-lg shadow-sm">
            <thead class="bg-gray-200">
                <tr>
                    <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">ID Review</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">Rating</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">Tanggal Ulasan</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">Pembeli</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">Pesanan ID</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">Menu Dipesan</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $seller_id = $_SESSION['user_id'];
                $query_seller_reviews = "
                    SELECT
                        r.id_rating,
                        r.rating,
                        r.tanggal_rating,
                        b.nama AS nama_pembeli,
                        p.pesanan_id,
                        GROUP_CONCAT(m.nama_menu SEPARATOR ', ') AS menu_items_ordered
                    FROM
                        Review r
                    JOIN
                        Pesanan p ON r.Pesanan_pesanan_id = p.pesanan_id
                    JOIN
                        Pembeli b ON p.Pembeli_id_mah = b.nrp
                    JOIN
                        DetailPesanan dp ON p.pesanan_id = dp.Pesanan_pesanan_id
                    JOIN
                        Menu m ON dp.Menu_id_menu = m.id_menu
                    WHERE
                        r.Penjual_id_kanti = ?
                    GROUP BY
                        r.id_rating, r.rating, r.tanggal_rating, b.nama, p.pesanan_id
                    ORDER BY
                        r.tanggal_rating DESC;
                ";
                $stmt_seller_reviews = mysqli_prepare($conn, $query_seller_reviews);
                mysqli_stmt_bind_param($stmt_seller_reviews, "s", $seller_id);
                mysqli_stmt_execute($stmt_seller_reviews);
                $result_seller_reviews = mysqli_stmt_get_result($stmt_seller_reviews);

                if ($result_seller_reviews && mysqli_num_rows($result_seller_reviews) > 0) {
                    while ($review = fetch_assoc_db($result_seller_reviews)) {
                        echo '<tr class="border-b border-gray-200 hover:bg-gray-50">';
                        echo '<td class="py-3 px-4 text-sm text-gray-700">' . htmlspecialchars($review['id_rating']) . '</td>';
                        echo '<td class="py-3 px-4 text-sm text-gray-700">' . htmlspecialchars($review['rating']) . ' ⭐</td>';
                        echo '<td class="py-3 px-4 text-sm text-gray-700">' . htmlspecialchars($review['tanggal_rating']) . '</td>';
                        echo '<td class="py-3 px-4 text-sm text-gray-700">' . htmlspecialchars($review['nama_pembeli']) . '</td>';
                        echo '<td class="py-3 px-4 text-sm text-gray-700">' . htmlspecialchars($review['pesanan_id']) . '</td>';
                        echo '<td class="py-3 px-4 text-sm text-gray-700">' . htmlspecialchars($review['menu_items_ordered']) . '</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="6" class="py-4 text-center text-gray-500">Belum ada ulasan yang masuk untuk kantin Anda.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
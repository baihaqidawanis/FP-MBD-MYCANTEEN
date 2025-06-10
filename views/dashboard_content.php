<?php
// views/dashboard_content.php
// Di-include oleh index.php, jadi variabel seperti $conn, $menu_data_fallback, $user_role, $order_success, $order_error sudah tersedia.
?>

<?php if ($user_role === 'pembeli' || $user_role === 'penjual'): ?>
    <!-- Section: Menu Kantin (Untuk Pembeli dan Penjual) -->
    <div class="mb-8">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">
            <?php echo ($user_role === 'pembeli') ? 'Daftar Menu Tersedia' : 'Menu Kantin Anda'; ?>
        </h3>
        <?php if (isset($order_success) && !empty($order_success)): ?>
            <p class="text-green-500 mb-4"><?php echo $order_success; ?></p>
        <?php endif; ?>
        <?php if (isset($order_error) && !empty($order_error)): ?>
            <p class="text-red-500 mb-4"><?php echo $order_error; ?></p>
        <?php endif; ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <?php
            $query_menu = "";
            $stmt_menu = null;
            $result_menu = false; // Initialize to false
        
            if ($user_role === 'pembeli') {
                // Pembeli melihat semua menu yang tersedia
                $query_menu = "
                SELECT
                    m.id_menu,
                    m.nama_menu,
                    m.deskripsi,
                    m.harga,
                    m.status_menu,
                    k.nama_kantin,
                    COALESCE(d.persentase_disko, 0) AS persentase_disko
                FROM
                    Menu m
                JOIN
                    Penjual k ON m.Penjual_id_kan = k.id_kantin
                LEFT JOIN
                    Diskon d ON m.Diskon_id_disk = d.id_diskon
                WHERE
                    m.status_menu = 'Tersedia'
                ORDER BY k.nama_kantin, m.nama_menu;
            ";
                $result_menu = query_db($conn, $query_menu);
            } elseif ($user_role === 'penjual' && isset($_SESSION['user_id'])) {
                // Penjual melihat menu mereka sendiri
                $seller_id = $_SESSION['user_id'];
                $query_menu = "
                SELECT
                    m.id_menu,
                    m.nama_menu,
                    m.deskripsi,
                    m.harga,
                    m.status_menu,
                    k.nama_kantin,
                    COALESCE(d.persentase_disko, 0) AS persentase_disko
                FROM
                    Menu m
                JOIN
                    Penjual k ON m.Penjual_id_kan = k.id_kantin
                LEFT JOIN
                    Diskon d ON m.Diskon_id_disk = d.id_diskon
                WHERE
                    m.Penjual_id_kan = ?
                ORDER BY m.nama_menu;
            ";
                $stmt_menu = mysqli_prepare($conn, $query_menu);
                if ($stmt_menu) {
                    mysqli_stmt_bind_param($stmt_menu, "s", $seller_id);
                    mysqli_stmt_execute($stmt_menu);
                    $result_menu = mysqli_stmt_get_result($stmt_menu);
                }
            }


            if ($result_menu && mysqli_num_rows($result_menu) > 0) {
                while ($menu = fetch_assoc_db($result_menu)) {
                    $display_harga_original_formatted = number_format($menu['harga'], 0, ',', '.');
                    $final_harga = $menu['harga'];
                    $diskon_text = '';
                    $display_harga_html = '';

                    if ($menu['persentase_disko'] > 0) {
                        $final_harga = $menu['harga'] * (1 - $menu['persentase_disko'] / 100);
                        $display_harga_final_formatted = number_format($final_harga, 0, ',', '.');
                        $display_harga_html = '<span class="line-through text-gray-500">Rp ' . $display_harga_original_formatted . '</span> <span class="text-green-600">Rp ' . $display_harga_final_formatted . '</span>';
                        $diskon_text = ' <span class="text-xs bg-red-200 text-red-800 px-2 py-0.5 rounded-full">' . htmlspecialchars($menu['persentase_disko']) . '% OFF</span>';
                    } else {
                        $display_harga_html = 'Rp ' . $display_harga_original_formatted;
                    }

                    echo '<div class="bg-white p-5 rounded-xl shadow-md transform hover:scale-105 transition-all duration-300 flex flex-col justify-between">';
                    echo '<div>';
                    echo '<h4 class="text-lg font-bold text-gray-800 mb-1">' . htmlspecialchars($menu['nama_menu']) . ' ' . $diskon_text . '</h4>';
                    echo '<p class="text-sm text-gray-600 mb-2">' . htmlspecialchars($menu['nama_kantin']) . '</p>';
                    echo '<p class="text-gray-700 text-base font-semibold">' . $display_harga_html . '</p>';
                    echo '<p class="text-gray-500 text-xs mt-2">' . htmlspecialchars($menu['deskripsi'] ?? 'Tidak ada deskripsi.') . '</p>';
                    echo '</div>';

                    if ($user_role === 'pembeli' && $menu['status_menu'] === 'Tersedia') {
                        echo '<form method="POST" action="?page=dashboard" class="mt-4">';
                        echo '<input type="hidden" name="menu_id" value="' . htmlspecialchars($menu['id_menu']) . '">';
                        echo '<div class="flex items-center mb-2">';
                        echo '<label for="qty_' . htmlspecialchars($menu['id_menu']) . '" class="block text-gray-700 text-sm font-semibold mr-2">Jumlah:</label>';
                        echo '<input type="number" id="qty_' . htmlspecialchars($menu['id_menu']) . '" name="quantity" min="1" value="1" class="w-20 px-2 py-1 border border-gray-300 rounded-md text-center focus:outline-none focus:ring-1 focus:ring-blue-500">';
                        echo '</div>';
                        echo '<div class="mb-2">';
                        echo '<label for="payment_method_' . htmlspecialchars($menu['id_menu']) . '" class="block text-gray-700 text-sm font-semibold mb-1">Bayar Via:</label>';
                        echo '<select id="payment_method_' . htmlspecialchars($menu['id_menu']) . '" name="payment_method" class="w-full px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500" required>';
                        echo '<option value="">Pilih Metode</option>';
                        echo '<option value="Cash">Cash</option>';
                        echo '<option value="QRIS">QRIS</option>';
                        echo '<option value="Transfer Bank">Transfer Bank</option>';
                        echo '</select>';
                        echo '</div>';
                        echo '<button type="submit" name="place_order_submit" class="w-full bg-blue-600 text-white py-2 rounded-lg font-bold hover:bg-blue-700 transition-colors duration-300">Pesan Sekarang</button>';
                        echo '</form>';
                    } else if ($user_role === 'penjual') {
                        echo '<div class="mt-4">';
                        echo '<span class="text-sm font-semibold text-gray-700">Status: ' . htmlspecialchars($menu['status_menu']) . '</span>';
                        echo '<br><a href="?page=tambah_menu&edit_menu_id=' . htmlspecialchars($menu['id_menu']) . '" class="inline-block mt-2 bg-yellow-500 text-white px-3 py-1 rounded-md hover:bg-yellow-600 transition-colors duration-200 text-xs">Edit Menu</a>';
                        echo '</div>';
                    }
                    echo '</div>';
                }
            } else {
                echo '<p class="text-gray-500">Tidak ada menu yang ditemukan saat ini.</p>';
            }
            ?>
        </div>
    </div>
<?php else: // Pengumuman dan Kegiatan untuk role lain (Guest / Not logged in) ?>
    <!-- Section: Pengumuman -->
    <div class="mb-8">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Pengumuman</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <?php
            $query_announcements = "SELECT judul, konten, tanggal_terbit FROM Pengumuman ORDER BY tanggal_terbit DESC LIMIT 4"; // Ambil 4 pengumuman terbaru
            $result_announcements = query_db($conn, $query_announcements);

            if ($result_announcements && mysqli_num_rows($result_announcements) > 0) {
                while ($announcement = fetch_assoc_db($result_announcements)) {
                    echo '<div class="bg-white p-5 rounded-xl shadow-md">';
                    echo '<div class="flex items-center mb-2">';
                    echo '<span class="text-orange-500 text-2xl mr-3">📢</span>'; // Contoh ikon
                    echo '<h4 class="font-semibold text-gray-800">' . htmlspecialchars($announcement['judul']) . '</h4>';
                    echo '</div>';
                    echo '<p class="text-gray-600 text-sm">' . htmlspecialchars($announcement['konten']) . '</p>';
                    echo '<p class="text-gray-400 text-xs mt-2">Diterbitkan: ' . htmlspecialchars($announcement['tanggal_terbit']) . '</p>';
                    echo '</div>';
                }
            } else {
                echo '<p class="text-gray-500">Tidak ada pengumuman saat ini.</p>';
            }
            ?>
        </div>
    </div>

    <!-- Section: Kegiatan Kampus -->
    <div>
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Kegiatan Kampus</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <?php
            $query_kegiatan = "SELECT nama_kegiatan, deskripsi_kegiatan, tanggal_mulai, tanggal_akhir FROM KegiatanKampus ORDER BY tanggal_mulai DESC LIMIT 4";
            $result_kegiatan = query_db($conn, $query_kegiatan);

            if ($result_kegiatan && mysqli_num_rows($result_kegiatan) > 0) {
                while ($kegiatan = fetch_assoc_db($result_kegiatan)) {
                    echo '<div class="bg-white p-5 rounded-xl shadow-md">';
                    echo '<div class="flex items-center mb-2">';
                    echo '<span class="text-green-500 text-2xl mr-3">🗓️</span>'; // Contoh ikon
                    echo '<h4 class="font-semibold text-gray-800">' . htmlspecialchars($kegiatan['nama_kegiatan']) . '</h4>';
                    echo '</div>';
                    echo '<p class="text-gray-600 text-sm">' . htmlspecialchars($kegiatan['deskripsi_kegiatan'] ?? 'Tidak ada deskripsi.') . '</p>';
                    echo '<p class="text-gray-400 text-xs mt-2">Tanggal: ' . htmlspecialchars($kegiatan['tanggal_mulai']) . ' - ' . htmlspecialchars($kegiatan['tanggal_akhir']) . '</p>';
                    echo '</div>';
                }
            } else {
                echo '<p class="text-gray-500">Tidak ada kegiatan kampus saat ini.</p>';
            }
            ?>
        </div>
    </div>
<?php endif; ?>
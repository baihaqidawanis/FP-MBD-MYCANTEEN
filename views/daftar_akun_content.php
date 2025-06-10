<?php
// views/daftar_akun_content.php
// Di-include oleh index.php, jadi $conn, $_SESSION['user_id'], $_SESSION['role'] sudah tersedia.
?>

<?php
$user_role = $_SESSION['role'] ?? null;
$logged_in_user_id = $_SESSION['user_id'] ?? null;

if ($user_role === 'admin'): // Admin bisa melihat semua akun
    ?>
    <div class="mb-8">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Daftar Akun Pembeli</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php
            $query_pembeli_list = "SELECT nrp, nama, email, nomor_telepon, alamat FROM Pembeli";
            $result_pembeli_list = query_db($conn, $query_pembeli_list);

            if ($result_pembeli_list && mysqli_num_rows($result_pembeli_list) > 0) {
                while ($pembeli = fetch_assoc_db($result_pembeli_list)) {
                    echo '<div class="bg-white p-5 rounded-xl shadow-md">';
                    echo '<h4 class="text-lg font-bold text-gray-800 mb-1">' . htmlspecialchars($pembeli['nama']) . '</h4>';
                    echo '<p class="text-gray-600 text-sm">NRP: ' . htmlspecialchars($pembeli['nrp']) . '</p>';
                    echo '<p class="text-gray-600 text-sm">Email: ' . htmlspecialchars($pembeli['email']) . '</p>';
                    echo '<p class="text-gray-600 text-sm">Telepon: ' . htmlspecialchars($pembeli['nomor_telepon']) . '</p>';
                    echo '<p class="text-gray-600 text-sm">Alamat: ' . htmlspecialchars($pembeli['alamat']) . '</p>';
                    echo '</div>';
                }
            } else {
                echo '<p class="text-gray-500">Tidak ada data pembeli.</p>';
            }
            ?>
        </div>
    </div>

    <div class="mb-8">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Daftar Akun Penjual</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php
            $query_penjual_list = "SELECT id_kantin, nama_kantin, nama_penanggung_jawab, email_kantin, nomor_telepon FROM Penjual";
            $result_penjual_list = query_db($conn, $query_penjual_list);

            if ($result_penjual_list && mysqli_num_rows($result_penjual_list) > 0) {
                while ($penjual = fetch_assoc_db($result_penjual_list)) {
                    echo '<div class="bg-white p-5 rounded-xl shadow-md">';
                    echo '<h4 class="text-lg font-bold text-gray-800 mb-1">' . htmlspecialchars($penjual['nama_kantin']) . '</h4>';
                    echo '<p class="text-gray-600 text-sm">ID Kantin: ' . htmlspecialchars($penjual['id_kantin']) . '</p>';
                    echo '<p class="text-gray-600 text-sm">Penanggung Jawab: ' . htmlspecialchars($penjual['nama_penanggung_jawab']) . '</p>';
                    echo '<p class="text-gray-600 text-sm">Email: ' . htmlspecialchars($penjual['email_kantin']) . '</p>';
                    echo '<p class="text-gray-600 text-sm">Telepon: ' . htmlspecialchars($penjual['nomor_telepon']) . '</p>';
                    echo '</div>';
                }
            } else {
                echo '<p class="text-gray-500">Tidak ada data penjual.</p>';
            }
            ?>
        </div>
    </div>

<?php elseif ($user_role === 'pembeli'): // Pembeli hanya melihat akun sendiri ?>
    <div class="mb-8">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Detail Akun Saya</h3>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <?php
            $query_my_account = "SELECT nrp, nama, email, nomor_telepon, alamat FROM Pembeli WHERE nrp = ? LIMIT 1";
            $stmt_my_account = mysqli_prepare($conn, $query_my_account);
            mysqli_stmt_bind_param($stmt_my_account, "s", $logged_in_user_id);
            mysqli_stmt_execute($stmt_my_account);
            $result_my_account = mysqli_stmt_get_result($stmt_my_account);

            if ($result_my_account && mysqli_num_rows($result_my_account) > 0) {
                $pembeli = fetch_assoc_db($result_my_account);
                echo '<div class="bg-white p-5 rounded-xl shadow-md">';
                echo '<h4 class="text-lg font-bold text-gray-800 mb-1">' . htmlspecialchars($pembeli['nama']) . '</h4>';
                echo '<p class="text-gray-600 text-sm">NRP: ' . htmlspecialchars($pembeli['nrp']) . '</p>';
                echo '<p class="text-gray-600 text-sm">Email: ' . htmlspecialchars($pembeli['email']) . '</p>';
                echo '<p class="text-gray-600 text-sm">Telepon: ' . htmlspecialchars($pembeli['nomor_telepon']) . '</p>';
                echo '<p class="text-gray-600 text-sm">Alamat: ' . htmlspecialchars($pembeli['alamat']) . '</p>';
                echo '</div>';
            } else {
                echo '<p class="text-gray-500">Detail akun Anda tidak ditemukan.</p>';
            }
            ?>
        </div>
    </div>

<?php elseif ($user_role === 'penjual'): // Penjual hanya melihat akun sendiri ?>
    <div class="mb-8">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Detail Akun Kantin Saya</h3>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <?php
            $query_my_account = "SELECT id_kantin, nama_kantin, nama_penanggung_jawab, email_kantin, nomor_telepon FROM Penjual WHERE id_kantin = ? LIMIT 1";
            $stmt_my_account = mysqli_prepare($conn, $query_my_account);
            mysqli_stmt_bind_param($stmt_my_account, "s", $logged_in_user_id);
            mysqli_stmt_execute($stmt_my_account);
            $result_my_account = mysqli_stmt_get_result($stmt_my_account);

            if ($result_my_account && mysqli_num_rows($result_my_account) > 0) {
                $penjual = fetch_assoc_db($result_my_account);
                echo '<div class="bg-white p-5 rounded-xl shadow-md">';
                echo '<h4 class="text-lg font-bold text-gray-800 mb-1">' . htmlspecialchars($penjual['nama_kantin']) . '</h4>';
                echo '<p class="text-gray-600 text-sm">ID Kantin: ' . htmlspecialchars($penjual['id_kantin']) . '</p>';
                echo '<p class="text-gray-600 text-sm">Penanggung Jawab: ' . htmlspecialchars($penjual['nama_penanggung_jawab']) . '</p>';
                echo '<p class="text-gray-600 text-sm">Email: ' . htmlspecialchars($penjual['email_kantin']) . '</p>';
                echo '<p class="text-gray-600 text-sm">Telepon: ' . htmlspecialchars($penjual['nomor_telepon']) . '</p>';
                echo '</div>';
            } else {
                echo '<p class="text-gray-500">Detail akun kantin Anda tidak ditemukan.</p>';
            }
            ?>
        </div>
    </div>

<?php else: // Pengguna belum login atau role tidak dikenal ?>
    <div class="bg-white p-8 rounded-xl shadow-md text-center">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Anda perlu login untuk melihat detail akun.</h3>
        <p class="text-gray-600">Silakan <a href="?mode=login" class="text-blue-600 hover:underline">Login</a> atau
            <a href="?mode=register" class="text-blue-600 hover:underline">Daftar</a>.
        </p>
    </div>
<?php endif; ?>
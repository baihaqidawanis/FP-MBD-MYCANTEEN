<?php
// views/admin_dashboard_content.php
// Di-include oleh index.php, jadi $conn, $user_delete_success, $user_delete_error,
// $announcement_success, $announcement_error, $kegiatan_success, $kegiatan_error sudah tersedia.

// Fungsi untuk mendapatkan total data dari tabel
function get_table_count($conn, $table_name)
{
    $query = "SELECT COUNT(*) AS total FROM $table_name";
    $result = query_db($conn, $query);
    if ($result) {
        $row = fetch_assoc_db($result);
        return $row['total'];
    }
    return 0;
}

// Data statistik untuk dashboard admin
$total_pembeli = get_table_count($conn, 'Pembeli');
$total_penjual = get_table_count($conn, 'Penjual');
$total_admin = get_table_count($conn, 'Admin');
$total_pesanan = get_table_count($conn, 'Pesanan');
$total_menu = get_table_count($conn, 'Menu');
$total_diskon = get_table_count($conn, 'Diskon');
$total_pengumuman = get_table_count($conn, 'Pengumuman');
$total_kegiatan = get_table_count($conn, 'KegiatanKampus'); // Statistik baru

$edit_announcement = null;
if (isset($_GET['edit_announcement_id']) && isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin') {
    $announcement_id_to_edit = (int) $_GET['edit_announcement_id'];
    $query_announcement_to_edit = "SELECT id_pengumuman, judul, konten, tanggal_terbit FROM Pengumuman WHERE id_pengumuman = ? LIMIT 1";
    $stmt_announcement_to_edit = mysqli_prepare($conn, $query_announcement_to_edit);
    mysqli_stmt_bind_param($stmt_announcement_to_edit, "i", $announcement_id_to_edit);
    mysqli_stmt_execute($stmt_announcement_to_edit);
    $result_announcement_to_edit = mysqli_stmt_get_result($stmt_announcement_to_edit);
    $edit_announcement = mysqli_fetch_assoc($result_announcement_to_edit);
}

$edit_kegiatan = null;
if (isset($_GET['edit_kegiatan_id']) && isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin') {
    $kegiatan_id_to_edit = (int) $_GET['edit_kegiatan_id'];
    $query_kegiatan_to_edit = "SELECT id_kegiatan, nama_kegiatan, deskripsi_kegiatan, tanggal_mulai, tanggal_akhir FROM KegiatanKampus WHERE id_kegiatan = ? LIMIT 1";
    $stmt_kegiatan_to_edit = mysqli_prepare($conn, $query_kegiatan_to_edit);
    mysqli_stmt_bind_param($stmt_kegiatan_to_edit, "i", $kegiatan_id_to_edit);
    mysqli_stmt_execute($stmt_kegiatan_to_edit);
    $result_kegiatan_to_edit = mysqli_stmt_get_result($stmt_kegiatan_to_edit);
    $edit_kegiatan = mysqli_fetch_assoc($result_kegiatan_to_edit);
}
?>

<div class="bg-white p-8 rounded-xl shadow-md mb-8">
    <h4 class="text-lg font-bold text-gray-800 mb-4">Ringkasan Sistem</h4>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-blue-100 p-4 rounded-lg flex items-center space-x-3">
            <span class="text-3xl">👥</span>
            <div>
                <p class="text-gray-600 text-sm">Total Pembeli</p>
                <p class="text-xl font-bold text-blue-800"><?php echo $total_pembeli; ?></p>
            </div>
        </div>
        <div class="bg-green-100 p-4 rounded-lg flex items-center space-x-3">
            <span class="text-3xl">🏢</span>
            <div>
                <p class="text-gray-600 text-sm">Total Penjual</p>
                <p class="text-xl font-bold text-green-800"><?php echo $total_penjual; ?></p>
            </div>
        </div>
        <div class="bg-yellow-100 p-4 rounded-lg flex items-center space-x-3">
            <span class="text-3xl">👑</span>
            <div>
                <p class="text-gray-600 text-sm">Total Admin</p>
                <p class="text-xl font-bold text-yellow-800"><?php echo $total_admin; ?></p>
            </div>
        </div>
        <div class="bg-purple-100 p-4 rounded-lg flex items-center space-x-3">
            <span class="text-3xl">🧾</span>
            <div>
                <p class="text-gray-600 text-sm">Total Pesanan</p>
                <p class="text-xl font-bold text-purple-800"><?php echo $total_pesanan; ?></p>
            </div>
        </div>
        <div class="bg-red-100 p-4 rounded-lg flex items-center space-x-3">
            <span class="text-3xl">🍔</span>
            <div>
                <p class="text-gray-600 text-sm">Total Menu</p>
                <p class="text-xl font-bold text-red-800"><?php echo $total_menu; ?></p>
            </div>
        </div>
        <div class="bg-orange-100 p-4 rounded-lg flex items-center space-x-3">
            <span class="text-3xl">🏷️</span>
            <div>
                <p class="text-gray-600 text-sm">Total Diskon</p>
                <p class="text-xl font-bold text-orange-800"><?php echo $total_diskon; ?></p>
            </div>
        </div>
        <div class="bg-indigo-100 p-4 rounded-lg flex items-center space-x-3">
            <span class="text-3xl">📢</span>
            <div>
                <p class="text-gray-600 text-sm">Total Pengumuman</p>
                <p class="text-xl font-bold text-indigo-800"><?php echo $total_pengumuman; ?></p>
            </div>
        </div>
        <div class="bg-pink-100 p-4 rounded-lg flex items-center space-x-3">
            <span class="text-3xl">🗓️</span>
            <div>
                <p class="text-gray-600 text-sm">Total Kegiatan Kampus</p>
                <p class="text-xl font-bold text-pink-800"><?php echo $total_kegiatan; ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Section: Pengelolaan Akun (untuk admin) -->
<div class="bg-white p-8 rounded-xl shadow-md mb-8">
    <h4 class="text-lg font-bold text-gray-800 mb-4">Pengelolaan Akun Pengguna</h4>
    <?php if (isset($user_delete_success) && !empty($user_delete_success)): ?>
        <p class="text-green-500 mb-4"><?php echo $user_delete_success; ?></p>
    <?php endif; ?>
    <?php if (isset($user_delete_error) && !empty($user_delete_error)): ?>
        <p class="text-red-500 mb-4"><?php echo $user_delete_error; ?></p>
    <?php endif; ?>

    <h5 class="font-semibold text-gray-700 mb-2 mt-4">Daftar Pembeli</h5>
    <div class="overflow-x-auto mb-6">
        <table class="min-w-full bg-white rounded-lg shadow-sm">
            <thead class="bg-gray-200">
                <tr>
                    <th class="py-2 px-3 text-left text-xs font-semibold text-gray-600">NRP</th>
                    <th class="py-2 px-3 text-left text-xs font-semibold text-gray-600">Nama</th>
                    <th class="py-2 px-3 text-left text-xs font-semibold text-gray-600">Email</th>
                    <th class="py-2 px-3 text-left text-xs font-semibold text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query_all_pembeli = "SELECT nrp, nama, email FROM Pembeli ORDER BY nama";
                $result_all_pembeli = query_db($conn, $query_all_pembeli);
                if ($result_all_pembeli && mysqli_num_rows($result_all_pembeli) > 0) {
                    while ($pembeli = fetch_assoc_db($result_all_pembeli)) {
                        echo '<tr class="border-b border-gray-200 hover:bg-gray-50">';
                        echo '<td class="py-2 px-3 text-xs text-gray-700">' . htmlspecialchars($pembeli['nrp']) . '</td>';
                        echo '<td class="py-2 px-3 text-xs text-gray-700">' . htmlspecialchars($pembeli['nama']) . '</td>';
                        echo '<td class="py-2 px-3 text-xs text-gray-700">' . htmlspecialchars($pembeli['email']) . '</td>';
                        echo '<td class="py-2 px-3 text-xs text-gray-700">';
                        echo '<form method="POST" action="?page=admin_dashboard" onsubmit="return confirm(\'Anda yakin ingin menghapus akun Pembeli ini? Semua pesanan dan review terkait akan terhapus!\');">';
                        echo '<input type="hidden" name="user_id_to_delete" value="' . htmlspecialchars($pembeli['nrp']) . '">';
                        echo '<input type="hidden" name="user_role_to_delete" value="pembeli">';
                        echo '<button type="submit" name="delete_user_submit" class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 transition-colors duration-200 text-xs">Hapus</button>';
                        echo '</form>';
                        echo '</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="4" class="py-3 text-center text-gray-500">Tidak ada data pembeli.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>

    <h5 class="font-semibold text-gray-700 mb-2 mt-4">Daftar Penjual</h5>
    <div class="overflow-x-auto mb-6">
        <table class="min-w-full bg-white rounded-lg shadow-sm">
            <thead class="bg-gray-200">
                <tr>
                    <th class="py-2 px-3 text-left text-xs font-semibold text-gray-600">ID Kantin</th>
                    <th class="py-2 px-3 text-left text-xs font-semibold text-gray-600">Nama Kantin</th>
                    <th class="py-2 px-3 text-left text-xs font-semibold text-gray-600">Email Kantin</th>
                    <th class="py-2 px-3 text-left text-xs font-semibold text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query_all_penjual = "SELECT id_kantin, nama_kantin, email_kantin FROM Penjual ORDER BY nama_kantin";
                $result_all_penjual = query_db($conn, $query_all_penjual);
                if ($result_all_penjual && mysqli_num_rows($result_all_penjual) > 0) {
                    while ($penjual = fetch_assoc_db($result_all_penjual)) {
                        echo '<tr class="border-b border-gray-200 hover:bg-gray-50">';
                        echo '<td class="py-2 px-3 text-xs text-gray-700">' . htmlspecialchars($penjual['id_kantin']) . '</td>';
                        echo '<td class="py-2 px-3 text-xs text-gray-700">' . htmlspecialchars($penjual['nama_kantin']) . '</td>';
                        echo '<td class="py-2 px-3 text-xs text-gray-700">' . htmlspecialchars($penjual['email_kantin']) . '</td>';
                        echo '<td class="py-2 px-3 text-xs text-gray-700">';
                        echo '<form method="POST" action="?page=admin_dashboard" onsubmit="return confirm(\'Anda yakin ingin menghapus akun Penjual ini? Semua menu, diskon, pesanan terkait, dan review akan terhapus!\');">';
                        echo '<input type="hidden" name="user_id_to_delete" value="' . htmlspecialchars($penjual['id_kantin']) . '">';
                        echo '<input type="hidden" name="user_role_to_delete" value="penjual">';
                        echo '<button type="submit" name="delete_user_submit" class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 transition-colors duration-200 text-xs">Hapus</button>';
                        echo '</form>';
                        echo '</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="4" class="py-3 text-center text-gray-500">Tidak ada data penjual.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Section: Pengelolaan Pengumuman (untuk admin) -->
<div class="bg-white p-8 rounded-xl shadow-md mb-8">
    <h4 class="text-lg font-bold text-gray-800 mb-4">Pengelolaan Pengumuman</h4>
    <?php if (isset($announcement_success) && !empty($announcement_success)): ?>
        <p class="text-green-500 mb-4"><?php echo $announcement_success; ?></p>
    <?php endif; ?>
    <?php if (isset($announcement_error) && !empty($announcement_error)): ?>
        <p class="text-red-500 mb-4"><?php echo $announcement_error; ?></p>
    <?php endif; ?>

    <h5 class="font-semibold text-gray-700 mb-2 mt-4">
        <?php echo $edit_announcement ? 'Edit Pengumuman' : 'Tambah Pengumuman Baru'; ?></h5>
    <form method="POST" action="?page=admin_dashboard">
        <input type="hidden" name="id_pengumuman_update"
            value="<?php echo htmlspecialchars($edit_announcement['id_pengumuman'] ?? ''); ?>">
        <div class="mb-3">
            <label for="judul_announcement" class="block text-gray-700 text-sm font-semibold mb-1">Judul
                Pengumuman</label>
            <input type="text" id="judul_announcement"
                name="judul_announcement<?php echo $edit_announcement ? '_update' : ''; ?>"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Masukkan judul pengumuman"
                value="<?php echo htmlspecialchars($edit_announcement['judul'] ?? ''); ?>" required>
        </div>
        <div class="mb-3">
            <label for="konten_announcement" class="block text-gray-700 text-sm font-semibold mb-1">Konten
                Pengumuman</label>
            <textarea id="konten_announcement"
                name="konten_announcement<?php echo $edit_announcement ? '_update' : ''; ?>" rows="3"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Isi pengumuman lengkap"><?php echo htmlspecialchars($edit_announcement['konten'] ?? ''); ?></textarea>
        </div>
        <div class="mb-6">
            <label for="tanggal_terbit_announcement" class="block text-gray-700 text-sm font-semibold mb-1">Tanggal
                Terbit</label>
            <input type="date" id="tanggal_terbit_announcement"
                name="tanggal_terbit_announcement<?php echo $edit_announcement ? '_update' : ''; ?>"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                value="<?php echo htmlspecialchars($edit_announcement['tanggal_terbit'] ?? date('Y-m-d')); ?>" required>
        </div>
        <button type="submit"
            name="<?php echo $edit_announcement ? 'update_announcement_submit' : 'add_announcement_submit'; ?>"
            class="w-full bg-blue-600 text-white py-3 rounded-lg font-bold hover:bg-blue-700 transition-colors duration-300">
            <?php echo $edit_announcement ? 'Perbarui Pengumuman' : 'Tambahkan Pengumuman'; ?>
        </button>
        <?php if ($edit_announcement): ?>
            <a href="?page=admin_dashboard"
                class="w-full block text-center mt-3 bg-gray-500 text-white py-3 rounded-lg font-bold hover:bg-gray-600 transition-colors duration-300">Batal
                Edit</a>
        <?php endif; ?>
    </form>

    <h5 class="font-semibold text-gray-700 mb-2 mt-8">Daftar Semua Pengumuman</h5>
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white rounded-lg shadow-sm">
            <thead class="bg-gray-200">
                <tr>
                    <th class="py-2 px-3 text-left text-xs font-semibold text-gray-600">ID</th>
                    <th class="py-2 px-3 text-left text-xs font-semibold text-gray-600">Judul</th>
                    <th class="py-2 px-3 text-left text-xs font-semibold text-gray-600">Tanggal Terbit</th>
                    <th class="py-2 px-3 text-left text-xs font-semibold text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query_all_announcements = "SELECT id_pengumuman, judul, tanggal_terbit FROM Pengumuman ORDER BY tanggal_terbit DESC";
                $result_all_announcements = query_db($conn, $query_all_announcements);
                if ($result_all_announcements && mysqli_num_rows($result_all_announcements) > 0) {
                    while ($announcement = fetch_assoc_db($result_all_announcements)) {
                        echo '<tr class="border-b border-gray-200 hover:bg-gray-50">';
                        echo '<td class="py-2 px-3 text-xs text-gray-700">' . htmlspecialchars($announcement['id_pengumuman']) . '</td>';
                        echo '<td class="py-2 px-3 text-xs text-gray-700">' . htmlspecialchars($announcement['judul']) . '</td>';
                        echo '<td class="py-2 px-3 text-xs text-gray-700">' . htmlspecialchars($announcement['tanggal_terbit']) . '</td>';
                        echo '<td class="py-2 px-3 text-xs text-gray-700 flex space-x-2">';
                        echo '<a href="?page=admin_dashboard&edit_announcement_id=' . htmlspecialchars($announcement['id_pengumuman']) . '" class="bg-yellow-500 text-white px-3 py-1 rounded-md hover:bg-yellow-600 transition-colors duration-200 text-xs">Edit</a>';
                        echo '<form method="POST" action="?page=admin_dashboard" onsubmit="return confirm(\'Anda yakin ingin menghapus pengumuman ini?\');">';
                        echo '<input type="hidden" name="id_pengumuman_delete" value="' . htmlspecialchars($announcement['id_pengumuman']) . '">';
                        echo '<button type="submit" name="delete_announcement_submit" class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 transition-colors duration-200 text-xs">Hapus</button>';
                        echo '</form>';
                        echo '</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="4" class="py-3 text-center text-gray-500">Tidak ada pengumuman.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Section: Pengelolaan Kegiatan Kampus (untuk admin) -->
<div class="bg-white p-8 rounded-xl shadow-md mb-8">
    <h4 class="text-lg font-bold text-gray-800 mb-4">Pengelolaan Kegiatan Kampus</h4>
    <?php if (isset($kegiatan_success) && !empty($kegiatan_success)): ?>
        <p class="text-green-500 mb-4"><?php echo $kegiatan_success; ?></p>
    <?php endif; ?>
    <?php if (isset($kegiatan_error) && !empty($kegiatan_error)): ?>
        <p class="text-red-500 mb-4"><?php echo $kegiatan_error; ?></p>
    <?php endif; ?>

    <h5 class="font-semibold text-gray-700 mb-2 mt-4">
        <?php echo $edit_kegiatan ? 'Edit Kegiatan Kampus' : 'Tambah Kegiatan Kampus Baru'; ?></h5>
    <form method="POST" action="?page=admin_dashboard">
        <input type="hidden" name="id_kegiatan_update"
            value="<?php echo htmlspecialchars($edit_kegiatan['id_kegiatan'] ?? ''); ?>">
        <div class="mb-3">
            <label for="nama_kegiatan" class="block text-gray-700 text-sm font-semibold mb-1">Nama Kegiatan</label>
            <input type="text" id="nama_kegiatan" name="nama_kegiatan<?php echo $edit_kegiatan ? '_update' : ''; ?>"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Masukkan nama kegiatan"
                value="<?php echo htmlspecialchars($edit_kegiatan['nama_kegiatan'] ?? ''); ?>" required>
        </div>
        <div class="mb-3">
            <label for="deskripsi_kegiatan" class="block text-gray-700 text-sm font-semibold mb-1">Deskripsi Kegiatan
                (Opsional)</label>
            <textarea id="deskripsi_kegiatan" name="deskripsi_kegiatan<?php echo $edit_kegiatan ? '_update' : ''; ?>"
                rows="3"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Isi deskripsi kegiatan lengkap"><?php echo htmlspecialchars($edit_kegiatan['deskripsi_kegiatan'] ?? ''); ?></textarea>
        </div>
        <div class="mb-3">
            <label for="tanggal_mulai_kegiatan" class="block text-gray-700 text-sm font-semibold mb-1">Tanggal
                Mulai</label>
            <input type="date" id="tanggal_mulai_kegiatan"
                name="tanggal_mulai_kegiatan<?php echo $edit_kegiatan ? '_update' : ''; ?>"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                value="<?php echo htmlspecialchars($edit_kegiatan['tanggal_mulai'] ?? date('Y-m-d')); ?>" required>
        </div>
        <div class="mb-6">
            <label for="tanggal_akhir_kegiatan" class="block text-gray-700 text-sm font-semibold mb-1">Tanggal
                Akhir</label>
            <input type="date" id="tanggal_akhir_kegiatan"
                name="tanggal_akhir_kegiatan<?php echo $edit_kegiatan ? '_update' : ''; ?>"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                value="<?php echo htmlspecialchars($edit_kegiatan['tanggal_akhir'] ?? date('Y-m-d')); ?>" required>
        </div>
        <button type="submit" name="<?php echo $edit_kegiatan ? 'update_kegiatan_submit' : 'add_kegiatan_submit'; ?>"
            class="w-full bg-purple-600 text-white py-3 rounded-lg font-bold hover:bg-purple-700 transition-colors duration-300">
            <?php echo $edit_kegiatan ? 'Perbarui Kegiatan' : 'Tambahkan Kegiatan'; ?>
        </button>
        <?php if ($edit_kegiatan): ?>
            <a href="?page=admin_dashboard"
                class="w-full block text-center mt-3 bg-gray-500 text-white py-3 rounded-lg font-bold hover:bg-gray-600 transition-colors duration-300">Batal
                Edit</a>
        <?php endif; ?>
    </form>

    <h5 class="font-semibold text-gray-700 mb-2 mt-8">Daftar Semua Kegiatan Kampus</h5>
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white rounded-lg shadow-sm">
            <thead class="bg-gray-200">
                <tr>
                    <th class="py-2 px-3 text-left text-xs font-semibold text-gray-600">ID</th>
                    <th class="py-2 px-3 text-left text-xs font-semibold text-gray-600">Nama Kegiatan</th>
                    <th class="py-2 px-3 text-left text-xs font-semibold text-gray-600">Mulai</th>
                    <th class="py-2 px-3 text-left text-xs font-semibold text-gray-600">Akhir</th>
                    <th class="py-2 px-3 text-left text-xs font-semibold text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query_all_kegiatan = "SELECT id_kegiatan, nama_kegiatan, tanggal_mulai, tanggal_akhir FROM KegiatanKampus ORDER BY tanggal_mulai DESC";
                $result_all_kegiatan = query_db($conn, $query_all_kegiatan);
                if ($result_all_kegiatan && mysqli_num_rows($result_all_kegiatan) > 0) {
                    while ($kegiatan = fetch_assoc_db($result_all_kegiatan)) {
                        echo '<tr class="border-b border-gray-200 hover:bg-gray-50">';
                        echo '<td class="py-2 px-3 text-xs text-gray-700">' . htmlspecialchars($kegiatan['id_kegiatan']) . '</td>';
                        echo '<td class="py-2 px-3 text-xs text-gray-700">' . htmlspecialchars($kegiatan['nama_kegiatan']) . '</td>';
                        echo '<td class="py-2 px-3 text-xs text-gray-700">' . htmlspecialchars($kegiatan['tanggal_mulai']) . '</td>';
                        echo '<td class="py-2 px-3 text-xs text-gray-700">' . htmlspecialchars($kegiatan['tanggal_akhir']) . '</td>';
                        echo '<td class="py-2 px-3 text-xs text-gray-700 flex space-x-2">';
                        echo '<a href="?page=admin_dashboard&edit_kegiatan_id=' . htmlspecialchars($kegiatan['id_kegiatan']) . '" class="bg-yellow-500 text-white px-3 py-1 rounded-md hover:bg-yellow-600 transition-colors duration-200 text-xs">Edit</a>';
                        echo '<form method="POST" action="?page=admin_dashboard" onsubmit="return confirm(\'Anda yakin ingin menghapus kegiatan ini?\');">';
                        echo '<input type="hidden" name="id_kegiatan_delete" value="' . htmlspecialchars($kegiatan['id_kegiatan']) . '">';
                        echo '<button type="submit" name="delete_kegiatan_submit" class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 transition-colors duration-200 text-xs">Hapus</button>';
                        echo '</form>';
                        echo '</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="5" class="py-3 text-center text-gray-500">Tidak ada kegiatan kampus.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    // JavaScript untuk menangani URL parameter edit_announcement_id dan edit_kegiatan_id agar form terisi dan scroll
    document.addEventListener('DOMContentLoaded', function () {
        const urlParams = new URLSearchParams(window.location.search);

        const editAnnouncementId = urlParams.get('edit_announcement_id');
        if (editAnnouncementId) {
            document.getElementById('judul_announcement').scrollIntoView({ behavior: 'smooth' });
        }

        const editKegiatanId = urlParams.get('edit_kegiatan_id');
        if (editKegiatanId) {
            document.getElementById('nama_kegiatan').scrollIntoView({ behavior: 'smooth' });
        }
    });
</script>
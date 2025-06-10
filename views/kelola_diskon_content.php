<?php
// views/kelola_diskon_content.php
// Di-include oleh index.php, jadi variabel seperti $conn, $_SESSION['user_id'], $diskon_error, $diskon_success, $diskon_delete_success, $diskon_delete_error sudah tersedia.

$edit_diskon = null;
if (isset($_GET['edit_diskon_id']) && isset($_SESSION['user_id']) && $_SESSION['role'] === 'penjual') {
    $diskon_id_to_edit = (int) $_GET['edit_diskon_id'];
    $seller_id = $_SESSION['user_id'];
    $query_diskon_to_edit = "SELECT id_diskon, nama_diskon, persentase_disko, tanggal_mulai, tanggal_akhir FROM Diskon WHERE id_diskon = ? AND Penjual_id_kantin = ? LIMIT 1";
    $stmt_diskon_to_edit = mysqli_prepare($conn, $query_diskon_to_edit);
    mysqli_stmt_bind_param($stmt_diskon_to_edit, "is", $diskon_id_to_edit, $seller_id);
    mysqli_stmt_execute($stmt_diskon_to_edit);
    $result_diskon_to_edit = mysqli_stmt_get_result($stmt_diskon_to_edit);
    $edit_diskon = mysqli_fetch_assoc($result_diskon_to_edit);
}
?>

<div class="bg-white p-8 rounded-xl shadow-md mb-8">
    <h4 class="text-lg font-bold text-gray-800 mb-4"><?php echo $edit_diskon ? 'Edit Diskon' : 'Tambah Diskon Baru'; ?>
    </h4>
    <?php if (isset($diskon_error) && !empty($diskon_error)): ?>
        <p class="text-red-500 mb-4"><?php echo $diskon_error; ?></p>
    <?php endif; ?>
    <?php if (isset($diskon_success) && !empty($diskon_success)): ?>
        <p class="text-green-500 mb-4"><?php echo $diskon_success; ?></p>
    <?php endif; ?>
    <form method="POST" action="?page=kelola_diskon">
        <input type="hidden" name="id_diskon_update"
            value="<?php echo htmlspecialchars($edit_diskon['id_diskon'] ?? ''); ?>">
        <div class="mb-3">
            <label for="nama_diskon" class="block text-gray-700 text-sm font-semibold mb-1">Nama Diskon</label>
            <input type="text" id="nama_diskon" name="nama_diskon<?php echo $edit_diskon ? '_update' : ''; ?>"
                maxlength="20"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Contoh: Diskon Merdeka"
                value="<?php echo htmlspecialchars($edit_diskon['nama_diskon'] ?? ''); ?>" required>
        </div>
        <div class="mb-3">
            <label for="persentase_disko" class="block text-gray-700 text-sm font-semibold mb-1">Persentase Diskon
                (%)</label>
            <input type="number" id="persentase_disko"
                name="persentase_disko<?php echo $edit_diskon ? '_update' : ''; ?>" min="1" max="100"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Contoh: 10" value="<?php echo htmlspecialchars($edit_diskon['persentase_disko'] ?? ''); ?>"
                required>
        </div>
        <div class="mb-3">
            <label for="tanggal_mulai" class="block text-gray-700 text-sm font-semibold mb-1">Tanggal Mulai</label>
            <input type="date" id="tanggal_mulai" name="tanggal_mulai<?php echo $edit_diskon ? '_update' : ''; ?>"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                value="<?php echo htmlspecialchars($edit_diskon['tanggal_mulai'] ?? ''); ?>" required>
        </div>
        <div class="mb-6">
            <label for="tanggal_akhir" class="block text-gray-700 text-sm font-semibold mb-1">Tanggal Akhir</label>
            <input type="date" id="tanggal_akhir" name="tanggal_akhir<?php echo $edit_diskon ? '_update' : ''; ?>"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                value="<?php echo htmlspecialchars($edit_diskon['tanggal_akhir'] ?? ''); ?>" required>
        </div>
        <button type="submit" name="<?php echo $edit_diskon ? 'update_diskon_submit' : 'add_diskon_submit'; ?>"
            class="w-full bg-purple-600 text-white py-3 rounded-lg font-bold hover:bg-purple-700 transition-colors duration-300">
            <?php echo $edit_diskon ? 'Perbarui Diskon' : 'Tambahkan Diskon'; ?>
        </button>
        <?php if ($edit_diskon): ?>
            <a href="?page=kelola_diskon"
                class="w-full block text-center mt-3 bg-gray-500 text-white py-3 rounded-lg font-bold hover:bg-gray-600 transition-colors duration-300">Batal
                Edit</a>
        <?php endif; ?>
    </form>
</div>

<div class="bg-white p-8 rounded-xl shadow-md mt-8">
    <h4 class="text-lg font-bold text-gray-800 mb-4">Daftar Diskon Anda</h4>
    <?php if (isset($diskon_delete_success) && !empty($diskon_delete_success)): ?>
        <p class="text-green-500 mb-4"><?php echo $diskon_delete_success; ?></p>
    <?php endif; ?>
    <?php if (isset($diskon_delete_error) && !empty($diskon_delete_error)): ?>
        <p class="text-red-500 mb-4"><?php echo $diskon_delete_error; ?></p>
    <?php endif; ?>
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white rounded-lg shadow-sm">
            <thead class="bg-gray-200">
                <tr>
                    <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">ID Diskon</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">Nama Diskon</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">Persentase</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">Mulai</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">Akhir</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $current_seller_id = $_SESSION['user_id'];
                $query_my_discounts = "SELECT id_diskon, nama_diskon, persentase_disko, tanggal_mulai, tanggal_akhir FROM Diskon WHERE Penjual_id_kantin = ?";
                $stmt_my_discounts = mysqli_prepare($conn, $query_my_discounts);
                mysqli_stmt_bind_param($stmt_my_discounts, "s", $current_seller_id);
                mysqli_stmt_execute($stmt_my_discounts);
                $result_my_discounts = mysqli_stmt_get_result($stmt_my_discounts);

                if ($result_my_discounts && mysqli_num_rows($result_my_discounts) > 0) {
                    while ($diskon = fetch_assoc_db($result_my_discounts)) {
                        echo '<tr class="border-b border-gray-200 hover:bg-gray-50">';
                        echo '<td class="py-3 px-4 text-sm text-gray-700">' . htmlspecialchars($diskon['id_diskon']) . '</td>';
                        echo '<td class="py-3 px-4 text-sm text-gray-700">' . htmlspecialchars($diskon['nama_diskon']) . '</td>';
                        echo '<td class="py-3 px-4 text-sm text-gray-700">' . htmlspecialchars($diskon['persentase_disko']) . '%</td>';
                        echo '<td class="py-3 px-4 text-sm text-gray-700">' . htmlspecialchars($diskon['tanggal_mulai']) . '</td>';
                        echo '<td class="py-3 px-4 text-sm text-gray-700">' . htmlspecialchars($diskon['tanggal_akhir']) . '</td>';
                        echo '<td class="py-3 px-4 text-sm text-gray-700 flex space-x-2">';
                        echo '<a href="?page=kelola_diskon&edit_diskon_id=' . htmlspecialchars($diskon['id_diskon']) . '" class="bg-yellow-500 text-white px-3 py-1 rounded-md hover:bg-yellow-600 transition-colors duration-200 text-xs">Edit</a>';
                        echo '<form method="POST" action="?page=kelola_diskon" onsubmit="return confirm(\'Anda yakin ingin menghapus diskon ini? Diskon di menu akan dihapus!\');">';
                        echo '<input type="hidden" name="id_diskon_delete" value="' . htmlspecialchars($diskon['id_diskon']) . '">';
                        echo '<button type="submit" name="delete_diskon_submit" class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 transition-colors duration-200 text-xs">Hapus</button>';
                        echo '</form>';
                        echo '</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="6" class="py-4 text-center text-gray-500">Belum ada diskon yang ditambahkan.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
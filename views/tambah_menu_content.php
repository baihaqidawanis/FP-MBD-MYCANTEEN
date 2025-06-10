<?php
// views/tambah_menu_content.php
// Di-include oleh index.php, jadi variabel seperti $conn, $_SESSION['user_id'], $menu_error, $menu_success, $menu_delete_success, $menu_delete_error sudah tersedia.

$edit_menu = null;
if (isset($_GET['edit_menu_id']) && isset($_SESSION['user_id']) && $_SESSION['role'] === 'penjual') {
    $menu_id_to_edit = trim($_GET['edit_menu_id']);
    $seller_id = $_SESSION['user_id'];
    $query_menu_to_edit = "SELECT id_menu, nama_menu, deskripsi, harga, status_menu, Diskon_id_disk FROM Menu WHERE id_menu = ? AND Penjual_id_kan = ? LIMIT 1";
    $stmt_menu_to_edit = mysqli_prepare($conn, $query_menu_to_edit);
    mysqli_stmt_bind_param($stmt_menu_to_edit, "ss", $menu_id_to_edit, $seller_id);
    mysqli_stmt_execute($stmt_menu_to_edit);
    $result_menu_to_edit = mysqli_stmt_get_result($stmt_menu_to_edit);
    $edit_menu = mysqli_fetch_assoc($result_menu_to_edit);
}
?>

<div class="bg-white p-8 rounded-xl shadow-md mb-8">
    <h4 class="text-lg font-bold text-gray-800 mb-4"><?php echo $edit_menu ? 'Edit Menu' : 'Form Tambah Menu Baru'; ?>
    </h4>
    <?php if (isset($menu_error) && !empty($menu_error)): ?>
        <p class="text-red-500 mb-4"><?php echo $menu_error; ?></p>
    <?php endif; ?>
    <?php if (isset($menu_success) && !empty($menu_success)): ?>
        <p class="text-green-500 mb-4"><?php echo $menu_success; ?></p>
    <?php endif; ?>
    <form method="POST" action="?page=tambah_menu">
        <input type="hidden" name="id_menu_update" value="<?php echo htmlspecialchars($edit_menu['id_menu'] ?? ''); ?>">
        <div class="mb-3">
            <label for="id_menu" class="block text-gray-700 text-sm font-semibold mb-1">ID Menu (maks 3 kar.)</label>
            <input type="text" id="id_menu" name="id_menu" maxlength="3"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Contoh: M01" value="<?php echo htmlspecialchars($edit_menu['id_menu'] ?? ''); ?>" <?php echo $edit_menu ? 'readonly' : 'required'; ?>>
            <?php if ($edit_menu): ?>
                <p class="text-gray-500 text-xs mt-1">ID Menu tidak bisa diubah.</p>
            <?php endif; ?>
        </div>
        <div class="mb-3">
            <label for="nama_menu" class="block text-gray-700 text-sm font-semibold mb-1">Nama Menu</label>
            <input type="text" id="nama_menu" name="nama_menu<?php echo $edit_menu ? '_update' : ''; ?>" maxlength="25"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Masukkan nama menu" value="<?php echo htmlspecialchars($edit_menu['nama_menu'] ?? ''); ?>"
                required>
        </div>
        <div class="mb-3">
            <label for="deskripsi" class="block text-gray-700 text-sm font-semibold mb-1">Deskripsi (Opsional)</label>
            <textarea id="deskripsi" name="deskripsi<?php echo $edit_menu ? '_update' : ''; ?>" rows="2" maxlength="255"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Deskripsi singkat menu"><?php echo htmlspecialchars($edit_menu['deskripsi'] ?? ''); ?></textarea>
        </div>
        <div class="mb-3">
            <label for="harga" class="block text-gray-700 text-sm font-semibold mb-1">Harga (Rp)</label>
            <input type="number" id="harga" name="harga<?php echo $edit_menu ? '_update' : ''; ?>" min="0"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Masukkan harga" value="<?php echo htmlspecialchars($edit_menu['harga'] ?? ''); ?>"
                required>
        </div>
        <div class="mb-3">
            <label for="status_menu" class="block text-gray-700 text-sm font-semibold mb-1">Status Menu</label>
            <select id="status_menu" name="status_menu<?php echo $edit_menu ? '_update' : ''; ?>"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                required>
                <option value="">Pilih Status</option>
                <option value="Tersedia" <?php echo ($edit_menu['status_menu'] ?? '') === 'Tersedia' ? 'selected' : ''; ?>>Tersedia</option>
                <option value="Habis" <?php echo ($edit_menu['status_menu'] ?? '') === 'Habis' ? 'selected' : ''; ?>>Habis
                </option>
                <option value="Pre-Order" <?php echo ($edit_menu['status_menu'] ?? '') === 'Pre-Order' ? 'selected' : ''; ?>>Pre-Order</option>
            </select>
        </div>
        <div class="mb-6">
            <label for="diskon_id_disk" class="block text-gray-700 text-sm font-semibold mb-1">Pilih Diskon
                (Opsional)</label>
            <select id="diskon_id_disk" name="diskon_id_disk<?php echo $edit_menu ? '_update' : ''; ?>"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Tidak Ada Diskon</option>
                <?php
                $current_seller_id = $_SESSION['user_id'];
                $query_diskon = "SELECT id_diskon, nama_diskon, persentase_disko FROM Diskon WHERE Penjual_id_kantin = ?";
                $stmt_diskon = mysqli_prepare($conn, $query_diskon);
                mysqli_stmt_bind_param($stmt_diskon, "s", $current_seller_id);
                mysqli_stmt_execute($stmt_diskon);
                $result_diskon = mysqli_stmt_get_result($stmt_diskon);

                if ($result_diskon && mysqli_num_rows($result_diskon) > 0) {
                    while ($diskon = fetch_assoc_db($result_diskon)) {
                        $selected = ($edit_menu['Diskon_id_disk'] ?? null) == $diskon['id_diskon'] ? 'selected' : '';
                        echo '<option value="' . htmlspecialchars($diskon['id_diskon']) . '" ' . $selected . '>' . htmlspecialchars($diskon['nama_diskon']) . ' (' . htmlspecialchars($diskon['persentase_disko']) . '%)</option>';
                    }
                }
                ?>
            </select>
        </div>
        <button type="submit" name="<?php echo $edit_menu ? 'update_menu_submit' : 'add_menu_submit'; ?>"
            class="w-full bg-blue-600 text-white py-3 rounded-lg font-bold hover:bg-blue-700 transition-colors duration-300">
            <?php echo $edit_menu ? 'Perbarui Menu' : 'Tambahkan Menu'; ?>
        </button>
        <?php if ($edit_menu): ?>
            <a href="?page=tambah_menu"
                class="w-full block text-center mt-3 bg-gray-500 text-white py-3 rounded-lg font-bold hover:bg-gray-600 transition-colors duration-300">Batal
                Edit</a>
        <?php endif; ?>
    </form>
</div>

<div class="bg-white p-8 rounded-xl shadow-md mt-8">
    <h4 class="text-lg font-bold text-gray-800 mb-4">Daftar Menu Kantin Anda</h4>
    <?php if (isset($menu_delete_success) && !empty($menu_delete_success)): ?>
        <p class="text-green-500 mb-4"><?php echo $menu_delete_success; ?></p>
    <?php endif; ?>
    <?php if (isset($menu_delete_error) && !empty($menu_delete_error)): ?>
        <p class="text-red-500 mb-4"><?php echo $menu_delete_error; ?></p>
    <?php endif; ?>
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white rounded-lg shadow-sm">
            <thead class="bg-gray-200">
                <tr>
                    <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">ID Menu</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">Nama Menu</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">Harga</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">Diskon (%)</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">Status</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $current_seller_id = $_SESSION['user_id'];
                $query_my_menus = "
                    SELECT m.id_menu, m.nama_menu, m.harga, m.status_menu, COALESCE(d.persentase_disko, 0) AS persentase_disko
                    FROM Menu m
                    LEFT JOIN Diskon d ON m.Diskon_id_disk = d.id_diskon
                    WHERE m.Penjual_id_kan = ? ORDER BY m.nama_menu";
                $stmt_my_menus = mysqli_prepare($conn, $query_my_menus);
                mysqli_stmt_bind_param($stmt_my_menus, "s", $current_seller_id);
                mysqli_stmt_execute($stmt_my_menus);
                $result_my_menus = mysqli_stmt_get_result($stmt_my_menus);

                if ($result_my_menus && mysqli_num_rows($result_my_menus) > 0) {
                    while ($menu = fetch_assoc_db($result_my_menus)) {
                        echo '<tr class="border-b border-gray-200 hover:bg-gray-50">';
                        echo '<td class="py-3 px-4 text-sm text-gray-700">' . htmlspecialchars($menu['id_menu']) . '</td>';
                        echo '<td class="py-3 px-4 text-sm text-gray-700">' . htmlspecialchars($menu['nama_menu']) . '</td>';
                        echo '<td class="py-3 px-4 text-sm text-gray-700">Rp ' . number_format($menu['harga'], 0, ',', '.') . '</td>';
                        echo '<td class="py-3 px-4 text-sm text-gray-700">' . htmlspecialchars($menu['persentase_disko']) . '%</td>';
                        echo '<td class="py-3 px-4 text-sm text-gray-700">' . htmlspecialchars($menu['status_menu']) . '</td>';
                        echo '<td class="py-3 px-4 text-sm text-gray-700 flex space-x-2">';
                        echo '<a href="?page=tambah_menu&edit_menu_id=' . htmlspecialchars($menu['id_menu']) . '" class="bg-yellow-500 text-white px-3 py-1 rounded-md hover:bg-yellow-600 transition-colors duration-200 text-xs">Edit</a>';
                        echo '<form method="POST" action="?page=tambah_menu" onsubmit="return confirm(\'Anda yakin ingin menghapus menu ini? Ini akan menghapus data yang terkait di Detail Pesanan!\');">';
                        echo '<input type="hidden" name="id_menu_delete" value="' . htmlspecialchars($menu['id_menu']) . '">';
                        echo '<button type="submit" name="delete_menu_submit" class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 transition-colors duration-200 text-xs">Hapus</button>';
                        echo '</form>';
                        echo '</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="6" class="py-4 text-center text-gray-500">Belum ada menu yang ditambahkan.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
<script>
    // JavaScript untuk menangani URL parameter edit_menu_id agar form terisi
    // Ini bisa dioptimalkan lebih lanjut, tapi untuk demo ini cukup.
    const urlParams = new URLSearchParams(window.location.search);
    const editMenuId = urlParams.get('edit_menu_id');

    if (editMenuId) {
        const idMenuInput = document.getElementById('id_menu');
        if (idMenuInput) {
            idMenuInput.value = editMenuId; // Value is already set by PHP, but ensure client-side reflects
        }
    }
</script>
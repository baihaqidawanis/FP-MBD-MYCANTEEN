<?php
session_start(); // Mulai sesi PHP

// Sertakan file koneksi database
include 'koneksi.php'; // Pastikan file koneksi.php berada di direktori yang sama

// Inisialisasi variabel pesan untuk mencegah error "Undefined variable"
$login_error = $register_error = $register_success = '';
$menu_error = $menu_success = $menu_delete_success = $menu_delete_error = '';
$diskon_error = $diskon_success = $diskon_delete_success = $diskon_delete_error = '';
$order_update_error = $order_update_success = '';
$order_success = $order_error = '';
$review_success = $review_error = '';

// Variabel pesan baru untuk admin
$user_delete_success = $user_delete_error = '';
$announcement_success = $announcement_error = '';
$kegiatan_success = $kegiatan_error = '';


// Tentukan mode awal (login, register_pembeli, register_penjual)
$mode = $_GET['mode'] ?? 'login';
if ($mode === 'register') { // Untuk kompatibilitas mundur jika hanya ?mode=register
    $mode = 'register_pembeli';
}

// Tangani semua request POST di satu tempat
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sertakan handler otentikasi jika ada tombol login atau register yang ditekan
    if (isset($_POST['login']) || isset($_POST['register_pembeli_submit']) || isset($_POST['register_penjual_submit'])) {
        include 'handlers/auth_handler.php';
    }
    // Sertakan handler penjual jika ada tombol fitur penjual yang ditekan dan pengguna adalah penjual
    if (
        isset($_POST['add_menu_submit']) || isset($_POST['update_menu_submit']) || isset($_POST['delete_menu_submit']) ||
        isset($_POST['add_diskon_submit']) || isset($_POST['update_diskon_submit']) || isset($_POST['delete_diskon_submit']) ||
        isset($_POST['update_status_submit'])
    ) {
        // Cek kembali peran di sini untuk keamanan
        if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'penjual') {
            include 'handlers/seller_handlers.php';
        } else {
            // Jika tidak diizinkan, arahkan kembali ke dashboard atau halaman error
            header("Location: ?page=dashboard");
            exit();
        }
    }
    // Sertakan handler pembeli jika ada tombol fitur pembeli yang ditekan dan pengguna adalah pembeli
    if (isset($_POST['place_order_submit']) || isset($_POST['submit_review'])) {
        if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'pembeli') {
            include 'handlers/buyer_handlers.php';
        } else {
            // Jika tidak diizinkan, arahkan kembali ke dashboard atau halaman error
            header("Location: ?page=dashboard");
            exit();
        }
    }
    // Sertakan handler admin jika ada tombol fitur admin yang ditekan dan pengguna adalah admin
    if (
        isset($_POST['delete_user_submit']) || isset($_POST['add_announcement_submit']) ||
        isset($_POST['update_announcement_submit']) || isset($_POST['delete_announcement_submit']) ||
        isset($_POST['add_kegiatan_submit']) || isset($_POST['update_kegiatan_submit']) || isset($_POST['delete_kegiatan_submit'])
    ) {
        if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin') {
            include 'handlers/admin_actions_handler.php'; // Inisialisasi variabel pesan di sini
        } else {
            // Jika tidak diizinkan, arahkan kembali ke dashboard atau halaman error
            header("Location: ?page=dashboard");
            exit();
        }
    }
}


// Logika Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: " . $_SERVER['PHP_SELF']); // Redirect ke halaman yang sama setelah logout
    exit();
}

// Cek apakah pengguna sudah login
$is_logged_in = isset($_SESSION['user_id']);
$user_role = $is_logged_in ? $_SESSION['role'] : null;

// Tentukan halaman konten dashboard
$page = $_GET['page'] ?? 'dashboard'; // Default ke 'dashboard'

// --- Mock Data Menu (akan digunakan jika database kosong atau error) ---
$menu_data_fallback = [
    ['nama_menu' => 'Nasi Goreng Spesial (Mock)', 'harga' => 25000],
    ['nama_menu' => 'Ayam Geprek Sambal Ijo (Mock)', 'harga' => 22000],
    ['nama_menu' => 'Es Teh Manis Jumbo (Mock)', 'harga' => 8000],
];

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>MyCanteen - Dashboard</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        /* Hide scrollbar for Chrome, Safari and Opera */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        /* Hide scrollbar for IE, Edge and Firefox */
        .no-scrollbar {
            -ms-overflow-style: none;
            /* IE and Edge */
            scrollbar-width: none;
            /* Firefox */
        }
    </style>
</head>

<body class="bg-gray-100 flex h-screen">

    <?php if (!$is_logged_in): ?>
        <!-- Login/Register Forms Container -->
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center z-50">
            <div class="bg-white p-8 rounded-xl shadow-2xl w-96">
                <?php
                // Sertakan form login/register berdasarkan mode
                include 'views/login_register_form.php';
                ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Sidebar -->
    <div class="fixed inset-y-0 left-0 w-64 bg-gray-800 text-white p-6 flex flex-col justify-between
                 md:w-64 sm:w-20 sm:p-2 sm:overflow-hidden no-scrollbar <?php echo !$is_logged_in ? 'hidden' : ''; ?>">
        <div>
            <h2 class="text-2xl font-bold text-center mb-10 hidden sm:block md:block">MyCanteen</h2>
            <h2 class="text-2xl font-bold text-center mb-10 block sm:hidden md:hidden">MC</h2>
            <ul class="space-y-4">
                <li class="flex items-center p-3 rounded-lg hover:bg-gray-700 cursor-pointer transition-colors duration-200 <?php echo $page === 'dashboard' ? 'bg-gray-700' : ''; ?>"
                    onclick="window.location.href='?page=dashboard'">
                    <span class="text-xl mr-3">🏠</span> <span class="hidden md:block">Beranda</span>
                </li>
                <?php if ($user_role !== 'admin'): // Daftar Akun hanya untuk non-admin di sidebar utama ?>
                    <li class="flex items-center p-3 rounded-lg hover:bg-gray-700 cursor-pointer transition-colors duration-200 <?php echo $page === 'daftar_akun' ? 'bg-gray-700' : ''; ?>"
                        onclick="window.location.href='?page=daftar_akun'">
                        <span class="text-xl mr-3">👥</span> <span class="hidden md:block">Daftar Akun</span>
                    </li>
                <?php endif; ?>
                <?php if ($user_role === 'pembeli'): ?>
                    <li class="flex items-center p-3 rounded-lg hover:bg-gray-700 cursor-pointer transition-colors duration-200 <?php echo $page === 'my_orders' ? 'bg-gray-700' : ''; ?>"
                        onclick="window.location.href='?page=my_orders'">
                        <span class="text-xl mr-3">🛒</span> <span class="hidden md:block">Pesanan Saya</span>
                    </li>
                <?php endif; ?>
                <?php if ($user_role === 'penjual'): ?>
                    <li class="flex items-center p-3 rounded-lg hover:bg-gray-700 cursor-pointer transition-colors duration-200 <?php echo $page === 'pesanan' ? 'bg-gray-700' : ''; ?>"
                        onclick="console.log('Pesanan diklik'); window.location.href='?page=pesanan'">
                        <span class="text-xl mr-3">🧾</span> <span class="hidden md:block">Pesanan Masuk</span>
                    </li>
                    <li class="flex items-center p-3 rounded-lg hover:bg-gray-700 cursor-pointer transition-colors duration-200 <?php echo $page === 'tambah_menu' ? 'bg-gray-700' : ''; ?>"
                        onclick="console.log('Tambah Menu diklik'); window.location.href='?page=tambah_menu'">
                        <span class="text-xl mr-3">➕</span> <span class="hidden md:block">Kelola Menu</span>
                    </li>
                    <li class="flex items-center p-3 rounded-lg hover:bg-gray-700 cursor-pointer transition-colors duration-200 <?php echo $page === 'kelola_diskon' ? 'bg-gray-700' : ''; ?>"
                        onclick="console.log('Kelola Diskon diklik'); window.location.href='?page=kelola_diskon'">
                        <span class="text-xl mr-3">🏷️</span> <span class="hidden md:block">Kelola Diskon</span>
                    </li>
                    <!-- Menu baru untuk Penjual: Ulasan Masuk -->
                    <li class="flex items-center p-3 rounded-lg hover:bg-gray-700 cursor-pointer transition-colors duration-200 <?php echo $page === 'seller_reviews' ? 'bg-gray-700' : ''; ?>"
                        onclick="window.location.href='?page=seller_reviews'">
                        <span class="text-xl mr-3">⭐</span> <span class="hidden md:block">Ulasan Masuk</span>
                    </li>
                <?php endif; ?>
                <?php if ($user_role === 'admin'): ?>
                    <li class="flex items-center p-3 rounded-lg hover:bg-gray-700 cursor-pointer transition-colors duration-200 <?php echo $page === 'admin_dashboard' ? 'bg-gray-700' : ''; ?>"
                        onclick="window.location.href='?page=admin_dashboard'">
                        <span class="text-xl mr-3">👑</span> <span class="hidden md:block">Admin Dashboard</span>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
        <div class="mb-4">
            <li class="flex items-center p-3 rounded-lg hover:bg-gray-700 cursor-pointer transition-colors duration-200 list-none"
                onclick="window.location.href='?logout=true'">
                <span class="text-xl mr-3">🚪</span> <span class="hidden md:block">Keluar</span>
            </li>
        </div>
    </div>

    <!-- Main Content -->
    <div
        class="flex-1 p-8 overflow-y-auto no-scrollbar <?php echo !$is_logged_in ? 'hidden' : 'ml-64 sm:ml-20 md:ml-64'; ?>">
        <!-- Top Bar -->
        <div
            class="flex flex-col md:flex-row justify-between items-start md:items-center bg-white p-6 rounded-xl shadow-md mb-8">
            <div class="flex-1">
                <h1 class="text-3xl font-extrabold text-gray-800 mb-2 md:mb-0">
                    <?php
                    // Judul dinamis berdasarkan halaman
                    switch ($page) {
                        case 'dashboard':
                            echo 'Dashboard';
                            break;
                        case 'daftar_akun':
                            echo 'Daftar Akun';
                            break;
                        case 'my_orders':
                            echo 'Pesanan Saya';
                            break;
                        case 'pesanan': // Untuk penjual
                            echo 'Pesanan Masuk';
                            break;
                        case 'tambah_menu':
                            echo 'Kelola Menu Kantin';
                            break;
                        case 'kelola_diskon':
                            echo 'Kelola Diskon Kantin';
                            break;
                        case 'seller_reviews':
                            echo 'Ulasan Masuk'; // Judul baru
                            break;
                        case 'admin_dashboard':
                            echo 'Admin Dashboard';
                            break;
                        default:
                            echo 'Dashboard';
                            break;
                    }
                    ?>
                </h1>
                <p class="text-gray-500">Selamat datang kembali, <span
                        class="font-semibold text-blue-600"><?php echo htmlspecialchars($_SESSION['username'] ?? 'Tamu'); ?></span>!
                </p>
            </div>
            <div class="mt-4 md:mt-0 flex items-center space-x-4 w-full md:w-auto">
                <div class="relative flex-grow">
                    <input type="text" placeholder="Cari..."
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">🔍</span>
                </div>
                <div class="hidden md:block bg-blue-100 text-blue-800 px-4 py-2 rounded-lg font-medium">
                    <?php echo date('l, d F Y'); ?>
                </div>
            </div>
        </div>

        <?php
        // Load konten berdasarkan halaman yang diminta
        switch ($page) {
            case 'dashboard':
                include 'views/dashboard_content.php';
                break;
            case 'daftar_akun':
                include 'views/daftar_akun_content.php';
                break;
            case 'my_orders':
                if ($user_role === 'pembeli') {
                    include 'views/my_orders_content.php';
                } else {
                    include 'views/access_denied.php';
                }
                break;
            case 'pesanan': // Ini untuk 'Pesanan Masuk' (penjual)
                if ($user_role === 'penjual') {
                    include 'views/pesanan_penjual_content.php';
                } else {
                    // Akses ditolak jika bukan penjual
                    include 'views/access_denied.php';
                }
                break;
            case 'tambah_menu':
                if ($user_role === 'penjual') {
                    include 'views/tambah_menu_content.php';
                } else {
                    include 'views/access_denied.php';
                }
                break;
            case 'kelola_diskon':
                if ($user_role === 'penjual') {
                    include 'views/kelola_diskon_content.php';
                } else {
                    include 'views/access_denied.php';
                }
                break;
            case 'seller_reviews': // Case baru untuk penjual
                if ($user_role === 'penjual') {
                    include 'views/seller_reviews_content.php';
                } else {
                    include 'views/access_denied.php';
                }
                break;
            case 'admin_dashboard':
                if ($user_role === 'admin') {
                    include 'views/admin_dashboard_content.php';
                } else {
                    include 'views/access_denied.php';
                }
                break;
            default:
                include 'views/access_denied.php'; // Default ke halaman akses ditolak jika halaman tidak dikenali
                break;
        }
        ?>
    </div>
</body>

</html>
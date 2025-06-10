<?php
// views/login_register_form.php
// Di-include oleh index.php, jadi $mode, $login_error, $register_error, $register_success sudah tersedia.
?>

<div class="relative w-full h-full">
    <?php if (isset($login_error) && !empty($login_error)): ?>
        <p class="text-red-500 text-center mb-4"><?php echo $login_error; ?></p>
    <?php endif; ?>
    <?php if (isset($register_error) && !empty($register_error)): ?>
        <p class="text-red-500 text-center mb-4"><?php echo $register_error; ?></p>
    <?php endif; ?>
    <?php if (isset($register_success) && !empty($register_success)): ?>
        <p class="text-green-500 text-center mb-4"><?php echo $register_success; ?></p>
    <?php endif; ?>

    <div id="login-form" class="<?php echo ($mode === 'login') ? 'block' : 'hidden'; ?>">
        <h3 class="text-2xl font-bold text-gray-800 mb-6 text-center">Login MyCanteen</h3>
        <form method="POST" action="?mode=login">
            <div class="mb-4">
                <label for="username" class="block text-gray-700 text-sm font-semibold mb-2">NRP / Email Kantin</label>
                <input type="text" id="username" name="username"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Masukkan NRP atau Email Kantin" required>
            </div>
            <div class="mb-6">
                <label for="password" class="block text-gray-700 text-sm font-semibold mb-2">Password</label>
                <input type="password" id="password" name="password"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Masukkan password" required>
            </div>
            <button type="submit" name="login"
                class="w-full bg-blue-600 text-white py-2 rounded-lg font-bold hover:bg-blue-700 transition-colors duration-300">Login</button>
        </form>
        <p class="text-center text-gray-600 text-sm mt-4">Belum punya akun?
            <a href="#" class="text-blue-600 hover:underline font-semibold" onclick="showRegisterOptions()">Daftar
                di sini</a>
        </p>
    </div>

    <div id="register-options"
        class="<?php echo ($mode === 'register_pembeli' || $mode === 'register_penjual') ? 'block' : 'hidden'; ?>">
        <h3 class="text-2xl font-bold text-gray-800 mb-6 text-center">Daftar Akun</h3>
        <p class="text-center text-gray-600 mb-4">Daftar sebagai:</p>
        <div class="flex flex-col space-y-4">
            <button type="button" onclick="showRegisterPembeli()"
                class="w-full bg-green-600 text-white py-2 rounded-lg font-bold hover:bg-green-700 transition-colors duration-300">Pembeli</button>
            <button type="button" onclick="showRegisterPenjual()"
                class="w-full bg-purple-600 text-white py-2 rounded-lg font-bold hover:bg-purple-700 transition-colors duration-300">Penjual</button>
        </div>
        <p class="text-center text-gray-600 text-sm mt-4">Sudah punya akun?
            <a href="#" class="text-blue-600 hover:underline font-semibold" onclick="showLoginForm()">Login
                di sini</a>
        </p>
    </div>

    <div id="register-pembeli-form" class="<?php echo ($mode === 'register_pembeli') ? 'block' : 'hidden'; ?>">
        <h3 class="text-2xl font-bold text-gray-800 mb-6 text-center">Daftar Akun Pembeli</h3>
        <form method="POST" action="?mode=register_pembeli">
            <div class="mb-3">
                <label for="nama_pembeli" class="block text-gray-700 text-sm font-semibold mb-1">Nama Lengkap</label>
                <input type="text" id="nama_pembeli" name="nama"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Masukkan nama lengkap" required>
            </div>
            <div class="mb-3">
                <label for="nrp_pembeli" class="block text-gray-700 text-sm font-semibold mb-1">NRP (maks 10
                    kar.)</label>
                <input type="text" id="nrp_pembeli" name="nrp" maxlength="10"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Masukkan NRP" required>
            </div>
            <div class="mb-3">
                <label for="email_pembeli" class="block text-gray-700 text-sm font-semibold mb-1">Email</label>
                <input type="email" id="email_pembeli" name="email"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Masukkan email" required>
            </div>
            <div class="mb-3">
                <label for="password_pembeli" class="block text-gray-700 text-sm font-semibold mb-1">Password</label>
                <input type="password" id="password_pembeli" name="password"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Masukkan password" required>
            </div>
            <div class="mb-3">
                <label for="nomor_telepon_pembeli" class="block text-gray-700 text-sm font-semibold mb-1">Nomor
                    Telepon</label>
                <input type="text" id="nomor_telepon_pembeli" name="nomor_telepon"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Masukkan nomor telepon" required>
            </div>
            <div class="mb-6">
                <label for="alamat_pembeli" class="block text-gray-700 text-sm font-semibold mb-1">Alamat</label>
                <textarea id="alamat_pembeli" name="alamat" rows="2"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Masukkan alamat lengkap" required></textarea>
            </div>
            <button type="submit" name="register_pembeli_submit"
                class="w-full bg-green-600 text-white py-2 rounded-lg font-bold hover:bg-green-700 transition-colors duration-300">Daftar
                sebagai Pembeli</button>
        </form>
        <p class="text-center text-gray-600 text-sm mt-4">
            <a href="#" class="text-blue-600 hover:underline font-semibold" onclick="showRegisterOptions()">Kembali
                ke pilihan daftar</a>
        </p>
    </div>

    <div id="register-penjual-form" class="<?php echo ($mode === 'register_penjual') ? 'block' : 'hidden'; ?>">
        <h3 class="text-2xl font-bold text-gray-800 mb-6 text-center">Daftar Akun Penjual</h3>
        <form method="POST" action="?mode=register_penjual">
            <div class="mb-3">
                <label for="id_kantin" class="block text-gray-700 text-sm font-semibold mb-1">ID Kantin (maks 4
                    kar.)</label>
                <input type="text" id="id_kantin" name="id_kantin" maxlength="4"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Contoh: K001" required>
            </div>
            <div class="mb-3">
                <label for="nama_kantin" class="block text-gray-700 text-sm font-semibold mb-1">Nama Kantin</label>
                <input type="text" id="nama_kantin" name="nama_kantin" maxlength="30"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Masukkan nama kantin" required>
            </div>
            <div class="mb-3">
                <label for="nama_penanggung_jawab" class="block text-gray-700 text-sm font-semibold mb-1">Nama
                    Penanggung Jawab</label>
                <input type="text" id="nama_penanggung_jawab" name="nama_penanggung_jawab" maxlength="100"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Masukkan nama penanggung jawab" required>
            </div>
            <div class="mb-3">
                <label for="email_kantin" class="block text-gray-700 text-sm font-semibold mb-1">Email Kantin</label>
                <input type="email" id="email_kantin" name="email_kantin"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Masukkan email kantin" required>
            </div>
            <div class="mb-3">
                <label for="password_kantin" class="block text-gray-700 text-sm font-semibold mb-1">Password</label>
                <input type="password" id="password_kantin" name="password_kantin"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Masukkan password" required>
            </div>
            <div class="mb-6">
                <label for="nomor_telepon_kantin" class="block text-gray-700 text-sm font-semibold mb-1">Nomor Telepon
                    Kantin</label>
                <input type="text" id="nomor_telepon_kantin" name="nomor_telepon"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Masukkan nomor telepon kantin" required>
            </div>
            <button type="submit" name="register_penjual_submit"
                class="w-full bg-purple-600 text-white py-2 rounded-lg font-bold hover:bg-purple-700 transition-colors duration-300">Daftar
                sebagai Penjual</button>
        </form>
        <p class="text-center text-gray-600 text-sm mt-4">
            <a href="#" class="text-blue-600 hover:underline font-semibold" onclick="showRegisterOptions()">Kembali
                ke pilihan daftar</a>
        </p>
    </div>
</div>

<script>
    function hideAllForms() {
        document.getElementById('login-form').classList.add('hidden');
        document.getElementById('register-options').classList.add('hidden');
        document.getElementById('register-pembeli-form').classList.add('hidden');
        document.getElementById('register-penjual-form').classList.add('hidden');
    }

    function showLoginForm() {
        hideAllForms();
        document.getElementById('login-form').classList.remove('hidden');
        // Update URL to reflect mode, without reloading
        window.history.pushState(null, '', '?mode=login');
    }

    function showRegisterOptions() {
        hideAllForms();
        document.getElementById('register-options').classList.remove('hidden');
        // Update URL to reflect mode, without reloading
        window.history.pushState(null, '', '?mode=register'); // Simplified to 'register' for general options
    }

    function showRegisterPembeli() {
        hideAllForms();
        document.getElementById('register-pembeli-form').classList.remove('hidden');
        // Update URL to reflect mode, without reloading
        window.history.pushState(null, '', '?mode=register_pembeli');
    }

    function showRegisterPenjual() {
        hideAllForms();
        document.getElementById('register-penjual-form').classList.remove('hidden');
        // Update URL to reflect mode, without reloading
        window.history.pushState(null, '', '?mode=register_penjual');
    }

    // Initial form display based on PHP $mode
    document.addEventListener('DOMContentLoaded', function () {
        const initialMode = "<?php echo $mode; ?>";
        if (initialMode === 'register_pembeli') {
            showRegisterPembeli();
        } else if (initialMode === 'register_penjual') {
            showRegisterPenjual();
        } else if (initialMode === 'register') { // Fallback for general register mode URL
            showRegisterOptions();
        } else {
            showLoginForm();
        }
    });

    // Handle back/forward browser button for form display
    window.addEventListener('popstate', function (event) {
        const urlParams = new URLSearchParams(window.location.search);
        const currentMode = urlParams.get('mode') || 'login';
        if (currentMode === 'register_pembeli') {
            showRegisterPembeli();
        } else if (currentMode === 'register_penjual') {
            showRegisterPenjual();
        } else if (currentMode === 'register') {
            showRegisterOptions();
        } else {
            showLoginForm();
        }
    });
</script>
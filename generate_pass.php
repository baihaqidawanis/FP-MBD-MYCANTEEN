<?php
$password_plain = '123'; // Ganti dengan password yang Anda inginkan untuk admin
$hashed_password = password_hash($password_plain, PASSWORD_DEFAULT);
echo "Password Anda: " . $password_plain . "<br>";
echo "Hash Password (salin ini ke database): <span style='font-weight:bold; color: blue;'>" . $hashed_password . "</span>";
?>
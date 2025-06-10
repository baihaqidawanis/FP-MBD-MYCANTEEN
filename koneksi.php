<?php
// koneksi.php

$db_host = 'localhost'; // Ganti jika host database Anda berbeda
$db_user = 'root';      // Ganti dengan username database Anda
$db_pass = '';          // Ganti dengan password database Anda
$db_name = 'mycanteen'; // Ganti dengan nama database Anda

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// Cek koneksi
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Fungsi helper untuk menjalankan query dan mengembalikan hasilnya
function query_db($conn, $query)
{
    $result = mysqli_query($conn, $query);
    if (!$result) {
        //echo "Error query: " . mysqli_error($conn); // Debugging: tampilkan error query
        return false;
    }
    return $result;
}

// Fungsi helper untuk mengambil satu baris hasil sebagai array asosiatif
function fetch_assoc_db($result)
{
    return mysqli_fetch_assoc($result);
}

// Fungsi helper untuk menjalankan prepared statement
function execute_prepared_stmt($conn, $query, $param_types, $params)
{
    $stmt = mysqli_prepare($conn, $query);
    if ($stmt === false) {
        //echo "Error preparing statement: " . mysqli_error($conn); // Debugging
        return false;
    }
    // Bind parameters dynamically
    mysqli_stmt_bind_param($stmt, $param_types, ...$params);
    $exec = mysqli_stmt_execute($stmt);
    if ($exec === false) {
        //echo "Error executing statement: " . mysqli_stmt_error($stmt); // Debugging
        mysqli_stmt_close($stmt);
        return false;
    }
    return $stmt; // Return statement object for fetching results
}

?>
<?php
$conn = mysqli_connect("localhost", "root", "", "db_sniffing_kasbon");
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

define('KUNCI_KASBON', 'KASBON-WARUNG-2026');

function enkripsi($teks) {
    $iv = openssl_random_pseudo_bytes(16);
    $cipher = openssl_encrypt($teks, 'AES-256-CBC', hash('sha256', KUNCI_KASBON, true), OPENSSL_RAW_DATA, $iv);
    return base64_encode($iv . $cipher);
}

function dekripsi($dataBase64) {
    $data = base64_decode($dataBase64);
    $iv = substr($data, 0, 16);
    $cipher = substr($data, 16);
    return openssl_decrypt($cipher, 'AES-256-CBC', hash('sha256', KUNCI_KASBON, true), OPENSSL_RAW_DATA, $iv);
}
?>
<?php
$host = "localhost";
$user = "root"; // Ganti sesuai user database
$pass = ""; // Ganti jika ada password
$db = "db_pos";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>

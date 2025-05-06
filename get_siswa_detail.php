<?php
include 'koneksi.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Pastikan ID adalah angka untuk keamanan
    $query = "SELECT * FROM siswa WHERE id = '$id'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
        echo json_encode($data); // Kirim data dalam format JSON
    } else {
        echo json_encode(["error" => "Data tidak ditemukan"]);
    }
}
?>

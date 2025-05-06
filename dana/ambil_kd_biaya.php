<?php
include 'koneksi.php';

$term = $_GET['term'] ?? '';

$data = [];
$result = mysqli_query($conn, "SELECT kd_biaya FROM tb_kd_biaya WHERE kd_biaya LIKE '$term%' GROUP BY kd_biaya");

while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row['kd_biaya'];
}

echo json_encode($data);
?>

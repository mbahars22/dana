<?php
include 'koneksi.php';


$q = $_GET['q'] ?? '';

$sql = "SELECT nis, nama, kelas, thajaran FROM siswa WHERE nis LIKE '%$q%' OR nama LIKE '%$q%' OR kelas LIKE '%$q%' LIMIT 10";
$result = $conn->query($sql);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

header('Content-Type: application/json');
echo json_encode($data);
?>


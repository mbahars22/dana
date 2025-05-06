<?php
include 'koneksi.php'; // pastikan ini mengarah ke file koneksi lo

$q = mysqli_query($conn, "SELECT logo FROM tb_profile LIMIT 1");
$d = mysqli_fetch_assoc($q);

$filename = $d['logo'] ?? 'logo-default.png';
$filepath = 'uploads/' . $filename;

if (!file_exists($filepath)) {
    $filepath = 'uploads/logo-default.png'; // fallback
}

$ext = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));

switch ($ext) {
    case 'png':
        $src = imagecreatefrompng($filepath);
        break;
    case 'jpg':
    case 'jpeg':
        $src = imagecreatefromjpeg($filepath);
        break;
    default:
        // fallback image default
        $src = imagecreatefrompng('uploads/logo-default.png');
}

$icon = imagecreatetruecolor(32, 32);
imagecopyresampled($icon, $src, 0, 0, 0, 0, 32, 32, imagesx($src), imagesy($src));

header('Content-Type: image/png'); // browser modern support png as favicon
imagepng($icon);

imagedestroy($icon);
imagedestroy($src);

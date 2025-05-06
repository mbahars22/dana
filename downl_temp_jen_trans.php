<?php
$filepath = 'jenis_trans.xlsx';

if (file_exists($filepath)) {
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="jenis_transaksi.xlsx"');
    header('Content-Length: ' . filesize($filepath));
    readfile($filepath);
    exit;
} else {
    echo "<script>alert('File tidak ditemukan!'); window.history.back();</script>";
}
?>

<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validasi awal
    if (!isset($_POST['kd_biaya']) || !isset($_POST['jumlah'])) {
        die("Error: Data biaya tidak ditemukan!");
    }

    // Ambil data utama
    $nis        = mysqli_real_escape_string($conn, $_POST['nis']);
    $nama       = mysqli_real_escape_string($conn, $_POST['nama']);
    $thajaran   = mysqli_real_escape_string($conn, $_POST['thajaran']);
    $kd_biaya_arr = $_POST['kd_biaya'];
    $jumlah_arr   = $_POST['jumlah'];

    // Ambil rombel dari tabel siswa
    $getRombel = mysqli_query($conn, "SELECT rombel FROM siswa WHERE nis = '$nis'");
    $dataRombel = mysqli_fetch_assoc($getRombel);
    $kelas = $dataRombel ? mysqli_real_escape_string($conn, $dataRombel['rombel']) : '';

    // Validasi array biaya
    if (empty($kd_biaya_arr) || empty($jumlah_arr)) {
        die("Error: Tidak ada data biaya yang dikirimkan!");
    }

    $success = false;

    // Loop simpan data per jenis biaya
    foreach ($kd_biaya_arr as $index => $kd_biaya) {
        $jumlah = $jumlah_arr[$index];
    
        if (!empty($kd_biaya) && $jumlah != '') {
            $kd_biaya_sanitized = mysqli_real_escape_string($conn, $kd_biaya);
            $jumlah_sanitized   = mysqli_real_escape_string($conn, $jumlah);
    
            // Simpan/replace
            $query = "INSERT INTO biaya_siswa (nis, nama, kelas, kd_biaya, jumlah, thajaran)
                      VALUES ('$nis', '$nama', '$kelas', '$kd_biaya_sanitized', '$jumlah_sanitized', '$thajaran')
                      ON DUPLICATE KEY UPDATE 
                          nama = VALUES(nama),
                          kelas = VALUES(kelas),
                          jumlah = VALUES(jumlah)";
    
            if (mysqli_query($conn, $query)) {
                $success = true;
            }
        }
    }
    

    // Redirect jika berhasil
    if ($success) {
        echo "<script>alert('Data biaya berhasil disimpan atau diperbarui!'); window.location.href='input_biaya.php';</script>";
        exit();
    } else {
        echo "<script>alert('Tidak ada data yang berhasil disimpan!'); window.location.href='input_biaya.php';</script>";
        exit();
    }
} else {
    die("Akses ditolak!");
}
?>

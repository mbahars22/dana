<!DOCTYPE html>
<html lang="id">
<?php
require '../functions.php';

// Pastikan koneksi database tersedia
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

$nis = $_POST['nis'];
$nama = $_POST['nama_anggota'];
$kelas = $_POST['kelas'];
$kd_biaya = $_POST['kd_biaya'];
$thajaran = $_POST['thajaran'];
$jumlah = $_POST['jumlah'];
$bayar = $_POST['bayar']; 
$kd_transaksi = $_POST['kd_transaksi'];
$tgl_trans = $_POST['tanggal'];
$username = $_POST['user'];
$method = $_POST['method'];

$total = count($nis);

for ($i = 0; $i < $total; $i++) {
    // Sanitasi input
    $username_sanitized = mysqli_real_escape_string($conn, $username[$i]);
    $nis_sanitized = mysqli_real_escape_string($conn, $nis[$i]);
    $nama_sanitized = mysqli_real_escape_string($conn, $nama[$i]);
    $kelas_sanitized = mysqli_real_escape_string($conn, $kelas[$i]);
    $kd_biaya_sanitized = mysqli_real_escape_string($conn, $kd_biaya[$i]);
    $thajaran_sanitized = mysqli_real_escape_string($conn, $thajaran[$i]);
    // Hilangkan titik pada jumlah dan bayar sebelum konversi ke integer
    $jumlah_sanitized = intval(str_replace('.', '', $jumlah[$i])); 
    $bayar_sanitized = intval(str_replace('.', '', $bayar[$i]));
    
    $kd_transaksi_sanitized = mysqli_real_escape_string($conn, $kd_transaksi[$i]);
    $tgl_trans_sanitized = mysqli_real_escape_string($conn, $tgl_trans[$i]);
    $method_sanitized = mysqli_real_escape_string($conn, $method[$i]);

    // Ambil jumlah biaya saat ini dari database
    $query_check = "SELECT jumlah FROM biaya_siswa WHERE nis = ? AND kd_biaya = ?";
    $stmt_check = mysqli_prepare($conn, $query_check);
    mysqli_stmt_bind_param($stmt_check, "ss", $nis_sanitized, $kd_biaya_sanitized);
    mysqli_stmt_execute($stmt_check);
    $result = mysqli_stmt_get_result($stmt_check);
    $row = mysqli_fetch_assoc($result);
    $jumlah_db = $row ? intval(str_replace('.', '', $row['jumlah'])) : 0;
    mysqli_stmt_close($stmt_check);

    // Pastikan jumlah tidak negatif
    $jumlah_update = max(0, $jumlah_db - $bayar_sanitized);

    $query = "INSERT INTO pembayaran_siswa (user, nis, nama, kelas, kd_biaya, thajaran, jumlah, bayar, kd_transaksi, tgl_trans, method) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        die("Prepare failed: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "ssssssdssss", 
        $username_sanitized, 
        $nis_sanitized, 
        $nama_sanitized, 
        $kelas_sanitized, 
        $kd_biaya_sanitized,
        $thajaran_sanitized, 
        $jumlah_sanitized, 
        $bayar_sanitized, 
        $kd_transaksi_sanitized, 
        $tgl_trans_sanitized,
        $method_sanitized
    );

    $sql = mysqli_stmt_execute($stmt);
    if (!$sql) {
        die("Execute failed: " . mysqli_stmt_error($stmt));
    }
    mysqli_stmt_close($stmt);

    // UPDATE jumlah di tabel biaya_siswa
    $update_query = "UPDATE biaya_siswa SET jumlah = ? WHERE nis = ? AND kd_biaya = ?";
    $stmt_update = mysqli_prepare($conn, $update_query);
    mysqli_stmt_bind_param($stmt_update, "iss", $jumlah_update, $nis_sanitized, $kd_biaya_sanitized);
    $update_sql = mysqli_stmt_execute($stmt_update);
    mysqli_stmt_close($stmt_update);

    if (!$update_sql) {
        echo "<script>alert('Pengurangan jumlah gagal: " . mysqli_error($conn) . "'); document.location.href = 'transaksi2.php';</script>";
        exit();
    }
}

// **Tutup PHP untuk menyisipkan HTML + SweetAlert2**


echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
echo "<script>
   document.addEventListener('DOMContentLoaded', function() {
       Swal.fire({
           title: 'Data berhasil diset dan jumlah biaya diperbarui!',
           text: 'Pilih jenis struk yang ingin dicetak:',
           icon: 'success',
           showCancelButton: true,
           confirmButtonText: 'Cetak',
           cancelButtonText: 'Tidak',
           html: `
               <label>
                   <input type='radio' name='struk' value='kecil' disabled>  Struk Kecil
               </label><br>
               <label>
                   <input type='radio' name='struk' value='besar'checked> Struk Besar
               </label><br>
               <label>
                   <input type='radio' name='struk' value='print' disabled> Cetak Langsung (Printer)
               </label>
           `,
           preConfirm: () => {
               const jenisStruk = document.querySelector('input[name=\"struk\"]:checked').value;
               if (jenisStruk) {
                   window.location.href = 'cetak_struk_' + jenisStruk + '.php?nis=$nis_sanitized&kd_transaksi=$kd_transaksi_sanitized';
               }
           }
       }).then((result) => {
           if (!result.isConfirmed) {
               window.location.href = 'opr_transaksi.php';
           }
       });
   });
</script>";


?>
</body>
</html>

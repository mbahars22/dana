<?php
include '../koneksi.php'; // atau sesuai koneksi kamu

$keyword = $_POST['keyword'] ?? '';

$sql = "SELECT * FROM siswa WHERE nis LIKE ? OR nama LIKE ? OR kelas LIKE ? ORDER BY nama ASC";
$stmt = $conn->prepare($sql);
$param = "%$keyword%";
$stmt->bind_param("sss", $param, $param, $param);
$stmt->execute();
$result = $stmt->get_result();

$no = 1;
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>{$no}</td>
            <td>{$row['nis']}</td>
            <td>{$row['nama']}</td>
            <td>Kelas {$row['kelas']}</td>
            <td>{$row['alamat']}</td>
             <td>{$row['rombel']}</td>
            <td align='center'>
                
                <button class='btn btn-info btn-sm btn-detail'
                    data-nis='{$row['nis']}'
                    data-bs-toggle='modal'
                    data-bs-target='#modalDetailBiaya'>Detail Biaya</button>
              
                <button class='btn btn-warning btn-sm btn-cekbiaya'
                    data-nis='{$row['nis']}'
                    data-bs-toggle='modal'
                    data-bs-target='#modalCekPembayaran'>Cek Pembayaran</button>
            </td>
        </tr>";
        $no++;
    }
} else {
    echo "<tr><td colspan='6' class='text-center'>Data tidak ditemukan.</td></tr>";
}
?>

<?php
include 'koneksi.php';

$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10; // Data per halaman
$offset = ($page - 1) * $limit;

// Hitung total data berdasarkan pencarian
$totalQuery = "SELECT COUNT(*) as total FROM pembayaran_siswa 
               WHERE nama LIKE '%$keyword%' OR nis LIKE '%$keyword%'";
$totalResult = mysqli_query($conn, $totalQuery);
$totalData = mysqli_fetch_assoc($totalResult)['total'];
$totalPages = ceil($totalData / $limit);

// Ambil data dengan LIMIT dan OFFSET
$query = "SELECT * FROM pembayaran_siswa 
          WHERE nama LIKE '%$keyword%' OR nis LIKE '%$keyword%' 
          ORDER BY tgl_trans DESC 
          LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);

// Tampilkan tabel
echo '<table class="table table-bordered table-striped">';
echo '<thead>
        <tr>
            <th>NIS</th><th>Nama</th><th>Kode Biaya</th><th>Tahun Ajaran</th>
            <th>Bayar</th><th>Kode Transaksi</th><th>Tanggal</th><th>Metode</th><th>Aksi</th>
        </tr>
      </thead><tbody>';

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>
                <td>{$row['nis']}</td>
                <td>{$row['nama']}</td>
                <td>{$row['kd_biaya']}</td>
                <td>{$row['thajaran']}</td>
                <td>Rp. " . number_format($row['bayar'], 0, ',', '.') . "</td>
                <td>{$row['kd_transaksi']}</td>
                <td>{$row['tgl_trans']}</td>
                <td>{$row['method']}</td>
                <td>
                                <button class='btn btn-danger btn-sm' onclick=\"hapusData(
                    '{$row['kd_transaksi']}',
                    '{$row['kd_biaya']}',
                    '{$row['nis']}',
                    '{$row['thajaran']}'
                )\">Hapus</button>

                </td>
              </tr>";
    }
} else {
    echo '<tr><td colspan="9" class="text-center">Data tidak ditemukan</td></tr>';
}
echo '</tbody></table>';

// Pagination
echo '<nav><ul class="pagination justify-content-center">';
$prev = $page - 1;
$next = $page + 1;

if ($page > 1) {
    echo "<li class='page-item'><a class='page-link' href='javascript:void(0)' onclick='gantiHalaman($prev)'>Previous</a></li>";
}

for ($i = 1; $i <= $totalPages; $i++) {
    $active = ($i == $page) ? 'active' : '';
    echo "<li class='page-item $active'><a class='page-link' href='javascript:void(0)' onclick='gantiHalaman($i)'>$i</a></li>";
}

if ($page < $totalPages) {
    echo "<li class='page-item'><a class='page-link' href='javascript:void(0)' onclick='gantiHalaman($next)'>Next</a></li>";
}
echo '</ul></nav>';
?>

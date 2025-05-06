<?php
include 'koneksi.php';
include 'sidebar.php';

$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// Buat query dasar
$where = "";
if (!empty($search)) {
    $where = "WHERE nis LIKE '%$search%' OR nama LIKE '%$search%'";
}

// Total data untuk pagination (perlu filter juga)
$query_total = "SELECT COUNT(*) as total FROM biaya_siswa $where";
$result_total = mysqli_query($conn, $query_total);
$total_data = mysqli_fetch_assoc($result_total)['total'];
$total_pages = ceil($total_data / $limit);

// Query data yang ditampilkan
$query_data = "SELECT * FROM biaya_siswa $where ORDER BY nis ASC LIMIT $limit OFFSET $offset";
$result_data = mysqli_query($conn, $query_data);

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Biaya Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<style>
    .input-group-sm .form-control {
        padding: 0.25rem 0.5rem;
        font-size: 0.85rem;
    }
    .input-group-sm .input-group-text {
        padding: 0.25rem 0.5rem;
    }
</style>
<body>
<div class="container mt-2">
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-10">
            <div class="row align-items-center mb-1">
                <div class="col-md-4">
                    <h3 class="m-0">Rincian Biaya Persiswa</h3>
                </div>
                <div class="col-md-8 text-end">
                     <div class="d-inline-flex align-items-center flex-wrap gap-2 shadow-sm p-2 rounded bg-white">
                    <!-- Search Input -->
                   
                    <div class="input-group input-group-sm" style="max-width: 200px;">
                            <input type="text" id="searchInput" class="form-control border-primary" placeholder="Cari NIS atau Nama..." >
                            <span class="input-group-text bg-primary text-white">
                                <i class="fas fa-search"></i>
                            </span>
                        </div>

        <!-- Tombol-tombol -->
        <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalHapusKolektif">
            🗑️ Hapus Kolektif
        </button>

        <a href="temp_down_biaya.php" class="btn btn-success btn-sm">
            <i class="bi bi-download"></i> Template Biaya Siswa
        </a>

        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalImportBiaya">
            📂 Impor Excel
        </button>
    </div>
</div>
<div id="dataTable" class="mt-3">
    <!-- Data hasil pencarian akan muncul di sini -->
</div>

<div id="resultTable">
    <table class="table table-striped  mt-3">
        <thead class="table-dark">
            <tr>
                <th>No</th>
                <th>NIS</th>
                <th>Nama</th>
                <th>Kd Kelas</th>
                <th>Kode Biaya</th>
                <th>Jumlah</th>
                <th>Th Ajaran</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            while ($row = mysqli_fetch_assoc($result_data)) :  ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= $row['nis']; ?></td>
                    <td text-left><?= $row['nama']; ?></td>
                    <td><?= $row['kelas']; ?></td>
                    <td><?= $row['kd_biaya']; ?></td>
                    <td align="right">Rp. <?= number_format($row['jumlah'], 0, ',', '.'); ?></td>
                    <td><?= $row['thajaran']; ?></td>
                    <td>
                        <a href="hapus_biaya_persiswa.php?id=<?= $row['nis']; ?>" 
                        onclick="return confirm('Yakin ingin menghapus data ini? NIS : <?= $row['nis']; ?> nama : <?= $row['nama']; ?>')"


                            class="btn btn-danger btn-sm">
                            Hapus
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>



            <!-- Pagination dan Limit -->
            <div class="row">
                <div class="col-md-6">
                    <label class="form-label me-2">Tampilkan</label>
                    <select id="limitSelect" class="form-select form-select-sm w-auto d-inline" onchange="updateLimit()">
                        <option value="10" <?= $limit == 10 ? 'selected' : '' ?>>10</option>
                        <option value="20" <?= $limit == 20 ? 'selected' : '' ?>>20</option>
                        <option value="50" <?= $limit == 50 ? 'selected' : '' ?>>50</option>
                    </select>
                    <label class="form-label ms-2">data per halaman</label>
                </div>
                <div class="col-md-6 text-end">
                <ul class="pagination justify-content-end">
    <?php if ($page > 1): ?>
        <li class="page-item">
            <a class="page-link" href="?limit=<?= $limit ?>&page=<?= $page - 1 ?>&search=<?= $search ?>">« Prev</a>
        </li>
    <?php endif; ?>

    <?php
    $adjacents = 2;
    $start = ($page > $adjacents) ? $page - $adjacents : 1;
    $end = ($page + $adjacents < $total_pages) ? $page + $adjacents : $total_pages;

    if ($start > 1) {
        echo '<li class="page-item"><a class="page-link" href="?limit='.$limit.'&page=1&search='.$search.'">1</a></li>';
        if ($start > 2) {
            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
    }

    for ($i = $start; $i <= $end; $i++) {
        $active = $page == $i ? 'active' : '';
        echo '<li class="page-item '.$active.'"><a class="page-link" href="?limit='.$limit.'&page='.$i.'&search='.$search.'">'.$i.'</a></li>';
    }

    if ($end < $total_pages) {
        if ($end < $total_pages - 1) {
            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
        echo '<li class="page-item"><a class="page-link" href="?limit='.$limit.'&page='.$total_pages.'&search='.$search.'">'.$total_pages.'</a></li>';
    }
    ?>

    <?php if ($page < $total_pages): ?>
        <li class="page-item">
            <a href="?limit=<?= $limit ?>&page=<?= $page + 1 ?>&search=<?= $search ?>" class="page-link">Next »</a>
        </li>
    <?php endif; ?>
</ul>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Import -->
<div class="modal fade" id="modalImportBiaya" tabindex="-1" aria-labelledby="modalImportLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="impor_biaya_rinci.php" method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalImportLabel">Impor Data Siswa dari Excel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label for="fileExcel" class="form-label">Pilih File Excel</label>
                    <input type="file" class="form-control" name="fileExcel" id="fileExcel" accept=".xls, .xlsx" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Upload & Impor</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Hapus Kolektif -->
<div class="modal fade" id="modalHapusKolektif" tabindex="-1" aria-labelledby="modalHapusLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="hapus_kolektif_biaya.php" method="POST" onsubmit="return confirm('Yakin ingin menghapus semua data untuk tahun ajaran ini?')">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalHapusLabel">Hapus Data Kolektif</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <label for="thajaran" class="form-label">Pilih Tahun Ajaran</label>
                    <select name="thajaran" id="thajaran" class="form-select" required>
                        <option value="">-- Pilih Tahun Ajaran --</option>
                        <?php
                        $qTh = mysqli_query($conn, "SELECT DISTINCT thajaran FROM biaya_siswa ORDER BY thajaran DESC");
                        while ($th = mysqli_fetch_assoc($qTh)) {
                            echo '<option value="'.$th['thajaran'].'">'.$th['thajaran'].'</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
function updateLimit() {
    const limit = document.getElementById('limitSelect').value;
    window.location.href = `?limit=${limit}&page=1`;
}
</script>
<script>
// Fungsi pencarian dengan AJAX
$('#searchInput').on('keyup', function() {
    let keyword = $(this).val();
    clearTimeout($.data(this, 'timer'));
    if (keyword) {
        $(this).data('timer', setTimeout(function() {
            $.ajax({
                url: 'search_biaya_siswa.php', // URL untuk handling pencarian via AJAX
                method: 'GET',
                data: { search: keyword },
                success: function(response) {
                    $('#resultTable').html(response); // update tabel dengan hasil pencarian
                }
            });
        }, 500));
    }
});

</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

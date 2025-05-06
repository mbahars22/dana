<?php
// koneksi & komponen
include 'koneksi.php';
include 'sidebar.php';

$status = '';  // Variabel untuk menyimpan status aksi

// === Aksi Simpan ===
if (isset($_POST['simpan'])) {
    $rombel = mysqli_real_escape_string($conn, $_POST['rombel']);
    mysqli_query($conn, "INSERT INTO mst_rombel (rombel) VALUES ('$rombel')");
    $status = 'simpan'; // Menandai bahwa data berhasil disimpan
}

// === Aksi Update ===
if (isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $rombel = mysqli_real_escape_string($conn, $_POST['rombel']);
    mysqli_query($conn, "UPDATE mst_rombel SET rombel='$rombel' WHERE id='$id'");
    $status = 'update'; // Menandai bahwa data berhasil diupdate
}

// === Aksi Hapus ===
if (isset($_POST['hapus'])) {
    $id = intval($_POST['id']);
    mysqli_query($conn, "DELETE FROM mst_rombel WHERE id='$id'");
    $status = 'hapus'; // Menandai bahwa data berhasil dihapus
}

// === Pagination Setup ===
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$total_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM mst_rombel"))['total'];
$total_pages = ceil($total_data / $limit);

$result_data = mysqli_query($conn, "SELECT * FROM mst_rombel ORDER BY id ASC LIMIT $limit OFFSET $offset");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Rombel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container mt-4">
    <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-11">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3>Data Rombel</h3>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahRombel">
                    + Tambah Rombel
                </button>
            </div>

            <!-- Tabel Data -->
            <table class="table table-bordered table-striped text-center">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Nama Rombel</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php $no = $offset + 1; while ($row = mysqli_fetch_assoc($result_data)): ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= htmlspecialchars($row['rombel']); ?></td>
                        <td>
                            <!-- Edit -->
                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editRombel<?= $row['id'] ?>">
                                <i class="fa fa-edit"></i> Edit
                            </button>

                            <!-- Hapus -->
                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#hapusRombel<?= $row['id'] ?>">
                                <i class="fa fa-trash"></i> Hapus
                            </button>
                        </td>
                    </tr>

                    <!-- Modal Edit -->
                    <div class="modal fade" id="editRombel<?= $row['id'] ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <form method="post">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Rombel</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                        <div class="mb-3">
                                            <label class="form-label">Nama Rombel</label>
                                            <input type="text" class="form-control" name="rombel" value="<?= htmlspecialchars($row['rombel']) ?>" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" name="update" class="btn btn-success">Simpan</button>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Modal Hapus -->
                    <div class="modal fade" id="hapusRombel<?= $row['id'] ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <form method="post">
                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger text-white">
                                        <h5 class="modal-title">Konfirmasi Hapus</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        Yakin ingin menghapus rombel <strong><?= htmlspecialchars($row['rombel']) ?></strong>?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" name="hapus" class="btn btn-danger">Ya, Hapus</button>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endwhile; ?>
                </tbody>
            </table>

            <!-- Pagination -->
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
                            <li class="page-item"><a class="page-link" href="?limit=<?= $limit ?>&page=<?= $page - 1 ?>">Previous</a></li>
                        <?php endif; ?>
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?= $page == $i ? 'active' : '' ?>">
                                <a class="page-link" href="?limit=<?= $limit ?>&page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        <?php if ($page < $total_pages): ?>
                            <li class="page-item"><a class="page-link" href="?limit=<?= $limit ?>&page=<?= $page + 1 ?>">Next</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

            <!-- Modal Tambah -->
            <div class="modal fade" id="modalTambahRombel" tabindex="-1">
                <div class="modal-dialog">
                    <form method="post">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Tambah Rombel</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Nama Rombel</label>
                                    <input type="text" class="form-control" name="rombel" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Script -->
<script>
    // Alert setelah aksi simpan, update, hapus
    <?php if ($status == 'simpan'): ?>
        alert('Data berhasil disimpan!');
    <?php elseif ($status == 'update'): ?>
        alert('Data berhasil diupdate!');
    <?php elseif ($status == 'hapus'): ?>
        alert('Data berhasil dihapus!');
    <?php endif; ?>

    function updateLimit() {
        const limit = document.getElementById('limitSelect').value;
        window.location.href = `?limit=${limit}&page=1`;
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

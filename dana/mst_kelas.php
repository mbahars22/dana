<?php
include 'koneksi.php';
include 'sidebar.php';

// Simpan data baru
if (isset($_POST['simpan'])) {
    $kelas = $_POST['kelas'];
    if (mysqli_query($conn, "INSERT INTO mst_kelas (kelas) VALUES ('$kelas')")) {
        echo "<script>alert('Kelas berhasil ditambahkan'); window.location.href = 'mst_kelas.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan saat menambahkan kelas');</script>";
    }
}

// Update data kelas
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $kelas = $_POST['kelas'];
    if (mysqli_query($conn, "UPDATE mst_kelas SET kelas='$kelas' WHERE id=$id")) {
        echo "<script>alert('Kelas berhasil diperbarui'); window.location.href = 'mst_kelas.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan saat memperbarui kelas');</script>";
    }
}

// Hapus data kelas
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    if (mysqli_query($conn, "DELETE FROM mst_kelas WHERE id=$id")) {
        echo "<script>alert('Kelas berhasil dihapus'); window.location.href = 'mst_kelas.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan saat menghapus kelas');</script>";
    }
}

// Pagination setup
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$query_total = "SELECT COUNT(*) as total FROM mst_kelas";
$result_total = mysqli_query($conn, $query_total);
$total_data = mysqli_fetch_assoc($result_total)['total'];
$total_pages = ceil($total_data / $limit);

$query_data = "SELECT * FROM mst_kelas ORDER BY id ASC LIMIT $limit OFFSET $offset";
$result_data = mysqli_query($conn, $query_data);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Kelas</title>
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
                <h3 class="m-0">Data Kelas</h3>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahKelas">
                    + Tambah Kelas
                </button>
            </div>

            <table class="table table-striped text-center mt-3">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Nama Kelas</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = $offset + 1; while ($row = mysqli_fetch_assoc($result_data)): ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= $row['kelas']; ?></td>
                        <td>
                            <!-- Tombol Edit -->
                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $row['id'] ?>">
                                <i class="fa fa-edit"></i> Edit
                            </button>

                            <!-- Tombol Hapus -->
                            <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus data ini?')">
                                <i class="fa fa-trash"></i> Hapus
                            </a>
                        </td>
                    </tr>

                    <!-- Modal Edit -->
                    <div class="modal fade" id="modalEdit<?= $row['id'] ?>" tabindex="-1" aria-labelledby="modalEditLabel<?= $row['id'] ?>" aria-hidden="true">
                        <div class="modal-dialog">
                            <form method="post">
                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Kelas</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Nama Kelas</label>
                                            <input type="text" class="form-control" name="kelas" value="<?= $row['kelas'] ?>" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" name="update" class="btn btn-primary">Update</button>
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
                            <li class="page-item">
                                <a class="page-link" href="?limit=<?= $limit ?>&page=<?= $page - 1 ?>">Previous</a>
                            </li>
                        <?php endif; ?>
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?= $page == $i ? 'active' : '' ?>">
                                <a class="page-link" href="?limit=<?= $limit ?>&page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        <?php if ($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?limit=<?= $limit ?>&page=<?= $page + 1 ?>">Next</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Modal Tambah Kelas -->
<div class="modal fade" id="modalTambahKelas" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="post">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Kelas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="kelas" class="form-label">Nama Kelas</label>
                        <input type="text" class="form-control" name="kelas" id="kelas" required>
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

<script>
function updateLimit() {
    const limit = document.getElementById('limitSelect').value;
    window.location.href = `?limit=${limit}&page=1`;
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

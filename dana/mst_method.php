<?php
include 'koneksi.php';
include 'sidebar.php';

// Tambah method
if (isset($_POST['simpan'])) {
    $method = $_POST['method'];
    $query = "INSERT INTO tb_method (method) VALUES ('$method')";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Method berhasil ditambahkan'); window.location.href='mst_method.php';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan method');</script>";
    }
}

// Update method
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $method = $_POST['method'];
    $query = "UPDATE tb_method SET method='$method' WHERE id=$id";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Method berhasil diperbarui'); window.location.href='mst_method.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui method');</script>";
    }
}

// Hapus method
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $query = "DELETE FROM tb_method WHERE id=$id";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Method berhasil dihapus'); window.location.href='mst_method.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus method');</script>";
    }
}

// Tampilkan data
$query_data = mysqli_query($conn, "SELECT * FROM tb_method ORDER BY method ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Method</title>
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
                <h3 class="m-0">Data Method</h3>
                <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalTambah">+ Tambah Method</button>
            </div>
            
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Method</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; while ($row = mysqli_fetch_assoc($query_data)) : ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= $row['method'] ?></td>
                        <td>
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $row['id'] ?>">
                                <i class="fa fa-edit"></i> Edit
                            </button>
                            <a href="?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')">
                                <i class="fa fa-trash"></i> Hapus
                            </a>
                        </td>
                    </tr>

                    <!-- Modal Edit -->
                    <div class="modal fade" id="modalEdit<?= $row['id'] ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <form method="post">
                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Method</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label>Method</label>
                                            <input type="text" class="form-control" name="method" value="<?= $row['method'] ?>" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" name="update" class="btn btn-primary">Simpan Perubahan</button>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </tbody>
            </table>

        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <form method="post">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Method</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Method</label>
                        <input type="text" class="form-control" name="method" placeholder="Contoh: Metode Bayar Tunai" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="simpan" class="btn btn-success">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
include 'koneksi.php';
include 'sidebar.php';

// Tambah data
if (isset($_POST['simpan'])) {
    $kd_biaya = $_POST['kd_biaya'];
    $nm_biaya = $_POST['nm_biaya'];
    $query = "INSERT INTO tb_kd_biaya (kd_biaya, nm_biaya) VALUES ('$kd_biaya', '$nm_biaya')";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Data berhasil ditambahkan'); window.location='kd_biaya.php';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan data');</script>";
    }
}

// Update data
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $kd_biaya = $_POST['kd_biaya'];
    $nm_biaya = $_POST['nm_biaya'];
    $query = "UPDATE tb_kd_biaya SET kd_biaya='$kd_biaya', nm_biaya='$nm_biaya' WHERE id=$id";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Data berhasil diupdate'); window.location='kd_biaya.php';</script>";
    } else {
        echo "<script>alert('Gagal update data');</script>";
    }
}

// Hapus data
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $query = "DELETE FROM tb_kd_biaya WHERE id=$id";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Data berhasil dihapus'); window.location='kd_biaya.php';</script>";
    } else {
        echo "<script>alert('Gagal hapus data');</script>";
    }
}

$data = mysqli_query($conn, "SELECT * FROM tb_kd_biaya ORDER BY id ASC");

if (!$data) {
    die("Query failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Biaya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container mt-4">
    <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-11">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Data Biaya</h3>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="fas fa-plus"></i> Tambah Data
        </button>
    </div>

    <table class="table table-bordered text-center">
        <thead class="table-dark">
            <tr>
                <th>No</th>
                <th>Kode Biaya</th>
                <th>Nama Biaya</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php $no = 1; while ($row = mysqli_fetch_assoc($data)) : ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $row['kd_biaya'] ?></td>
                <td><?= $row['nm_biaya'] ?></td>
                <td>
                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $row['id'] ?>">Edit</button>
                    <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus data ini?')">Hapus</a>
                </td>
            </tr>

            <!-- Modal Edit -->
            <div class="modal fade" id="modalEdit<?= $row['id'] ?>" tabindex="-1">
                <div class="modal-dialog">
                    <form method="post">
                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Data</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label>Kode Biaya</label>
                                    <input type="text" class="form-control" name="kd_biaya" value="<?= $row['kd_biaya'] ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label>Nama Biaya</label>
                                    <input type="text" class="form-control" name="nm_biaya" value="<?= $row['nm_biaya'] ?>" required>
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
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <form method="post">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Data Biaya</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Kode Biaya</label>
                        <input type="text" class="form-control" name="kd_biaya" placeholder="Maksimal 3 huruf" required>
                    </div>
                    <div class="mb-3">
                        <label>Nama Biaya</label>
                        <input type="text" class="form-control" name="nm_biaya" placeholder="Maksimal 20 Huruf" required>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

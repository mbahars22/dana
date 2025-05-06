<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}
include 'koneksi.php';
include 'sidebar.php';

// Tambah user
if (isset($_POST['tambah'])) {
    $user = $_POST['user'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    $thajaran = $_POST['thajaran'];

    $insert = mysqli_query($conn, "INSERT INTO users (user, username, password, role, thajaran)
                                   VALUES ('$user', '$username', '$password', '$role', '$thajaran')");
    $pesan = $insert ? "User berhasil ditambahkan." : "Gagal menambahkan user.";
}

// Update user
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $user = $_POST['user'];
    $username = $_POST['username'];
    $role = $_POST['role'];
    $thajaran = $_POST['thajaran'];

    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $update = mysqli_query($conn, "UPDATE users SET user='$user', username='$username',
                                  password='$password', role='$role', thajaran='$thajaran' WHERE id=$id");
    } else {
        $update = mysqli_query($conn, "UPDATE users SET user='$user', username='$username',
                                  role='$role', thajaran='$thajaran' WHERE id=$id");
    }
    $pesan = $update ? "User berhasil diperbarui." : "Gagal memperbarui user.";
}

// Hapus user
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM users WHERE id=$id");
    $pesan = "User berhasil dihapus.";
}

// Ambil data
$users = mysqli_query($conn, "SELECT * FROM users");
$editData = null;
if (isset($_GET['edit'])) {
    $editId = $_GET['edit'];
    $editData = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id=$editId"));
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manajemen User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style3.css"> <!-- CSS tambahan -->
</head>
<body>
<div class="container mt-2">
       <div class="row">
            <!-- Kolom kiri kosong -->
            <div class="col-md-1"></div>
            <div class="col-11">
    <h3>Manajemen User</h3>

    <?php if (!empty($pesan)) echo "<div class='alert alert-info'>$pesan</div>"; ?>

    <!-- Tombol Tambah -->
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#userModal">Tambah User</button>

    <!-- Tabel Data User -->
    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th>#</th>
            <th>Nama</th>
            <th>Username</th>
            <th>Role</th>
            <th>Thajaran</th>
            <th>Aksi</th>
        </tr>
        </thead>
        <tbody>
        <?php $no = 1; while ($u = mysqli_fetch_assoc($users)): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $u['user'] ?></td>
                <td><?= $u['username'] ?></td>
                <td><?= $u['role'] ?></td>
                <td><?= $u['thajaran'] ?></td>
                <td>
                    <a href="?edit=<?= $u['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                    <a href="?hapus=<?= $u['id'] ?>" onclick="return confirm('Yakin?')" class="btn btn-danger btn-sm">Hapus</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Modal Tambah/Edit User -->
<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form method="post" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="userModalLabel"><?= $editData ? 'Edit User' : 'Tambah User' ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <input type="hidden" name="id" value="<?= $editData['id'] ?? '' ?>">
        <div class="row g-2">
            <div class="col-md-4">
                <label>Nama</label>
                <input type="text" name="user" class="form-control" required value="<?= $editData['user'] ?? '' ?>">
            </div>
            <div class="col-md-4">
                <label>Username</label>
                <input type="text" name="username" class="form-control" required value="<?= $editData['username'] ?? '' ?>">
            </div>
            <div class="col-md-4">
                <label>Password <?= $editData ? '(Kosongkan jika tidak diganti)' : '' ?></label>
                <input type="password" name="password" class="form-control" <?= $editData ? '' : 'required' ?>>
            </div>
            <div class="col-md-6 mt-2">
                <label>Role</label>
                <select name="role" class="form-control" required>
                    <option value="">--Pilih--</option>
                    <option value="admin" <?= (isset($editData['role']) && $editData['role'] == 'admin') ? 'selected' : '' ?>>Admin</option>
                    <option value="operator" <?= (isset($editData['role']) && $editData['role'] == 'operator') ? 'selected' : '' ?>>Operator</option>
                </select>
            </div>
            <div class="col-md-6 mt-2">
                <label>Th. Ajaran</label>
                <select name="thajaran" class="form-control" required>
                    <option value="">-- Pilih Tahun Ajaran --</option>
                    <?php
                    $qryAjaran = mysqli_query($conn, "SELECT th_ajaran FROM tb_ajaran ORDER BY th_ajaran DESC");
                    while ($row = mysqli_fetch_assoc($qryAjaran)) {
                        $selected = (isset($editData['thajaran']) && $editData['thajaran'] == $row['th_ajaran']) ? 'selected' : '';
                        echo "<option value='{$row['th_ajaran']}' $selected>{$row['th_ajaran']}</option>";
                    }
                    ?>
                </select>
        </div>

        </div>
      </div>

      <div class="modal-footer">
        <button type="submit" name="<?= $editData ? 'update' : 'tambah' ?>" class="btn btn-success">
            <?= $editData ? 'Update User' : 'Tambah User' ?>
        </button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </form>
  </div>
</div>

<!-- Script Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Auto-show Modal if Edit -->
<?php if ($editData): ?>
<script>
    const modal = new bootstrap.Modal(document.getElementById('userModal'));
    modal.show();
</script>
<?php endif; ?>
</body>
</html>

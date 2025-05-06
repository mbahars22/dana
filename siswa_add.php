<?php
// session_start();
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'koneksi.php';
include  'sidebar.php'; 

// Pastikan hanya admin yang bisa mengakses halaman ini
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

// Ambil tahun ajaran dari session
$thajaran = isset($_SESSION['thajaran']) ? $_SESSION['thajaran'] : '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nis = htmlspecialchars($_POST['nis'], ENT_QUOTES, 'UTF-8');
    $nama = htmlspecialchars($_POST['nama'], ENT_QUOTES, 'UTF-8');
    $tempat = htmlspecialchars($_POST['t_lahir'], ENT_QUOTES, 'UTF-8');
    $tgl_lahir = htmlspecialchars($_POST['tgl_lahir'], ENT_QUOTES, 'UTF-8');
    $kelas = htmlspecialchars($_POST['kelas'], ENT_QUOTES, 'UTF-8');
    $alamat = htmlspecialchars($_POST['alamat'], ENT_QUOTES, 'UTF-8');
    $rombel = htmlspecialchars($_POST['rombel'], ENT_QUOTES, 'UTF-8');
    $thajaran = htmlspecialchars($_POST['thajaran'], ENT_QUOTES, 'UTF-8');

    $query = "INSERT INTO siswa (nis, nama, t_lahir, tgl_lahir, kelas, alamat, rombel, thajaran) 
              VALUES ('$nis', '$nama', '$tempat', '$tgl_lahir', '$kelas', '$alamat', '$rombel', '$thajaran')";
    
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Siswa berhasil ditambahkan!'); window.location='siswa_add.php';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan siswa!');</script>";
    }
}
$kelasResult = mysqli_query($conn, "SELECT DISTINCT kelas FROM mst_kelas ORDER BY kelas ASC");
$rombelResult = mysqli_query($conn, "SELECT DISTINCT rombel FROM mst_rombel ORDER BY rombel ASC");
$thajaranResult = mysqli_query($conn, "SELECT DISTINCT th_ajaran FROM tb_ajaran ORDER BY th_ajaran ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Siswa</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css"> <!-- CSS tambahan -->
</head>
<body>
    <div class="container mt-5">
        <div class="form-container p-4 shadow-lg rounded bg-white">
            <h2 class="text-center mb-4">Tambah Data Siswa</h2>
            <form action="" method="POST">
                <div class="row">
                    <!-- Kolom 1 -->
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="nis" class="form-label">NIS</label>
                            <input type="text" class="form-control" id="nis" name="nis" required>
                            <label for="nama" class="form-label">Nama Lengkap</label>                          
                            <input type="text" class="form-control" id="nama" name="nama" required>
                            <label for="t_lahir" class="form-label"> Tempat Lahir </label>                          
                            <input type="text" class="form-control" id="t_lahir" name="t_lahir" required>
                            <label for="tgl_lahir" class="form-label"> Tanggal Lahir </label>                          
                            <input type="date" class="form-control" id="tgl_lahir" name="tgl_lahir" required>
                            <label for="kelas" class="form-label">Kelas</label>
                            <select name="kelas" class="form-select" required>
                                <option value="">-- Pilih Kelas --</option>
                                <?php while ($row = mysqli_fetch_assoc($kelasResult)) : ?>
                                    <option value="<?= htmlspecialchars($row['kelas']) ?>"><?= htmlspecialchars($row['kelas']) ?></option>
                                <?php endwhile; ?>
                            </select>
                            <label for="rombel" class="form-label">rombel</label>
                            <select name="rombel" class="form-select" required>
                                <option value="">-- Pilih Rombel --</option>
                                <?php while ($row = mysqli_fetch_assoc($rombelResult)) : ?>
                                    <option value="<?= htmlspecialchars($row['rombel']) ?>"><?= htmlspecialchars($row['rombel']) ?></option>
                                <?php endwhile; ?>
                            </select>
                            <select name="thajaran" class="form-select" required>
                                <option value="">-- Pilih thajaran --</option>
                                <?php while ($row = mysqli_fetch_assoc($thajaranResult)) : ?>
                                    <option value="<?= htmlspecialchars($row['th_ajaran']) ?>"><?= htmlspecialchars($row['th_ajaran']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat</label>
                            <textarea class="form-control" id="alamat" name="alamat" rows="3" required></textarea>
                            <input type="hidden" name="thajaran" value="<?= htmlspecialchars($thajaran) ?>">

                        </div>
                       <!-- Tombol Aksi -->
                  <button type="submit" class="btn btn-primary w-100">Simpan</button>
                <a href="admin_dashboard2.php" class="btn btn-secondary w-100 mt-2">Kembali</a>
                </form>
            </div>
           </div>
        </div>
    </div>

    <?php include 'admin_footer.php'; ?>
</body>
</html>

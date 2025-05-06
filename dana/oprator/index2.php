<?php

session_start();
include '../koneksi.php'; // Koneksi database

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $thajaran = $_POST['thajaran'];

    // Cek user dengan role admin
    $query = "SELECT * FROM users WHERE username = ? AND thajaran = ? AND role = 'operator'";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $username, $thajaran);  // bind parameter untuk username dan thajaran
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Verifikasi password
    if ($user && password_verify($password, $user['password'])) {
        // Set session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['operator'];
        $_SESSION['user'] = $user['user'];
        $_SESSION['thajaran'] = $thajaran;

        // Redirect ke dashboard admin di folder administrator
        header("Location: opr_dashboard.php");
        exit();
    } else {
        echo "<script>alert('Login gagal! Username, password, atau tahun ajaran salah.');</script>";
    }
}

?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Keuangan Sekolah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card shadow p-4" style="width: 350px;">
            <h3 class="text-center mb-3">Login</h3>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Tahun Ajaran</label>
                    <select name="thajaran" class="form-select" required>
                        <option value="">Pilih Tahun Ajaran</option>
                        <?php
                        $query = "SELECT DISTINCT th_ajaran FROM tb_ajaran";
                        $result = mysqli_query($conn, $query);
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo '<option value="'.$row['th_ajaran'].'">'.$row['th_ajaran'].'</option>';
                        }
                        ?>
                    </select>
                </div>
                <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
            </form>
        </div>
    </div>
</body>
</html>

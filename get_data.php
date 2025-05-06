<?php
include 'koneksi.php'; // Koneksi ke database

if (isset($_POST['search'])) {
    $search = "%{$_POST['search']}%";
    
    // Menggunakan prepared statement untuk keamanan
    $query = "SELECT * FROM biaya_siswa 
    WHERE (nis LIKE ? OR nama LIKE ?) 
    AND jumlah > 0 
    ORDER BY 
      nis ASC,
      RIGHT(kd_biaya, 2) ASC,
      FIELD(SUBSTRING(kd_biaya, 8, 3), 
        'JUL', 'AGU', 'SEP', 'OKT', 'NOV', 'DES', 
        'JAN', 'FEB', 'MAR', 'APR', 'MEI', 'JUN')
    LIMIT 30";

          
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $search, $search);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $no = 1; // Mulai nomor urut dari 1

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>".htmlspecialchars($no++)."</td>"; // Kolom nomor urut
            echo "<td>".htmlspecialchars($row['nis'])."</td>";
            echo "<td>".htmlspecialchars($row['nama'])."</td>";
            echo "<td>".htmlspecialchars($row['kelas'])."</td>";
            echo "<td>".htmlspecialchars($row['kd_biaya'])."</td>";
            echo "<td>".htmlspecialchars($row['thajaran'])."</td>";
            echo "<td>".htmlspecialchars($row['jumlah'])."</td>";
           
            echo "<td>
            <a class='btn btn-info btn-xs' 
               onClick=\"masuk(this, 
               '".addslashes($row['nis'])."', 
               '".addslashes($row['nama'])."', 
               '".addslashes($row['kelas'])."', 
               '".addslashes($row['kd_biaya'])."', 
               '".addslashes($row['thajaran'])."', 
               '".addslashes($row['jumlah'])."')\" 
               href='javascript:void(0)'>
                <i class='glyphicon glyphicon-check'></i> Select
            </a>
          </td>";
    
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='7' class='text-danger text-center'>Data tidak ditemukan.</td></tr>";
    }

    $stmt->close();
}
?>


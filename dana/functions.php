<?php  
    
require 'koneksi.php';

function query($query) {
	global $conn;
	$result =mysqli_query($conn, $query);
	$rows = [];
	while( $row = mysqli_fetch_assoc($result)) {
		$rows[] = $row;
	}
	return $rows;
}



function simpanabsen($data){
	global $conn;

	$tgl = htmlspecialchars($data["tgl"]);
	$bulan = htmlspecialchars($data["bulan"]);
	$nisn = htmlspecialchars($data["nisn"]);
	$nama_siswa = htmlspecialchars($data["nama_siswa"]);
	$kls = htmlspecialchars($data["kls"]);
	$ket= htmlspecialchars($data["absen"]);

	$query = "INSERT INTO s_absen_siswa VALUES('','$tgl','$bulan','$nisn','$nama_siswa','$kls','$ket')";

	mysqli_query($conn, $query);
	return mysqli_affected_rows($conn);
}

function ubahabsensis($data){
	global $conn;
	$id = $data["id"];
	$tgl = htmlspecialchars($data["tgl"]);
	$nisn = htmlspecialchars($data["nisn"]);
	$nama_siswa = htmlspecialchars($data["nama_siswa"]);
	$kls = htmlspecialchars($data["kls"]);
	$ket= htmlspecialchars($data["ket"]);
	
$query = "UPDATE s_absen_siswa SET
		tgl = '$tgl',
		nisn = '$nisn',
		nama_siswa = '$nama_siswa',	
		kls = '$kls',	
		ket = '$ket'
		
		WHERE id = $id
	";

	mysqli_query($conn, $query);
	return mysqli_affected_rows($conn);
}


?>
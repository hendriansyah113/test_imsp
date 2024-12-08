<?php
// Koneksi ke database
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'spmi';
$conn = mysqli_connect($host, $user, $password, $dbname);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

$indikator_id = $_GET['indikator_id']; // Ambil indikator_id dari parameter URL

// Query untuk mendapatkan soal berdasarkan indikator_id
$query = "
    SELECT *
    FROM nilai_indikator 
    WHERE nilai_indikator.id_indikator = '$indikator_id'
";
$result = mysqli_query($conn, $query);

$soalData = [];
while ($row = mysqli_fetch_assoc($result)) {
    $soalData[] = $row;
}

// Menutup koneksi
mysqli_close($conn);

// Mengembalikan data soal dalam format JSON
echo json_encode($soalData);

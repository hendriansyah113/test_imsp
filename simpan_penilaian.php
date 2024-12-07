<?php
// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "spmi");

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Simpan nilai
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['nilai'] as $indikator_id => $nilai) {
        $stmt = $conn->prepare("INSERT INTO penilaian (indikator_id, nilai) VALUES (?, ?)");
        $stmt->bind_param("ii", $indikator_id, $nilai);
        $stmt->execute();
    }
    echo "Data berhasil disimpan.";
}
?>

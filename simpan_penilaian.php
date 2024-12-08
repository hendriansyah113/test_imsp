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

// Cek apakah data penilaian diterima
if (isset($_POST['penilaian']) && is_array($_POST['penilaian'])) {
    $penilaian = $_POST['penilaian'];

    // Siapkan query untuk update penilaian berdasarkan indikator_id
    $stmt = $conn->prepare("UPDATE indikator SET skor = ? WHERE id = ?");

    // Cek jika prepare gagal
    if ($stmt === false) {
        die("Error preparing the statement: " . mysqli_error($conn));
    }

    // Loop melalui data penilaian dan update data berdasarkan indikator_id
    foreach ($penilaian as $data) {
        $indikator_id = $data['soal_id']; // ID indikator
        $poin = $data['poin']; // Poin penilaian

        // Bind dan execute query
        if (!$stmt->bind_param("ii", $poin, $indikator_id)) {
            die("Error binding parameters: " . $stmt->error);
        }

        if (!$stmt->execute()) {
            die("Error executing statement: " . $stmt->error);
        }
    }

    echo "Penilaian berhasil diperbarui!";
} else {
    echo "Tidak ada data penilaian yang diterima.";
}

// Tutup koneksi
$stmt->close();
mysqli_close($conn);

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

// Query untuk mengambil data standar, sub-standar, indikator, dan nilai indikator
$query = "
    SELECT 
        standar.id AS standar_id, 
        standar.nama AS standar_nama, 
        sub_standar.id AS sub_standar_id, 
        sub_standar.nama AS sub_standar_nama,
        indikator.id AS indikator_id,
        indikator.nama AS indikator_nama,
        nilai_indikator.nilai AS nilai_indikator,
        nilai_indikator.deskripsi_nilai
    FROM standar
    LEFT JOIN sub_standar ON standar.id = sub_standar.standar_id
    LEFT JOIN indikator ON sub_standar.id = indikator.sub_standar_id
    LEFT JOIN indikator_nilai ON indikator.id = indikator_nilai.id_indikator
    LEFT JOIN nilai_indikator ON indikator_nilai.id_nilai = nilai_indikator.id_nilai
    ORDER BY standar.id, sub_standar.id, indikator.id;
";
$result = mysqli_query($conn, $query);

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    // Cek nilai NULL dan gantikan dengan string kosong ('')
    $standar_nama = $row['standar_nama'] ?: '';
    $sub_standar_nama = $row['sub_standar_nama'] ?: '';
    $indikator_nama = $row['indikator_nama'] ?: '';
    $nilai_indikator = $row['nilai_indikator'] ?: '';
    $deskripsi_nilai = $row['deskripsi_nilai'] ?: '';

    $data[$row['standar_id']]['nama'] = $standar_nama;
    $data[$row['standar_id']]['sub_standar'][$row['sub_standar_id']]['nama'] = $sub_standar_nama;
    $data[$row['standar_id']]['sub_standar'][$row['sub_standar_id']]['indikator'][] = [
        'id' => $row['indikator_id'],
        'nama' => $indikator_nama,
        'nilai' => $nilai_indikator,
        'deskripsi_nilai' => $deskripsi_nilai
    ];
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabel Standar, Sub-Standar, dan Indikator</title>
    <style>
    table {
        width: 100%;
        border-collapse: collapse;
    }

    th,
    td {
        border: 1px solid #000;
        padding: 8px;
        text-align: left;
    }

    th {
        background-color: #f2f2f2;
    }

    .vertical {
        writing-mode: vertical-rl;
        text-orientation: mixed;
    }
    </style>
</head>

<body>
    <h1>Tabel Standar, Sub-Standar, dan Indikator</h1>
    <table>
        <thead>
            <tr>
                <th>Standar</th>
                <th>Sub Standar</th>
                <th>Indikator</th>
                <th>Nilai Indikator</th>
                <th>Deskripsi Indikator</th>
                <th>Skor</th>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Loop untuk menampilkan data Standar, Sub-Standar, dan Indikator
            foreach ($data as $standar):
                $total_indikator = 0; // Variabel untuk menghitung total indikator per standar
                foreach ($standar['sub_standar'] as $sub_standar) {
                    $total_indikator += count($sub_standar['indikator']);
                }
                $first_sub_standar = true; // Penanda untuk pertama kali sub-standar
                foreach ($standar['sub_standar'] as $sub_standar): ?>
            <tr>
                <!-- Menampilkan standar hanya sekali per grup, menghitung total indikator -->
                <?php if ($first_sub_standar): ?>
                <td rowspan="<?= $total_indikator; ?>">
                    <?= $standar['nama']; ?>
                </td>
                <?php $first_sub_standar = false;
                        endif; ?>

                <!-- Menampilkan sub-standar hanya sekali per grup dengan rowspan sesuai indikator -->
                <td class="vertical" rowspan="<?= count($sub_standar['indikator']); ?>">
                    <?= $sub_standar['nama']; ?>
                </td>

                <!-- Menampilkan indikator dan nilai untuk sub-standar -->
                <td><?= $sub_standar['indikator'][0]['nama']; ?></td>
                <td><?= $sub_standar['indikator'][0]['nilai']; ?></td>
                <td><?= $sub_standar['indikator'][0]['deskripsi_nilai']; ?></td>
            </tr>
            <!-- Tampilkan indikator lainnya jika ada -->
            <?php for ($i = 1; $i < count($sub_standar['indikator']); $i++): ?>
            <tr>
                <td><?= $sub_standar['indikator'][$i]['nama']; ?></td>
                <td><?= $sub_standar['indikator'][$i]['nilai']; ?></td>
                <td><?= $sub_standar['indikator'][$i]['deskripsi_nilai']; ?></td>
            </tr>
            <?php endfor; ?>
            <?php endforeach; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>

</html>
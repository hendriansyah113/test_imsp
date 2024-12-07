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

// Query untuk mengambil data standar, sub-standar, dan indikator
$query = "
    SELECT 
        standar.id AS standar_id, 
        standar.nama AS standar_nama, 
        sub_standar.id AS sub_standar_id, 
        sub_standar.nama AS sub_standar_nama,
        indikator.id AS indikator_id,
        indikator.nama AS indikator_nama
    FROM standar
    LEFT JOIN sub_standar ON standar.id = sub_standar.standar_id
    LEFT JOIN indikator ON sub_standar.id = indikator.sub_standar_id
    ORDER BY standar.id, sub_standar.id, indikator.id
";
$result = mysqli_query($conn, $query);

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    // Cek nilai NULL dan gantikan dengan string kosong ('')
    $standar_nama = $row['standar_nama'] ?: '';
    $sub_standar_nama = $row['sub_standar_nama'] ?: '';
    $indikator_nama = $row['indikator_nama'] ?: '';

    $data[$row['standar_id']]['nama'] = $standar_nama;
    $data[$row['standar_id']]['sub_standar'][$row['sub_standar_id']]['nama'] = $sub_standar_nama;
    $data[$row['standar_id']]['sub_standar'][$row['sub_standar_id']]['indikator'][] = [
        'id' => $row['indikator_id'],
        'nama' => $indikator_nama
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

                        <!-- Menampilkan indikator untuk sub-standar -->
                        <td><?= $sub_standar['indikator'][0]['nama']; ?></td>
                    </tr>
                    <!-- Tampilkan indikator lainnya jika ada -->
                    <?php for ($i = 1; $i < count($sub_standar['indikator']); $i++): ?>
                        <tr>
                            <td><?= $sub_standar['indikator'][$i]['nama']; ?></td>
                        </tr>
                    <?php endfor; ?>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>

</html>
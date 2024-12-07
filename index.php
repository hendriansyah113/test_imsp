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
    ORDER BY standar.id, sub_standar.id, indikator.id;
";
$result = mysqli_query($conn, $query);

// Proses data untuk mempermudah looping
$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $standar_id = $row['standar_id'];
    $sub_standar_id = $row['sub_standar_id'];
    $indikator = [
        'id' => $row['indikator_id'],
        'nama' => $row['indikator_nama']
    ];

    if (!isset($data[$standar_id])) {
        $data[$standar_id] = [
            'nama' => $row['standar_nama'],
            'sub_standar' => []
        ];
    }

    if (!isset($data[$standar_id]['sub_standar'][$sub_standar_id])) {
        $data[$standar_id]['sub_standar'][$sub_standar_id] = [
            'nama' => $row['sub_standar_nama'],
            'indikator' => []
        ];
    }

    $data[$standar_id]['sub_standar'][$sub_standar_id]['indikator'][] = $indikator;
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
                <th>Sub-Standar</th>
                <th>Indikator</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $standar): ?>
                <?php
                $total_indikator = 0;
                foreach ($standar['sub_standar'] as $sub_standar) {
                    $total_indikator += count($sub_standar['indikator']) ?: 1; // Minimal 1
                }
                $first_sub_standar = true;
                ?>
                <?php foreach ($standar['sub_standar'] as $sub_standar): ?>
                    <?php
                    $indikator_count = count($sub_standar['indikator']);
                    $first_indikator = true;
                    ?>
                    <tr>
                        <!-- Standar -->
                        <?php if ($first_sub_standar): ?>
                            <td rowspan="<?= $total_indikator; ?>">
                                <?= $standar['nama']; ?>
                            </td>
                            <?php $first_sub_standar = false; ?>
                        <?php endif; ?>

                        <!-- Sub-Standar -->
                        <?php if ($first_indikator): ?>
                            <td rowspan="<?= $indikator_count ?: 1; ?>" class="vertical">
                                <?= $sub_standar['nama']; ?>
                            </td>
                            <?php $first_indikator = false; ?>
                        <?php endif; ?>

                        <!-- Indikator -->
                        <td><?= $sub_standar['indikator'][0]['nama']; ?></td>
                    </tr>

                    <!-- Indikator lainnya -->
                    <?php for ($i = 1; $i < $indikator_count; $i++): ?>
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
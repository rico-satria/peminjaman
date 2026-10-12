<?php
declare(strict_types=1);

$config = require __DIR__ . "/config.php";
$done = "";
$error = "";

function installer_e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, "UTF-8");
}

try {
    if ($config["db_pass"] === "GANTI_DENGAN_PASSWORD_DATABASE") {
        throw new RuntimeException(
            "Password database belum diisi pada config.php.",
        );
    }

    $pdo = new PDO(
        "mysql:host={$config["db_host"]};port={$config["db_port"]};dbname={$config["db_name"]};charset=utf8mb4",
        $config["db_user"],
        $config["db_pass"],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ],
    );

    $tableExists = (bool) $pdo
        ->query("SHOW TABLES LIKE 'users'")
        ->fetchColumn();

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        if ($tableExists) {
            throw new RuntimeException(
                "Aplikasi sudah terinstal. Installer tidak menjalankan reset untuk melindungi data.",
            );
        }

        try {
            $pdo->exec(
                (string) file_get_contents(__DIR__ . "/database/schema.sql"),
            );

            $users = [
                ["Administrator", "admin", "Admin123!", "admin", "Sarpras"],
                [
                    "Petugas Sarpras",
                    "sarpras",
                    "Sarpras123!",
                    "petugas",
                    "Sarpras",
                ],
                ["Waka Sarpras", "waka", "Waka123!", "waka", "Waka Sarpras"],
                [
                    "Kepala Tata Usaha",
                    "katu",
                    "TU123!",
                    "kepala_tu",
                    "Tata Usaha",
                ],
                [
                    "Rina Susanti",
                    "guru",
                    "Guru123!",
                    "peminjam",
                    "Teknik Kimia Industri",
                ],
                [
                    "Budi Pratama",
                    "tendik",
                    "Guru123!",
                    "peminjam",
                    "Tata Usaha",
                ],
            ];

            $statement = $pdo->prepare(
                "INSERT INTO users(name,username,password,role,unit) VALUES(?,?,?,?,?)",
            );
            foreach ($users as $account) {
                $account[2] = password_hash($account[2], PASSWORD_DEFAULT);
                $statement->execute($account);
            }

            $pdo->exec("INSERT INTO loans(loan_no,user_id,asset_id,loan_type,purpose,start_date,due_date,status,approver_note,checkout_condition,approved_by,checkout_at,returned_at,return_condition) VALUES
                ('PJM/2026/0001',5,1,'jangka_panjang','Menunjang pembelajaran dan administrasi guru','2026-07-01','2027-06-30','dipinjam','Disetujui untuk satu tahun','Baik; kelengkapan lengkap',3,'2026-07-01 09:10:00',NULL,NULL),
                ('PJM/2026/0002',6,3,'sementara','Presentasi rapat evaluasi','2026-09-19','2026-09-20','dipinjam','Dikembalikan setelah rapat','Baik; HDMI dan remote lengkap',2,'2026-09-19 08:15:00',NULL,NULL),
                ('PJM/2026/0003',5,5,'jangka_panjang','Printer untuk ruang program keahlian','2026-09-25','2027-09-24','disetujui','Menunggu serah terima',NULL,4,NULL,NULL,NULL),
                ('PJM/2026/0004',6,6,'sementara','Dokumentasi kegiatan sekolah','2026-09-23','2026-09-23','menunggu',NULL,NULL,NULL,NULL,NULL,NULL),
                ('PJM/2026/0005',5,4,'sementara','Kegiatan sosialisasi siswa baru','2026-08-02','2026-08-02','dikembalikan','Disetujui','Baik',2,'2026-08-02 07:30:00','2026-08-02 15:20:00','Baik dan lengkap')");

            $done =
                "Instalasi berhasil. Hapus atau ganti nama install.php setelah menguji login.";
            $tableExists = true;
        } catch (Throwable $exception) {
            throw $exception;
        }
    }
} catch (Throwable $exception) {
    $error = $exception->getMessage();
}
?><!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Instalasi Peminjaman Barang</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="login-body">
<main class="login-card">
    <div class="brand-mark">S</div>
    <h1>Instalasi Aplikasi</h1>
    <h2>SMK SMTI Padang</h2>

    <?php if ($done): ?>
        <div class="alert success"><?= installer_e($done) ?></div>
        <a class="btn primary full" href="login.php">Buka Halaman Login</a>
    <?php elseif ($error): ?>
        <div class="alert danger"><?= installer_e($error) ?></div>
    <?php elseif ($tableExists): ?>
        <div class="alert success">Aplikasi sudah terinstal. Installer dikunci untuk melindungi data.</div>
        <a class="btn primary full" href="login.php">Buka Halaman Login</a>
    <?php else: ?>
        <p class="muted">Database terhubung. Klik tombol berikut untuk membuat tabel dan data awal.</p>
        <form method="post">
            <button class="btn primary full" type="submit">Instal Aplikasi</button>
        </form>
    <?php endif; ?>

    <p class="muted">Database: <?= installer_e($config["db_name"]) ?></p>
</main>
</body>
</html>

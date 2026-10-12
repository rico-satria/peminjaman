<?php
declare(strict_types=1);
// Simpan session aplikasi di folder privat di luar public_html.
$sessionPath = dirname(__DIR__, 2) . "/peminjaman_sessions";
if (
    !is_dir($sessionPath) &&
    !mkdir($sessionPath, 0700, true) &&
    !is_dir($sessionPath)
) {
    http_response_code(503);
    die(
        "<h2>Session belum siap</h2><p>Folder session aplikasi tidak dapat dibuat.</p>"
    );
}
if (!is_writable($sessionPath)) {
    http_response_code(503);
    die(
        "<h2>Session belum siap</h2><p>Folder session aplikasi tidak dapat ditulis.</p>"
    );
}

session_save_path($sessionPath);
session_name("SMTI_PEMINJAMAN_SESSION");
session_set_cookie_params([
    "lifetime" => 0,
    "path" => "/",
    "secure" =>
        (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off") ||
        ($_SERVER["HTTP_X_FORWARDED_PROTO"] ?? "") === "https",
    "httponly" => true,
    "samesite" => "Lax",
]);
if (!session_start()) {
    http_response_code(503);
    die("<h2>Session belum siap</h2><p>Session PHP gagal dimulai.</p>");
}
$config = require __DIR__ . "/../config.php";
date_default_timezone_set($config["timezone"]);
try {
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
} catch (Throwable $e) {
    http_response_code(503);
    die(
        '<h2>Database belum siap</h2><p>Jalankan <a href="install.php">install.php</a> terlebih dahulu.</p>'
    );
}
function e(?string $v): string
{
    return htmlspecialchars($v ?? "", ENT_QUOTES, "UTF-8");
}
function user(): ?array
{
    $u = $_SESSION["peminjaman_user"] ?? null;
    if (!is_array($u) || !isset($u["id"], $u["name"], $u["role"])) {
        return null;
    }
    return $u;
}
function require_login(): void
{
    if (!user()) {
        unset($_SESSION["peminjaman_user"]);
        header("Location: login.php");
        exit();
    }
}
function csrf(): string
{
    if (empty($_SESSION["csrf"])) {
        $_SESSION["csrf"] = bin2hex(random_bytes(24));
    }
    return $_SESSION["csrf"];
}
function check_csrf(): void
{
    if (!hash_equals($_SESSION["csrf"] ?? "", $_POST["csrf"] ?? "")) {
        http_response_code(419);
        die("Sesi formulir tidak valid. Muat ulang halaman.");
    }
}
function flash(string $message, string $type = "success"): void
{
    $_SESSION["flash"] = [$message, $type];
}
function can(array $roles): bool
{
    return user() && in_array(user()["role"], $roles, true);
}
function status_label(?string $s): string
{
    return ucwords(str_replace("_", " ", $s ?? "-"));
}

<?php
require __DIR__.'/includes/bootstrap.php';
if(user()){header('Location: index.php');exit;}
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 $st=$pdo->prepare('SELECT * FROM users WHERE username=? AND active=1');$st->execute([trim($_POST['username']??'')]);$u=$st->fetch();
 if($u && password_verify($_POST['password']??'',$u['password'])){unset($u['password']);$_SESSION['peminjaman_user']=$u;session_regenerate_id(true);header('Location:index.php');exit;}
 $error='Nama pengguna atau kata sandi salah.';
}
?><!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width"><title>Masuk</title><link rel="stylesheet" href="assets/style.css"></head><body class="login-body"><main class="login-card"><div class="brand-mark">S</div><h1>Peminjaman Barang</h1><h2>SMK SMTI Padang</h2><p class="muted">Masuk untuk mengelola atau mengajukan peminjaman aset.</p><?php if($error):?><div class="alert danger"><?=e($error)?></div><?php endif?><form method="post"><label>Nama pengguna<input name="username" autocomplete="username" required></label><label>Kata sandi<input type="password" name="password" autocomplete="current-password" required></label><button class="btn primary full">Masuk</button></form><details><summary>Akun demonstrasi</summary><small>admin / Admin123!<br>sarpras / Sarpras123!<br>waka / Waka123!<br>katu / TU123!<br>guru / Guru123!<br>tendik / Guru123!</small></details></main></body></html>

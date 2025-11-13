<?php
require_once __DIR__ . '/../inc/auth.php';
if (is_logged_in()) { header('Location: ./index.php'); exit; }
$err = '';
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
  $token = $_POST['csrf_token'] ?? '';
  if (!verify_csrf($token)) {
    $err = 'Invalid token';
  } else {
    $pass = $_POST['password'] ?? '';
    if (login($pass)) { header('Location: ./index.php'); exit; }
    $err = 'Wrong password';
  }
}
?><!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Login • CHRISYSTEMATIXX</title>
  <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
  <div class="nav"><div class="container inner"><div class="brand">CHRI<span>SYSTEMATIXX</span> Admin</div></div></div>
  <div class="container">
    <div class="card" style="max-width:460px;margin:40px auto;">
      <h1 class="h1">Admin Login</h1>
      <?php if ($err): ?><div class="flash err"><?php echo htmlspecialchars($err); ?></div><?php endif; ?>
      <form method="post" style="display:grid; gap:10px;">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
        <input class="input" type="password" name="password" placeholder="Password" required>
        <button class="button" type="submit">Login</button>
      </form>
      <p class="muted" style="color:#9aa4b2;margin-top:10px;"></p>
    </div>
  </div>
</body>
</html>

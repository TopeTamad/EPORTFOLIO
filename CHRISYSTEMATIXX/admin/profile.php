<?php
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/db.php';
require_login();
$flash = '';$ok = null;
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
  if (!verify_csrf($_POST['csrf_token'] ?? '')) { $ok=false; $flash='Invalid token'; }
  else {
    $full_name = trim($_POST['full_name'] ?? '');
    $headline = trim($_POST['headline'] ?? '');
    $about = trim($_POST['about'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $avatar = trim($_POST['avatar'] ?? '');
    $facebook_url = trim($_POST['facebook_url'] ?? '');
    $instagram_url = trim($_POST['instagram_url'] ?? '');
    if ($full_name === '' || $headline === '') { $ok=false; $flash='Full name and headline are required.'; }
    else {
      $stmt = $pdo->prepare('INSERT INTO profile(full_name, headline, about, avatar, email, location, facebook_url, instagram_url) VALUES(?,?,?,?,?,?,?,?)');
      $stmt->execute([$full_name,$headline,$about,$avatar,$email,$location,$facebook_url,$instagram_url]);
      $ok=true; $flash='Profile updated.'; regenerate_csrf();
    }
  }
}
$profile = $pdo->query('SELECT * FROM profile ORDER BY id DESC LIMIT 1')->fetch() ?: ['full_name'=>'','headline'=>'','about'=>'','avatar'=>'','email'=>'','location'=>'','facebook_url'=>'','instagram_url'=>''];
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Edit Profile • Admin</title>
  <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
  <div class="nav"><div class="container inner"><div class="brand">CHRI<span>SYSTEMATIXX</span> Admin</div><div><a href="./">Dashboard</a><a href="./logout.php">Logout</a></div></div></div>
  <div class="container">
    <div class="card">
      <h1 class="h1">Edit Profile</h1>
      <?php if ($ok!==null): ?><div class="flash <?php echo $ok?'ok':'err'; ?>"><?php echo htmlspecialchars($flash); ?></div><?php endif; ?>
      <form method="post" style="display:grid; gap:12px;">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
        <div class="row">
          <div><label>Full name<br><input class="input" type="text" name="full_name" value="<?php echo htmlspecialchars($profile['full_name']); ?>" required></label></div>
          <div><label>Headline<br><input class="input" type="text" name="headline" value="<?php echo htmlspecialchars($profile['headline']); ?>" required></label></div>
        </div>
        <label>About<br><textarea class="input" name="about" rows="5"><?php echo htmlspecialchars($profile['about']); ?></textarea></label>
        <div class="row">
          <div><label>Email<br><input class="input" type="email" name="email" value="<?php echo htmlspecialchars($profile['email']); ?>"></label></div>
          <div><label>Location<br><input class="input" type="text" name="location" value="<?php echo htmlspecialchars($profile['location']); ?>"></label></div>
        </div>
        <label>Avatar (URL or relative path)<br><input class="input" type="text" name="avatar" value="<?php echo htmlspecialchars($profile['avatar']); ?>" placeholder="assets/img/profile.jpg"></label>
        <div class="row">
          <div><label>Facebook URL<br><input class="input" type="url" name="facebook_url" value="<?php echo htmlspecialchars($profile['facebook_url']); ?>" placeholder="https://facebook.com/yourpage"></label></div>
          <div><label>Instagram URL<br><input class="input" type="url" name="instagram_url" value="<?php echo htmlspecialchars($profile['instagram_url']); ?>" placeholder="https://instagram.com/yourhandle"></label></div>
        </div>
        <div class="actions"><button class="button" type="submit">Save</button><a class="button outline" href="../" target="_blank">View site</a></div>
      </form>
    </div>
  </div>
</body>
</html>

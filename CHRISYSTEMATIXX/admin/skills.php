<?php
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/db.php';
require_login();
$flash='';$ok=null;
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
  if (!verify_csrf($_POST['csrf_token'] ?? '')) { $ok=false; $flash='Invalid token'; }
  else {
    if (isset($_POST['add'])) {
      $name = trim($_POST['name'] ?? '');
      $level = (int)($_POST['level'] ?? 0);
      $sort = (int)($_POST['sort_order'] ?? 0);
      if ($name==='') { $ok=false; $flash='Name required.'; }
      elseif ($level<0 || $level>100) { $ok=false; $flash='Level must be 0-100.'; }
      else { $stmt=$pdo->prepare('INSERT INTO skills(name,level,sort_order) VALUES(?,?,?)'); $stmt->execute([$name,$level,$sort]); $ok=true; $flash='Skill added.'; }
    }
    if (isset($_POST['delete'])) {
      $id = (int)($_POST['id'] ?? 0);
      if ($id>0) { $pdo->prepare('DELETE FROM skills WHERE id=?')->execute([$id]); $ok=true; $flash='Deleted.'; }
    }
    if (isset($_POST['update'])) {
      $id = (int)($_POST['id'] ?? 0);
      $name = trim($_POST['name'] ?? '');
      $level = (int)($_POST['level'] ?? 0);
      $sort = (int)($_POST['sort_order'] ?? 0);
      if ($id>0 && $name!=='') { $pdo->prepare('UPDATE skills SET name=?, level=?, sort_order=? WHERE id=?')->execute([$name,$level,$sort,$id]); $ok=true; $flash='Updated.'; }
    }
    regenerate_csrf();
  }
}
$skills = $pdo->query('SELECT * FROM skills ORDER BY sort_order ASC, id ASC')->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Skills • Admin</title>
  <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
  <div class="nav"><div class="container inner"><div class="brand">CHRI<span>SYSTEMATIXX</span> Admin</div><div><a href="./">Dashboard</a><a href="./logout.php">Logout</a></div></div></div>
  <div class="container">
    <div class="card">
      <h1 class="h1">Skills</h1>
      <?php if ($ok!==null): ?><div class="flash <?php echo $ok?'ok':'err'; ?>"><?php echo htmlspecialchars($flash); ?></div><?php endif; ?>
      <form method="post" class="row" style="margin-bottom:16px;">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
        <div><label>Name<br><input class="input" type="text" name="name" required></label></div>
        <div><label>Level (0-100)<br><input class="input" type="number" name="level" min="0" max="100" value="50" required></label></div>
        <div><label>Order<br><input class="input" type="number" name="sort_order" value="0"></label></div>
        <div style="display:flex;align-items:end;"><button class="button" type="submit" name="add" value="1">Add</button></div>
      </form>
      <table class="table">
        <thead><tr><th>ID</th><th>Name</th><th>Level</th><th>Order</th><th>Actions</th></tr></thead>
        <tbody>
          <?php foreach($skills as $s): ?>
            <tr>
              <td><?php echo (int)$s['id']; ?></td>
              <td>
                <form method="post" class="actions" style="gap:6px;">
                  <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
                  <input type="hidden" name="id" value="<?php echo (int)$s['id']; ?>">
                  <input class="input" type="text" name="name" value="<?php echo htmlspecialchars($s['name']); ?>" style="max-width:220px;">
              </td>
              <td><input class="input" type="number" name="level" min="0" max="100" value="<?php echo (int)$s['level']; ?>" style="max-width:100px;"></td>
              <td><input class="input" type="number" name="sort_order" value="<?php echo (int)$s['sort_order']; ?>" style="max-width:100px;"></td>
              <td class="actions">
                  <button class="button" name="update" value="1">Save</button>
                </form>
                <form method="post" onsubmit="return confirm('Delete this skill?')">
                  <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
                  <input type="hidden" name="id" value="<?php echo (int)$s['id']; ?>">
                  <button class="button outline" name="delete" value="1">Delete</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>

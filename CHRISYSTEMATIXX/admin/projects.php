<?php
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/db.php';
require_login();
$flash='';$ok=null;
$imgDir = __DIR__ . '/../assets/img/';
if (!is_dir($imgDir)) { @mkdir($imgDir, 0777, true); }
function handle_upload(string $field): ?string {
  global $imgDir;
  if (!isset($_FILES[$field]) || empty($_FILES[$field]['name'])) return null;
  if (!is_uploaded_file($_FILES[$field]['tmp_name'])) return null;
  $ext = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));
  if (!in_array($ext, ['jpg','jpeg','png','gif','webp'])) return null;
  $name = 'proj_' . date('Ymd_His') . '_' . bin2hex(random_bytes(3)) . '.' . $ext;
  $dest = $imgDir . $name;
  if (move_uploaded_file($_FILES[$field]['tmp_name'], $dest)) {
    return 'assets/img/' . $name;
  }
  return null;
}
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
  if (!verify_csrf($_POST['csrf_token'] ?? '')) { $ok=false; $flash='Invalid token'; }
  else {
    if (isset($_POST['add'])) {
      $title = trim($_POST['title'] ?? '');
      $description = trim($_POST['description'] ?? '');
      $tech_stack = trim($_POST['tech_stack'] ?? '');
      $project_url = trim($_POST['project_url'] ?? '');
      $sort_order = (int)($_POST['sort_order'] ?? 0);
      $image_url = trim($_POST['image_url'] ?? '');
      $up = handle_upload('image_file'); if ($up) $image_url = $up;
      if ($title==='') { $ok=false; $flash='Title required.'; }
      else { $pdo->prepare('INSERT INTO projects(title,description,tech_stack,project_url,image_url,sort_order) VALUES(?,?,?,?,?,?)')->execute([$title,$description,$tech_stack,$project_url,$image_url,$sort_order]); $ok=true; $flash='Project added.'; }
    }
    if (isset($_POST['update'])) {
      $id = (int)($_POST['id'] ?? 0);
      if ($id>0) {
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $tech_stack = trim($_POST['tech_stack'] ?? '');
        $project_url = trim($_POST['project_url'] ?? '');
        $sort_order = (int)($_POST['sort_order'] ?? 0);
        $image_url = trim($_POST['image_url'] ?? '');
        $up = handle_upload('image_file'); if ($up) $image_url = $up;
        $pdo->prepare('UPDATE projects SET title=?, description=?, tech_stack=?, project_url=?, image_url=?, sort_order=? WHERE id=?')->execute([$title,$description,$tech_stack,$project_url,$image_url,$sort_order,$id]);
        $ok=true; $flash='Updated.';
      }
    }
    if (isset($_POST['delete'])) {
      $id = (int)($_POST['id'] ?? 0);
      if ($id>0) { $pdo->prepare('DELETE FROM projects WHERE id=?')->execute([$id]); $ok=true; $flash='Deleted.'; }
    }
    regenerate_csrf();
  }
}
$rows = $pdo->query('SELECT * FROM projects ORDER BY sort_order ASC, id ASC')->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Projects • Admin</title>
  <link rel="stylesheet" href="/CHRISYSTEMATIXX/assets/css/admin.css">
</head>
<body>
  <div class="nav"><div class="container inner"><div class="brand">CHRI<span>SYSTEMATIXX</span> Admin</div><div><a href="./">Dashboard</a><a href="./logout.php">Logout</a></div></div></div>
  <div class="container">
    <div class="card">
      <h1 class="h1">Projects</h1>
      <?php if ($ok!==null): ?><div class="flash <?php echo $ok?'ok':'err'; ?>"><?php echo htmlspecialchars($flash); ?></div><?php endif; ?>
      <form method="post" enctype="multipart/form-data" style="display:grid; gap:10px; margin-bottom:16px;">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
        <div class="row">
          <div><label>Title<br><input class="input" type="text" name="title" required></label></div>
          <div><label>Tech stack<br><input class="input" type="text" name="tech_stack" placeholder="PHP, MySQL, JS"></label></div>
        </div>
        <label>Description<br><textarea class="input" name="description" rows="3"></textarea></label>
        <div class="row">
          <div><label>Project URL<br><input class="input" type="url" name="project_url" placeholder="# or https://..."></label></div>
          <div><label>Order<br><input class="input" type="number" name="sort_order" value="0"></label></div>
        </div>
        <div class="row">
          <div><label>Image URL<br><input class="input" type="text" name="image_url" placeholder="assets/img/myproj.jpg"></label></div>
          <div><label>Upload image<br><input class="input" type="file" name="image_file" accept="image/*"></label></div>
        </div>
        <button class="button" type="submit" name="add" value="1">Add Project</button>
      </form>
      <table class="table">
        <thead><tr><th>ID</th><th>Title</th><th>Image</th><th>Order</th><th>Actions</th></tr></thead>
        <tbody>
          <?php foreach($rows as $r): ?>
            <tr>
              <td><?php echo (int)$r['id']; ?></td>
              <td style="min-width:260px;">
                <form method="post" enctype="multipart/form-data" class="actions" style="gap:6px;">
                  <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
                  <input type="hidden" name="id" value="<?php echo (int)$r['id']; ?>">
                  <input class="input" type="text" name="title" value="<?php echo htmlspecialchars($r['title']); ?>">
                  <input class="input" type="text" name="tech_stack" value="<?php echo htmlspecialchars($r['tech_stack']); ?>" placeholder="Tech stack" style="max-width:180px;">
                  <input class="input" type="url" name="project_url" value="<?php echo htmlspecialchars($r['project_url']); ?>" placeholder="URL" style="max-width:180px;">
                  <input class="input" type="text" name="image_url" value="<?php echo htmlspecialchars($r['image_url']); ?>" placeholder="Image URL" style="max-width:220px;">
                  <input class="input" type="file" name="image_file" accept="image/*" style="max-width:220px;">
                  <input class="input" type="number" name="sort_order" value="<?php echo (int)$r['sort_order']; ?>" style="max-width:100px;">
                  <button class="button" name="update" value="1">Save</button>
                </form>
              </td>
              <td><?php if(!empty($r['image_url'])): ?><img src="../<?php echo htmlspecialchars($r['image_url']); ?>" alt="" style="width:90px;height:60px;object-fit:cover;border-radius:8px;"><?php endif; ?></td>
              <td><?php echo (int)$r['sort_order']; ?></td>
              <td>
                <form method="post" onsubmit="return confirm('Delete this project?')">
                  <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
                  <input type="hidden" name="id" value="<?php echo (int)$r['id']; ?>">
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

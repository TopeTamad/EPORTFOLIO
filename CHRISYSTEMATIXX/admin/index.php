<?php
require_once __DIR__ . '/../inc/auth.php';
require_login();
?><!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin • CHRISYSTEMATIXX</title>
  <link rel="stylesheet" href="/EPORT/CHRISYSTEMATIXX/assets/css/admin.css">
</head>
<body>
  <div class="nav"><div class="container inner"><div class="brand">CHRI<span>SYSTEMATIXX</span> Admin</div><div><a href="/EPORT/CHRISYSTEMATIXX/" target="_blank">View site</a><a href="/EPORT/CHRISYSTEMATIXX/admin/logout.php">Logout</a></div></div></div>
  <div class="container">
    <div class="card">
      <h1 class="h1">Dashboard</h1>
      <div class="row">
        <a class="button outline" href="/EPORT/CHRISYSTEMATIXX/admin/profile.php">Edit Profile</a>
        <a class="button outline" href="/EPORT/CHRISYSTEMATIXX/admin/skills.php">Manage Skills</a>
        <a class="button outline" href="/EPORT/CHRISYSTEMATIXX/admin/projects.php">Manage Projects</a>
        <a class="button outline" href="/EPORT/CHRISYSTEMATIXX/admin/certificates.php">Manage Certificates</a>
      </div>
    </div>
  </div>
</body>
</html>

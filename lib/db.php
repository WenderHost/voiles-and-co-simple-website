<?php
function get_db(string $app_root): PDO {
  if (!in_array('sqlite', PDO::getAvailableDrivers(), true)) {
    $ver = PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION;
    throw new RuntimeException("pdo_sqlite is not enabled. Run: sudo apt install php{$ver}-sqlite3 -y && sudo systemctl restart php{$ver}-fpm");
  }
  $data_dir = $app_root . '/data';
  if (!is_dir($data_dir)) {
    mkdir($data_dir, 0755, true);
  }
  $pdo = new PDO('sqlite:' . $data_dir . '/submissions.sqlite');
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $pdo->exec("CREATE TABLE IF NOT EXISTS submissions (
    id           INTEGER PRIMARY KEY AUTOINCREMENT,
    name         TEXT NOT NULL,
    email        TEXT NOT NULL,
    service      TEXT,
    message      TEXT,
    ip           TEXT,
    submitted_at TEXT NOT NULL DEFAULT (datetime('now')),
    is_read      INTEGER NOT NULL DEFAULT 0
  )");
  return $pdo;
}

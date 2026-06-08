<?php
session_start();

$app_root = dirname(__DIR__, 2);
$config_path = $app_root . '/config.php';
if (!is_readable($config_path)) {
  http_response_code(503);
  exit('Admin unavailable: missing config.php');
}
$config = require $config_path;
if (empty($config['admin_user']) || empty($config['admin_password'])) {
  http_response_code(503);
  exit('Admin unavailable: add admin_user and admin_password to config.php');
}

// Logout
if (isset($_GET['logout'])) {
  session_destroy();
  header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
  exit;
}

// Login POST
$login_error = '';
if (!isset($_SESSION['admin_authed']) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'])) {
  if (
    hash_equals($config['admin_user'], $_POST['username'] ?? '') &&
    hash_equals($config['admin_password'], $_POST['password'] ?? '')
  ) {
    session_regenerate_id(true);
    $_SESSION['admin_authed'] = true;
    header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
  }
  $login_error = 'Invalid username or password.';
}

// Gate
if (empty($_SESSION['admin_authed'])) {
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Admin Login — Voiles &amp; Co.</title>
  <style>
    :root {
      --bg:    #071a24;
      --text:  rgba(255,255,255,.92);
      --muted: rgba(255,255,255,.6);
      --line:  rgba(255,255,255,.12);
      --brand: #41d3ff;
      --danger: rgba(255,140,140,.95);
    }
    *, *::before, *::after { box-sizing: border-box; }
    body {
      margin: 0;
      font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif;
      background: var(--bg);
      color: var(--text);
      min-height: 100vh;
      display: grid;
      place-items: center;
      font-size: 15px;
    }
    .card {
      width: 100%;
      max-width: 360px;
      background: rgba(255,255,255,.06);
      border: 1px solid var(--line);
      border-radius: 18px;
      padding: 32px 28px;
      margin: 20px;
    }
    h1 { margin: 0 0 6px; font-size: 18px; }
    .sub { margin: 0 0 24px; font-size: 13px; color: var(--muted); }
    label { display: block; font-size: 12px; color: var(--muted); margin-bottom: 4px; }
    input {
      width: 100%;
      padding: 11px 13px;
      border-radius: 12px;
      border: 1px solid var(--line);
      background: rgba(0,0,0,.2);
      color: var(--text);
      font-size: 15px;
      outline: none;
      margin-bottom: 14px;
    }
    input:focus { border-color: rgba(65,211,255,.45); box-shadow: 0 0 0 4px rgba(65,211,255,.10); }
    .btn {
      width: 100%;
      padding: 11px;
      border-radius: 12px;
      border: 1px solid rgba(65,211,255,.35);
      background: linear-gradient(135deg, rgba(65,211,255,.28), rgba(121,255,168,.18));
      color: var(--text);
      font-size: 15px;
      font-weight: 600;
      cursor: pointer;
      transition: opacity .15s ease;
    }
    .btn:hover { opacity: .88; }
    .error { font-size: 13px; color: var(--danger); margin-bottom: 14px; }
  </style>
</head>
<body>
  <div class="card">
    <h1>Voiles &amp; Co.</h1>
    <p class="sub">Admin — sign in to continue</p>
    <?php if ($login_error !== ''): ?>
      <div class="error"><?= htmlspecialchars($login_error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>
    <form method="post">
      <label for="username">Username</label>
      <input id="username" name="username" autocomplete="username" required autofocus />
      <label for="password">Password</label>
      <input id="password" name="password" type="password" autocomplete="current-password" required />
      <button class="btn" type="submit">Sign in</button>
    </form>
  </div>
</body>
</html>
<?php
  exit;
}

// Authenticated — open DB
require_once $app_root . '/lib/db.php';
$db       = null;
$db_error = '';
try {
  $db = get_db($app_root);
} catch (Exception $e) {
  $db_error = $e->getMessage();
}

if ($db !== null && $_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';
  $id     = (int)($_POST['id'] ?? 0);
  if ($action === 'toggle_read' && $id > 0) {
    $stmt = $db->prepare('UPDATE submissions SET is_read = CASE WHEN is_read = 1 THEN 0 ELSE 1 END WHERE id = ?');
    $stmt->execute([$id]);
  } elseif ($action === 'delete' && $id > 0) {
    $stmt = $db->prepare('DELETE FROM submissions WHERE id = ?');
    $stmt->execute([$id]);
  }
  header('Location: ' . $_SERVER['PHP_SELF']);
  exit;
}

$submissions = [];
$total       = 0;
$unread      = 0;
if ($db !== null) {
  $submissions = $db->query('SELECT * FROM submissions ORDER BY submitted_at DESC')->fetchAll(PDO::FETCH_ASSOC);
  $total       = count($submissions);
  $unread      = count(array_filter($submissions, fn($r) => !$r['is_read']));
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Submissions — Voiles &amp; Co. Admin</title>
  <style>
    :root {
      --bg:      #071a24;
      --text:    rgba(255,255,255,.92);
      --muted:   rgba(255,255,255,.6);
      --muted2:  rgba(255,255,255,.42);
      --line:    rgba(255,255,255,.12);
      --brand:   #41d3ff;
      --brand2:  #79ffa8;
      --danger:  rgba(255,100,100,.85);
      --radius:  14px;
      --max:     900px;
    }
    *, *::before, *::after { box-sizing: border-box; }
    body {
      margin: 0;
      font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif;
      background: var(--bg);
      color: var(--text);
      line-height: 1.55;
      font-size: 15px;
    }
    a { color: var(--brand); text-decoration: none; }
    a:hover { text-decoration: underline; }
    .wrap { max-width: var(--max); margin: 0 auto; padding: 0 20px; }

    header {
      border-bottom: 1px solid var(--line);
      padding: 16px 0;
      margin-bottom: 28px;
    }
    .hrow {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
    }
    header h1 { margin: 0; font-size: 17px; font-weight: 700; letter-spacing: .3px; }
    header p  { margin: 3px 0 0; font-size: 13px; color: var(--muted); }

    .hright { display: flex; align-items: center; gap: 12px; }

    .badge {
      display: inline-block;
      padding: 4px 12px;
      border-radius: 999px;
      font-size: 12px;
      font-weight: 700;
      background: rgba(65,211,255,.15);
      border: 1px solid rgba(65,211,255,.3);
      color: var(--brand);
      white-space: nowrap;
    }
    .badge.zero {
      background: rgba(255,255,255,.05);
      border-color: var(--line);
      color: var(--muted);
    }
    .logout {
      font-size: 12px;
      color: var(--muted);
      padding: 5px 10px;
      border-radius: 8px;
      border: 1px solid var(--line);
      transition: background .15s ease;
    }
    .logout:hover { background: rgba(255,255,255,.06); color: var(--text); text-decoration: none; }

    .empty {
      text-align: center;
      padding: 72px 20px;
      color: var(--muted);
      font-size: 15px;
    }
    .db-error {
      display: grid;
      gap: 10px;
      padding: 20px 22px;
      border-radius: var(--radius);
      background: rgba(255,100,100,.07);
      border: 1px solid rgba(255,100,100,.25);
      color: var(--danger);
    }
    .db-error strong { font-size: 15px; }
    .db-error code {
      font-family: ui-monospace, monospace;
      font-size: 13px;
      color: rgba(255,180,180,.85);
      word-break: break-word;
    }

    .submissions { display: grid; gap: 8px; }

    details.sub {
      background: rgba(255,255,255,.05);
      border: 1px solid var(--line);
      border-radius: var(--radius);
      overflow: hidden;
    }
    details.sub[open]  { background: rgba(255,255,255,.07); border-color: rgba(255,255,255,.18); }
    details.sub.unread { border-left: 3px solid var(--brand); }

    summary {
      list-style: none;
      cursor: pointer;
      padding: 13px 16px;
      user-select: none;
    }
    summary::-webkit-details-marker { display: none; }

    .sum-row {
      display: grid;
      grid-template-columns: 180px 1fr auto auto auto;
      gap: 12px;
      align-items: center;
    }
    .sum-name    { font-weight: 600; font-size: 14px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .sum-email   { font-size: 13px; color: var(--muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .sum-service { font-size: 12px; color: var(--muted2); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .sum-date    { font-size: 12px; color: var(--muted); white-space: nowrap; }

    .tag {
      font-size: 11px;
      font-weight: 700;
      padding: 3px 9px;
      border-radius: 999px;
      white-space: nowrap;
    }
    .tag-new  { background: rgba(65,211,255,.14); border: 1px solid rgba(65,211,255,.3); color: var(--brand); }
    .tag-read { background: rgba(255,255,255,.05); border: 1px solid var(--line); color: var(--muted); font-weight: 400; }

    .sub-detail {
      padding: 14px 16px 16px;
      border-top: 1px solid var(--line);
    }
    .detail-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 12px 24px;
      margin-bottom: 14px;
    }
    .detail-field label {
      display: block;
      font-size: 11px;
      text-transform: uppercase;
      letter-spacing: .6px;
      color: var(--muted);
      margin-bottom: 3px;
    }
    .detail-field p { margin: 0; font-size: 14px; }
    .detail-field.full { grid-column: span 2; }
    .msg {
      background: rgba(0,0,0,.22);
      border: 1px solid var(--line);
      border-radius: 10px;
      padding: 12px;
      font-size: 14px;
      white-space: pre-wrap;
      word-break: break-word;
      margin: 0;
    }

    .actions { display: flex; gap: 8px; flex-wrap: wrap; }
    .btn {
      display: inline-flex;
      align-items: center;
      padding: 8px 14px;
      border-radius: 10px;
      border: 1px solid var(--line);
      background: rgba(255,255,255,.07);
      color: var(--text);
      font-size: 13px;
      font-weight: 600;
      cursor: pointer;
      transition: background .15s ease;
    }
    .btn:hover      { background: rgba(255,255,255,.11); }
    .btn-read       { border-color: rgba(121,255,168,.3); background: rgba(121,255,168,.08); color: var(--brand2); }
    .btn-read:hover { background: rgba(121,255,168,.14); }
    .btn-delete       { border-color: rgba(255,100,100,.3); background: rgba(255,100,100,.07); color: var(--danger); }
    .btn-delete:hover { background: rgba(255,100,100,.13); }

    @media (max-width: 640px) {
      .sum-row { grid-template-columns: 1fr auto auto; }
      .sum-email, .sum-service { display: none; }
      .detail-grid { grid-template-columns: 1fr; }
      .detail-field.full { grid-column: span 1; }
    }
  </style>
</head>
<body>
  <header>
    <div class="wrap">
      <div class="hrow">
        <div>
          <h1>Voiles &amp; Co. — Contact Submissions</h1>
          <p><?= $total ?> total submission<?= $total !== 1 ? 's' : '' ?></p>
        </div>
        <div class="hright">
          <div class="badge <?= $unread === 0 ? 'zero' : '' ?>"><?= $unread ?> unread</div>
          <a class="logout" href="?logout=1">Sign out</a>
        </div>
      </div>
    </div>
  </header>

  <main>
    <div class="wrap">
      <?php if ($db_error !== ''): ?>
        <div class="db-error">
          <strong>Storage unavailable</strong>
          <code><?= htmlspecialchars($db_error, ENT_QUOTES, 'UTF-8') ?></code>
        </div>
      <?php elseif ($total === 0): ?>
        <div class="empty">No submissions yet.</div>
      <?php else: ?>
        <div class="submissions">
          <?php foreach ($submissions as $sub): ?>
            <?php $unread_row = !$sub['is_read']; ?>
            <details class="sub <?= $unread_row ? 'unread' : '' ?>" id="sub-<?= (int)$sub['id'] ?>">
              <summary>
                <div class="sum-row">
                  <span class="sum-name"><?= htmlspecialchars($sub['name'], ENT_QUOTES, 'UTF-8') ?></span>
                  <span class="sum-email"><?= htmlspecialchars($sub['email'], ENT_QUOTES, 'UTF-8') ?></span>
                  <span class="sum-service"><?= htmlspecialchars($sub['service'] ?: '—', ENT_QUOTES, 'UTF-8') ?></span>
                  <span class="sum-date"><?= htmlspecialchars($sub['submitted_at'], ENT_QUOTES, 'UTF-8') ?></span>
                  <span class="tag <?= $unread_row ? 'tag-new' : 'tag-read' ?>"><?= $unread_row ? 'New' : 'Read' ?></span>
                </div>
              </summary>

              <div class="sub-detail">
                <div class="detail-grid">
                  <div class="detail-field">
                    <label>Name</label>
                    <p><?= htmlspecialchars($sub['name'], ENT_QUOTES, 'UTF-8') ?></p>
                  </div>
                  <div class="detail-field">
                    <label>Email</label>
                    <p><a href="mailto:<?= htmlspecialchars($sub['email'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($sub['email'], ENT_QUOTES, 'UTF-8') ?></a></p>
                  </div>
                  <div class="detail-field">
                    <label>Service needed</label>
                    <p><?= htmlspecialchars($sub['service'] ?: 'Not specified', ENT_QUOTES, 'UTF-8') ?></p>
                  </div>
                  <div class="detail-field">
                    <label>Submitted</label>
                    <p><?= htmlspecialchars($sub['submitted_at'], ENT_QUOTES, 'UTF-8') ?></p>
                  </div>
                  <div class="detail-field full">
                    <label>Message</label>
                    <p class="msg"><?= $sub['message'] !== '' ? htmlspecialchars($sub['message'], ENT_QUOTES, 'UTF-8') : '<span style="color:var(--muted)">No message provided.</span>' ?></p>
                  </div>
                </div>

                <div class="actions">
                  <form method="post" style="display:contents">
                    <input type="hidden" name="id"     value="<?= (int)$sub['id'] ?>">
                    <input type="hidden" name="action" value="toggle_read">
                    <button type="submit" class="btn btn-read"><?= $unread_row ? 'Mark as read' : 'Mark as unread' ?></button>
                  </form>
                  <form method="post" style="display:contents"
                        onsubmit="return confirm('Delete this submission from <?= htmlspecialchars(addslashes($sub['name']), ENT_QUOTES, 'UTF-8') ?>? This cannot be undone.')">
                    <input type="hidden" name="id"     value="<?= (int)$sub['id'] ?>">
                    <input type="hidden" name="action" value="delete">
                    <button type="submit" class="btn btn-delete">Delete</button>
                  </form>
                </div>
              </div>
            </details>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </main>
</body>
</html>

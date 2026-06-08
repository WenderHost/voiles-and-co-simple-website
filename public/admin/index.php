<?php
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

if (
  empty($_SERVER['PHP_AUTH_USER']) ||
  !hash_equals($config['admin_user'], $_SERVER['PHP_AUTH_USER']) ||
  !hash_equals($config['admin_password'], $_SERVER['PHP_AUTH_PW'] ?? '')
) {
  header('WWW-Authenticate: Basic realm="Voiles Admin"');
  http_response_code(401);
  exit('Unauthorized');
}

require_once $app_root . '/lib/db.php';
$db = get_db($app_root);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

$submissions = $db->query('SELECT * FROM submissions ORDER BY submitted_at DESC')->fetchAll(PDO::FETCH_ASSOC);
$total  = count($submissions);
$unread = count(array_filter($submissions, fn($r) => !$r['is_read']));
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

    .empty {
      text-align: center;
      padding: 72px 20px;
      color: var(--muted);
      font-size: 15px;
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
    .sum-name  { font-weight: 600; font-size: 14px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .sum-email { font-size: 13px; color: var(--muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .sum-service { font-size: 12px; color: var(--muted2); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .sum-date  { font-size: 12px; color: var(--muted); white-space: nowrap; }

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
    .btn:hover { background: rgba(255,255,255,.11); }
    .btn-read   { border-color: rgba(121,255,168,.3); background: rgba(121,255,168,.08); color: var(--brand2); }
    .btn-read:hover   { background: rgba(121,255,168,.14); }
    .btn-delete { border-color: rgba(255,100,100,.3); background: rgba(255,100,100,.07); color: var(--danger); }
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
        <div class="badge <?= $unread === 0 ? 'zero' : '' ?>"><?= $unread ?> unread</div>
      </div>
    </div>
  </header>

  <main>
    <div class="wrap">
      <?php if ($total === 0): ?>
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

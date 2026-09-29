<?php
/**
 * أداة توليد هاش كلمة مرور الأدمن (متاحة فقط بعد تسجيل الدخول).
 * انسخ الناتج وحطه في config/admin.php داخل 'password_hash'.
 */

declare(strict_types=1);

$https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');

session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'httponly' => true,
    'secure'   => $https,
    'samesite' => 'Lax',
]);
session_name('LOGISTI_ADMIN');
session_start();

header('Content-Type: text/html; charset=UTF-8');
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-store');

if (empty($_SESSION['admin_logged'])) {
    header('Location: index.php');
    exit;
}

function e($v): string
{
    return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8');
}

$hash = null;
$err  = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf'], $_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], (string)$_POST['csrf'])) {
        $err = 'انتهت صلاحية الصفحة، حاول مرة أخرى';
    } else {
        $pass = (string)($_POST['new_password'] ?? '');
        if (strlen($pass) < 10) {
            $err = 'كلمة المرور لازم تكون 10 حروف على الأقل';
        } else {
            $hash = password_hash($pass, PASSWORD_BCRYPT, ['cost' => 12]);
        }
    }
}

$csrf = $_SESSION['csrf'] ?? ($_SESSION['csrf'] = bin2hex(random_bytes(32)));
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, nofollow">
  <title>توليد هاش كلمة المرور</title>
  <style>
    body { margin:0; padding:40px 16px; font-family:Arial, sans-serif; background:#0b1220; color:#e7eefc; }
    .card { max-width:620px; margin:auto; background:#131d31; border:1px solid #27354f; border-radius:14px; padding:24px; }
    input { width:100%; padding:11px 12px; border-radius:9px; border:1px solid #27354f; background:#1b2841; color:#e7eefc; }
    button { margin-top:14px; padding:11px 22px; border:none; border-radius:9px; background:#10b981; color:#06251b; font-weight:700; cursor:pointer; }
    code { display:block; background:#1b2841; padding:12px; border-radius:9px; word-break:break-all; margin-top:12px; }
    a { color:#60a5fa; }
    .error { background:#7f1d1d; padding:12px; border-radius:9px; margin-bottom:12px; }
  </style>
</head>

<body>
  <div class="card">
    <h2>توليد هاش كلمة مرور جديدة</h2>

    <?php if ($err): ?><div class="error"><?= e($err) ?></div><?php endif; ?>

    <form method="POST" autocomplete="off">
      <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
      <input type="password" name="new_password" placeholder="كلمة المرور الجديدة (10 حروف فأكثر)" required>
      <button type="submit">توليد</button>
    </form>

    <?php if ($hash): ?>
      <p>انسخ السطر ده وحطه في <b>config/admin.php</b> مكان قيمة <code style="display:inline">password_hash</code>:</p>
      <code><?= e($hash) ?></code>
    <?php endif; ?>

    <p><a href="index.php">رجوع للوحة التحكم</a></p>
  </div>
</body>

</html>

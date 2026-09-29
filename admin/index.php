<?php
/**
 * لوحة تحكم Logisti
 * - تسجيل دخول آمن (هاش كلمة المرور + CSRF + حظر مؤقت بعد محاولات فاشلة + انتهاء الجلسة بالخمول)
 * - إضافة أي نوع من الثلاثة: بطاقة سائق / بطاقة تشغيل / ترخيص
 * - يطلع التوكن + رابط التحقق + QR بعد الإضافة
 */

declare(strict_types=1);

$CONFIG = require __DIR__ . '/../config/admin.php';

/* ============================== SESSION ============================== */

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

/* ============================== HEADERS ============================== */

header('Content-Type: text/html; charset=UTF-8');
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: no-referrer');
header('Cache-Control: no-store, no-cache, must-revalidate');

require_once __DIR__ . '/../config/database.php';

/* ============================== HELPERS ============================== */

function e($v): string
{
    return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8');
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_check(): bool
{
    return isset($_POST['csrf'], $_SESSION['csrf'])
        && is_string($_POST['csrf'])
        && hash_equals($_SESSION['csrf'], $_POST['csrf']);
}

function is_logged_in(): bool
{
    return !empty($_SESSION['admin_logged']);
}

function generate_token(): string
{
    $b = random_bytes(16);
    $b[6] = chr((ord($b[6]) & 0x0f) | 0x40);
    $b[8] = chr((ord($b[8]) & 0x3f) | 0x80);
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($b), 4));
}

function generate_driver_card_number(): string
{
    $prefixes = ['38', '39', '40'];
    return $prefixes[random_int(0, 2)] . '.' . random_int(10000000, 99999999);
}

function generate_operation_card_number(): string
{
    return '81-' . str_pad((string)random_int(1, 99999), 8, '0', STR_PAD_LEFT);
}

/** الرابط العام لصفحة التحقق */
function public_base_url(array $CONFIG): string
{
    $base = trim((string)$CONFIG['public_base_url']);
    if ($base !== '') {
        return rtrim($base, '/');
    }
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $root   = rtrim(str_replace('\\', '/', dirname(dirname($_SERVER['SCRIPT_NAME']))), '/');
    return $scheme . '://' . $host . $root;
}

function validation_url(array $CONFIG, string $token): string
{
    return public_base_url($CONFIG) . '/naql/validate-driver-card?token=' . rawurlencode($token);
}

/** أعمدة الجدول الفعلية (عشان الإدخال ما يكسرش لو عمود ناقص) */
function table_columns(PDO $pdo, string $table): array
{
    static $cache = [];
    if (isset($cache[$table])) {
        return $cache[$table];
    }
    try {
        $stmt = $pdo->query("SHOW COLUMNS FROM `{$table}`");
        return $cache[$table] = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'Field');
    } catch (PDOException $e) {
        return $cache[$table] = [];
    }
}

/* ===================== حظر محاولات الدخول الفاشلة ===================== */

function attempts_file(): string
{
    return sys_get_temp_dir() . '/logisti_login_attempts.json';
}

function attempts_key(): string
{
    return hash('sha256', $_SERVER['REMOTE_ADDR'] ?? 'cli');
}

function attempts_read(): array
{
    $f = attempts_file();
    if (!is_file($f)) {
        return [];
    }
    $data = json_decode((string)file_get_contents($f), true);
    return is_array($data) ? $data : [];
}

function attempts_write(array $data): void
{
    @file_put_contents(attempts_file(), json_encode($data), LOCK_EX);
}

function attempts_state(array $CONFIG): array
{
    $all = attempts_read();
    $rec = $all[attempts_key()] ?? ['count' => 0, 'until' => 0];
    if (($rec['until'] ?? 0) < time() && ($rec['until'] ?? 0) > 0) {
        $rec = ['count' => 0, 'until' => 0];
    }
    return $rec;
}

function attempts_fail(array $CONFIG): void
{
    $all = attempts_read();
    $k   = attempts_key();
    $rec = $all[$k] ?? ['count' => 0, 'until' => 0];
    $rec['count'] = (int)$rec['count'] + 1;
    if ($rec['count'] >= (int)$CONFIG['max_login_attempts']) {
        $rec['until'] = time() + (int)$CONFIG['lockout_seconds'];
        $rec['count'] = 0;
    }
    $all[$k] = $rec;
    attempts_write($all);
}

function attempts_reset(): void
{
    $all = attempts_read();
    unset($all[attempts_key()]);
    attempts_write($all);
}

/* ============================== LOGOUT ============================== */

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    if (csrf_check()) {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }
    header('Location: index.php');
    exit;
}

/* ====================== انتهاء الجلسة بالخمول ====================== */

if (is_logged_in()) {
    $idle = time() - (int)($_SESSION['last_activity'] ?? time());
    if ($idle > (int)$CONFIG['session_idle_timeout']) {
        $_SESSION = [];
        session_destroy();
        header('Location: index.php?expired=1');
        exit;
    }
    $_SESSION['last_activity'] = time();
}

/* ============================== LOGIN ============================== */

$error = null;
$lock  = attempts_state($CONFIG);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login']) && !is_logged_in()) {

    if (!csrf_check()) {
        $error = 'انتهت صلاحية الصفحة، حاول مرة أخرى';
    } elseif (($lock['until'] ?? 0) > time()) {
        $error = 'تم حظر المحاولات مؤقتًا، جرّب بعد ' . (int)ceil(($lock['until'] - time()) / 60) . ' دقيقة';
    } else {
        $password = (string)($_POST['password'] ?? '');
        usleep(300000); // تأخير بسيط ضد التخمين السريع
        if (password_verify($password, (string)$CONFIG['password_hash'])) {
            attempts_reset();
            session_regenerate_id(true);
            $_SESSION['admin_logged']  = true;
            $_SESSION['last_activity'] = time();
            $_SESSION['csrf']          = bin2hex(random_bytes(32));
            header('Location: index.php');
            exit;
        }
        attempts_fail($CONFIG);
        $error = 'كلمة المرور غير صحيحة';
        $lock  = attempts_state($CONFIG);
    }
}

if (isset($_GET['expired'])) {
    $error = 'انتهت الجلسة بسبب عدم النشاط، سجّل الدخول من جديد';
}

/* ====================== تعريف الأنواع الثلاثة ====================== */

$TYPES = [

    'driver' => [
        'table'  => 'driver_cards',
        'label'  => 'بطاقة سائق — Driver Card',
        'fields' => [
            'first_name_ar'       => ['الاسم الأول', 'text', '', true],
            'family_name_ar'      => ['اسم العائلة', 'text', '', true],
            'driver_id_number'    => ['رقم الهوية', 'text', '', true],
            'card_type_ar'        => ['نوع البطاقة (عربي)', 'text', 'نشاط النقل الخفيف للبضائع لأغراض تجارية (للغير - منشآت)', true],
            'card_type_en'        => ['نوع البطاقة (إنجليزي)', 'text', 'Light Truck Driver', true],
            'issue_date'          => ['تاريخ الإصدار', 'date', '', true],
            'expiry_date'         => ['تاريخ الإنتهاء (لو فاضي: الإصدار + سنة ويومين)', 'date', '', false],
            'card_number'         => ['رقم البطاقة (اتركه فاضي للتوليد التلقائي)', 'text', '', false],
            'entity_name'         => ['اسم المنشأة', 'text', 'مؤسسة الإنجاز المتميزة للخدمات اللوجستية', false],
            'entity_id'           => ['رقم هوية المنشأة', 'text', '7027992556', false],
            'license_number'      => ['رقم الترخيص', 'text', '38/00014540', false],
            'license_type'        => ['نوع الترخيص/النشاط', 'text', 'نشاط النقل الخفيف للبضائع لأغراض تجارية (للغير - منشآت)', false],
            'city'                => ['المدينة', 'text', 'محافظة جدة', false],
            'license_issue_date'  => ['تاريخ إصدار الترخيص (هجري)', 'text', '1446/09/08', false],
            'license_expiry_date' => ['تاريخ إنتهاء الترخيص (هجري)', 'text', '1449/10/12', false],
        ],
    ],

    'operation' => [
        'table'  => 'operation_cards',
        'label'  => 'بطاقة تشغيل — Operation Card',
        'fields' => [
            'entity_name'      => ['اسم المنشأة/الفرد', 'text', 'مؤسسة غايتكم للخدمات اللوجستية', true],
            'license_number'   => ['رقم الترخيص', 'text', '81/00000424', true],
            'license_type'     => ['نوع الترخيص/النشاط', 'text', 'نقل البضائع عبر الدراجات الآلية لأغراض تجارية', true],
            'city'             => ['المدينة', 'text', 'محافظة جدة', true],
            'card_number'      => ['رقم البطاقة (اتركه فاضي للتوليد التلقائي)', 'text', '', false],
            'card_type'        => ['نوع بطاقة التشغيل', 'text', 'نقل البضائع عبر الدراجة الآلية لأغراض تجارية', true],
            'issue_date'       => ['تاريخ الإصدار (هجري)', 'text', '1448/03/19', true],
            'expiry_date'      => ['تاريخ الإنتهاء (هجري)', 'text', '1449/03/10', true],
            'renewal_date'     => ['تاريخ التجديد (اختياري)', 'text', '', false],
            'vehicle_model'    => ['نوع السيارة - الماركة و الطراز', 'text', 'دراجة نارية سويد', true],
            'plate_number'     => ['رقم اللوحة', 'text', 'د ب 6285', true],
            'manufacture_year' => ['سنة الصنع', 'text', '2025', true],
            'vehicle_color'    => ['لون المركبة', 'text', 'رصاصي', true],
            'serial_number'    => ['الرقم التسلسلي', 'text', '127771120', true],
        ],
    ],

    'license' => [
        'table'  => 'licenses',
        'label'  => 'ترخيص — License',
        'fields' => [
            'activity'       => ['نشاط الترخيص (العنوان الأخضر أعلى الصفحة)', 'text', 'نقل البضائع عبر الدراجات الآلية لأغراض تجارية', true],
            'license_kind'   => ['النوع', 'text', 'رئيسي', true],
            'license_number' => ['رقم الترخيص', 'text', '81/00000424', true],
            'request_status' => ['حالة الطلب', 'text', 'نشط', true],
            'created_date'   => ['تاريخ الإنشاء (هجري)', 'text', '1448-02-29', true],
            'expiry_date'    => ['تاريخ الإنتهاء (هجري)', 'text', '1449-03-10', true],
            'cr_name'        => ['اسم السجل التجاري', 'text', 'مؤسسة غايتكم للخدمات اللوجستية', true],
            'cr_number'      => ['رقم السجل التجاري', 'text', '1010650025', true],
            'cr_status'      => ['حالة السجل التجاري', 'text', 'نشط', true],
            'cr_expiry_date' => ['تاريخ إنتهاء السجل (اختياري)', 'text', '', false],
            'cr_activity'    => ['نشاط السجل التجاري', 'text', '492311 - النقل الخفيف', true],
            'entity_name'    => ['اسم المنشأة', 'text', 'مؤسسة غايتكم للخدمات اللوجستية', true],
            'entity_id'      => ['رقم هوية المنشأة', 'text', '7017775516', true],
            'region'         => ['المنطقة', 'text', 'مكّة المكرّمة', true],
            'city'           => ['المدينة', 'text', 'محافظة جدة', true],
            'address'        => ['مقر مزاولة النشاط', 'text', '23466, عمرو ابن سنان, الاجواد', true],
            'contact_name'   => ['مسؤول الاتصال', 'text', 'محمد بن غرامه الاسمري', true],
            'contact_mobile' => ['رقم الجوال', 'text', '966559879689', true],
            'contact_email'  => ['البريد الالكتروني', 'email', 'ghaya.com21@gmail.com', true],
        ],
    ],
];

$activeType = $_GET['type'] ?? $_POST['type'] ?? 'driver';
if (!isset($TYPES[$activeType])) {
    $activeType = 'driver';
}

/* ============================== ADD RECORD ============================== */

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_record']) && is_logged_in()) {

    if (!csrf_check()) {
        $_SESSION['flash_error'] = 'انتهت صلاحية الصفحة، حاول مرة أخرى';
        header('Location: index.php?type=' . urlencode($activeType));
        exit;
    }

    $type   = $TYPES[$activeType];
    $table  = $type['table'];
    $values = [];

    foreach ($type['fields'] as $key => [$label, $inputType, $default, $required]) {
        $v = trim((string)($_POST[$key] ?? ''));
        if ($required && $v === '') {
            $_SESSION['flash_error'] = 'الحقل مطلوب: ' . $label;
            header('Location: index.php?type=' . urlencode($activeType));
            exit;
        }
        $values[$key] = $v;
    }

    // قيم محسوبة تلقائيًا
    if ($activeType === 'driver') {
        if ($values['card_number'] === '') {
            $values['card_number'] = generate_driver_card_number();
        }
        if ($values['expiry_date'] === '') {
            $values['expiry_date'] = date('Y-m-d', strtotime($values['issue_date'] . ' +1 year +2 days'));
        }
    } elseif ($activeType === 'operation' && $values['card_number'] === '') {
        $values['card_number'] = generate_operation_card_number();
    }

    $values['token'] = generate_token();

    // استبعاد أي حقل مش موجود كعمود في الجدول
    $columns = table_columns($pdo, $table);
    if (!$columns) {
        $_SESSION['flash_error'] = "الجدول {$table} غير موجود في قاعدة البيانات";
        header('Location: index.php?type=' . urlencode($activeType));
        exit;
    }
    $values = array_intersect_key($values, array_flip($columns));

    $cols         = array_keys($values);
    $placeholders = implode(', ', array_fill(0, count($cols), '?'));
    $colList      = '`' . implode('`, `', $cols) . '`';

    try {
        $stmt = $pdo->prepare("INSERT INTO `{$table}` ({$colList}) VALUES ({$placeholders})");
        $stmt->execute(array_values($values));

        $_SESSION['flash_result'] = [
            'type'   => $activeType,
            'label'  => $type['label'],
            'token'  => $values['token'],
            'number' => $values['card_number'] ?? ($values['license_number'] ?? ''),
            'url'    => validation_url($CONFIG, $values['token']),
        ];
    } catch (PDOException $ex) {
        $_SESSION['flash_error'] = 'خطأ أثناء الحفظ: ' . $ex->getMessage();
    }

    header('Location: index.php?type=' . urlencode($activeType));
    exit;
}

/* ============================== FLASH ============================== */

$result      = $_SESSION['flash_result'] ?? null;
$flashError  = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_result'], $_SESSION['flash_error']);

/* ============================== LISTS ============================== */

$recent = [];
if (is_logged_in()) {
    foreach ($TYPES as $k => $t) {
        try {
            $stmt = $pdo->query("SELECT * FROM `{$t['table']}` ORDER BY id DESC LIMIT 10");
            $recent[$k] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $ex) {
            $recent[$k] = [];
        }
    }
}

$csrf = csrf_token();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, nofollow">
  <title>لوحة تحكم Logisti</title>
  <style>
    :root {
      --bg: #0b1220;
      --panel: #131d31;
      --panel-2: #1b2841;
      --line: #27354f;
      --text: #e7eefc;
      --muted: #93a4c4;
      --green: #10b981;
      --blue: #60a5fa;
    }

    * { box-sizing: border-box; }

    body {
      margin: 0;
      padding: 32px 16px;
      font-family: 'Tajawal', Arial, sans-serif;
      background: var(--bg);
      color: var(--text);
    }

    .wrap { max-width: 960px; margin: auto; }

    .card {
      background: var(--panel);
      border: 1px solid var(--line);
      border-radius: 14px;
      padding: 24px;
      margin-bottom: 20px;
    }

    h2 { margin: 0 0 16px; font-size: 20px; }
    h3 { margin: 24px 0 10px; font-size: 16px; color: var(--muted); }

    .topbar {
      display: flex; justify-content: space-between; align-items: center;
      gap: 12px; margin-bottom: 18px;
    }

    label { display: block; font-size: 13px; color: var(--muted); margin: 12px 0 6px; }

    input, select {
      width: 100%; padding: 11px 12px; border-radius: 9px;
      border: 1px solid var(--line); background: var(--panel-2);
      color: var(--text); font-family: inherit; font-size: 14px;
    }

    input:focus, select:focus { outline: 2px solid var(--green); outline-offset: 1px; }

    button {
      padding: 11px 22px; border: none; border-radius: 9px;
      background: var(--green); color: #06251b; font-weight: 700;
      cursor: pointer; font-family: inherit; font-size: 14px;
    }

    button.ghost { background: transparent; border: 1px solid var(--line); color: var(--muted); font-weight: 400; }

    .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0 16px; }
    @media (max-width:700px) { .grid { grid-template-columns: 1fr; } }

    a { color: var(--blue); word-break: break-all; }

    .success { background: #064e3b; border: 1px solid #0d6b52; padding: 14px; border-radius: 10px; margin-bottom: 16px; }
    .error { background: #7f1d1d; border: 1px solid #a12b2b; padding: 14px; border-radius: 10px; margin-bottom: 16px; }
    .hint { color: var(--muted); font-size: 13px; }

    table { width: 100%; border-collapse: collapse; font-size: 13px; }
    th, td { text-align: right; padding: 8px; border-bottom: 1px solid var(--line); }
    th { color: var(--muted); font-weight: 500; }

    .tabs { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 6px; }
    .tabs a {
      padding: 9px 14px; border-radius: 9px; border: 1px solid var(--line);
      color: var(--muted); text-decoration: none; font-size: 14px;
    }
    .tabs a.active { background: var(--green); color: #06251b; border-color: var(--green); font-weight: 700; }

    .qr-box { display: flex; gap: 18px; align-items: center; flex-wrap: wrap; }
    .qr-box img { background: #fff; padding: 8px; border-radius: 10px; }
  </style>
</head>

<body>
  <div class="wrap">

    <?php if (!is_logged_in()): ?>

      <div class="card" style="max-width:420px;margin:60px auto;">
        <h2>تسجيل دخول الأدمن</h2>

        <?php if ($error): ?>
          <div class="error"><?= e($error) ?></div>
        <?php endif; ?>

        <?php if (($lock['until'] ?? 0) > time()): ?>
          <div class="error">
            محظور مؤقتًا حتى <?= e(date('H:i', (int)$lock['until'])) ?>
          </div>
        <?php endif; ?>

        <form method="POST" autocomplete="off">
          <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
          <label for="password">كلمة المرور</label>
          <input id="password" type="password" name="password" required autofocus>
          <br><br>
          <button name="login" value="1">دخول</button>
        </form>
      </div>

    <?php else: ?>

      <div class="topbar">
        <h2 style="margin:0;">لوحة تحكم Logisti</h2>
        <form method="POST" style="margin:0;">
          <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
          <button class="ghost" name="logout" value="1">تسجيل الخروج</button>
        </form>
      </div>

      <?php if ($flashError): ?>
        <div class="error"><?= e($flashError) ?></div>
      <?php endif; ?>

      <?php if ($result): ?>
        <div class="card">
          <div class="success">تمت إضافة (<?= e($result['label']) ?>) بنجاح</div>
          <p><b>التوكن:</b> <?= e($result['token']) ?></p>
          <?php if ($result['number'] !== ''): ?>
            <p><b>الرقم:</b> <?= e($result['number']) ?></p>
          <?php endif; ?>
          <p><b>رابط التحقق:</b></p>
          <p>
            <a id="resultUrl" href="<?= e($result['url']) ?>" target="_blank" rel="noopener"><?= e($result['url']) ?></a>
          </p>

          <?php
          $qrPrimary  = 'https://quickchart.io/qr?size=300&margin=1&text=' . urlencode($result['url']);
          $qrFallback = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($result['url']);
          ?>

          <div class="qr-box">
            <img id="qrImg" src="<?= e($qrPrimary) ?>" width="230" height="230"
              alt="QR Code" data-fallback="<?= e($qrFallback) ?>"
              onerror="if(!this.dataset.done){this.dataset.done=1;this.src=this.dataset.fallback;}">
            <div>
              <p class="hint">امسح الكود أو حمّله كصورة</p>
              <p>
                <a href="<?= e($qrPrimary) ?>" download="qr-<?= e($result['token']) ?>.png"
                  target="_blank" rel="noopener">تحميل صورة الـ QR</a>
              </p>
              <button type="button" class="ghost" onclick="
                navigator.clipboard.writeText(document.getElementById('resultUrl').href)
                  .then(()=>{this.textContent='تم النسخ ✓';});
              ">نسخ الرابط</button>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <div class="card">
        <h2>إضافة سجل جديد</h2>

        <div class="tabs">
          <?php foreach ($TYPES as $key => $t): ?>
            <a href="?type=<?= e($key) ?>" class="<?= $key === $activeType ? 'active' : '' ?>"><?= e($t['label']) ?></a>
          <?php endforeach; ?>
        </div>

        <p class="hint">
          الجدول: <code><?= e($TYPES[$activeType]['table']) ?></code>
          — التوكن بيتولّد تلقائيًا، والرابط بيطلع بعد الحفظ.
        </p>

        <form method="POST" autocomplete="off">
          <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
          <input type="hidden" name="type" value="<?= e($activeType) ?>">

          <div class="grid">
            <?php
            $cols = table_columns($pdo, $TYPES[$activeType]['table']);
            foreach ($TYPES[$activeType]['fields'] as $key => [$label, $inputType, $default, $required]):
                $exists = !$cols || in_array($key, $cols, true);
                if (!$exists) { continue; }
            ?>
              <div>
                <label for="f_<?= e($key) ?>">
                  <?= e($label) ?><?= $required ? ' *' : '' ?>
                </label>
                <input id="f_<?= e($key) ?>" type="<?= e($inputType) ?>" name="<?= e($key) ?>"
                  value="<?= e($default) ?>" <?= $required ? 'required' : '' ?>>
              </div>
            <?php endforeach; ?>
          </div>

          <br>
          <button name="add_record" value="1">حفظ وإنشاء الرابط</button>
        </form>
      </div>

      <div class="card">
        <h2>آخر السجلات</h2>
        <?php foreach ($TYPES as $key => $t): ?>
          <h3><?= e($t['label']) ?></h3>
          <?php if (empty($recent[$key])): ?>
            <p class="hint">لا يوجد سجلات (أو الجدول غير موجود).</p>
          <?php else: ?>
            <table>
              <tr>
                <th>#</th>
                <th>الاسم / الرقم</th>
                <th>الرابط</th>
                <th>QR</th>
              </tr>
              <?php foreach ($recent[$key] as $row): ?>
                <?php
                $name = $row['first_name_ar'] ?? $row['entity_name'] ?? $row['cr_name'] ?? '';
                if (isset($row['family_name_ar'])) { $name .= ' ' . $row['family_name_ar']; }
                $num  = $row['card_number'] ?? $row['license_number'] ?? '';
                $url  = validation_url($CONFIG, (string)$row['token']);
                ?>
                <tr>
                  <td><?= e($row['id']) ?></td>
                  <td><?= e(trim($name . ' — ' . $num, ' —')) ?></td>
                  <td><a href="<?= e($url) ?>" target="_blank" rel="noopener"><?= e($row['token']) ?></a></td>
                  <td>
                    <a href="https://quickchart.io/qr?size=300&margin=1&text=<?= e(urlencode($url)) ?>"
                      target="_blank" rel="noopener">عرض</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </table>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>

      <p class="hint">
        لتغيير كلمة المرور: <a href="hash-password.php">توليد هاش كلمة مرور جديدة</a>
      </p>

    <?php endif; ?>

  </div>
</body>

</html>

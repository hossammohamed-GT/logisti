<?php
/**
 * الاتصال بقاعدة البيانات.
 *
 * بيشتغل على الاتنين تلقائيًا:
 *   - محليًا (localhost / 127.0.0.1)  → إعدادات الجهاز
 *   - على الاستضافة (logisti.gt.tc)  → إعدادات InfinityFree
 *
 * وتقدر كمان تتحكم فيها من متغيرات البيئة:
 *   DB_HOST / DB_NAME / DB_USER / DB_PASS
 *
 * مهم: الملف ده مش بيعمل die() لو الاتصال فشل — بيسيب $pdo = null
 * والصفحة اللي فوقه هي اللي تتصرف (تحويل للموقع الرسمي).
 */

$host = (string)($_SERVER['HTTP_HOST'] ?? '');
$isLocal = $host === ''
    || strpos($host, 'localhost') === 0
    || strpos($host, '127.0.0.1') === 0
    || strpos($host, '::1') === 0;

if ($isLocal) {
    // ===== إعدادات الجهاز المحلي =====
    $DB_HOST = 'localhost';
    $DB_NAME = 'logisti';
    $DB_USER = 'root';
    $DB_PASS = '';
} else {
    // ===== إعدادات الاستضافة (InfinityFree) =====
    $DB_HOST = 'sql207.infinityfree.com';
    $DB_NAME = 'if0_42703968_logisti';
    $DB_USER = 'if0_42703968';
    $DB_PASS = 'Hy6xGHOvuQ';
}

// أي متغير بيئة موجود بيتغلب على اللي فوق
$DB_HOST = getenv('DB_HOST') ?: $DB_HOST;
$DB_NAME = getenv('DB_NAME') ?: $DB_NAME;
$DB_USER = getenv('DB_USER') ?: $DB_USER;
$DB_PASS = getenv('DB_PASS') !== false ? (string)getenv('DB_PASS') : $DB_PASS;

$pdo       = null;
$dbError   = null;

try {
    $pdo = new PDO(
        "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_TIMEOUT            => 5,
        ]
    );
} catch (PDOException $e) {
    $dbError = $e->getMessage();
    error_log('[logisti] DB connection failed: ' . $dbError);
}

<?php
/**
 * رابط موحّد لعرض الثلاث أنواع (Roles) حسب التوكن — كل البيانات ديناميك من قاعدة البيانات:
 *   1) Driver Card    - بطاقة سائق   → جدول driver_cards
 *   2) Operation Card - بطاقة تشغيل  → جدول operation_cards
 *   3) License        - ترخيص        → جدول licenses
 *
 * الرابط: /logisti/naql/validate-driver-card?token=XXXX
 */

header('Content-Type: text/html; charset=UTF-8');

$token = trim($_GET['token'] ?? '');

if ($token === '') {
    echo '<script>window.close();</script>';
    exit;
}

require_once __DIR__ . '/../config/database.php';

$assetBase   = '../';
$data        = null;
$pageTitle   = '';
$contentFile = '';

/** جلب صف واحد بالتوكن من أي جدول */
$fetchByToken = static function (PDO $pdo, string $table, string $token) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM `{$table}` WHERE token = ? LIMIT 1");
        $stmt->execute([$token]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    } catch (PDOException $e) {
        // الجدول غير موجود أو أي خطأ آخر → تجاهل وجرّب الجدول التالي
        return null;
    }
};

// 1) بطاقة سائق
if ($row = $fetchByToken($pdo, 'driver_cards', $token)) {

    $card        = $row;
    $data        = $row;
    $pageTitle   = 'تفاصيل بطاقة السائق';
    $contentFile = __DIR__ . '/views/driver-card.php';

// 2) بطاقة تشغيل
} elseif ($row = $fetchByToken($pdo, 'operation_cards', $token)) {

    $data        = $row;
    $pageTitle   = 'تفاصيل بطاقة التشغيل';
    $contentFile = __DIR__ . '/views/operation-card.php';

// 3) ترخيص
} elseif ($row = $fetchByToken($pdo, 'licenses', $token)) {

    $data      = $row;
    // العنوان الأخضر بالأعلى = نشاط الترخيص
    $pageTitle = trim((string)($row['activity'] ?? '')) !== ''
        ? $row['activity']
        : 'تفاصيل الترخيص';
    $contentFile = __DIR__ . '/views/license.php';

} else {
    echo '<script>window.close();</script>';
    exit;
}

include __DIR__ . '/../templates/layout.php';

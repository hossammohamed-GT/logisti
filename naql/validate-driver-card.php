<?php
/**
 * رابط موحّد لعرض الثلاث أنواع (Roles) حسب التوكن:
 *   1) Driver Card    - بطاقة سائق   (من قاعدة البيانات: driver_cards)
 *   2) Operation Card - بطاقة تشغيل  (بيانات ثابتة حاليًا)
 *   3) License        - ترخيص        (بيانات ثابتة حاليًا)
 *
 * الرابط: /logisti/naql/validate-driver-card?token=XXXX
 */

header('Content-Type: text/html; charset=UTF-8');

$token = trim($_GET['token'] ?? '');

if ($token === '') {
    echo '<script>window.close();</script>';
    exit;
}

$assetBase = '../';
$data        = null;
$pageTitle   = '';
$contentFile = '';

// 1) البيانات الثابتة (بطاقة التشغيل / الترخيص)
$staticRecords = require __DIR__ . '/static-data.php';

if (isset($staticRecords[$token])) {
    $record      = $staticRecords[$token];
    $data        = $record['data'];
    $pageTitle   = $record['title'];
    $contentFile = __DIR__ . '/views/' . ($record['type'] === 'operation_card' ? 'operation-card.php' : 'license.php');
} else {
    // 2) بطاقة السائق من قاعدة البيانات
    require_once __DIR__ . '/../config/database.php';

    $stmt = $pdo->prepare("
        SELECT
            card_number,
            driver_id_number,
            first_name_ar,
            family_name_ar,
            card_type_ar,
            card_type_en,
            issue_date,
            expiry_date
        FROM driver_cards
        WHERE token = ?
        LIMIT 1
    ");

    $stmt->execute([$token]);
    $card = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$card) {
        echo '<script>window.close();</script>';
        exit;
    }

    $pageTitle   = 'تفاصيل بطاقة السائق';
    $contentFile = __DIR__ . '/views/driver-card.php';
}

include __DIR__ . '/../templates/layout.php';

<?php
/**
 * الرابط القديم - يحوّل تلقائيًا إلى الرابط الجديد داخل فولدر naql
 * القديم : /logisti/validate-driver-card?token=XXXX
 * الجديد : /logisti/naql/validate-driver-card?token=XXXX
 */

$token = trim($_GET['token'] ?? '');

$base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
$url  = $base . '/naql/validate-driver-card';

if ($token !== '') {
    $url .= '?token=' . rawurlencode($token);
}

header('Location: ' . $url, true, 301);
exit;

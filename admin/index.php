<?php

session_start();

require_once '../config/database.php';

/*
|--------------------------------------------------------------------------
| CONFIG
|--------------------------------------------------------------------------
*/

$ADMIN_PASSWORD = 'Hossam512@';

/*
|--------------------------------------------------------------------------
| LOGIN
|--------------------------------------------------------------------------
*/

if (isset($_POST['login'])) {

    if ($_POST['password'] === $ADMIN_PASSWORD) {

        $_SESSION['admin_logged'] = true;

        header("Location: index.php");
        exit;
    }

    $error = "Wrong Password";
}

/*
|--------------------------------------------------------------------------
| LOGOUT
|--------------------------------------------------------------------------
*/

if (isset($_GET['logout'])) {

    session_destroy();

    header("Location: index.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| HELPERS
|--------------------------------------------------------------------------
*/

function generateToken()
{
    return sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff)
    );
}

function generateCardNumber()
{
    $prefixes = ['38', '39', '40'];

    $prefix = $prefixes[array_rand($prefixes)];

    return $prefix . "." . rand(10000000, 99999999);
}

/*
|--------------------------------------------------------------------------
| ADD DRIVER
|--------------------------------------------------------------------------
*/

if (
    isset($_SESSION['admin_logged'])
    &&
    isset($_POST['add_driver'])
) {

    $first_name = trim($_POST['first_name']);
    $family_name = trim($_POST['family_name']);
    $driver_id = trim($_POST['driver_id']);

    $token = generateToken();

    $card_number = generateCardNumber();

        $issue_date = $_POST['issue_date'];

        $expiry_date = date(
            'Y-m-d',
            strtotime($issue_date . ' +1 year +2 days')
        );

    $card_type_ar =
        'نشاط النقل الخفيف للبضائع لأغراض تجارية (للغير - م)';

    $card_type_en =
        'Light Truck Driver';

    $stmt = $pdo->prepare("
        INSERT INTO driver_cards
        (
            token,
            card_number,
            driver_id_number,
            first_name_ar,
            family_name_ar,
            card_type_ar,
            card_type_en,
            issue_date,
            expiry_date,
            created_at
        )
        VALUES
        (
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            NOW()
        )
    ");

    $stmt->execute([
        $token,
        $card_number,
        $driver_id,
        $first_name,
        $family_name,
        $card_type_ar,
        $card_type_en,
        $issue_date,
        $expiry_date
    ]);

    $validation_url =
        "https://logisti.gt.tc/naql/validate-driver-card?token=" . $token;

    
    $qr_url =
            "https://quickchart.io/qr?size=300&text="
            . urlencode($validation_url);
  
}

?>

<!DOCTYPE html>
<html lang="ar">
<head>
<meta charset="UTF-8">
<title>Driver Cards Admin</title>

<style>

body{
    font-family:Arial;
    background:#111827;
    color:white;
    padding:40px;
}

.card{
    background:#1f2937;
    padding:25px;
    border-radius:12px;
    max-width:700px;
    margin:auto;
}

input{
    width:100%;
    padding:12px;
    margin-top:10px;
    margin-bottom:15px;
}

button{
    padding:12px 20px;
    background:#10b981;
    border:none;
    color:white;
    cursor:pointer;
}

img{
    margin-top:20px;
}

a{
    color:#60a5fa;
}

.success{
    background:#064e3b;
    padding:15px;
    margin-bottom:20px;
}

.error{
    background:#7f1d1d;
    padding:15px;
    margin-bottom:20px;
}

</style>

</head>
<body>

<div class="card">

<?php if(!isset($_SESSION['admin_logged'])): ?>

<h2>Admin Login</h2>

<?php if(isset($error)): ?>
<div class="error">
<?= $error ?>
</div>
<?php endif; ?>

<form method="POST">

<input
type="password"
name="password"
placeholder="Password"
required>

<button name="login">
Login
</button>

</form>

<?php else: ?>

<h2>إضافة سائق جديد</h2>

<p>
<a href="?logout=1">
Logout
</a>
</p>

<form method="POST">

<input
type="text"
name="first_name"
placeholder="الاسم الأول"
required>

<input
type="text"
name="family_name"
placeholder="الاسم الثاني"
required>

<input
type="text"
name="driver_id"
placeholder="رقم الهوية"
required>
    
    <input
type="date"
name="issue_date"
required>

<button name="add_driver">
إضافة
</button>

</form>

<?php if(isset($validation_url)): ?>

<hr>

<div class="success">

تم إنشاء البطاقة بنجاح

</div>

<p>

<b>Card Number:</b>

<?= $card_number ?>

</p>

<p>

<b>Token:</b>

<?= $token ?>

</p>

<p>

<b>Validation URL:</b>

</p>

<a
href="<?= $validation_url ?>"
target="_blank">

<?= $validation_url ?>

</a>

<br><br>

<img
src="<?= $qr_url ?>"
width="250">

<?php endif; ?>

<?php endif; ?>

</div>

</body>
</html>
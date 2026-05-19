<?php
session_start();
include "config.php";

if (!isset($_SESSION["taxpayer_id"])) {
    header("Location: login.php");
    exit;
}

$taxpayer_id = $_SESSION["taxpayer_id"];

/* بيانات المكلف */
$query = "
SELECT
    taxpayer_number,
    taxpayer_name,
    electronic_payment_number
FROM taxpayers
WHERE taxpayer_id = ?
LIMIT 1
";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $taxpayer_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (!$taxpayer = mysqli_fetch_assoc($result)) {
    die("لم يتم العثور على بيانات المكلف");
}

/* أوامر القبض */
$query2 = "
SELECT
    declaration_type,
    period,
    positive_tax_due,
    diff_amount
FROM tax_declaration
WHERE taxpayer_id = ?
ORDER BY declaration_id DESC
";

$stmt2 = mysqli_prepare($conn, $query2);
mysqli_stmt_bind_param($stmt2, "i", $taxpayer_id);
mysqli_stmt_execute($stmt2);

$rows = mysqli_stmt_get_result($stmt2);

$payments = [];

while ($row = mysqli_fetch_assoc($rows)) {

    $type = $row["declaration_type"];
    $period = $row["period"];

    $positive = floatval($row["positive_tax_due"]);
    $diff = floatval($row["diff_amount"]);

    $amount = 0;
    $description = "إقرار ضريبي";

    if ($type == "اصلي" || $type == "أصلي" || $type == "Original") {

        $description = "إقرار ضريبي أصلي";

        if ($positive > 0) {
            $amount = $positive;
        }

    } elseif ($type == "معدل" || $type == "Amended") {

        $description = "إقرار ضريبي معدل";

        if ($diff < 0) {
            $amount = abs($diff);
        }
    }

    if ($amount > 0) {

        $payments[] = [
            "description" => $description,
            "period" => $period,
            "amount" => number_format($amount, 3)
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
<meta charset="UTF-8">
<title>الدفع الإلكتروني</title>

<style>

*{
    box-sizing:border-box;
}

body{
    margin:0;
    background:#f4f4f4;
    font-family:"Segoe UI",Tahoma,sans-serif;
}

.container{
    width:1130px;
    max-width:96%;
    margin:auto;
    background:white;
    min-height:100vh;
    border:1px solid #ccc;
}

.header{
    width:100%;
    height:82px;
    overflow:hidden;
    border-bottom:5px solid royalblue;
}

.header img{
    width:100%;
    height:100%;
    object-fit:cover;
}

.title{
    text-align:center;
    font-size:34px;
    font-weight:bold;
    margin-top:25px;
    margin-bottom:35px;
}

.top-info{
    display:flex;
    justify-content:center;
    gap:70px;
    margin-bottom:25px;
}

.info-group{
    width:250px;
}

.info-group label{
    display:block;
    text-align:right;
    font-size:18px;
    font-weight:bold;
    margin-bottom:10px;
}

.info-group input{
    width:100%;
    height:38px;
    border:1px solid #999;
    background:#f8f8f8;
    font-size:16px;
    text-align:center;
}

.pdf-section{
    width:900px;
    margin:auto;
    display:flex;
    justify-content:flex-end;
    align-items:center;
    gap:18px;
    margin-top:15px;
    margin-bottom:40px;
}

.pdf-label{
    font-size:18px;
    font-weight:bold;
}

.pdf-btn{
    width:150px;
    height:40px;
    background:royalblue;
    color:white;
    border:none;
    font-size:16px;
    font-weight:bold;
    cursor:pointer;
}

.pdf-btn:hover{
    background:#274fc0;
}

.table-title{
    text-align:center;
    font-size:24px;
    font-weight:bold;
    margin-bottom:18px;
}

.table-container{
    width:540px;
    margin:auto;
}

table{
    width:100%;
    border-collapse:collapse;
    text-align:center;
}

th{
    background:#f1f1f1;
    border:1px solid #999;
    padding:14px;
    font-size:17px;
}

td{
    border:1px solid #999;
    padding:14px;
    font-size:15px;
}

.no-data{
    text-align:center;
    color:red;
    font-size:22px;
    font-weight:bold;
    margin-top:35px;
}

.buttons{
    text-align:center;
    margin-top:55px;
    margin-bottom:35px;
}

.btn{
    width:120px;
    height:44px;
    background:royalblue;
    color:white;
    border:none;
    font-size:18px;
    font-weight:bold;
    cursor:pointer;
    margin:0 25px;
}

.btn:hover{
    background:#274fc0;
}

@media (max-width:950px){

    .top-info{
        flex-direction:column;
        align-items:center;
    }

    .pdf-section{
        width:95%;
        justify-content:center;
        flex-direction:column;
    }

    .table-container{
        width:95%;
        overflow:auto;
    }

    table{
        min-width:500px;
    }
}

</style>
</head>

<body>

<div class="container">

<div class="header">
    <img src="photo.jpeg">
</div>

<div class="title">
الدفع الإلكتروني
</div>

<div class="top-info">

<div class="info-group">
<label>
رقم المكلف
</label>

<input
type="text"
value="<?= htmlspecialchars($taxpayer["taxpayer_number"]) ?>"
readonly>
</div>

<div class="info-group">
<label>
اسم المكلف
</label>

<input
type="text"
value="<?= htmlspecialchars($taxpayer["taxpayer_name"]) ?>"
readonly>
</div>

<div class="info-group">
<label>
رقم الدفع الإلكتروني
</label>

<input
type="text"
value="<?= htmlspecialchars($taxpayer["electronic_payment_number"]) ?>"
readonly>
</div>

</div>

<div class="pdf-section">

<div class="pdf-label">
آلية الدفع الإلكتروني
</div>

<button
class="pdf-btn"
onclick="window.open('اليه الدفع الالكتروني.pdf')">
PDF آلية الدفع
</button>

</div>

<div class="table-title">
أمر قبض
</div>

<div class="table-container">

<?php if(count($payments) > 0): ?>

<table>

<tr>
<th>وصف الحركة</th>
<th>الفترة</th>
<th>المبلغ</th>
</tr>

<?php foreach($payments as $pay): ?>

<tr>

<td>
<?= htmlspecialchars($pay["description"]) ?>
</td>

<td>
<?= htmlspecialchars($pay["period"]) ?>
</td>

<td>
<?= htmlspecialchars($pay["amount"]) ?>
</td>

</tr>

<?php endforeach; ?>

</table>

<?php else: ?>

<div class="no-data">
لا يوجد مبالغ مستحقة للدفع
</div>

<?php endif; ?>

</div>

<div class="buttons">

<button
class="btn"
onclick="window.location.href='mains.php'">
خروج
</button>

<button
class="btn"
onclick="window.print()">
طباعة
</button>

</div>

</div>

</body>
</html>
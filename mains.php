<?php
session_start();
include "config.php";

if (!isset($_SESSION["taxpayer_id"])) {
    header("Location: login.php");
    exit;
}


$taxpayer_id = $_SESSION["taxpayer_id"];

$query = "
    SELECT taxpayer_number, taxpayer_name
    FROM taxpayers
    WHERE taxpayer_id = ?
    LIMIT 1
";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $taxpayer_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$taxpayer_number = "";
$taxpayer_name = "";

if ($row = mysqli_fetch_assoc($result)) {
    $taxpayer_number = $row["taxpayer_number"];
    $taxpayer_name = $row["taxpayer_name"];
} else {
    die("لم يتم العثور على بيانات المكلف");
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<title>الشاشة الرئيسية</title>

<style>
* {
    box-sizing: border-box;
}

body {
    margin: 0;
    background: white;
    font-family: "Segoe UI", Tahoma, sans-serif;
    color: black;
}

.header {
    width: 100%;
    height: 105px;
    overflow: hidden;
    border-bottom: 6px solid #1f86d7;
}

.header img {
    width: 100%;
    height: 105px;
    object-fit: cover;
    display: block;
}

.container {
    width: 1160px;
    max-width: 96%;
    margin: 0 auto;
    padding-top: 35px;
}

.title {
    text-align: center;
    font-size: 30px;
    font-weight: bold;
    margin-bottom: 32px;
}

.navbar {
    height: 52px;
    background: #4774c4;
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    align-items: center;
    margin-bottom: 40px;
}

.navbar a,
.dropdown-btn {
    color: white;
    text-decoration: none;
    text-align: center;
    font-size: 17px;
    font-weight: bold;
    padding: 15px;
    display: block;
}

.navbar a:hover,
.dropdown:hover .dropdown-btn {
    background: #315da8;
}

.dropdown {
    position: relative;
}

.dropdown-content {
    display: none;
    position: absolute;
    top: 52px;
    right: 0;
    background: white;
    min-width: 240px;
    border: 1px solid #ccc;
    z-index: 10;
}

.dropdown-content a {
    color: black;
    text-align: right;
    padding: 12px 15px;
    font-size: 15px;
    border-bottom: 1px solid #eee;
}

.dropdown-content a:hover {
    background: #f1f1f1;
}

.dropdown:hover .dropdown-content {
    display: block;
}

.info-row {
    display: flex;
    justify-content: center;
    gap: 55px;
    margin-bottom: 45px;
}

.info-box {
    display: flex;
    align-items: center;
    gap: 15px;
}

.info-box label {
    font-weight: bold;
    font-size: 17px;
}

.info-box input {
    width: 275px;
    height: 37px;
    border: 1px solid #777;
    background: white;
    font-size: 16px;
    padding: 5px 10px;
    text-align: right;
}

.cards {
    display: grid;
    grid-template-columns: repeat(2, 480px);
    justify-content: center;
    gap: 38px 40px;
}

.card-btn {
    height: 115px;
    border: 1px solid #ddd;
    border-radius: 8px;
    background: white;
    color: black;
    text-decoration: none;
    font-size: 23px;
    font-weight: bold;
    display: flex;
    justify-content: center;
    align-items: center;
    box-shadow: inset 0 18px 30px rgba(0,0,0,0.05),
                inset 0 -18px 30px rgba(0,0,0,0.08),
                0 2px 8px rgba(0,0,0,0.08);
}

.card-btn:hover {
    background: #f5f7ff;
    color: royalblue;
}

.ai-btn {
    position: fixed;
    bottom: 45px;
    left: 45px;
    width: 70px;
    height: 70px;
    background: #4774c4;
    color: white;
    border-radius: 4px;
    text-decoration: none;
    font-size: 24px;
    font-weight: bold;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 10px rgba(0,0,0,0.25);
}

@media (max-width: 1000px) {
    .cards {
        grid-template-columns: 1fr;
    }

    .card-btn {
        width: 100%;
    }

    .info-row {
        flex-direction: column;
        align-items: center;
    }

    .navbar {
        grid-template-columns: 1fr;
        height: auto;
    }

    .dropdown-content {
        position: static;
    }
}
.logout-btn{
    position:fixed;
    bottom:20px;
    right:20px;

    width:150px;
    height:48px;

    background:#dc3545;
    color:white;

    border:none;
    border-radius:6px;

    font-size:18px;
    font-weight:bold;

    cursor:pointer;

    z-index:999;
}

.logout-btn:hover{
    background:#b52a37;
}
.support-btn{
    position:fixed;
    bottom:100px;
    left:20px;

    width:140px;
    height:48px;

    background:royalblue;
    color:white;

    border:none;
    border-radius:6px;

    font-size:18px;
    font-weight:bold;

    cursor:pointer;
}
</style>
</head>

<body>


<div class="header">
    <img src="photo.jpeg" alt="الشعار">
</div>

<div class="container">

    <div class="title">الشاشة الرئيسية</div>

    <div class="navbar">

        <a href="myrequests.php">طلباتي</a>

        <a href="personal_data.php">البيانات الشخصية</a>

        <div class="dropdown">
            <div class="dropdown-btn">الخدمات الداخلية</div>

            <div class="dropdown-content">
                <a href="taxreturn.php">إقرار ضريبة المبيعات</a>
                <a href="installment.php">طلب تقسيط ضريبة المبيعات</a>
                <a href="amendment.php">طلب تعديل إقرار مبيعات</a>
                <a href="cancel.php">إلغاء تسجيل مكلف</a>
            </div>
        </div>

        <a href="payment.php">الدفع الإلكتروني</a>

    </div>

    <div class="info-row">

        <div class="info-box">
            <label>رقم المكلف</label>
            <input type="text" value="<?= htmlspecialchars($taxpayer_number) ?>" readonly>
        </div>

        <div class="info-box">
            <label>اسم المكلف</label>
            <input type="text" value="<?= htmlspecialchars($taxpayer_name) ?>" readonly>
        </div>

    </div>

    <div class="cards">

        <a href="taxreturn.php" class="card-btn">
            إقرار ضريبة المبيعات
        </a>

        <a href="installment.php" class="card-btn">
            طلب تقسيط ضريبة المبيعات
        </a>

        <a href="amendment.php" class="card-btn">
            طلب تعديل إقرار مبيعات
        </a>

        <a href="cancel.php" class="card-btn">
            إلغاء تسجيل مكلف
        </a>

    </div>
    

</div>

<a href="chatbot.php" class="ai-btn">Ai</a>
<button
onclick="window.location.href='support_chat.php'"
class="support-btn">

الدعم

</button>
<button
onclick="window.location.href='logout.php'"
class="logout-btn">


تسجيل خروج

</button>
</body>
</html>
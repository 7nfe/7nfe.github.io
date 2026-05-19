<?php
session_start();
include "config.php";

if (!isset($_SESSION["taxpayer_id"])) {
    header("Location: login.php");
    exit;
}

$taxpayer_id = $_SESSION["taxpayer_id"];
$message = "";

$stmt = mysqli_prepare($conn, "
    SELECT taxpayer_name, phone_number, email, trade_name, address
    FROM taxpayers
    WHERE taxpayer_id = ?
");
mysqli_stmt_bind_param($stmt, "i", $taxpayer_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $taxpayer_name = trim($_POST["taxpayer_name"]);
    $phone_number = trim($_POST["phone_number"]);
    $email = trim($_POST["email"]);
    $trade_name = trim($_POST["trade_name"]);
    $address = trim($_POST["address"]);

    $insert = mysqli_prepare($conn, "
        INSERT INTO edit_requests
        (taxpayer_id, taxpayer_name, phone_number, email, trade_name, address)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    mysqli_stmt_bind_param(
        $insert,
        "isssss",
        $taxpayer_id,
        $taxpayer_name,
        $phone_number,
        $email,
        $trade_name,
        $address
    );

    mysqli_stmt_execute($insert);

    $message = "تم إرسال طلب تعديل البيانات إلى الأدمن";
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>طلب تعديل البيانات</title>

    <style>
        body {
            font-family: Segoe UI;
            background: #f4f4f4;
            margin: 0
        }

        .box {
            width: 520px;
            background: white;
            margin: 60px auto;
            padding: 30px;
            border: 1px solid #ccc
        }

        h2 {
            text-align: center;
            color: royalblue
        }

        label {
            font-weight: bold;
            display: block;
            margin-top: 15px
        }

        input,
        textarea {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            font-size: 16px;
            box-sizing: border-box
        }

        textarea {
            height: 90px;
            resize: none
        }

        button {
            width: 100%;
            height: 45px;
            background: royalblue;
            color: white;
            border: none;
            margin-top: 20px;
            font-size: 18px;
            font-weight: bold
        }

        .msg {
            text-align: center;
            color: green;
            font-weight: bold;
            margin-top: 15px
        }

        .back {
            text-align: center;
            display: block;
            margin-top: 15px
        }
    </style>
</head>

<body>

    <div class="box">
        <h2>طلب تعديل البيانات الشخصية</h2>

        <form method="POST">

            <label>اسم المكلف</label>
            <input type="text" name="taxpayer_name" value="<?= htmlspecialchars($user["taxpayer_name"]) ?>" required>

            <label>رقم الهاتف</label>
            <input type="text" name="phone_number" value="<?= htmlspecialchars($user["phone_number"]) ?>" required>

            <label>البريد الإلكتروني</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user["email"]) ?>" required>

            <label>الاسم التجاري</label>
            <input type="text" name="trade_name" value="<?= htmlspecialchars($user["trade_name"]) ?>" required>

            <label>العنوان</label>
            <textarea name="address" required><?= htmlspecialchars($user["address"]) ?></textarea>

            <button type="submit">إرسال الطلب للأدمن</button>

        </form>

        <?php if ($message != ""): ?>
            <div class="msg"><?= $message ?></div>
        <?php endif; ?>

        <a href="personal_data.php" class="back">رجوع</a>
    </div>

</body>

</html>
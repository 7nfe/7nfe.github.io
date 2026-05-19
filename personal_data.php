<?php
session_start();
include "config.php";

if (!isset($_SESSION["taxpayer_id"])) {
    header("Location: login.php");
    exit;
}

$taxpayer_id = $_SESSION["taxpayer_id"];

$query = "
SELECT 
    taxpayer_number,
    taxpayer_name,
    phone_number,
    email,
    trade_name,
    registration_number,
    address,
    national_establishment_number,
    date_of_establishment,
    taxpayer_classification,
    designated_directorate
FROM taxpayers
WHERE taxpayer_id = ?
LIMIT 1
";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $taxpayer_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (!$row = mysqli_fetch_assoc($result)) {
    die("لم يتم العثور على بيانات المكلف");
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>البيانات الشخصية</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #f3f3f3;
            font-family: "Segoe UI", Tahoma, sans-serif;
            direction: rtl;
        }

        .header {
            width: 100%;
            height: 82px;
            overflow: hidden;
            border-bottom: 6px solid #1f86d7;
            background: white;
        }

        .header img {
            width: 100%;
            height: 82px;
            object-fit: cover;
            display: block;
        }

        .container {
            width: 1200px;
            max-width: 96%;
            margin: auto;
            padding-top: 30px;
        }

        .title {
            text-align: center;
            font-size: 34px;
            font-weight: bold;
            margin-bottom: 50px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 420px);
            justify-content: center;
            column-gap: 90px;
            row-gap: 35px;
        }

        .form-group {
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .form-group label {
            width: 170px;
            font-size: 18px;
            font-weight: bold;
            text-align: right;
        }

        .form-group input {
            width: 240px;
            height: 38px;
            border: 1px solid #aaa;
            background: white;
            font-size: 17px;
            padding: 5px 10px;
            font-family: "Segoe UI", Tahoma, sans-serif;
        }

        .buttons {
            text-align: center;
            margin-top: 85px;
        }

        .btn {
            width: 110px;
            height: 45px;
            background: royalblue;
            color: white;
            border: none;
            font-size: 20px;
            font-weight: bold;
            cursor: pointer;
            margin: 0 35px;
        }

        .btn:hover {
            background: #244fc7;
        }

        @media (max-width:1000px) {

            .form-grid {
                grid-template-columns: 1fr;
                justify-items: center;
            }

            .form-group {
                width: 100%;
                justify-content: center;
            }

            .buttons {
                display: flex;
                flex-direction: column;
                gap: 15px;
                align-items: center;
            }

            .btn {
                margin: 0;
            }
        }
    </style>
</head>

<body>

    <div class="header">
        <img src="photo.jpeg">
    </div>

    <div class="container">

        <div class="title">
            البيانات الشخصية
        </div>

        <div class="form-grid">

            <div class="form-group">
                <label>رقم المكلف</label>
                <input type="text"
                    value="<?= htmlspecialchars($row["taxpayer_number"]) ?>"
                    readonly>
            </div>

            <div class="form-group">
                <label>البريد الإلكتروني</label>
                <input type="text"
                    value="<?= htmlspecialchars($row["email"]) ?>"
                    readonly>
            </div>

            <div class="form-group">
                <label>تصنيف المكلف</label>
                <input type="text"
                    value="<?= htmlspecialchars($row["taxpayer_classification"]) ?>"
                    readonly>
            </div>

            <div class="form-group">
                <label>رقم السجل التجاري</label>
                <input type="text"
                    value="<?= htmlspecialchars($row["registration_number"]) ?>"
                    readonly>
            </div>

            <div class="form-group">
                <label>رقم الهاتف</label>
                <input type="text"
                    value="<?= htmlspecialchars($row["phone_number"]) ?>"
                    readonly>
            </div>

            <div class="form-group">
                <label>الرقم الوطني للمنشأة</label>
                <input type="text"
                    value="<?= htmlspecialchars($row["national_establishment_number"]) ?>"
                    readonly>
            </div>

            <div class="form-group">
                <label>مديرية المكلف</label>
                <input type="text"
                    value="<?= htmlspecialchars($row["designated_directorate"]) ?>"
                    readonly>
            </div>

            <div class="form-group">
                <label>تاريخ إنشاء الشركة</label>
                <input type="text"
                    value="<?= htmlspecialchars($row["date_of_establishment"]) ?>"
                    readonly>
            </div>

            <div class="form-group">
                <label>الاسم التجاري</label>
                <input type="text"
                    value="<?= htmlspecialchars($row["trade_name"]) ?>"
                    readonly>
            </div>

            <div class="form-group">
                <label>اسم المكلف</label>
                <input type="text"
                    value="<?= htmlspecialchars($row["taxpayer_name"]) ?>"
                    readonly>
            </div>

            <div class="form-group">
                <label>العنوان</label>
                <input type="text"
                    value="<?= htmlspecialchars($row["address"]) ?>"
                    readonly>
            </div>

        </div>

        <div class="buttons">

            <button
                class="btn"
                onclick="window.location.href='edit_data_request.php'">
                تعديل
            </button>

            <button
                class="btn"
                onclick="window.location.href='mains.php'">
                رجوع
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
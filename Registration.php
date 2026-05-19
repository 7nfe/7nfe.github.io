<?php
session_start();
include "config.php";

if (!isset($_SESSION["taxpayer_id"])) {
    header("Location: login.php");
    exit;
}

$taxpayer_id = $_SESSION["taxpayer_id"];
$message = "";

function generatePaymentNumber($conn)
{
    do {
        $number = rand(10000000, 99999999);

        $query = "SELECT COUNT(*) AS total FROM taxpayers WHERE electronic_payment_number = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $number);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
    } while ($row["total"] > 0);

    return $number;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (isset($_POST["register"])) {

        $taxpayer_name = trim($_POST["taxpayer_name"]);
        $phone_number = trim($_POST["phone_number"]);
        $email = trim($_POST["email"]);
        $trade_name = trim($_POST["trade_name"]);
        $address = trim($_POST["address"]);
        $registration_date = $_POST["registration_date"];
        $registration_type = $_POST["registration_type"];
        $designated_directorate = $_POST["designated_directorate"];
        $nature_of_activity = $_POST["nature_of_activity"];
        $registration_number = trim($_POST["registration_number"]);
        $national_establishment_number = trim($_POST["national_establishment_number"]);
        $date_of_establishment = $_POST["date_of_establishment"];
        $taxpayer_classification = $_POST["taxpayer_classification"];

        if (strlen($registration_number) < 6) {

            $message = "رقم السجل التجاري يجب أن يكون 6 خانات أو أكثر.";
        } elseif (!preg_match("/^[0-9]{10}$/", $phone_number)) {

            $message = "رقم الهاتف يجب أن يكون 10 خانات.";
        } elseif (!preg_match("/^[0-9]{9}$/", $national_establishment_number)) {

            $message = "الرقم الوطني للمنشأة يجب أن يكون 9 خانات بالضبط.";
        } else {

            $payment_number = generatePaymentNumber($conn);

            $query = "
            UPDATE taxpayers
            SET
                taxpayer_name = ?,
                phone_number = ?,
                email = ?,
                trade_name = ?,
                address = ?,
                registration_date = ?,
                registration_type = ?,
                designated_directorate = ?,
                nature_of_activity = ?,
                registration_number = ?,
                national_establishment_number = ?,
                date_of_establishment = ?,
                taxpayer_classification = ?,
                electronic_payment_number = ?,
                is_registered = 2
            WHERE taxpayer_id = ?
            ";

            $stmt = mysqli_prepare($conn, $query);

            mysqli_stmt_bind_param(
                $stmt,
                "ssssssssssssssi",
                $taxpayer_name,
                $phone_number,
                $email,
                $trade_name,
                $address,
                $registration_date,
                $registration_type,
                $designated_directorate,
                $nature_of_activity,
                $registration_number,
                $national_establishment_number,
                $date_of_establishment,
                $taxpayer_classification,
                $payment_number,
                $taxpayer_id
            );

            if (mysqli_stmt_execute($stmt)) {

                session_unset();
                session_destroy();

                echo "
                <script>
                    alert('تم إرسال طلب التسجيل للأدمن، يرجى انتظار الموافقة');
                    window.location.href = 'login.php';
                </script>
                ";
                exit;
            } else {

                $message = "حدث خطأ أثناء حفظ البيانات";
            }
        }
    }

    if (isset($_POST["info"])) {
        $message = "تاريخ بداية التسجيل هو التاريخ الذي يبدأ منه اعتبار المكلف مسجلاً في ضريبة المبيعات.";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>التسجيل في ضريبة المبيعات</title>

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
            width: 1300px;
            max-width: 98%;
            margin: 0 auto;
            padding-top: 20px;
        }

        .title {
            text-align: center;
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 40px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(3, 320px);
            justify-content: center;
            column-gap: 85px;
            row-gap: 35px;
        }

        .form-group {
            width: 320px;
        }

        .form-group label {
            display: block;
            text-align: right;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        input,
        select {
            width: 100%;
            height: 36px;
            border: 1px solid #aaa;
            background: white;
            padding: 5px 10px;
            font-size: 16px;
            font-family: "Segoe UI", Tahoma, sans-serif;
        }

        .radio-box {
            width: 100%;
            height: 52px;
            border: 1px solid #aaa;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 35px;
            background: white;
        }

        .radio-box label {
            margin: 0;
            font-weight: normal;
            font-size: 17px;
        }

        .info-btn {
            width: 55px;
            height: 42px;
            background: royalblue;
            color: white;
            border: none;
            font-size: 22px;
            font-weight: bold;
            cursor: pointer;
        }

        .date-row {
            display: flex;
            gap: 10px;
        }

        .classification {
            width: 710px;
            border: 1px solid #ddd;
            background: #fafafa;
            padding: 15px;
            margin-top: 10px;
            margin-right: auto;
        }

        .classification-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 18px;
        }

        .classification-options {
            display: flex;
            gap: 50px;
            font-size: 18px;
        }

        .buttons {
            text-align: center;
            margin-top: 90px;
            margin-bottom: 40px;
        }

        .main-btn {
            width: 120px;
            height: 45px;
            background: royalblue;
            color: white;
            border: none;
            font-size: 20px;
            font-weight: bold;
            cursor: pointer;
            margin: 0 18px;
        }

        .main-btn:hover,
        .info-btn:hover {
            background: #244fc7;
        }

        .message {
            text-align: center;
            color: red;
            font-size: 18px;
            font-weight: bold;
            margin-top: 20px;
        }

        @media (max-width:1200px) {
            .form-grid {
                grid-template-columns: 1fr;
                justify-items: center;
            }

            .classification {
                width: 320px;
                margin: auto;
            }

            .classification-options {
                flex-direction: column;
                gap: 15px;
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
            التسجيل في ضريبة المبيعات
        </div>

        <?php if ($message != ""): ?>
            <div class="message">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <div class="form-grid">

                <div class="form-group">
                    <label>نوع الطلب</label>

                    <div class="radio-box">
                        <label>
                            <input type="radio" name="registration_type" value="تسجيل" checked>
                            تسجيل
                        </label>

                        <label>
                            <input type="radio" name="registration_type" value="اعادة تسجيل">
                            اعادة تسجيل
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>اسم المكلف</label>
                    <input type="text" name="taxpayer_name" required>
                </div>

                <div class="form-group">
                    <label>مديرية المكلف</label>
                    <select name="designated_directorate">
                        <option value="العقبة">العقبة</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>العنوان</label>
                    <input type="text" name="address" required>
                </div>

                <div class="form-group">
                    <label>رقم الهاتف</label>
                    <input type="text" name="phone_number" minlength="10" maxlength="10" required>
                </div>

                <div class="form-group">
                    <label>البريد الإلكتروني</label>
                    <input type="email" name="email" required>
                </div>

                <div class="form-group">
                    <label>طبيعة النشاط التجاري</label>
                    <select name="nature_of_activity" required>
                        <option value="">اختر</option>
                        <option value="خدمي">خدمي</option>
                        <option value="تجاري">تجاري</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>رقم السجل التجاري</label>
                    <input type="text" name="registration_number" minlength="6" required>
                </div>

                <div class="form-group">
                    <label>الاسم التجاري</label>
                    <input type="text" name="trade_name" required>
                </div>

                <div class="form-group">
                    <label>تاريخ بداية التسجيل</label>

                    <div class="date-row">
                        <button type="submit" name="info" class="info-btn">!</button>
                        <input type="date" name="registration_date" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>الرقم الوطني للمنشأة</label>
                    <input type="text" name="national_establishment_number" minlength="9" maxlength="9" required>
                </div>

                <div class="form-group">
                    <label>تاريخ إنشاء الشركة</label>
                    <input type="date" name="date_of_establishment" required>
                </div>

            </div>

            <div class="classification">

                <div class="classification-title">
                    تصنيف المكلف
                </div>

                <div class="classification-options">

                    <label>
                        <input type="radio" name="taxpayer_classification" value="تضامن توصية بسيطة" checked>
                        تضامن توصية بسيطة
                    </label>

                    <label>
                        <input type="radio" name="taxpayer_classification" value="ذات مسؤولية محدودة">
                        ذات مسؤولية محدودة
                    </label>

                </div>
            </div>

            <div class="buttons">

                <button type="button" onclick="window.print()" class="main-btn">
                    طباعة
                </button>

                <button type="submit" name="register" class="main-btn">
                    تسجيل
                </button>

            </div>

        </form>

    </div>

</body>

</html>
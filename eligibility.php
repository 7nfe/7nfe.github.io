<?php
session_start();
include "config.php";

if (!isset($_SESSION["taxpayer_id"])) {
    header("Location: login.php");
    exit;
}

$taxpayer_id = $_SESSION["taxpayer_id"];
$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (isset($_POST["open_pdf"])) {
        $pdfPath = "جدول السلع والخدمات.pdf";

        if (file_exists($pdfPath)) {
            header("Location: " . $pdfPath);
            exit;
        } else {
            $message = "ملف جدول السلع والخدمات غير موجود داخل مجلد المشروع.";
            $message_type = "error";
        }
    }

    if (isset($_POST["next"])) {

        $goods = $_POST["goods"] ?? "";
        $services = $_POST["services"] ?? "";

        if ($goods === "" || $services === "") {
            $message = "يجب الإجابة على السؤالين قبل المتابعة.";
            $message_type = "error";
        } else {

            $is_goods = ($goods === "yes") ? 1 : 0;
            $is_service = ($services === "yes") ? 1 : 0;

            $query = "
                UPDATE taxpayers
                SET is_goods = ?, is_service = ?
                WHERE taxpayer_id = ?
            ";

            $stmt = mysqli_prepare($conn, $query);

            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "iii", $is_goods, $is_service, $taxpayer_id);
                mysqli_stmt_execute($stmt);

                if (mysqli_stmt_affected_rows($stmt) >= 0) {

                   if ($is_goods == 0 && $is_service == 0) {

    $message = "أنت غير ملزم بالتسجيل في ضريبة المبيعات.";

} else {

    header("Location: registration.php");
    exit;
}

                } else {
                    $message = "لم يتم العثور على المكلف في قاعدة البيانات.";
                    $message_type = "error";
                }

                mysqli_stmt_close($stmt);
            } else {
                $message = "حدث خطأ أثناء تجهيز الاستعلام.";
                $message_type = "error";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<title>هل أنت ملزم؟</title>

<style>
* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: "Segoe UI", Tahoma, sans-serif;
    background: white;
    color: black;
}

.header {
    width: 100%;
    height: 82px;
    overflow: hidden;
    border-bottom: 1px solid #ddd;
}

.header img {
    width: 100%;
    height: 82px;
    object-fit: cover;
}

.container {
    width: 100%;
    min-height: calc(100vh - 82px);
    display: flex;
    flex-direction: column;
    align-items: center;
    padding-top: 55px;
}

.title {
    font-size: 32px;
    font-weight: bold;
    margin-bottom: 10px;
}

.form-row {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 15px;
}

.questions-box {
    width: 560px;
    background: white;
    border: 1px solid #ddd;
    padding: 35px 25px;
    margin-top: 10px;
}

.question {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 35px;
    font-size: 20px;
}

.options {
    display: flex;
    gap: 20px;
    font-size: 18px;
}

.options label {
    cursor: pointer;
}

.btn {
    background: royalblue;
    color: white;
    border: none;
    width: 150px;
    height: 45px;
    font-size: 17px;
    cursor: pointer;
    font-family: "Segoe UI", Tahoma, sans-serif;
}

.btn:hover {
    background: #244fc7;
}

.btn-pdf {
    margin-top: 95px;
}

.btn-next {
    margin-top: 18px;
}

.message {
    margin-top: 20px;
    font-size: 18px;
    font-weight: bold;
    text-align: center;
}

.error {
    color: red;
}

.info {
    color: royalblue;
}

@media (max-width: 750px) {
    .form-row {
        flex-direction: column;
    }

    .questions-box {
        width: 90%;
    }

    .question {
        flex-direction: column;
        gap: 15px;
        text-align: center;
    }

    .btn-pdf {
        margin-top: 10px;
    }
}
</style>
</head>

<body>

<div class="header">
    <img src="photo.jpeg" alt="الشعار">
</div>

<div class="container">

    <div class="title">هل أنت ملزم؟</div>

    <form method="POST">

        <div class="form-row">

            <div class="questions-box">

                <div class="question">
                    <div>هل أنت خاضع لضريبة المبيعات على السلع؟</div>

                    <div class="options">
                        <label>
                            <input type="radio" name="goods" value="yes">
                            نعم
                        </label>

                        <label>
                            <input type="radio" name="goods" value="no">
                            لا
                        </label>
                    </div>
                </div>

                <div class="question">
                    <div>هل أنت خاضع لضريبة المبيعات على الخدمات؟</div>

                    <div class="options">
                        <label>
                            <input type="radio" name="services" value="yes">
                            نعم
                        </label>

                        <label>
                            <input type="radio" name="services" value="no">
                            لا
                        </label>
                    </div>
                </div>

            </div>

            <button type="submit" name="open_pdf" class="btn btn-pdf">
                عرض القائمة
            </button>

        </div>

        <div style="text-align:center;">
            <button type="submit" name="next" class="btn btn-next">
                التالي
            </button>
        </div>

    </form>

    <?php if (!empty($message)): ?>
        <div class="message <?= $message_type ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

</div>

</body>
</html>
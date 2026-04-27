<?php
session_start();

// معالجة البيانات عند الضغط على "التالي"
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $goods_tax = $_POST['goods_tax'] ?? '';
    $services_tax = $_POST['services_tax'] ?? '';

    // تخزين الإجابات في الجلسة لاستخدامها لاحقاً في النظام
    $_SESSION['is_subject_to_goods'] = $goods_tax;
    $_SESSION['is_subject_to_services'] = $services_tax;

    // التوجيه إلى صفحة تسجيل الدخول أو الصفحة التالية
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>هل أنت ملزم؟ - ضريبة المبيعات</title>
    <style>
        /* التنسيقات العامة */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            height: 100vh;
        }

        /* الترويسة العلوية */
        .header-banner {
            width: 100%;
            height: 80px;
            background-color: #ffffff;
            border-bottom: 4px solid #4873c4;
            display: flex;
            align-items: center;
            padding: 0 20px;
            box-sizing: border-box;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .header-text {
            font-weight: bold;
            color: #4873c4;
            text-align: left;
            width: 100%;
            line-height: 1.5;
        }

        /* حاوية المحتوى الرئيسي */
        .content-container {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            width: 100%;
        }

        .page-title {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 30px;
            color: #000;
        }

        /* الصندوق الأبيض المركزي */
        .white-box {
            background-color: #ffffff;
            width: 700px;
            padding: 50px;
            border: 1px solid #ccc;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            position: relative;
        }

        /* تنسيق الأسئلة والخيارات */
        .question-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }

        .question-text {
            font-size: 18px;
            font-weight: bold;
            flex-grow: 1;
            text-align: right;
        }

        .options-group {
            display: flex;
            gap: 30px;
            margin-right: 40px;
        }

        .option {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 18px;
        }

        /* تنسيق الأزرار */
        .btn-list {
            position: absolute;
            left: -120px; /* تموضعه بجانب الصندوق كما في الصورة */
            top: 50%;
            transform: translateY(-50%);
            background-color: #4873c4;
            color: white;
            border: none;
            padding: 10px 25px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn-next {
            background-color: #4873c4;
            color: white;
            border: none;
            padding: 12px 60px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 50px;
            transition: background-color 0.3s;
        }

        .btn-list:hover, .btn-next:hover {
            background-color: #365a9e;
        }

        input[type="radio"] {
            transform: scale(1.2);
            cursor: pointer;
        }
    </style>
</head>
<body>

    <div class="header-banner">
        <div class="header-text">
            المملكة الأردنية الهاشمية<br>وزارة المالية<br>دائرة ضريبة الدخل والمبيعات
        </div>
    </div>

    <div class="content-container">
        <div class="page-title">هل أنت ملزم؟</div>

        <form method="POST" action="">
            <div class="white-box">
                <button type="button" class="btn-list">عرض القائمة</button>

                <div class="question-row">
                    <div class="question-text">هل أنت خاضع لضريبة المبيعات على السلع؟</div>
                    <div class="options-group">
                        <label class="option">
                            <input type="radio" name="goods_tax" value="yes" required> نعم
                        </label>
                        <label class="option">
                            <input type="radio" name="goods_tax" value="no"> لا
                        </label>
                    </div>
                </div>

                <div class="question-row" style="margin-bottom: 0;">
                    <div class="question-text">هل أنت خاضع لضريبة المبيعات على الخدمات؟</div>
                    <div class="options-group">
                        <label class="option">
                            <input type="radio" name="services_tax" value="yes" required> نعم
                        </label>
                        <label class="option">
                            <input type="radio" name="services_tax" value="no"> لا
                        </label>
                    </div>
                </div>
            </div>

            <div style="text-align: center;">
                <button type="submit" class="btn-next">التالي</button>
            </div>
        </form>
    </div>

</body>
</html>
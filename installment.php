<?php
// معالجة البيانات عند إرسال طلب التقسيط
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // استلام البيانات من النموذج
    // $taxpayer_number = $_POST['taxpayer_number'];
    // $tax_period = $_POST['tax_period'];
    // $first_payment = $_POST['first_payment'];
    // $installments_count = $_POST['installments_count'];
    
    // يمكن هنا إضافة كود الاتصال بقاعدة البيانات الخاصة بنظام ضريبة العقبة
    // لحفظ طلب التقسيط وربطه بملف المكلف
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>طلب تقسيط ضريبة المبيعات</title>
    <style>
        /* التنسيقات العامة */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
        }

        .main-container {
            width: 95%;
            max-width: 1100px;
            background-color: #ffffff;
            min-height: 100vh;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            position: relative;
            padding-bottom: 50px;
        }

        /* الترويسة */
        .header-banner {
            width: 100%;
            height: 80px;
            background-color: #ffffff;
            border-bottom: 4px solid #4873c4;
            display: flex;
            align-items: center;
            padding: 0 20px;
            box-sizing: border-box;
        }

        .header-text {
            font-weight: bold;
            color: #4873c4;
            text-align: left;
            width: 100%;
            line-height: 1.5;
        }

        .page-title {
            text-align: center;
            color: #000;
            margin: 30px 0 50px 0;
            font-size: 26px;
            font-weight: bold;
        }

        /* الصف العلوي (رقم المكلف واسم المكلف) */
        .top-row {
            display: flex;
            justify-content: center;
            gap: 100px;
            margin-bottom: 40px;
        }

        .top-group {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 300px;
        }

        .top-group label {
            font-weight: bold;
            margin-bottom: 10px;
            font-size: 16px;
        }

        .top-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid #aaa;
            text-align: center;
            font-size: 15px;
        }

        /* القائمة المركزية (الصفوف التي تحتوي على زر i) */
        .middle-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
        }

        .row-item {
            display: flex;
            align-items: center;
            width: 600px;
            justify-content: space-between;
        }

        .row-item label {
            width: 150px;
            font-weight: bold;
            font-size: 16px;
            text-align: right;
        }

        .row-item input {
            width: 300px;
            padding: 8px;
            border: 1px solid #aaa;
            text-align: center;
            font-size: 15px;
        }

        .btn-info {
            background-color: #4873c4;
            color: white;
            border: none;
            width: 35px;
            height: 35px;
            font-weight: bold;
            font-size: 18px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* زر AI العائم على اليسار */
        .btn-ai {
            position: absolute;
            left: 60px;
            top: 50%;
            transform: translateY(-50%);
            background-color: #4873c4;
            color: white;
            border: none;
            width: 60px;
            height: 60px;
            font-size: 20px;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 2px 2px 5px rgba(0,0,0,0.2);
        }

        /* أزرار الإجراءات السفلية */
        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin-top: 60px;
        }

        .btn-action {
            background-color: #4873c4;
            color: white;
            border: none;
            padding: 10px 40px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            width: 120px;
            transition: 0.3s;
        }

        .btn-action:hover {
            background-color: #365a9e;
        }
    </style>
</head>
<body>

    <div class="main-container">
        
        <div class="header-banner">
            <div class="header-text">
                المملكة الأردنية الهاشمية<br>وزارة المالية<br>دائرة ضريبة الدخل والمبيعات
            </div>
        </div>

        <div class="page-title">طلب تقسيط ضريبة المبيعات</div>

        <button type="button" class="btn-ai">AI</button>

        <form method="POST" action="">
            
            <div class="top-row">
                <div class="top-group">
                    <label>رقم المكلف</label>
                    <input type="text" name="taxpayer_number">
                </div>
                <div class="top-group">
                    <label>اسم المكلف</label>
                    <input type="text" name="taxpayer_name">
                </div>
            </div>

            <div class="middle-section">
                
                <div class="row-item">
                    <label>الفترة الضريبية</label>
                    <input type="text" name="tax_period">
                    <button type="button" class="btn-info">i</button>
                </div>

                <div class="row-item">
                    <label>الرصيد المستحق</label>
                    <input type="text" name="due_balance">
                    <button type="button" class="btn-info">i</button>
                </div>

                <div class="row-item">
                    <label>الدفعة الأولى</label>
                    <input type="text" name="first_payment">
                    <button type="button" class="btn-info">i</button>
                </div>

                <div class="row-item">
                    <label>عدد الأقساط</label>
                    <input type="text" name="installments_count">
                    <button type="button" class="btn-info">i</button>
                </div>

            </div>

            <div class="action-buttons">
                <button type="button" class="btn-action">طباعة</button>
                <button type="submit" class="btn-action">إرسال</button>
              <button type="button" class="btn-action" onclick="window.location.href='mains.php'">عودة</button>
            </div>

        </form>

    </div>

</body>
</html>
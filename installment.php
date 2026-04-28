<?php
// معالجة البيانات عند إرسال طلب التقسيط
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // هنا يتم استقبال البيانات ومعالجتها
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

     .header-banner {
    width: 100%;
    height: 100px; /* يمكنك زيادة الارتفاع حسب حجم الصورة */
    background-color: #ffffff;
    border-bottom: 6px solid #4873c4;
    padding: 0; /* أزلنا الحشو الداخلي لتأخذ الصورة كامل المساحة إذا أردت */
    overflow: hidden; /* لمنع خروج الصورة عن الإطار */
    display: flex;
    align-items: center;
    justify-content: center; /* لتوسيط الصورة */
}

.header-image {
    width: 200%; /* تجعل الصورة بعرض الهيدر */
    height: 200%; /* تجعل الصورة بارتفاع الهيدر */
    object-fit: contain; /* أهم خاصية: تحافظ على تناسق أبعاد الصورة دون تمطيطها */
}

        .page-title {
            text-align: center;
            color: #000;
            margin: 30px 0 50px 0;
            font-size: 26px;
            font-weight: bold;
        }

        /* الصف العلوي */
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
            background-color: #f9f9f9;
        }

        /* القائمة المركزية */
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
            width: 200px;
            font-weight: bold;
            font-size: 16px;
            text-align: right;
        }

        /* حاوية الحقل الجديدة */
        .field-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .row-item input {
            width: 300px;
            /* إضافة مساحة من اليسار للأيقونة */
            padding: 8px 8px 8px 35px; 
            border: 1px solid #aaa;
            text-align: left;
            direction: ltr;
            font-size: 15px;
            box-sizing: border-box;
        }

        /* أيقونة i داخل الحقل من جهة اليسار */
        .btn-info-inside {
            position: absolute;
            left: 8px; 
            background-color: #4873c4;
            color: white;
            border: none;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            font-size: 14px;
            font-weight: bold;
            cursor: help;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 0;
            line-height: 1;
        }

        /* زر AI */
        .btn-ai {
            position: absolute;
            left: 40px;
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

        /* أزرار الإجراءات */
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
            min-width: 130px;
            transition: 0.3s;
        }

        .btn-action:hover { background-color: #365a9e; }
    </style>
</head>
<body>

    <div class="main-container">
        
        <div class="header-banner">
              <div class="header-banner">
    <img src="photo.jpeg" alt="شعار دائرة ضريبة الدخل والمبيعات" class="header-image">
</div>
        </div>

        <div class="page-title">طلب تقسيط ضريبة المبيعات</div>

        <button type="button" class="btn-ai">AI</button>

        <form method="POST" action="">
            
            <div class="top-row">
                <div class="top-group">
                    <label>رقم المكلف</label>
                    <input type="text" name="taxpayer_number" value="123456789" readonly>
                </div>
                <div class="top-group">
                    <label>اسم المكلف</label>
                    <input type="text" name="taxpayer_name" value="شركة العقبة للتجارة" readonly>
                </div>
            </div>

            <div class="middle-section">
                
                <div class="row-item">
                    <label>الفترة الضريبية</label>
                    <div class="field-wrapper">
                        <button type="button" class="btn-info-inside" title="حدد الفترة المراد تقسيطها">i</button>
                        <input type="text" name="tax_period">
                    </div>
                </div>

                <div class="row-item">
                    <label>الرصيد المستحق</label>
                    <div class="field-wrapper">
                        <button type="button" class="btn-info-inside" title="إجمالي المبلغ المستحق للدائرة">i</button>
                        <input type="number" step="0.01" name="due_balance">
                    </div>
                </div>

                <div class="row-item">
                    <label>الدفعة الأولى</label>
                    <div class="field-wrapper">
                        <button type="button" class="btn-info-inside" title="الحد الأدنى للدفعة الأولى هو 25%">i</button>
                        <input type="number" step="0.01" name="first_payment">
                    </div>
                </div>

                <div class="row-item">
                    <label>عدد الأقساط</label>
                    <div class="field-wrapper">
                        <button type="button" class="btn-info-inside" title="أقصى عدد أقساط مسموح به هو 12 شهر">i</button>
                        <input type="number" name="installments_count">
                    </div>
                </div>

            </div>

            <div class="action-buttons">
                <button type="submit" class="btn-action">إرسال</button>
                <button type="button" class="btn-action" onclick="window.print()">طباعة</button>
                <button type="button" class="btn-action" onclick="window.history.back()">عودة</button>
            </div>

        </form>

    </div>

</body>
</html>
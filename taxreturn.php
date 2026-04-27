<?php
// محاكاة لبيانات المكلف التي تأتي من قاعدة البيانات
$taxpayer_number = "123456789"; 
$taxpayer_name = "شركة العقبة للتجارة العامة"; 
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إقرار ضريبة المبيعات</title>
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
            width: 90%;
            max-width: 1000px;
            background-color: #ffffff;
            min-height: 100vh;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            position: relative;
            padding-bottom: 30px;
        }

        /* تعديل الهيدر ليحتوي على صورة */
        .header-banner {
            width: 100%;
            height: 100px; /* زيادة الارتفاع ليتناسب مع الصور عادةً */
            background-color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .header-banner img {
            width: 100%;
            height: 100%;
            object-fit: contain; /* يضمن ظهور الصورة كاملة بدون تشويه */
        }

        .page-title {
            text-align: center;
            color: #000;
            margin: 30px 0;
            font-size: 26px;
            font-weight: bold;
        }

        /* تخطيط الصفوف العلوي */
        .top-section { padding: 0 50px; margin-bottom: 30px; }
        .flex-row { display: flex; justify-content: space-around; margin-bottom: 20px; }
        .input-group { display: flex; flex-direction: column; align-items: center; gap: 10px; }
        .input-group label { font-weight: bold; font-size: 15px; }
        .input-group input { padding: 6px 10px; border: 1px solid #aaa; width: 200px; text-align: center; background-color: #f9f9f9; }

        /* قسم الحقول المالية */
        .finance-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
            margin-top: 20px;
        }

        .finance-row {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            width: 600px;
        }

        .finance-row label {
            width: 300px;
            font-weight: bold;
            font-size: 14px;
            text-align: right;
        }

        .field-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .finance-row input {
            width: 200px;
            /* التعديل المطلوب: مسافة من اليسار للأيقونة */
            padding: 8px 8px 8px 35px; 
            border: 1px solid #aaa;
            text-align: left;
            direction: ltr;
            box-sizing: border-box;
        }

        /* أيقونة i داخل الحقل من اليسار */
        .btn-info-inside {
            position: absolute;
            left: 8px; 
            background-color: #4873c4;
            color: white;
            border: none;
            width: 20px;
            height: 20px;
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

        .btn-info-inside:hover { background-color: #365a9e; }

        /* أزرار الإجراءات */
        .action-buttons { display: flex; justify-content: center; gap: 30px; margin-top: 40px; }
        .btn-action { background-color: #4873c4; color: white; border: none; padding: 10px 40px; font-size: 18px; font-weight: bold; cursor: pointer; transition: 0.3s; }
        .btn-action:hover { background-color: #365a9e; }
    </style>
</head>
<body>

    <div class="main-container">
        
        <div class="header-banner">
            <img src="photo.jpeg" alt="شعار الدائرة">
        </div>

        <div class="page-title">إقرار ضريبة المبيعات</div>

        <form method="POST" action="process_tax.php">
            
            <div class="top-section">
                <div class="flex-row">
                    <div class="input-group">
                        <label>رقم المكلف</label>
                        <input type="text" value="<?php echo htmlspecialchars($taxpayer_number); ?>" readonly>
                    </div>
                    <div class="input-group">
                        <label>اسم المكلف</label>
                        <input type="text" value="<?php echo htmlspecialchars($taxpayer_name); ?>" readonly>
                    </div>
                </div>
            </div>

            <div class="finance-section">
                
                <div class="finance-row">
                    <label>رصيد مدور من الفترة السابقة</label>
                    <div class="field-wrapper">
                        <button type="button" class="btn-info-inside" title="المبالغ الضريبية الفائضة من الفترة السابقة">i</button>
                        <input type="number" step="0.01" name="carried_forward">
                    </div>
                </div>

                <div class="finance-row">
                    <label>مبيعات خاضعة لنسبة 7%</label>
                    <div class="field-wrapper">
                        <button type="button" class="btn-info-inside" title="إجمالي المبيعات التي تخضع لضريبة 7%">i</button>
                        <input type="number" step="0.01" name="sales_7_percent">
                    </div>
                </div>

                <div class="finance-row">
                    <label>ضريبة المبيعات الخاضعة للنسبة</label>
                    <div class="field-wrapper">
                        <button type="button" class="btn-info-inside" title="قيمة الضريبة المستحقة على هذه المبيعات">i</button>
                        <input type="number" step="0.01" name="tax_subject_to_ratio">
                    </div>
                </div>

                <div class="finance-row">
                    <label>حركة تعديل لصالح المسجل</label>
                    <div class="field-wrapper">
                        <button type="button" class="btn-info-inside" title="خصومات ضريبية إضافية لصالحك">i</button>
                        <input type="number" step="0.01" name="adj_registrant">
                    </div>
                </div>

                <div class="finance-row">
                    <label>حركة تعديل لصالح الدائرة</label>
                    <div class="field-wrapper">
                        <button type="button" class="btn-info-inside" title="مبالغ إضافية لصالح دائرة الضريبة">i</button>
                        <input type="number" step="0.01" name="adj_department">
                    </div>
                </div>

                <div class="finance-row">
                    <label>الضريبة المستحقة موجبة</label>
                    <div class="field-wrapper">
                        <button type="button" class="btn-info-inside" title="المبلغ النهائي الواجب دفعه">i</button>
                        <input type="number" step="0.01" name="tax_due_positive">
                    </div>
                </div>

                <div class="finance-row">
                    <label>الضريبة المستحقة سالبة</label>
                    <div class="field-wrapper">
                        <button type="button" class="btn-info-inside" title="الرصيد المدور للفترة القادمة">i</button>
                        <input type="number" step="0.01" name="tax_due_negative">
                    </div>
                </div>

            </div>

            <div class="action-buttons">
                <button type="submit" class="btn-action">إرسال</button>
                <button type="button" class="btn-action" onclick="window.print()">طباعة</button>
                <button type="button" class="btn-action" onclick="window.location.href='mains.php'">إغلاق</button>
            </div>

        </form>
    </div>

</body>
</html>
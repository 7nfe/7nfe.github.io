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

        /* الترويسة */
        .header-banner {
            width: 100%;
            height: 80px;
            background-color: #ffffff;
            border-bottom: 4px solid #4873c4;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            box-sizing: border-box;
        }

        .page-title {
            text-align: center;
            color: #000;
            margin: 30px 0;
            font-size: 26px;
            font-weight: bold;
        }

        /* تخطيط الصفوف العلوية (رقم المكلف، القوائم المنسدلة) */
        .top-section {
            padding: 0 50px;
            margin-bottom: 30px;
        }

        .flex-row {
            display: flex;
            justify-content: space-around;
            margin-bottom: 20px;
        }

        .input-group {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        .input-group label {
            font-weight: bold;
            font-size: 15px;
        }

        .input-group input, .input-group select {
            padding: 6px 10px;
            border: 1px solid #aaa;
            width: 200px;
            text-align: center;
            font-size: 15px;
        }

        /* قسم الحقول المالية المتراصة */
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
            width: 600px; /* تحديد عرض ثابت لضمان المحاذاة */
        }

        .finance-row label {
            width: 300px;
            font-weight: bold;
            font-size: 14px;
            text-align: right;
        }

        .finance-row input {
            width: 200px;
            padding: 8px;
            border: 1px solid #aaa;
            text-align: left; /* الأرقام تكتب من اليسار */
            direction: ltr;
        }

        /* زر التعجب الأزرق الصغير */
        .btn-info-small {
            background-color: #4873c4;
            color: white;
            border: none;
            width: 35px;
            height: 35px;
            margin-right: 15px;
            font-weight: bold;
            font-size: 18px;
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* أزرار الإجراءات السفلية */
        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-top: 40px;
        }

        .btn-action {
            background-color: #4873c4;
            color: white;
            border: none;
            padding: 10px 40px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-action:hover {
            background-color: #365a9e;
        }

        /* زر الذكاء الاصطناعي */
        .ai-button {
            position: absolute;
            left: 40px;
            bottom: 100px;
            background-color: #4873c4;
            color: white;
            border: none;
            width: 60px;
            height: 60px;
            font-size: 20px;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>

    <div class="main-container">
        
        <div class="header-banner">
            <div style="font-weight: bold; color: #4873c4; text-align: left; width: 100%;">
                المملكة الأردنية الهاشمية<br>وزارة المالية<br>دائرة ضريبة الدخل والمبيعات
            </div>
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

                <div class="flex-row">
                    <div class="input-group">
                        <label>نوع الإقرار</label>
                        <select name="return_type">
                            <option value="">-- اختر --</option>
                            <option value="1">أصلي</option>
                            <option value="2">معدل</option>
                        </select>
                    </div>
                    <div class="input-group">
                        <label>السنة</label>
                        <select name="tax_year">
                            <option value="">-- اختر --</option>
                            <option value="2024">2024</option>
                            <option value="2025">2025</option>
                            <option value="2026">2026</option>
                        </select>
                    </div>
                    <div class="input-group">
                        <label>الفترة</label>
                        <select name="tax_period">
                            <option value="">-- اختر --</option>
                            <option value="1">الفترة الأولى</option>
                            <option value="2">الفترة الثانية</option>
                            <option value="3">الفترة الثالثة</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="finance-section">
                
                <div class="finance-row">
                    <label>رصيد مدور من الفترة السابقة</label>
                    <input type="number" step="0.01" name="carried_forward">
                    <button type="button" class="btn-info-small">!</button>
                </div>

                <div class="finance-row">
                    <label>مبيعات خاضعة لنسبة 7%</label>
                    <input type="number" step="0.01" name="sales_7_percent">
                    <button type="button" class="btn-info-small">!</button>
                </div>

                <div class="finance-row">
                    <label>ضريبة المبيعات الخاضعة للنسبة</label>
                    <input type="number" step="0.01" name="tax_subject_to_ratio">
                    <button type="button" class="btn-info-small">!</button>
                </div>

                <div class="finance-row">
                    <label>حركة تعديل لصالح المسجل</label>
                    <input type="number" step="0.01" name="adj_registrant">
                    <button type="button" class="btn-info-small">!</button>
                </div>

                <div class="finance-row">
                    <label>حركة تعديل لصالح الدائرة</label>
                    <input type="number" step="0.01" name="adj_department">
                    <button type="button" class="btn-info-small">!</button>
                </div>

                <div class="finance-row">
                    <label>الضريبة المستحقة موجبة <br><small>(مبلغ واجب دفعه)</small></label>
                    <input type="number" step="0.01" name="tax_due_positive">
                    <button type="button" class="btn-info-small">!</button>
                </div>

                <div class="finance-row">
                    <label>الضريبة المستحقة سالبة <br><small>(المدور لصالحك الفترة القادمة)</small></label>
                    <input type="number" step="0.01" name="tax_due_negative">
                    <button type="button" class="btn-info-small">!</button>
                </div>

            </div>

            <div class="action-buttons">
                <button type="submit" class="btn-action">إرسال</button>
                <button type="button" class="btn-action" onclick="window.print()">طباعة</button>
                <button type="button" class="btn-action" onclick="window.location.href='mains.php'">إلغاء / عودة</button>
            </div>

        </form>


    </div>

</body>
</html>
<?php
// معالجة البيانات عند إرسال النموذج (Submit)
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // استلام البيانات من النموذج
    $taxpayer_name = $_POST['taxpayer_name'] ?? '';
    $taxpayer_number = $_POST['taxpayer_number'] ?? '';
    
    // هنا يمكنك إضافة كود الحفظ في قاعدة البيانات
    $message = "تم استلام طلب المكلف: " . htmlspecialchars($taxpayer_name);
}
include 'config.php';



?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>التسجيل في ضريبة المبيعات - سلطة العقبة</title>
    <style>
        /* التنسيقات العامة */
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
        }

        .main-container {
            width: 100%;
            max-width: 1000px;
            background-color: #ffffff;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border-radius: 8px;
            overflow: hidden;
            padding-bottom: 40px;
        }

        /* تنسيق الهيدر والصورة */
        .header-banner {
            width: 100%;
            height: 100px;
            background-color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px 0;
        }

        .header-banner img {
            max-height: 100%;
            max-width: 100%;
            object-fit: contain;
        }

        .page-title {
            text-align: center;
            color: #333;
            margin: 25px 0;
            font-size: 24px;
            font-weight: bold;
            position: relative;
        }

        .page-title::after {
            content: '';
            display: block;
            width: 50px;
            height: 3px;
            background: #4873c4;
            margin: 8px auto;
        }

        /* شبكة الحقول */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            padding: 0 40px;
        }

        .input-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .input-group label {
            font-weight: 600;
            font-size: 14px;
            color: #555;
        }

        .input-group input, 
        .input-group select {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
            transition: border 0.3s;
        }

        .input-group input:focus {
            border-color: #4873c4;
            outline: none;
        }

        /* تنسيق الـ Fieldset */
        .full-width {
            grid-column: span 3;
            display: flex;
            justify-content: center;
            margin: 10px 0;
        }

        fieldset {
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 15px;
            display: flex;
            gap: 20px;
            justify-content: center;
            width: 100%;
        }

        legend {
            font-weight: bold;
            font-size: 14px;
            color: #4873c4;
            padding: 0 10px;
        }

        .radio-option {
            display: flex;
            align-items: center;
            gap: 5px;
            cursor: pointer;
        }

        /* أزرار الإجراءات */
        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 40px;
        }

        .btn-action {
            padding: 12px 40px;
            font-size: 16px;
            font-weight: bold;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-submit { background-color: #4873c4; color: white; }
        .btn-submit:hover { background-color: #365a9e; }

        .btn-cancel { background-color: #e74c3c; color: white; }
        .btn-print { background-color: #2ecc71; color: white; }

        .alert {
            margin: 20px 40px;
            padding: 15px;
            background-color: #d4edda;
            color: #155724;
            border-radius: 4px;
            text-align: center;
        }

        /* للموبايل */
        @media (max-width: 768px) {
            .form-grid { grid-template-columns: 1fr; }
            .full-width { grid-column: span 1; }
            .action-buttons { flex-direction: column; align-items: center; }
            .btn-action { width: 80%; }
        }
    </style>
</head>
<body>

    <div class="main-container">
        
        <!-- الهيدر -->
        <div class="header-banner">
            <!-- تأكد أن ملف الصورة photo.jpeg موجود في نفس المجلد -->
            <img src="photo.jpeg" alt="شعار منطقة العقبة الاقتصادية الخاصة">
        </div>

        <div class="page-title">نموذج التسجيل في ضريبة المبيعات</div>

        <?php if ($message): ?>
            <div class="alert"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="POST" action="insert_registration.php">
            <div class="form-grid">
                
                <!-- الصف الأول -->
                <div class="input-group">
                    <label>رقم المكلف</label>
                    <input type="text" name="taxpayer_number" required>
                </div>
                <div class="input-group">
                    <label>اسم المكلف</label>
                    <input type="text" name="taxpayer_name" required>
                </div>
                <div class="input-group">
                    <label>رقم الهاتف</label>
                    <input type="text" name="phone">
                </div>

                <!-- نوع الشخص الاعتباري -->
                <div class="full-width">
                    <fieldset>
                        <legend>نوع الشخص الاعتباري</legend>
                        <label class="radio-option"><input type="radio" name="legal_entity_type" value="تضامن توصية بسيطة"> تضامن توصية بسيطة</label>
                        <label class="radio-option"><input type="radio" name="legal_entity_type" value="ذات مسؤولية محدودة"> ذات مسؤولية محدودة</label>
                    </fieldset>
                </div>

                <!-- الصف الثالث -->
                <div class="input-group">
                    <label>مديرية المكلف</label>
                    <input type="text" name="directorate">
                </div>
                <div class="input-group">
                    <label>البريد الإلكتروني</label>
                    <input type="email" name="email">
                </div>
                <div class="input-group">
                    <label>الاسم التجاري</label>
                    <input type="text" name="trade_name">
                </div>

                <!-- الصف الرابع -->
                <div class="input-group">
                    <label>رقم السجل التجاري</label>
                    <input type="text" name="commercial_record">
                </div>
                <div class="input-group">
                    <label>تاريخ إنشاء الشركة</label>
                    <input type="date" name="creation_date">
                </div>
                <div class="input-group">
                    <label>العنوان</label>
                    <input type="text" name="address">
                </div>

                <!-- الصف الخامس -->
                <div class="input-group">
                    <label>الرقم الوطني للمنشأة</label>
                    <input type="text" name="facility_national_id">
                </div>
                
                <div class="input-group">
                    <label>طبيعة النشاط التجاري</label>
                    <select name="business_nature">
                        <option value="">-- اختر --</option>
                        <option value="تجاري">تجاري</option>
                        <option value="صناعي">صناعي</option>
                        <option value="خدمي">خدمي</option>
                    </select>
                </div>

                <div class="input-group">
                    <fieldset style="gap: 10px; padding: 5px 15px;">
                        <legend>نوع الطلب</legend>


                        <label class="radio-option"><input type="radio" name="request_type" value="تسجيل" checked> تسجيل</label>
                        <label class="radio-option"><input type="radio" name="request_type" value="إعادة تسجيل"> إعادة</label>
                    </fieldset>
                </div>
                <div class="input-group">
                    <label>الرمز </label>
                    <input type="text" name="password">
                </div>

            </div>

            <!-- أزرار الإجراءات -->
            <div class="action-buttons">
                <button type="submit" class="btn-action btn-submit">تسجيل البيانات</button>
                <button type="button" class="btn-action btn-print" onclick="window.print()">طباعة النموذج</button>
                <button type="reset" class="btn-action btn-cancel">إلغاء</button>
            </div>
        </form>
    </div>

</body>
</html>
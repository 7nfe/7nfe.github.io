<?php
// معالجة البيانات عند إرسال النموذج (Submit)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // يمكنك هنا استلام البيانات مثل:
    // $taxpayer_name = $_POST['taxpayer_name'];
    // ثم حفظها في قاعدة بيانات النظام الضريبي الخاص بالعقبة
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>التسجيل في ضريبة المبيعات</title>
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
            padding-bottom: 40px;
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

        .page-title {
            text-align: center;
            color: #000;
            margin: 30px 0;
            font-size: 26px;
            font-weight: bold;
        }

        /* شبكة الحقول (3 أعمدة) */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px 40px;
            padding: 0 50px;
            margin-bottom: 40px;
        }

        /* تنسيق الحقول */
        .input-group {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }

        .input-group label {
            font-weight: bold;
            font-size: 15px;
        }

        .input-group input[type="text"],
        .input-group input[type="email"],
        .input-group input[type="date"],
        .input-group select {
            width: 80%;
            padding: 8px;
            border: 1px solid #aaa;
            text-align: center;
            font-size: 14px;
            box-sizing: border-box;
        }

        /* تنسيق أزرار الاختيار (Radio Buttons) داخل إطار */
        .radio-fieldset {
            border: 1px solid #ddd;
            padding: 10px;
            width: 80%;
            box-sizing: border-box;
            display: flex;
            justify-content: space-around;
            border-radius: 4px;
        }

        .radio-fieldset legend {
            font-weight: bold;
            font-size: 14px;
            text-align: center;
            padding: 0 5px;
        }

        .radio-option {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 14px;
        }

        /* حقل مع زر تعجب جانبي */
        .input-with-btn {
            display: flex;
            width: 80%;
            gap: 5px;
        }

        .input-with-btn input {
            width: 100%;
        }

        .btn-info-small {
            background-color: #4873c4;
            color: white;
            border: none;
            width: 35px;
            font-weight: bold;
            font-size: 18px;
            cursor: pointer;
        }

        /* أزرار الإجراءات السفلية */
        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin-top: 20px;
        }

        .btn-action {
            background-color: #4873c4;
            color: white;
            border: none;
            padding: 10px 50px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
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
            <div style="font-weight: bold; color: #4873c4; text-align: left; width: 100%;">
                المملكة الأردنية الهاشمية<br>وزارة المالية<br>دائرة ضريبة الدخل والمبيعات
            </div>
        </div>

        <div class="page-title">التسجيل في ضريبة المبيعات</div>

        <form method="POST" action="">
            <div class="form-grid">
                
                <div class="input-group">
                    <label>رقم المكلف</label>
                    <input type="text" name="taxpayer_number">
                </div>
                <div class="input-group">
                    <label>اسم المكلف</label>
                    <input type="text" name="taxpayer_name">
                </div>
                <div class="input-group">
                    <label>رقم الهاتف</label>
                    <input type="text" name="phone">
                </div>

                <div></div>

                <div class="input-group" style="grid-column: span 3;">
                    <fieldset class="radio-fieldset" style="width: 70%;">
                        <legend>نوع الشخص الاعتباري</legend>
                        <div class="radio-option"><input type="radio" name="legal_entity_type" value="تضامن توصية بسيطة"> تضامن توصية بسيطة</div>
                        <div class="radio-option"><input type="radio" name="legal_entity_type" value="ذات مسؤولية محدودة"> ذات مسؤولية محدودة</div>
                    </fieldset>
                </div>

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

                <div class="input-group">
                    <label>الرقم الوطني للمنشأة</label>
                    <input type="text" name="facility_national_id">
                </div>
                
                
                <div></div> <div class="input-group">
                    <fieldset class="radio-fieldset">
                        <legend>نوع الطلب</legend>
                        <div class="radio-option"><input type="radio" name="request_type" value="تسجيل" checked> تسجيل</div>
                        <div class="radio-option"><input type="radio" name="request_type" value="إعادة تسجيل"> اعادة تسجيل</div>
                    </fieldset>
                </div>

                <div class="input-group">
                    <label>طبيعة النشاط التجاري</label>
                    <select name="business_nature">
                        <option value=""></option>
                        <option value="تجاري">تجاري</option>
                        <option value="صناعي">صناعي</option>
                        <option value="خدمي">خدمي</option>
                    </select>
                </div>
                

            </div>

            <div class="action-buttons">
                <button type="submit" class="btn-action">تسجيل</button>
                <button type="button" class="btn-action">إلغاء</button>
                <button type="button" class="btn-action">طباعة</button>
            </div>

        </form>

    </div>

</body>
</html>
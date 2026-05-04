<?php
// يمكنك لاحقاً استخدام هذا الجزء للاتصال بقاعدة البيانات لجلب بيانات المكلف
$taxpayer_no = "";
$classification = "";
$phone = "";
$directorate = "";
$commercial_name = "";
$address = "";
$email = "";
$commercial_reg = "";
$national_id = "";
$est_national_id = "";
$creation_date = "";
$taxpayer_name = "";
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>البيانات الشخصية - النظام الضريبي</title>
    <style>
        /* الإعدادات العامة */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        /* الحاوية الرئيسية - متناسقة مع باقي الشاشات */
        .window-container {
            background-color: #ffffff;
            width: 85%; 
            max-width: 1100px; 
            min-height: 90vh;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            position: relative;
            display: flex;
            flex-direction: column;
            border: 1px solid #ccc;
            overflow: hidden; /* لضمان عدم خروج الصورة عن الحواف */
        }

        /* الهيدر الملاصق للحواف تماماً */
        .header-banner {
            width: 100%;
            height: 120px; 
            background-color: #ffffff;
            border-bottom: 6px solid #4873c4;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 0;
        }

        .header-banner img {
            width: 100%;
            height: 100%;
            object-fit: fill; /* تمطيط الصورة لتغطي كامل العرض والارتفاع */
        }

        .page-title {
            text-align: center;
            font-size: 28px;
            font-weight: bold;
            margin: 30px 0;
            color: #333;
        }

        /* شبكة الحقول بنظام عمودين */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            padding: 0 60px;
            flex-grow: 1;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 15px;
        }

        .form-group label {
            font-weight: bold;
            font-size: 15px;
            color: #444;
        }

        .form-group input {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
            background-color: #f9f9f9;
            outline: none;
        }

        .form-group input:focus {
            border-color: #4873c4;
            background-color: #fff;
        }

        /* زر AI الجانبي */
        .ai-btn-container {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
        }

        .btn-ai {
            background-color: #4873c4;
            color: white;
            border: none;
            width: 55px;
            height: 55px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 18px;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        /* الأزرار السفلية */
        .footer-actions {
            display: flex;
            justify-content: center;
            gap: 30px;
            padding: 40px 0;
        }

        .action-btn {
            background-color: #4873c4;
            color: white;
            border: none;
            width: 130px;
            padding: 10px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            border-radius: 4px;
            transition: background 0.3s;
        }

        .action-btn:hover {
            background-color: #365a9e;
        }
    </style>
</head>
<body>

    <div class="window-container">
        <div class="header-banner">
            <img src="photo.jpeg" alt="شعار دائرة ضريبة الدخل والمبيعات">
        </div>

     

        <div class="page-title">البيانات الشخصية</div>

        <form id="personalDataForm">
            <div class="form-grid">
                <div>
                    <div class="form-group">
                        <label>رقم المكلف</label>
                        <input type="text" id="taxpayer_no" value="<?= $taxpayer_no ?>">
                    </div>
                    <div class="form-group">
                        <label>تصنيف المكلف</label>
                        <input type="text" id="classification" value="<?= $classification ?>">
                    </div>
                    <div class="form-group">
                        <label>رقم الهاتف</label>
                        <input type="text" id="phone" value="<?= $phone ?>">
                    </div>
                    <div class="form-group">
                        <label>مديرية المكلف</label>
                        <input type="text" id="directorate" value="<?= $directorate ?>">
                    </div>
                    <div class="form-group">
                        <label>الاسم التجاري</label>
                        <input type="text" id="commercial_name" value="<?= $commercial_name ?>">
                    </div>
                    <div class="form-group">
                        <label>العنوان</label>
                        <input type="text" id="address" value="<?= $address ?>">
                    </div>
                </div>

                <div>
                    <div class="form-group">
                        <label>البريد الإلكتروني</label>
                        <input type="email" id="email" value="<?= $email ?>">
                    </div>
                    <div class="form-group">
                        <label>رقم السجل التجاري</label>
                        <input type="text" id="commercial_reg" value="<?= $commercial_reg ?>">
                    </div>
                    <div class="form-group">
                        <label>الرقم الوطني</label>
                        <input type="text" id="national_id" value="<?= $national_id ?>">
                    </div>
                    <div class="form-group">
                        <label>الرقم الوطني للمنشأة</label>
                        <input type="text" id="est_national_id" value="<?= $est_national_id ?>">
                    </div>
                    <div class="form-group">
                        <label>تاريخ إنشاء الشركة</label>
                        <input type="text" id="creation_date" value="<?= $creation_date ?>">
                    </div>
                    <div class="form-group">
                        <label>اسم المكلف</label>
                        <input type="text" id="taxpayer_name" value="<?= $taxpayer_name ?>">
                    </div>
                </div>
            </div>

            <div class="footer-actions">
                <button type="button" class="action-btn" onclick="window.location.href='mains.php'">إغلاق</button>
                <button type="button" class="action-btn" onclick="window.print()">طباعة</button>
            </div>
        </form>
    </div>

    <script>
        function fillMockData() {
            // تعبئة البيانات تجريبياً لمحاكاة عمل الـ AI
            document.getElementById('taxpayer_no').value = "102938475";
            document.getElementById('classification').value = "شركات تجارية";
            document.getElementById('phone').value = "0791234567";
            document.getElementById('directorate').value = "مديرية العقبة";
            document.getElementById('commercial_name').value = "شركة العقبة للأنظمة";
            document.getElementById('address').value = "العقبة - الأردن";
            document.getElementById('email').value = "info@aqaba-systems.jo";
            document.getElementById('commercial_reg').value = "TR-12345";
            document.getElementById('national_id').value = "9988776655";
            document.getElementById('est_national_id').value = "2000334455";
            document.getElementById('creation_date').value = "2020-05-15";
            document.getElementById('taxpayer_name').value = "شركة العقبة للتجارة العامة";
            
            alert("تم استرجاع وتعبئة البيانات بنجاح!");
        }
    </script>
</body>
</html>
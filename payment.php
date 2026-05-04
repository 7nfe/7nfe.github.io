<?php
// بيانات تجريبية للمكلف (يمكن ربطها بقاعدة البيانات لاحقاً)
$taxpayer_number = "123456789";
$taxpayer_name = "شركة العقبة للتجارة العامة";
$e_payment_no = "987654321";

// بيانات تجريبية لجدول أمر القبض
$payments = [
    ['desc' => 'ضريبة مبيعات شهر 3', 'year' => '2024', 'period' => '3', 'amount' => '1500.50'],
];
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الدفع الإلكتروني - نظام ضريبة المبيعات</title>
    <style>
        /* الإعدادات العامة للصفحة */
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

        /* الحاوية الرئيسية - تم تصغير العرض ليكون متناسقاً */
        .window-container {
            background-color: #ffffff;
            width: 85%; /* العرض المصغر */
            max-width: 1100px; /* الحد الأقصى المتناسق */
            min-height: 90vh;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            position: relative;
            display: flex;
            flex-direction: column;
            border: 1px solid #ccc;
            overflow: hidden;
        }

        /* الهيدر الموحد - يغطي كامل العرض من الأعلى */
        .header-banner {
            width: 100%;
            height: 120px; 
            background-color: #ffffff;
            border-bottom: 6px solid #4873c4; /* الخط الأزرق الفاصل */
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 0;
        }

        .header-banner img {
            width: 100%;
            height: 100%;
            object-fit: fill; /* تمطيط الصورة لتغطي العرض بالكامل */
        }

        /* العناوين */
        .page-title {
            text-align: center;
            font-size: 28px;
            font-weight: bold;
            margin: 25px 0;
            color: #000;
        }

        /* شبكة المعلومات العلوية */
        .top-info-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            padding: 0 50px;
            margin-bottom: 25px;
        }

        .input-group {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }

        .input-group label {
            font-weight: bold;
            font-size: 16px;
        }

        .input-group input {
            width: 220px;
            padding: 10px;
            border: 1px solid #7a7a7a;
            text-align: center;
            font-size: 15px;
            background-color: #f9f9f9;
        }

        /* صف آلية الدفع */
        .payment-method-row {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .btn-pdf {
            background-color: #4873c4;
            color: white;
            border: none;
            padding: 8px 20px;
            font-weight: bold;
            cursor: pointer;
            border-radius: 4px;
        }

        .section-label {
            text-align: center;
            font-weight: bold;
            font-size: 20px;
            margin: 15px 0;
            text-decoration: underline;
        }

        /* تنسيق الجدول */
        .table-container {
            padding: 0 50px;
            margin-bottom: 30px;
            flex-grow: 1;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #7a7a7a;
        }

        th, td {
            border: 1px solid #7a7a7a;
            padding: 12px;
            text-align: center;
        }

        th { background-color: #f0f0f0; color: #333; font-size: 16px; }

        /* حقول الأقساط */
        .bottom-info-grid {
            display: flex;
            justify-content: center;
            gap: 60px;
            margin-bottom: 30px;
        }

        .field-with-icon {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-info-circle {
            background-color: #4873c4;
            color: white;
            border: none;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            font-weight: bold;
            cursor: help;
        }

        /* زر AI العائم */
        .ai-btn {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
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

        /* أزرار التحكم السفلية */
        .footer-actions {
            display: flex;
            justify-content: center;
            gap: 30px;
            padding-bottom: 30px;
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


        <div class="page-title">الدفع الإلكتروني</div>

        <div class="top-info-grid">
            <div class="input-group">
                <label>رقم الدفع الإلكتروني</label>
                <input type="text" value="<?= htmlspecialchars($e_payment_no) ?>" readonly>
            </div>
            <div class="input-group">
                <label>اسم المكلف</label>
                <input type="text" value="<?= htmlspecialchars($taxpayer_name) ?>" readonly>
            </div>
            <div class="input-group">
                <label>رقم المكلف</label>
                <input type="text" value="<?= htmlspecialchars($taxpayer_number) ?>" readonly>
            </div>
        </div>

        <div class="payment-method-row">
            <button class="btn-pdf">PDF آلية الدفع</button>
            <label style="font-weight: bold;">آلية الدفع الإلكتروني</label>
        </div>

        <div class="section-label">أمر قبض</div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>وصف الحركة</th>
                        <th>السنة</th>
                        <th>الفترة</th>
                        <th>المبلغ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($payments as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['desc']) ?></td>
                        <td><?= htmlspecialchars($p['year']) ?></td>
                        <td><?= htmlspecialchars($p['period']) ?></td>
                        <td><?= htmlspecialchars($p['amount']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr><td style="height: 35px;"></td><td></td><td></td><td></td></tr>
                    <tr><td style="height: 35px;"></td><td></td><td></td><td></td></tr>
                </tbody>
            </table>
        </div>

        <div class="bottom-info-grid">
            <div class="input-group">
                <label>قيمة القسط</label>
                <div class="field-with-icon">
                    <button class="btn-info-circle" title="القيمة الشهرية للقسط">!</button>
                    <input type="text" placeholder="0.00" style="width: 180px;">
                </div>
            </div>
            <div class="input-group">
                <label>عدد الأقساط</label>
                <input type="text" placeholder="0" style="width: 180px;">
            </div>
        </div>

        <div class="footer-actions">
            <button class="action-btn" onclick="window.location.href='mains.php'">خروج</button>
            <button class="action-btn" onclick="window.print()">طباعة</button>
            
        </div>
    </div>

</body>
</html>
<?php
// محاكاة للبيانات القادمة من قاعدة البيانات 
$taxpayer_number = "123456789"; 
$taxpayer_name = "شركة العقبة للتجارة العامة"; 

// محاكاة لبيانات جدول الطلبات
$requests = [
    ['transaction_num' => 'TRX-10293', 'service_name' => 'إصدار براءة ذمة', 'date' => '2026-04-20', 'status' => 'مكتمل'],
    ['transaction_num' => 'TRX-10304', 'service_name' => 'تعديل إقرار ضريبي', 'date' => '2026-04-25', 'status' => 'قيد المراجعة'],
    ['transaction_num' => 'TRX-10355', 'service_name' => 'طلب تقسيط', 'date' => '2026-04-26', 'status' => 'مرفوض'],
];
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>طلباتي</title>
    <style>
        /* التنسيقات العامة الأساسية */
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

        /* الحاوية الرئيسية (تم تكبيرها) */
        .window-container {
            background-color: #ffffff;
            width: 95%; /* زيادة العرض ليأخذ مساحة أكبر من الشاشة */
            max-width: 1400px; /* زيادة الحد الأقصى للعرض */
            min-height: 95vh; /* زيادة الارتفاع ليمتد تقريباً على طول الشاشة */
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            position: relative;
            display: flex;
            flex-direction: column;
            border: 1px solid #ccc;
        }

        /* ترويسة الشاشة */
        .header-banner {
            width: 100%;
            height: 100px; /* تكبير الهيدر قليلاً */
            background-color: #ffffff;
            border-bottom: 6px solid #3b71ca; 
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 40px;
            box-sizing: border-box;
        }

        .header-text {
            text-align: left;
            font-size: 16px;
            font-weight: bold;
            color: #333;
            line-height: 1.5;
        }

        .header-logo {
            text-align: right;
            font-size: 18px;
            font-weight: bold;
            color: #b31b1b; 
        }

        /* العنوان الرئيسي */
        .page-title {
            text-align: center;
            font-size: 32px; /* تكبير الخط */
            font-weight: bold;
            margin: 40px 0;
            color: #000;
        }

        /* قسم معلومات المكلف */
        .info-section {
            display: flex;
            justify-content: center;
            gap: 200px; /* زيادة المسافة بين الحقلين */
            margin-bottom: 50px;
        }

        .input-group {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
        }

        .input-group label {
            font-weight: bold;
            font-size: 18px; /* تكبير الخط */
            color: #000;
        }

        .input-group input {
            width: 300px; /* تكبير الحقل */
            padding: 10px;
            border: 1px solid #7a7a7a;
            text-align: center;
            font-size: 18px; /* تكبير الخط داخل الحقل */
            background-color: #ffffff; 
        }

        /* قسم الجدول */
        .table-container {
            padding: 0 80px;
            flex-grow: 1; 
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #7a7a7a;
        }

        th, td {
            border: 1px solid #7a7a7a;
            padding: 15px; /* زيادة الحشوة الداخلية لتكبير الخلايا */
            text-align: center;
        }

        th {
            background-color: #ffffff;
            font-weight: bold;
            color: #666; 
            font-size: 18px; /* تكبير خط العناوين */
        }

        td {
            font-size: 16px; /* تكبير خط البيانات */
            color: #000;
        }

        /* زر الـ AI الجانبي */
        .ai-btn {
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            background-color: #4169e1; 
            color: white;
            border: none;
            width: 70px; /* تكبير الزر */
            height: 70px;
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 2px 2px 5px rgba(0,0,0,0.2);
            transition: 0.3s;
        }

        .ai-btn:hover {
            background-color: #2744a0;
        }

        /* الأزرار السفلية */
        .footer-actions {
            display: flex;
            justify-content: center;
            gap: 50px;
            padding: 50px 0;
        }

        .action-btn {
            background-color: #4169e1;
            color: white;
            border: none;
            width: 160px; /* تكبير الزر */
            padding: 15px 0;
            font-size: 18px; /* تكبير الخط */
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }

        .action-btn:hover {
            background-color: #2744a0;
        }

    </style>
</head>
<body>

    <div class="window-container">
        
        <div class="header-banner">
            <div class="header-text">
                بوابة الخدمات الإلكترونية
            </div>
            <div class="header-logo">
                المملكة الأردنية الهاشمية<br>وزارة المالية<br><span style="color: #4169e1;">دائرة ضريبة الدخل والمبيعات</span>
            </div>
        </div>

        <button type="button" class="ai-btn" title="مساعد الذكاء الاصطناعي">AI</button>

        <div class="page-title">طلباتي</div>

        <div class="info-section">
            <div class="input-group">
                <label>رقم المكلف</label>
                <input type="text" value="<?php echo htmlspecialchars($taxpayer_number); ?>" readonly>
            </div>
            <div class="input-group">
                <label>اسم المكلف</label>
                <input type="text" value="<?php echo htmlspecialchars($taxpayer_name); ?>" readonly>
            </div>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>رقم المعاملة</th>
                        <th>اسم الخدمة</th>
                        <th>تاريخ تقديم الطلب</th>
                        <th>الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($requests)): ?>
                        <?php foreach ($requests as $request): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($request['transaction_num']); ?></td>
                                <td><?php echo htmlspecialchars($request['service_name']); ?></td>
                                <td><?php echo htmlspecialchars($request['date']); ?></td>
                                <td><?php echo htmlspecialchars($request['status']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4" style="height: 50px;"></td></tr>
                        <tr><td colspan="4" style="height: 50px;"></td></tr>
                        <tr><td colspan="4" style="height: 50px;"></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="footer-actions">
            <button type="button" class="action-btn" onclick="window.location.href='mains.php'">إغلاق</button>
            <button type="button" class="action-btn" onclick="window.print();">طباعة</button>
        </div>

    </div>

</body>
</html>
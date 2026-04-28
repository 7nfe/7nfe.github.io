<?php
// محاكاة للبيانات القادمة من قاعدة البيانات 
$taxpayer_number = "123456789"; 
$taxpayer_name = "شركة العقبة للتجارة العامة"; 

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

        /* --- تعديل حجم الشاشة هنا --- */
        .window-container {
            background-color: #ffffff;
            width: 85%; /* تم تصغيرها من 95% إلى 85% */
            max-width: 1100px; /* تم تصغير الحد الأقصى من 1400px إلى 1100px */
            min-height: 90vh; 
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            position: relative;
            display: flex;
            flex-direction: column;
            border: 1px solid #ccc;
        }

        .header-banner {
            width: 100%;
            height: 120px; 
            background-color: #ffffff;
            border-bottom: 6px solid #4873c4;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .header-image {
            width: 100%;
            height: 100%;
            object-fit: fill; 
        }

        .page-title {
            text-align: center;
            font-size: 28px; 
            font-weight: bold;
            margin: 30px 0;
            color: #000;
        }

        .info-section {
            display: flex;
            justify-content: center;
            gap: 100px; /* تقليل المسافة لتناسب الحجم الجديد */
            margin-bottom: 40px;
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
            color: #000;
        }

        .input-group input {
            width: 250px; 
            padding: 10px;
            border: 1px solid #7a7a7a;
            text-align: center;
            font-size: 16px;
        }

        .table-container {
            padding: 0 40px; /* تقليل الحشو الجانبي */
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

        th {
            background-color: #f9f9f9;
            font-weight: bold;
            font-size: 16px;
        }

     

        .footer-actions {
            display: flex;
            justify-content: center;
            gap: 30px;
            padding: 40px 0;
        }

        .action-btn {
            background-color: #4169e1;
            color: white;
            border: none;
            width: 140px;
            padding: 12px 0;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }
    </style>
</head>
<body>

    <div class="window-container">
        <div class="header-banner">
            <img src="photo.jpeg" alt="شعار دائرة ضريبة الدخل والمبيعات" class="header-image">
        </div>


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
                    <?php foreach ($requests as $request): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($request['transaction_num']); ?></td>
                            <td><?php echo htmlspecialchars($request['service_name']); ?></td>
                            <td><?php echo htmlspecialchars($request['date']); ?></td>
                            <td><?php echo htmlspecialchars($request['status']); ?></td>
                        </tr>
                    <?php endforeach; ?>
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
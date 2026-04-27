<?php
// بيانات تجريبية للمكلف
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
    <title>الدفع الإلكتروني</title>
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

        .window-container {
            background-color: #ffffff;
            width: 95%;
            max-width: 1400px;
            min-height: 90vh;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            position: relative;
            display: flex;
            flex-direction: column;
            border: 1px solid #ccc;
        }

        /* الهيدر */
        .header-banner {
            width: 100%;
            height: 100px;
            background-color: #ffffff;
            border-bottom: 6px solid #3b71ca;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 40px;
            box-sizing: border-box;
        }

        .header-logo {
            text-align: right;
            font-size: 18px;
            font-weight: bold;
            color: #b31b1b;
        }

        .page-title {
            text-align: center;
            font-size: 32px;
            font-weight: bold;
            margin: 30px 0;
            color: #000;
        }

        /* الشبكة العلوية للحقول */
        .top-info-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            padding: 0 80px;
            margin-bottom: 30px;
        }

        .input-group {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }

        .input-group label {
            font-weight: bold;
            font-size: 18px;
        }

        .input-group input {
            width: 250px;
            padding: 10px;
            border: 1px solid #7a7a7a;
            text-align: center;
            font-size: 16px;
        }

        /* صف آلية الدفع */
        .payment-method-row {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
            margin-bottom: 30px;
        }

        .btn-pdf {
            background-color: #4873c4;
            color: white;
            border: none;
            padding: 8px 20px;
            font-weight: bold;
            cursor: pointer;
        }

        /* قسم الجدول */
        .section-label {
            text-align: center;
            font-weight: bold;
            font-size: 22px;
            margin: 20px 0 10px 0;
        }

        .table-container {
            padding: 0 80px;
            margin-bottom: 40px;
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

        th { background-color: #f9f9f9; color: #666; font-size: 18px; }

        /* حقول الأقساط السفلية */
        .bottom-info-grid {
            display: flex;
            justify-content: center;
            gap: 100px;
            margin-bottom: 40px;
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
            width: 35px;
            height: 35px;
            font-weight: bold;
            cursor: help;
        }

        /* الأزرار الجانبية والسفلية */
        .ai-btn {
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            background-color: #4873c4;
            color: white;
            border: none;
            width: 60px;
            height: 60px;
            font-weight: bold;
            font-size: 20px;
        }

        .footer-actions {
            display: flex;
            justify-content: center;
            gap: 40px;
            padding-bottom: 40px;
        }

        .action-btn {
            background-color: #4873c4;
            color: white;
            border: none;
            width: 150px;
            padding: 12px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
        }
    </style>
</head>
<body>

    <div class="window-container">
        <div class="header-banner">
            <div style="font-size: 14px;">بوابة الدفع الضريبي</div>
            <div class="header-logo">
                المملكة الأردنية الهاشمية<br>وزارة المالية<br>دائرة ضريبة الدخل والمبيعات
            </div>
        </div>

        <button class="ai-btn">AI</button>

        <div class="page-title">الدفع الالكتروني</div>

        <div class="top-info-grid">
            <div class="input-group">
                <label>رقم الدفع الالكتروني</label>
                <input type="text" value="<?= $e_payment_no ?>" readonly>
            </div>
            <div class="input-group">
                <label>اسم المكلف</label>
                <input type="text" value="<?= $taxpayer_name ?>" readonly>
            </div>
            <div class="input-group">
                <label>رقم المكلف</label>
                <input type="text" value="<?= $taxpayer_number ?>" readonly>
            </div>
        </div>

        <div class="payment-method-row">
            <button class="btn-pdf">PDF آلية الدفع</button>
            <label style="font-weight: bold; font-size: 18px;">آلية الدفع الالكتروني</label>
        </div>

        <div class="section-label">امر قبض</div>

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
                        <td><?= $p['desc'] ?></td>
                        <td><?= $p['year'] ?></td>
                        <td><?= $p['period'] ?></td>
                        <td><?= $p['amount'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr><td style="height: 40px;"></td><td></td><td></td><td></td></tr>
                    <tr><td style="height: 40px;"></td><td></td><td></td><td></td></tr>
                </tbody>
            </table>
        </div>

        <div class="bottom-info-grid">
            <div class="input-group">
                <label>قيمة القسط</label>
                <div class="field-with-icon">
                    <button class="btn-info-circle" title="شرح حول قيمة القسط">!</button>
                    <input type="text" placeholder="0.00">
                </div>
            </div>
            <div class="input-group">
                <label>عدد الاقساط</label>
                <input type="text" placeholder="0">
            </div>
        </div>

        <div class="footer-actions">
            <button class="action-btn" onclick="window.location.href='mains.php'">خروج</button>
            <button class="action-btn" onclick="window.print()">طباعة</button>
        </div>
    </div>

</body>
</html>
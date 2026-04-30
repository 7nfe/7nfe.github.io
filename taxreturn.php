<?php
// إعداد متغيرات الرسائل والقيم الافتراضية المحاكية لجلب البيانات من قاعدة البيانات
$message = "";

// القيم الثابتة للمكلف (يمكنك تغييرها هنا وسيتم تحديثها في كامل الصفحة)
$fixed_tax_number = "123456789";
$fixed_tax_name = "شركة العقبة للتجارة";

$default_balance = 0.00; // الخانة 1: رصيد مدور
$default_adj_reg = 0.00; // الخانة 4: حركة تعديل لصالح المسجل

// معالجة البيانات عند إرسال النموذج
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // استقبال البيانات (استخدام القيم الثابتة بدلاً من القادمة من POST للأمان)
    $tax_number = $fixed_tax_number;
    $tax_name = $fixed_tax_name;
    
    $dec_type = $_POST['dec_type'] ?? '';
    $year = $_POST['year'] ?? '';
    $period = $_POST['period'] ?? '';
    
    // استقبال المدخلات المالية
    $balance = floatval($_POST['balance'] ?? 0);     
    $sales_7 = floatval($_POST['sales_7'] ?? 0);     
    $adj_reg = floatval($_POST['adj_reg'] ?? 0);     
    $adj_dep = floatval($_POST['adj_dep'] ?? 0);     

    // العمليات الحسابية
    $tax_subject = $sales_7 * 0.07; 
    $result = $tax_subject - ($balance + $adj_reg) + $adj_dep;

    if ($result > 0) {
        $tax_due_pos = round($result, 3);
        $tax_due_neg = 0;
    } else {
        $tax_due_pos = 0;
        $tax_due_neg = round(abs($result), 3);
    }

    // رسالة نجاح مؤقتة
    $message = "<div class='alert success'>تم معالجة الإقرار بنجاح للمكلف: " . htmlspecialchars($tax_name) . "! الضريبة المستحقة: $tax_due_pos</div>";
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إقرار ضريبة المبيعات</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
        }
        .container {
            background-color: #ffffff;
            width: 900px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            border: 1px solid #ccc;
            padding-bottom: 30px;
        }
        .header-banner { width: 100%; height: 100px; background-color: #ffffff;
            display: flex; align-items: center; justify-content: center; overflow: hidden;
            border-bottom: 4px solid #4169E1;
        }
        .header-banner img { height: 90%; object-fit: contain; }
        h2.title {
            text-align: center; margin: 20px 0 40px 0; color: #111; font-size: 24px;
        }
        .top-row { display: flex; justify-content: space-around; margin-bottom: 20px; padding: 0 50px; }
        .input-group { display: flex; flex-direction: column; align-items: center; width: 45%; }
        .input-group label { font-weight: bold; margin-bottom: 8px; font-size: 14px; }
        .input-group input, .input-group select {
            width: 100%; padding: 8px; border: 1px solid #999; text-align: center; border-radius: 4px;
        }
        .middle-row { display: flex; justify-content: space-between; margin-bottom: 40px; padding: 0 50px; }
        .middle-row .input-group { width: 30%; }
        .main-content { display: flex; position: relative; padding: 0 50px; justify-content: center; }
        
        .finance-list { width: 100%; display: flex; flex-direction: column; gap: 12px; }
        .finance-row { display: flex; align-items: center; justify-content: flex-end; }
        .finance-row label { width: 40%; text-align: right; font-weight: bold; font-size: 13px; }
        .finance-row input {
            width: 250px; padding: 8px; border: 1px solid #999; margin: 0 15px; text-align: center; border-radius: 4px;
        }
        
        /* تمييز الحقول التلقائية والثابتة للقراءة فقط */
        .readonly-field { 
            background-color: #e9ecef; 
            cursor: not-allowed; 
            font-weight: bold; 
            color: #555;
            border-color: #ddd;
        }
        
        .btn-info {
            background-color: #4169E1; color: white; border: none; width: 30px; height: 30px; font-weight: bold; font-size: 16px; cursor: pointer; border-radius: 4px;
        }
        .action-buttons { display: flex; justify-content: center; gap: 30px; margin-top: 50px; }
        .action-buttons button {
            background-color: #4169E1; color: white; border: none; padding: 10px 40px; font-size: 16px; font-weight: bold; cursor: pointer; border-radius: 4px; transition: 0.3s;
        }
        .action-buttons button:hover, .btn-info:hover { background-color: #2b4cad; }
        .alert.success { background-color: #d4edda; color: #155724; padding: 15px; text-align: center; margin: 10px 50px; border: 1px solid #c3e6cb; border-radius: 4px; }
    </style>
</head>
<body>

<div class="container">
    <div class="header-banner">
        <img src="photo.jpeg" alt="شعار الدائرة">
    </div>

    <?php if(!empty($message)) echo $message; ?>

    <h2 class="title">إقرار ضريبة المبيعات</h2>

    <form method="POST" action="">
        
        <div class="top-row">
            <div class="input-group">
                <label>رقم المكلف</label>
                <!-- قيمة ثابتة للقراءة فقط -->
                <input type="text" name="tax_number" value="<?php echo $fixed_tax_number; ?>" readonly class="readonly-field">
            </div>
            <div class="input-group">
                <label>اسم المكلف</label>
                <!-- قيمة ثابتة للقراءة فقط -->
                <input type="text" name="tax_name" value="<?php echo $fixed_tax_name; ?>" readonly class="readonly-field">
            </div>
        </div>

        <div class="middle-row">
            <div class="input-group">
                <label>نوع الإقرار</label>
                <select name="dec_type">
                    <option value="أصلي">أصلي</option>
                </select>
            </div>
            <div class="input-group">
                <label>السنة</label>
                <select name="year">
                    <option value="2026">2026</option>
                    <option value="2025">2025</option>
                    <option value="2024">2024</option>
                </select>
            </div>
            <div class="input-group">
                <label>الفترة</label>
                <select name="period">
                    <option value="1">1+2</option>
                    <option value="2">3+4</option>
                    <option value="3">5+6</option>
                    <option value="4">7+8</option>
                    <option value="5">9+10</option>
                    <option value="6">11+12</option>
                </select>
            </div>
        </div>

        <div class="main-content">
            <div class="finance-list">
                
                <div class="finance-row">
                    <label>الخانة (1): رصيد مدور من الفترة السابقة</label>
                    <input type="number" step="0.01" id="balance" name="balance" value="<?php echo $default_balance; ?>" readonly class="readonly-field">
                    <button type="button" class="btn-info" title="يتم جلب هذه القيمة تلقائياً من الفترات السابقة">!</button>
                </div>

                <div class="finance-row">
                    <label>الخانة (2): مبيعات خاضعة للنسبة 7%</label>
                    <input type="number" step="0.01" id="sales_7" name="sales_7" required oninput="calculateTax()">
                    <button type="button" class="btn-info" title="الرجاء إدخال إجمالي المبيعات الخاضعة للضريبة">!</button>
                </div>

                <div class="finance-row">
                    <label>الخانة (3): ضريبة المبيعات الخاضعة للنسبة</label>
                    <input type="number" step="0.01" id="tax_subject" name="tax_subject" readonly class="readonly-field">
                    <button type="button" class="btn-info" title="تُحسب تلقائياً (الخانة 2 × 7%)">!</button>
                </div>

                <div class="finance-row">
                    <label>الخانة (4): حركة تعديل لصالح المسجل</label>
                    <input type="number" step="0.01" id="adj_reg" name="adj_reg" value="<?php echo $default_adj_reg; ?>" readonly class="readonly-field">
                    <button type="button" class="btn-info" title="يتم جلبها تلقائياً إن وجدت">!</button>
                </div>

                <div class="finance-row">
                    <label>الخانة (5): حركة تعديل لصالح الدائرة</label>
                    <input type="number" step="0.01" id="adj_dep" name="adj_dep" value="0" oninput="calculateTax()">
                    <button type="button" class="btn-info" title="أدخل قيمة التعديلات المستحقة للدائرة إن وجدت">!</button>
                </div>

                <div class="finance-row">
                    <label>الخانة (6): الضريبة المستحقة موجبة<br>(مبلغ واجب دفعه)</label>
                    <input type="number" step="0.01" id="tax_due_pos" name="tax_due_pos" readonly class="readonly-field">
                    <button type="button" class="btn-info" title="تُحسب تلقائياً إذا كان الناتج أكبر من صفر">!</button>
                </div>

                <div class="finance-row">
                    <label>الخانة (7): الضريبة المستحقة سالبة<br>(مدور لصالحك الفترة القادمة)</label>
                    <input type="number" step="0.01" id="tax_due_neg" name="tax_due_neg" readonly class="readonly-field">
                    <button type="button" class="btn-info" title="تُحسب تلقائياً إذا كان الناتج أقل من أو يساوي صفر">!</button>
                </div>

            </div>
        </div>

        <div class="action-buttons">
            <button type="submit">إرسال الإقرار</button>
            <button type="button" onclick="window.location.href='mains.php'">إلغاء</button>
            <button type="button" onclick="window.print()">طباعة التقرير (PDF)</button>
        </div>

    </form>
</div>

<script>
    function calculateTax() {
        let balance = parseFloat(document.getElementById('balance').value) || 0;
        let sales_7 = parseFloat(document.getElementById('sales_7').value) || 0;
        let adj_reg = parseFloat(document.getElementById('adj_reg').value) || 0;
        let adj_dep = parseFloat(document.getElementById('adj_dep').value) || 0;

        let tax_subject = sales_7 * 0.07;
        document.getElementById('tax_subject').value = tax_subject.toFixed(3);

        let result = tax_subject - (balance + adj_reg) + adj_dep;

        if (result > 0) {
            document.getElementById('tax_due_pos').value = result.toFixed(3);
            document.getElementById('tax_due_neg').value = 0;
        } else {
            document.getElementById('tax_due_pos').value = 0;
            document.getElementById('tax_due_neg').value = Math.abs(result).toFixed(3);
        }
    }

    window.onload = calculateTax;
</script>

</body>
</html>
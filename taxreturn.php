<?php
// 1. بدء الجلسة والاتصال بقاعدة البيانات
session_start();
include("config.php"); 

// 2. التحقق من أن المستخدم سجل دخوله مسبقاً
if (!isset($_SESSION['taxpayer_id'])) {
    header("Location: login.php");
    exit();
}

// 3. جلب بيانات المكلف الحقيقية من قاعدة البيانات
$session_id = $_SESSION['taxpayer_id'];
$tax_number = "";
$tax_name = "";

$stmt = $conn->prepare("SELECT taxpayer_id, taxpayer_name FROM taxpayers WHERE taxpayer_id = ?");
$stmt->bind_param("s", $session_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $tax_number = $row['taxpayer_id'];
    $tax_name   = $row['taxpayer_name'];
} else {
    // احتياطياً إذا لم توجد بيانات
    $tax_number = $session_id;
    $tax_name   = "مكلف غير معروف";
}
$stmt->close();

// إعداد متغيرات الرسائل والقيم الافتراضية
$message = "";
$default_balance = 0.00; // يمكن لاحقاً جلبها من جدول الإقرارات السابقة
$default_adj_reg = 0.00; 

// معالجة البيانات عند إرسال النموذج (insert_taxreturn.php)
// ملاحظة: يفضل أن تكون عملية الإدخال في ملف منفصل كما هو محدد في الـ Action الخاص بالفورم
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
    </style>
</head>
<body>

<div class="container">
    <div class="header-banner">
        <img src="photo.jpeg" alt="شعار الدائرة">
    </div>

    <h2 class="title">إقرار ضريبة المبيعات</h2>

    <form method="POST" action="insert_taxreturn.php">
        
        <div class="top-row">
            <div class="input-group">
                <label>رقم المكلف</label>
                <!-- جلب القيمة من المتغير القادم من قاعدة البيانات -->
                <input type="text" name="tax_number" value="<?php echo htmlspecialchars($tax_number); ?>" readonly class="readonly-field">
            </div>
            <div class="input-group">
                <label>اسم المكلف</label>
                <!-- جلب القيمة من المتغير القادم من قاعدة البيانات -->
                <input type="text" name="tax_name" value="<?php echo htmlspecialchars($tax_name); ?>" readonly class="readonly-field">
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
                <label>الفترة</label>
                <select name="period">
                    <option value="1+2/2026">1+2/2026</option>
                    <option value="3+4/2026">3+4/2026</option>
                    <option value="5+6/2026">5+6/2026</option>
                    <option value="7+8/2026">7+8/2026</option>
                    <option value="9+10/2026">9+10/2026</option>
                    <option value="11+12/2026">11+12/2026</option>
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
                    <label>الخانة (6): الضريبة المستحقة موجبة (دفع)</label>
                    <input type="number" step="0.01" id="tax_due_pos" name="tax_due_pos" readonly class="readonly-field">
                    <button type="button" class="btn-info" title="تُحسب تلقائياً">!</button>
                </div>

                <div class="finance-row">
                    <label>الخانة (7): الضريبة المستحقة سالبة (مدور)</label>
                    <input type="number" step="0.01" id="tax_due_neg" name="tax_due_neg" readonly class="readonly-field">
                    <button type="button" class="btn-info" title="تُحسب تلقائياً">!</button>
                </div>

            </div>
        </div>

        <div class="action-buttons">
            <button type="submit">إرسال الإقرار</button>
            <button type="button" onclick="window.location.href='mains.php'">إلغاء</button>
            <button type="button" onclick="window.print()">طباعة (PDF)</button>
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
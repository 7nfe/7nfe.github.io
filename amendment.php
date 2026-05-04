<?php
session_start();
include("config.php");

// جزء معالجة AJAX لجلب بيانات الإقرار الأصلي
if (isset($_GET['period'])) {
    if (ob_get_length()) ob_clean();
    header('Content-Type: application/json; charset=utf-8');

    $period = $_GET['period'];
    $session_id = $_SESSION['taxpayer_id'] ?? '';

    // الاستعلام عن الإقرار الأصلي للفترة المختارة
    $sql = "SELECT * FROM tax_returns WHERE taxpayer_id = ? AND period = ? AND (declaration_type = 'أصلي' OR declaration_type = 'اصلي') LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $session_id, $period);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo json_encode(['status' => 'success', 'data' => $row]);
    } else {
        echo json_encode(['status' => 'empty']);
    }
    exit();
}

// جلب بيانات المكلف للشاشة
$tax_number = $_SESSION['taxpayer_id'] ?? '69';
$tax_name = "عمر"; // افتراضي إذا لم يتوفر في الجلسة

$stmt_user = $conn->prepare("SELECT taxpayer_name FROM taxpayers WHERE taxpayer_id = ?");
$stmt_user->bind_param("s", $tax_number);
$stmt_user->execute();
$res_user = $stmt_user->get_result();
if ($u = $res_user->fetch_assoc()) $tax_name = $u['taxpayer_name'];
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>طلب تعديل إقرار ضريبة المبيعات</title>
    <style>
        :root {
            --primary-blue: #4169e1;
            --bg-gray: #f4f7f6;
            --border-color: #ccc;
        }

        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background-color: white;
            margin: 0;
            padding: 0;
        }

        .header-img {
            width: 100%;
            border-bottom: 3px solid var(--primary-blue);
            margin-bottom: 20px;
        }

        .header-img img {
            width: 100%;
            max-height: 120px;
            object-fit: cover;
        }

        .main-container {
            max-width: 1000px;
            margin: auto;
            padding: 20px;
        }

        h1.title {
            text-align: center;
            font-size: 24px;
            color: #000;
            margin-bottom: 40px;
        }

        .top-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 30px;
        }

        .field-box {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .field-box label {
            font-weight: bold;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .field-box input,
        .field-box select {
            width: 80%;
            padding: 8px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            background-color: #f1f3f4;
            text-align: center;
            font-size: 15px;
        }

        .status-msg {
            text-align: center;
            color: blue;
            font-weight: bold;
            margin: 20px 0;
            min-height: 24px;
        }

        .comparison-section {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            margin-top: 20px;
        }

        .card {
            flex: 1;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            background: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .card h3 {
            text-align: center;
            color: var(--primary-blue);
            font-size: 18px;
            margin-top: 0;
        }

        .row-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .row-item label {
            font-size: 12px;
            font-weight: bold;
            width: 65%;
            text-align: right;
        }

        .row-item input {
            width: 30%;
            padding: 5px;
            text-align: center;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            font-size: 13px;
        }

        /* ألوان الحقول في الجانبين */
        .original-side input {
            background-color: #fff0f0;
        }

        /* أحمر فاتح كما في الصورة */
        .amended-side input {
            background-color: #f1f3f4;
        }

        .footer-buttons {
            text-align: center;
            margin-top: 40px;
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        .btn {
            padding: 10px 40px;
            border: none;
            border-radius: 5px;
            color: white;
            cursor: pointer;
            font-weight: bold;
            font-size: 16px;
            min-width: 150px;
        }

        .btn-send {
            background-color: var(--primary-blue);
        }

        .btn-cancel {
            background-color: var(--primary-blue);
            opacity: 0.9;
        }

        .btn:hover {
            opacity: 0.8;
        }

        .btn:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }
    </style>
</head>

<body>

    <div class="header-img">
        <img src="photo.jpeg" alt="Logo">
    </div>

    <div class="main-container">
        <h1 class="title">طلب تعديل إقرار ضريبة المبيعات</h1>

        <form action="insert_taxreturn.php" method="POST">
            <div class="top-info">
                <div class="field-box">
                    <label>رقم المكلف</label>
                    <input type="text" value="<?php echo $tax_number; ?>" readonly>
                </div>
                <div class="field-box">
                    <label>اسم المكلف</label>
                    <input type="text" value="<?php echo $tax_name; ?>" readonly>
                </div>
                <div class="field-box">
                    <label>السنة / الفترة</label>
                    <select id="period_select" name="period" onchange="fetchOriginalData()">
                        <option value="">-- اختر الفترة --</option>
                        <option value="1+2/2026">1+2/2026</option>
                        <option value="3+4/2026">3+4/2026</option>
                        <option value="5+6/2026">5+6/2026</option>
                        <option value="7+8/2026">7+8/2026</option>
                        <option value="9+10/2026">9+10/2026</option>
                        <option value="11+12/2026">11+12/2026</option>


                    </select>
                </div>
                <div class="field-box">
                    <label>نوع الإقرار</label>
                    <input type="text" value="معدل" name="declaration_type" readonly>
                </div>
            </div>

            <div class="status-msg" id="status-msg"></div>

            <div class="comparison-section">
                <!-- الإقرار الأصلي (السابق) -->
                <div class="card original-side">
                    <h3>الإقرار الأصلي (السابق)</h3>
                    <?php
                    $fields = [
                        "o_bal" => "(1) رصيد مدور من الفترة السابقة",
                        "o_sales" => "(2) مبيعات خاضعة للنسبة 7%",
                        "o_tax" => "(3) ضريبة المبيعات الخاضعة",
                        "o_reg" => "(4) حركة تعديل لصالح المسجل",
                        "o_dept" => "(5) حركة تعديل لصالح الدائرة",
                        "o_pos" => "(6) الضريبة المستحقة (دفع)",
                        "o_neg" => "(7) الضريبة المستحقة (مدور)"
                    ];
                    foreach ($fields as $id => $label): ?>
                        <div class="row-item">
                            <label><?php echo $label; ?></label>
                            <input type="text" id="<?php echo $id; ?>" readonly>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- الإقرار المعدل (الجديد) -->
                <div class="card amended-side">
                    <h3>الإقرار المعدل (الجديد)</h3>
                    <div class="row-item">
                        <label>(1) رصيد مدور من الفترة السابقة</label>
                        <input type="number" step="0.001" name="balance_previous_period" id="n_bal" readonly>
                    </div>
                    <div class="row-item">
                        <label>(2) مبيعات خاضعة للنسبة 7%</label>
                        <input type="number" step="0.001" name="sales_percent" id="n_sales" oninput="calc()" required disabled>
                    </div>
                    <div class="row-item">
                        <label>(3) ضريبة المبيعات الخاضعة</label>
                        <input type="number" step="0.001" name="tax_on_percent" id="n_tax" readonly>
                    </div>
                    <div class="row-item">
                        <label>(4) حركة تعديل لصالح المسجل</label>
                        <input type="number" step="0.001" name="amend_for_registration" id="n_reg" oninput="calc()" value="0">
                    </div>
                    <div class="row-item">
                        <label>(5) حركة تعديل لصالح الدائرة</label>
                        <input type="number" step="0.001" name="amend_for_department" id="n_dept" oninput="calc()" value="0">
                    </div>
                    <div class="row-item">
                        <label>(6) الضريبة المستحقة (دفع)</label>
                        <input type="number" step="0.001" name="positive_tax_due" id="n_pos" readonly>
                    </div>
                    <div class="row-item">
                        <label>(7) الضريبة المستحقة (مدور)</label>
                        <input type="number" step="0.001" name="negative_tax_due" id="n_neg" readonly>
                    </div>
                </div>
            </div>

            <div class="footer-buttons">
                <button type="submit" class="btn btn-send" id="btn-submit" disabled>إرسال التعديل</button>
                <button type="button" class="btn btn-cancel" onclick="window.history.back()">إلغاء</button>
            </div>
        </form>
    </div>

    <script>
        function fetchOriginalData() {
            const period = document.getElementById('period_select').value;
            const msg = document.getElementById('status-msg');
            if (!period) return;

            msg.innerText = "جاري التحقق من قاعدة البيانات...";

            fetch('?ajax_period=' + encodeURIComponent(period))
                .then(r => r.json())
                .then(res => {
                    if (res.status === 'success') {
                        msg.innerText = "";
                        // تعبئة الجانب الأحمر (الأصلي)
                        document.getElementById('o_bal').value = res.data.balance_previous_period;
                        document.getElementById('o_sales').value = res.data.sales_percent;
                        document.getElementById('o_tax').value = res.data.tax_on_percent;
                        document.getElementById('o_reg').value = res.data.amend_for_registration;
                        document.getElementById('o_dept').value = res.data.amend_for_department;
                        document.getElementById('o_pos').value = res.data.positive_tax_due;
                        document.getElementById('o_neg').value = res.data.negative_tax_due;

                        // تفعيل الجانب الجديد
                        document.getElementById('n_bal').value = res.data.balance_previous_period;
                        document.getElementById('n_sales').disabled = false;
                        document.getElementById('btn-submit').disabled = false;
                    } else {
                        msg.innerText = "فشل الاتصال بقاعدة البيانات: لا يوجد إقرار أصلي لهذه الفترة.";
                        document.getElementById('btn-submit').disabled = true;
                    }
                });
        }

        function calc() {
            const sales = parseFloat(document.getElementById('n_sales').value) || 0;
            const bal = parseFloat(document.getElementById('n_bal').value) || 0;
            const reg = parseFloat(document.getElementById('n_reg').value) || 0;
            const dept = parseFloat(document.getElementById('n_dept').value) || 0;

            const tax = sales * 0.07;
            document.getElementById('n_tax').value = tax.toFixed(3);

            const total = tax - bal - reg + dept;

            if (total >= 0) {
                document.getElementById('n_pos').value = total.toFixed(3);
                document.getElementById('n_neg').value = "0.000";
            } else {
                document.getElementById('n_pos').value = "0.000";
                document.getElementById('n_neg').value = Math.abs(total).toFixed(3);
            }
        }
    </script>
</body>

</html>
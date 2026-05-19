<?php
session_start();
include "config.php";

if (!isset($_SESSION["taxpayer_id"])) {
    header("Location: login.php");
    exit;
}

$taxpayer_id = $_SESSION["taxpayer_id"];
$message = "";
$tax_rate = 0.07;

$taxpayer_name = "";
$taxpayer_number = "";

$original_id = 0;
$original_final = 0;

function num($v) {
    return number_format((float)$v, 3, '.', '');
}

function generateTransactionNumber($conn) {
    $today = date("Ymd");
    $seq = 1;

    $stmt = mysqli_prepare($conn, "
        SELECT transaction_number
        FROM service_requests
        WHERE transaction_number LIKE CONCAT(?, '%')
        ORDER BY transaction_number DESC
        LIMIT 1
    ");
    mysqli_stmt_bind_param($stmt, "s", $today);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($res)) {
        $last = $row["transaction_number"];
        if (strlen($last) == 14) {
            $seq = intval(substr($last, 8, 6)) + 1;
        }
    }

    return $today . str_pad($seq, 6, "0", STR_PAD_LEFT);
}

/* بيانات المكلف */
$stmt = mysqli_prepare($conn, "
    SELECT taxpayer_name, taxpayer_number
    FROM taxpayers
    WHERE taxpayer_id = ?
    LIMIT 1
");
mysqli_stmt_bind_param($stmt, "i", $taxpayer_id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($res)) {
    $taxpayer_name = $row["taxpayer_name"];
    $taxpayer_number = $row["taxpayer_number"];
} else {
    die("لم يتم العثور على بيانات المكلف");
}

/* الفترات الأصلية */
$periods = [];
$stmt = mysqli_prepare($conn, "
    SELECT period
    FROM tax_declaration
    WHERE taxpayer_id = ?
    AND declaration_type = 'اصلي'
    ORDER BY declaration_id
");
mysqli_stmt_bind_param($stmt, "i", $taxpayer_id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

while ($row = mysqli_fetch_assoc($res)) {
    $periods[] = $row["period"];
}

$selected_period = $_POST["period"] ?? ($_GET["period"] ?? ($periods[0] ?? ""));
$data = [
    "balance_previous_period" => 0,
    "sales_percent" => 0,
    "tax_on_percent" => 0,
    "amend_for_registration" => 0,
    "amend_for_department" => 0,
    "positive_tax_due" => 0,
    "negative_tax_due" => 0
];

/* تحميل الإقرار الأصلي */
if ($selected_period !== "") {
    $stmt = mysqli_prepare($conn, "
        SELECT *
        FROM tax_declaration
        WHERE taxpayer_id = ?
        AND period = ?
        AND declaration_type = 'اصلي'
        ORDER BY declaration_id DESC
        LIMIT 1
    ");
    mysqli_stmt_bind_param($stmt, "is", $taxpayer_id, $selected_period);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($res)) {
        $original_id = $row["declaration_id"];
        $data = $row;
        $original_final = (float)$row["positive_tax_due"] - (float)$row["negative_tax_due"];
    }
}

/* حفظ التعديل */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["submit"])) {

    $selected_period = $_POST["period"];
    $original_id = intval($_POST["original_id"]);
    $original_final = floatval($_POST["original_final"]);

    $balance = floatval($_POST["balance_previous_period"]);
    $sales = floatval($_POST["sales_percent"]);
    $sales_tax = $sales * $tax_rate;
    $adj_reg = floatval($_POST["amend_for_registration"]);
    $adj_dep = floatval($_POST["amend_for_department"]);

    $amended_result = $sales_tax + $adj_dep - $adj_reg - $balance;

    $positive = $amended_result > 0 ? $amended_result : 0;
    $negative = $amended_result < 0 ? abs($amended_result) : 0;

    $amended_final = $positive - $negative;
    $difference = $original_final - $amended_final;

    if ($original_id <= 0) {
        $message = "يرجى اختيار فترة لها إقرار أصلي.";
    } elseif ($sales < 0 || $adj_dep < 0) {
        $message = "لا يجوز إدخال رقم سالب.";
    } else {
        $check = mysqli_prepare($conn, "
            SELECT declaration_id
            FROM tax_declaration
            WHERE parent_declaration_id = ?
            AND declaration_type = 'معدل'
            LIMIT 1
        ");
        mysqli_stmt_bind_param($check, "i", $original_id);
        mysqli_stmt_execute($check);
        $checkRes = mysqli_stmt_get_result($check);

        if (mysqli_num_rows($checkRes) > 0) {
            $message = "هذا الإقرار تم تقديم طلب تعديل عليه سابقاً.";
        } else {
            mysqli_begin_transaction($conn);

            try {
                $stmt = mysqli_prepare($conn, "
                    INSERT INTO tax_declaration
                    (
                        taxpayer_id,
                        declaration_type,
                        period,
                        balance_previous_period,
                        sales_percent,
                        tax_on_percent,
                        amend_for_registration,
                        amend_for_department,
                        positive_tax_due,
                        negative_tax_due,
                        diff_amount,
                        parent_declaration_id,
                        is_diff_used,
                        is_balance_used
                    )
                    VALUES (?, 'معدل', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 0)
                ");

                mysqli_stmt_bind_param(
                    $stmt,
                    "isddddddddi",
                    $taxpayer_id,
                    $selected_period,
                    $balance,
                    $sales,
                    $sales_tax,
                    $adj_reg,
                    $adj_dep,
                    $positive,
                    $negative,
                    $difference,
                    $original_id
                );

                mysqli_stmt_execute($stmt);
                $new_declaration_id = mysqli_insert_id($conn);

                $transaction_number = generateTransactionNumber($conn);

                $stmt2 = mysqli_prepare($conn, "
                    INSERT INTO service_requests
                    (
                        taxpayer_id,
                        service_name,
                        submit_date,
                        status,
                        declaration_id,
                        installment_id,
                        transaction_number
                    )
                    VALUES (?, 'طلب تعديل إقرار ضريبة المبيعات', CURDATE(), 'تم تعديل الاقرار', ?, NULL, ?)
                ");

                mysqli_stmt_bind_param($stmt2, "iis", $taxpayer_id, $new_declaration_id, $transaction_number);
                mysqli_stmt_execute($stmt2);

                mysqli_commit($conn);

                if ($difference > 0) {
                    $msg = "تم حفظ طلب التعديل بنجاح\\nنتيجة التعديل لصالح المسجل\\nالقيمة: " . num($difference);
                } elseif ($difference < 0) {
                    $msg = "تم حفظ طلب التعديل بنجاح\\nنتيجة التعديل لصالح الدائرة\\nالمبلغ الإضافي: " . num(abs($difference));
                } else {
                    $msg = "تم حفظ طلب التعديل بنجاح\\nلا يوجد فرق مالي.";
                }

                echo "<script>alert('$msg'); window.location.href='mains.php';</script>";
                exit;

            } catch (Exception $e) {
                mysqli_rollback($conn);
                $message = "حدث خطأ أثناء حفظ طلب تعديل الإقرار.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<title>طلب تعديل إقرار ضريبة المبيعات</title>

<style>
*{box-sizing:border-box}
body{margin:0;background:#f4f4f4;font-family:"Segoe UI",Tahoma,sans-serif}
.container{width:900px;margin:auto;background:white;min-height:100vh;border:1px solid #ccc}
.header{height:100px;border-bottom:4px solid #4169e1;overflow:hidden}
.header img{width:100%;height:100%;object-fit:cover}
.title{text-align:center;font-size:28px;margin:25px 0 35px}
.top-row,.middle-row{display:flex;justify-content:space-around;margin-bottom:28px;padding:0 50px}
.input-group{width:38%;text-align:center}
.input-group label{display:block;font-weight:bold;margin-bottom:8px}
.input-group input,.input-group select{width:100%;height:34px;border:1px solid #999;text-align:center;font-size:15px}
.finance-list{width:800px;margin:25px auto 0}
.finance-row{display:grid;grid-template-columns:40px 270px 1fr;gap:15px;align-items:center;margin-bottom:14px}
.finance-row label{font-weight:bold;font-size:14px}
.finance-row input{height:34px;border:1px solid #999;text-align:center;font-size:15px}
.readonly-field{background:#e9ecef;color:#444}
.btn-info{background:#4169e1;color:white;border:none;width:30px;height:30px;font-weight:bold;font-size:16px}
.message{text-align:center;color:red;font-weight:bold;margin-bottom:20px}
.action-buttons{display:flex;justify-content:center;gap:30px;margin:45px 0 30px}
.action-buttons button{background:#4169e1;color:white;border:none;padding:10px 35px;font-size:16px;font-weight:bold;cursor:pointer}
.action-buttons button:hover,.btn-info:hover{background:#2b4cad}
</style>
</head>

<body>

<div class="container">

<div class="header">
    <img src="photo.jpeg">
</div>

<h2 class="title">طلب تعديل إقرار ضريبة المبيعات</h2>

<?php if ($message != ""): ?>
<div class="message"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<form method="POST">

<input type="hidden" name="original_id" value="<?= $original_id ?>">
<input type="hidden" name="original_final" value="<?= $original_final ?>">

<div class="top-row">
    <div class="input-group">
        <label>رقم المكلف</label>
        <input type="text" value="<?= htmlspecialchars($taxpayer_number) ?>" readonly class="readonly-field">
    </div>

    <div class="input-group">
        <label>اسم المكلف</label>
        <input type="text" value="<?= htmlspecialchars($taxpayer_name) ?>" readonly class="readonly-field">
    </div>
</div>

<div class="middle-row">
    <div class="input-group">
        <label>نوع الإقرار</label>
        <input type="text" value="معدل" readonly class="readonly-field">
    </div>

    <div class="input-group">
        <label>الفترة</label>
        <select name="period" onchange="window.location.href='amendment.php?period=' + encodeURIComponent(this.value)">
    <?php foreach ($periods as $p): ?>
        <option value="<?= htmlspecialchars($p) ?>" <?= $p == $selected_period ? "selected" : "" ?>>
            <?= htmlspecialchars($p) ?>
        </option>
    <?php endforeach; ?>
</select>
    </div>
</div>

<div class="finance-list">

<div class="finance-row">
    <button type="button" class="btn-info" title="يتم جلبه من الإقرار الأصلي">!</button>
    <input type="number" step="0.001" name="balance_previous_period" id="balance" value="<?= num($data["balance_previous_period"]) ?>" readonly class="readonly-field">
    <label>الخانة (1): رصيد مدور من الفترة السابقة</label>
</div>

<div class="finance-row">
    <button type="button" class="btn-info" title="أدخل قيمة المبيعات المصححة">!</button>
    <input type="number" step="0.001" name="sales_percent" id="sales" value="<?= num($data["sales_percent"]) ?>" oninput="calc()">
    <label>الخانة (2): مبيعات خاضعة للنسبة 7%</label>
</div>

<div class="finance-row">
    <button type="button" class="btn-info" title="تحسب تلقائياً">!</button>
    <input type="number" step="0.001" id="sales_tax" value="<?= num($data["tax_on_percent"]) ?>" readonly class="readonly-field">
    <label>الخانة (3): ضريبة المبيعات الخاضعة للنسبة</label>
</div>

<div class="finance-row">
    <button type="button" class="btn-info" title="يتم جلبها من الإقرار الأصلي">!</button>
    <input type="number" step="0.001" name="amend_for_registration" id="adj_reg" value="<?= num($data["amend_for_registration"]) ?>" readonly class="readonly-field">
    <label>الخانة (4): حركة تعديل لصالح المسجل</label>
</div>

<div class="finance-row">
    <button type="button" class="btn-info" title="أدخل حركة التعديل المصححة لصالح الدائرة">!</button>
    <input type="number" step="0.001" name="amend_for_department" id="adj_dep" value="<?= num($data["amend_for_department"]) ?>" oninput="calc()">
    <label>الخانة (5): حركة تعديل لصالح الدائرة</label>
</div>

<div class="finance-row">
    <button type="button" class="btn-info" title="تحسب تلقائياً">!</button>
    <input type="number" step="0.001" id="positive" value="<?= num($data["positive_tax_due"]) ?>" readonly class="readonly-field">
    <label>الخانة (6): الضريبة المستحقة موجبة (دفع)</label>
</div>

<div class="finance-row">
    <button type="button" class="btn-info" title="تحسب تلقائياً">!</button>
    <input type="number" step="0.001" id="negative" value="<?= num($data["negative_tax_due"]) ?>" readonly class="readonly-field">
    <label>الخانة (7): الضريبة المستحقة سالبة (مدور)</label>
</div>

</div>

<div class="action-buttons">
    <button type="submit" name="submit">إرسال</button>
    <button type="button" onclick="location.href='mains.php'">رجوع</button>
    <button type="button" onclick="location.reload()">إلغاء</button>
    <button type="button" onclick="window.print()">طباعة</button>
</div>

</form>
</div>

<script>
function calc(){
    let balance = parseFloat(document.getElementById("balance").value) || 0;
    let sales = parseFloat(document.getElementById("sales").value) || 0;
    let adjReg = parseFloat(document.getElementById("adj_reg").value) || 0;
    let adjDep = parseFloat(document.getElementById("adj_dep").value) || 0;

    let salesTax = sales * 0.07;
    document.getElementById("sales_tax").value = salesTax.toFixed(3);

    let result = salesTax + adjDep - adjReg - balance;

    if(result > 0){
        document.getElementById("positive").value = result.toFixed(3);
        document.getElementById("negative").value = "0.000";
    }else{
        document.getElementById("positive").value = "0.000";
        document.getElementById("negative").value = Math.abs(result).toFixed(3);
    }
}
window.onload = calc;
</script>

</body>
</html>
<?php
session_start();
include "config.php";

if (!isset($_SESSION["taxpayer_id"])) {
    header("Location: login.php");
    exit;
}

$taxpayer_id = $_SESSION["taxpayer_id"];
$message = "";
$balanceSourceDeclarationId = 0;
$tax_rate = 0.07;

/* جلب بيانات المكلف */
$stmt = mysqli_prepare($conn, "
    SELECT taxpayer_name, taxpayer_number
    FROM taxpayers
    WHERE taxpayer_id = ?
    LIMIT 1
");
mysqli_stmt_bind_param($stmt, "i", $taxpayer_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    $tax_name = $row["taxpayer_name"];
    $tax_number = $row["taxpayer_number"];
} else {
    die("لم يتم العثور على بيانات المكلف");
}

/* الرصيد المدور */
$default_balance = 0;

$stmtBal = mysqli_prepare($conn, "
    SELECT declaration_id, negative_tax_due
    FROM tax_declaration
    WHERE taxpayer_id = ?
    AND declaration_type = 'اصلي'
    AND negative_tax_due > 0
    AND is_balance_used = 0
    ORDER BY declaration_id DESC
    LIMIT 1
");
mysqli_stmt_bind_param($stmtBal, "i", $taxpayer_id);
mysqli_stmt_execute($stmtBal);
$resBal = mysqli_stmt_get_result($stmtBal);

if ($bal = mysqli_fetch_assoc($resBal)) {
    $balanceSourceDeclarationId = $bal["declaration_id"];
    $default_balance = $bal["negative_tax_due"];
}

/* حركة تعديل لصالح المسجل */
$default_adj_reg = 0;

$stmtAdj = mysqli_prepare($conn, "
    SELECT IFNULL(SUM(diff_amount), 0) AS total_adj
    FROM tax_declaration
    WHERE taxpayer_id = ?
    AND declaration_type = 'معدل'
    AND diff_amount > 0
    AND is_diff_used = 0
");
mysqli_stmt_bind_param($stmtAdj, "i", $taxpayer_id);
mysqli_stmt_execute($stmtAdj);
$resAdj = mysqli_stmt_get_result($stmtAdj);
$adj = mysqli_fetch_assoc($resAdj);
$default_adj_reg = $adj["total_adj"] ?? 0;

function generateTransactionNumber($conn) {
    $today = date("Ymd");

    $query = "
        SELECT transaction_number
        FROM service_requests
        WHERE transaction_number LIKE CONCAT(?, '%')
        ORDER BY transaction_number DESC
        LIMIT 1
    ";

    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $today);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $seq = 1;

    if ($row = mysqli_fetch_assoc($result)) {
        $last = $row["transaction_number"];
        if (strlen($last) == 14) {
            $seq = intval(substr($last, 8, 6)) + 1;
        }
    }

    return $today . str_pad($seq, 6, "0", STR_PAD_LEFT);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $period = $_POST["period"] ?? "";
    $declaration_type = "اصلي";

    $balance_previous_period = floatval($_POST["balance"] ?? 0);
    $sales_percent = floatval($_POST["sales_7"] ?? 0);
    $tax_on_percent = $sales_percent * $tax_rate;
    $amend_for_registration = floatval($_POST["adj_reg"] ?? 0);
    $amend_for_department = floatval($_POST["adj_dep"] ?? 0);

    $result_tax =
        $tax_on_percent
        + $amend_for_department
        - $amend_for_registration
        - $balance_previous_period;

    $positive_tax_due = $result_tax > 0 ? $result_tax : 0;
    $negative_tax_due = $result_tax < 0 ? abs($result_tax) : 0;

    if ($sales_percent <= 0) {
        $message = "يرجى تعبئة خانة المبيعات قبل إرسال الإقرار";
    } else {

        $check = mysqli_prepare($conn, "
            SELECT declaration_id
            FROM tax_declaration
            WHERE taxpayer_id = ?
            AND period = ?
            AND declaration_type = 'اصلي'
            LIMIT 1
        ");
        mysqli_stmt_bind_param($check, "is", $taxpayer_id, $period);
        mysqli_stmt_execute($check);
        $checkResult = mysqli_stmt_get_result($check);

        if (mysqli_num_rows($checkResult) > 0) {
            $message = "لا يمكن تقديم إقرار لهذه الفترة مرة أخرى. تم تقديم إقرار سابق لنفس الفترة.";
        } else {

            mysqli_begin_transaction($conn);

            try {
                $insert = mysqli_prepare($conn, "
                    INSERT INTO tax_declaration
                    (
                        taxpayer_id,
                        declaration_type,
                        balance_previous_period,
                        sales_percent,
                        tax_on_percent,
                        amend_for_registration,
                        amend_for_department,
                        positive_tax_due,
                        negative_tax_due,
                        diff_amount,
                        is_diff_used,
                        is_balance_used,
                        period
                    )
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 0, 0, ?)
                ");

                mysqli_stmt_bind_param(
                    $insert,
                    "isddddddds",
                    $taxpayer_id,
                    $declaration_type,
                    $balance_previous_period,
                    $sales_percent,
                    $tax_on_percent,
                    $amend_for_registration,
                    $amend_for_department,
                    $positive_tax_due,
                    $negative_tax_due,
                    $period
                );

                mysqli_stmt_execute($insert);
                $declaration_id = mysqli_insert_id($conn);

                $transaction_number = generateTransactionNumber($conn);

                $service = mysqli_prepare($conn, "
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
                    VALUES (?, 'طلب تقديم إقرار ضريبة المبيعات', CURDATE(), 'تم تقديم الاقرار', ?, NULL, ?)
                ");

                mysqli_stmt_bind_param(
                    $service,
                    "iis",
                    $taxpayer_id,
                    $declaration_id,
                    $transaction_number
                );

                mysqli_stmt_execute($service);

                if ($balanceSourceDeclarationId > 0 && $balance_previous_period > 0) {
                    $updateBalance = mysqli_prepare($conn, "
                        UPDATE tax_declaration
                        SET is_balance_used = 1
                        WHERE declaration_id = ?
                    ");
                    mysqli_stmt_bind_param($updateBalance, "i", $balanceSourceDeclarationId);
                    mysqli_stmt_execute($updateBalance);
                }

                if ($amend_for_registration > 0) {
                    $updateDiff = mysqli_prepare($conn, "
                        UPDATE tax_declaration
                        SET is_diff_used = 1
                        WHERE taxpayer_id = ?
                        AND declaration_type = 'معدل'
                        AND diff_amount > 0
                        AND is_diff_used = 0
                    ");
                    mysqli_stmt_bind_param($updateDiff, "i", $taxpayer_id);
                    mysqli_stmt_execute($updateDiff);
                }

                mysqli_commit($conn);

                echo "
                <script>
                    alert('تم تقديم الإقرار وحفظ طلب الخدمة بنجاح');
                    window.location.href = 'mains.php';
                </script>
                ";
                exit;

            } catch (Exception $e) {
                mysqli_rollback($conn);
                $message = "حدث خطأ أثناء حفظ الإقرار";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<title>إقرار ضريبة المبيعات</title>

<style>
body {
    font-family: "Segoe UI", Tahoma, sans-serif;
    background: #f4f4f4;
    margin: 0;
}

.container {
    background: white;
    width: 900px;
    margin: 0 auto;
    min-height: 100vh;
    border: 1px solid #ccc;
}

.header-banner {
    width: 100%;
    height: 100px;
    border-bottom: 4px solid #4169e1;
    overflow: hidden;
}

.header-banner img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.title {
    text-align: center;
    font-size: 28px;
    margin: 25px 0 35px;
}

.top-row,
.middle-row {
    display: flex;
    justify-content: space-around;
    margin-bottom: 28px;
    padding: 0 50px;
}

.input-group {
    width: 38%;
    text-align: center;
}

.input-group label {
    display: block;
    font-weight: bold;
    margin-bottom: 8px;
}

.input-group input,
.input-group select {
    width: 100%;
    height: 34px;
    border: 1px solid #999;
    text-align: center;
    font-size: 15px;
}

.finance-list {
    width: 800px;
    margin: 25px auto 0;
}

.finance-row {
    display: grid;
    grid-template-columns: 40px 270px 1fr;
    gap: 15px;
    align-items: center;
    margin-bottom: 14px;
}

.finance-row label {
    font-weight: bold;
    font-size: 14px;
}

.finance-row input {
    height: 34px;
    border: 1px solid #999;
    text-align: center;
    font-size: 15px;
}

.readonly-field {
    background: #e9ecef;
    color: #444;
}

.btn-info {
    background: #4169e1;
    color: white;
    border: none;
    width: 30px;
    height: 30px;
    font-weight: bold;
    font-size: 16px;
}

.message {
    text-align: center;
    color: red;
    font-weight: bold;
    margin-bottom: 20px;
}

.action-buttons {
    display: flex;
    justify-content: center;
    gap: 30px;
    margin: 50px 0 30px;
}

.action-buttons button {
    background: #4169e1;
    color: white;
    border: none;
    padding: 10px 35px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
}

.action-buttons button:hover,
.btn-info:hover {
    background: #2b4cad;
}
</style>
</head>

<body>

<div class="container">

    <div class="header-banner">
        <img src="photo.jpeg">
    </div>

    <h2 class="title">إقرار ضريبة المبيعات</h2>

    <?php if ($message != ""): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST">

        <div class="top-row">
            <div class="input-group">
                <label>رقم المكلف</label>
                <input type="text" value="<?= htmlspecialchars($tax_number) ?>" readonly class="readonly-field">
            </div>

            <div class="input-group">
                <label>اسم المكلف</label>
                <input type="text" value="<?= htmlspecialchars($tax_name) ?>" readonly class="readonly-field">
            </div>
        </div>

        <div class="middle-row">
            <div class="input-group">
                <label>نوع الإقرار</label>
                <input type="text" value="اصلي" readonly class="readonly-field">
            </div>

            <div class="input-group">
                <label>الفترة</label>
                <select name="period" required>
                    <option value="1+2 / 2026">1+2 / 2026</option>
                    <option value="3+4 / 2026">3+4 / 2026</option>
                    <option value="5+6 / 2026">5+6 / 2026</option>
                    <option value="7+8 / 2026">7+8 / 2026</option>
                    <option value="9+10 / 2026">9+10 / 2026</option>
                    <option value="11+12 / 2026">11+12 / 2026</option>
                </select>
            </div>
        </div>

        <div class="finance-list">

            <div class="finance-row">
                <button type="button" class="btn-info" title="الرصيد المدور من آخر إقرار أصلي غير مستخدم">!</button>
                <input type="number" step="0.001" id="balance" name="balance" value="<?= number_format($default_balance, 3, '.', '') ?>" readonly class="readonly-field">
                <label>الخانة (1): رصيد مدور من الفترة السابقة</label>
            </div>

            <div class="finance-row">
                <button type="button" class="btn-info" title="أدخل المبيعات الخاضعة لضريبة 7%">!</button>
                <input type="number" step="0.001" id="sales_7" name="sales_7" value="0" oninput="calculateTax()" required>
                <label>الخانة (2): مبيعات خاضعة للنسبة 7%</label>
            </div>

            <div class="finance-row">
                <button type="button" class="btn-info" title="تحسب تلقائياً: المبيعات × 7%">!</button>
                <input type="number" step="0.001" id="tax_subject" readonly class="readonly-field">
                <label>الخانة (3): ضريبة المبيعات الخاضعة للنسبة</label>
            </div>

            <div class="finance-row">
                <button type="button" class="btn-info" title="يتم جلبها تلقائياً من التعديلات غير المستخدمة">!</button>
                <input type="number" step="0.001" id="adj_reg" name="adj_reg" value="<?= number_format($default_adj_reg, 3, '.', '') ?>" readonly class="readonly-field">
                <label>الخانة (4): حركة تعديل لصالح المسجل</label>
            </div>

            <div class="finance-row">
                <button type="button" class="btn-info" title="مبلغ يتم إضافته لصالح الدائرة">!</button>
                <input type="number" step="0.001" id="adj_dep" name="adj_dep" value="0" oninput="calculateTax()">
                <label>الخانة (5): حركة تعديل لصالح الدائرة</label>
            </div>

            <div class="finance-row">
                <button type="button" class="btn-info" title="مبلغ واجب دفعه">!</button>
                <input type="number" step="0.001" id="tax_due_pos" readonly class="readonly-field">
                <label>الخانة (6): الضريبة المستحقة موجبة (دفع)</label>
            </div>

            <div class="finance-row">
                <button type="button" class="btn-info" title="رصيد يدور للفترة القادمة">!</button>
                <input type="number" step="0.001" id="tax_due_neg" readonly class="readonly-field">
                <label>الخانة (7): الضريبة المستحقة سالبة (مدور)</label>
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
    let balance = parseFloat(document.getElementById("balance").value) || 0;
    let sales = parseFloat(document.getElementById("sales_7").value) || 0;
    let adjReg = parseFloat(document.getElementById("adj_reg").value) || 0;
    let adjDep = parseFloat(document.getElementById("adj_dep").value) || 0;

    let tax = sales * 0.07;
    document.getElementById("tax_subject").value = tax.toFixed(3);

    let result = tax + adjDep - adjReg - balance;

    if (result > 0) {
        document.getElementById("tax_due_pos").value = result.toFixed(3);
        document.getElementById("tax_due_neg").value = "0.000";
    } else {
        document.getElementById("tax_due_pos").value = "0.000";
        document.getElementById("tax_due_neg").value = Math.abs(result).toFixed(3);
    }
}

window.onload = calculateTax;
</script>

</body>
</html>
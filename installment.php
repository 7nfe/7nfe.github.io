<?php
session_start();
include "config.php";

if (!isset($_SESSION["taxpayer_id"])) {
    header("Location: login.php");
    exit;
}

$taxpayer_id = $_SESSION["taxpayer_id"];

$message = "";

$current_due = 0;
$installment_value = 0;
$current_declaration_id = 0;

/* بيانات المكلف */
$query = "
SELECT
    taxpayer_number,
    taxpayer_name
FROM taxpayers
WHERE taxpayer_id = ?
LIMIT 1
";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $taxpayer_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (!$taxpayer = mysqli_fetch_assoc($result)) {
    die("لم يتم العثور على بيانات المكلف");
}

/* الفترات */
$periods = [];

$query2 = "
SELECT DISTINCT period
FROM tax_declaration
WHERE taxpayer_id = ?
AND positive_tax_due > 0
AND period IS NOT NULL
ORDER BY declaration_id DESC
";

$stmt2 = mysqli_prepare($conn, $query2);
mysqli_stmt_bind_param($stmt2, "i", $taxpayer_id);
mysqli_stmt_execute($stmt2);

$res2 = mysqli_stmt_get_result($stmt2);

while ($row = mysqli_fetch_assoc($res2)) {
    $periods[] = $row["period"];
}

/* تحميل الرصيد */
$selected_period =
    $_POST["period"]
    ?? $_GET["period"]
    ?? "";

if ($selected_period != "") {

    $query3 = "
    SELECT
        declaration_id,
        positive_tax_due
    FROM tax_declaration
    WHERE taxpayer_id = ?
    AND period = ?
    AND positive_tax_due > 0
    ORDER BY declaration_id DESC
    LIMIT 1
    ";

    $stmt3 = mysqli_prepare($conn, $query3);

    mysqli_stmt_bind_param(
        $stmt3,
        "is",
        $taxpayer_id,
        $selected_period
    );

    mysqli_stmt_execute($stmt3);

    $res3 = mysqli_stmt_get_result($stmt3);

    if ($due = mysqli_fetch_assoc($res3)) {

        $current_declaration_id =
            $due["declaration_id"];

        $current_due =
            floatval($due["positive_tax_due"]);
    }
}

/* حساب القسط */
$first_payment =
    floatval($_POST["first_payment"] ?? 0);

$installment_count =
    intval($_POST["installment_count"] ?? 1);

if (
    $current_due > 0 &&
    $first_payment > 0 &&
    $installment_count > 0
) {

    $remaining =
        $current_due - $first_payment;

    if ($remaining > 0) {

        $interest =
            $remaining * 0.09;

        $installment_value =
            ($remaining + $interest)
            / $installment_count;
    }
}

function generateTransactionNumber($conn)
{
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

            $seq =
                intval(substr($last, 8, 6)) + 1;
        }
    }

    return $today .
        str_pad($seq, 6, "0", STR_PAD_LEFT);
}

/* حفظ */
if (
    $_SERVER["REQUEST_METHOD"] === "POST"
    &&
    isset($_POST["send"])
) {

    if ($selected_period == "") {

        $message =
            "يرجى اختيار الفترة الضريبية";
    } elseif ($current_due <= 0) {

        $message =
            "لا يوجد رصيد مستحق";
    } elseif ($first_payment <= 0) {

        $message =
            "الدفعة الأولى يجب أن تكون أكبر من صفر";
    } else {

        $minimum =
            $current_due * 0.25;

        if ($first_payment < $minimum) {

            $message =
                "الدفعة الأولى يجب أن لا تقل عن 25% من الرصيد المستحق";
        } elseif ($first_payment >= $current_due) {

            $message =
                "الدفعة الأولى يجب أن تكون أقل من الرصيد المستحق";
        } else {

            $check = "
            SELECT installment_id
            FROM installment
            WHERE taxpayer_id = ?
            AND tax_period = ?
            LIMIT 1
            ";

            $stmtCheck =
                mysqli_prepare($conn, $check);

            mysqli_stmt_bind_param(
                $stmtCheck,
                "is",
                $taxpayer_id,
                $selected_period
            );

            mysqli_stmt_execute($stmtCheck);

            $resCheck =
                mysqli_stmt_get_result($stmtCheck);

            if (mysqli_num_rows($resCheck) > 0) {

                $message =
                    "يوجد طلب تقسيط سابق لنفس الفترة";
            } else {

                mysqli_begin_transaction($conn);

                try {

                    $insert = "
                    INSERT INTO installment
                    (
                        taxpayer_id,
                        tax_period,
                        total_due,
                        first_payment,
                        number_of_installments,
                        installment_value
                    )
                    VALUES
                    (
                        ?, ?, ?, ?, ?, ?
                    )
                    ";

                    $stmtInsert =
                        mysqli_prepare($conn, $insert);

                    mysqli_stmt_bind_param(
                        $stmtInsert,
                        "isddid",
                        $taxpayer_id,
                        $selected_period,
                        $current_due,
                        $first_payment,
                        $installment_count,
                        $installment_value
                    );

                    mysqli_stmt_execute($stmtInsert);

                    $installment_id =
                        mysqli_insert_id($conn);

                    $transaction_number =
                        generateTransactionNumber($conn);

                    $service = "
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
                    VALUES
                    (
                        ?,
                        'طلب تقسيط ضريبة المبيعات',
                        CURDATE(),
                        'تم التنفيذ',
                        NULL,
                        ?,
                        ?
                    )
                    ";

                    $stmtService =
                        mysqli_prepare($conn, $service);

                    mysqli_stmt_bind_param(
                        $stmtService,
                        "iis",
                        $taxpayer_id,
                        $installment_id,
                        $transaction_number
                    );

                    mysqli_stmt_execute($stmtService);

                    mysqli_commit($conn);

                    echo "
                    <script>

                    alert(
                    'تم حفظ طلب التقسيط بنجاح\\n\\n' +
                    'رقم المعاملة: $transaction_number\\n' +
                    'قيمة القسط: " .
                        number_format($installment_value, 3)
                        . "'
                    );

                    window.location.href='mains.php';

                    </script>
                    ";

                    exit;
                } catch (Exception $e) {

                    mysqli_rollback($conn);

                    $message =
                        'حدث خطأ أثناء حفظ الطلب';
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>
        طلب تقسيط ضريبة المبيعات
    </title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #f4f4f4;
            font-family: "Segoe UI", Tahoma, sans-serif;
        }

        .container {
            width: 1130px;
            max-width: 96%;
            margin: auto;
            background: white;
            min-height: 100vh;
            border: 1px solid #ccc;
        }

        .header {
            width: 100%;
            height: 82px;
            overflow: hidden;
            border-bottom: 5px solid royalblue;
        }

        .header img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .title {
            text-align: center;
            font-size: 34px;
            font-weight: bold;
            margin-top: 25px;
            margin-bottom: 45px;
        }

        .form-box {
            width: 430px;
            margin: auto;
        }

        .group {
            margin-bottom: 28px;
        }

        .group label {
            display: block;
            text-align: right;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .input-row {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .group input,
        .group select {
            width: 100%;
            height: 38px;
            border: 1px solid #999;
            background: white;
            padding: 5px 10px;
            font-size: 16px;
        }
        .top-row{
    display:flex;
    gap:20px;
}

.half{
    flex:1;
}

        .readonly {
            background: #f3f3f3 !important;
        }

        .info-btn {
            width: 40px;
            height: 38px;
            background: royalblue;
            color: white;
            border: none;
            font-size: 18px;
            font-weight: bold;
        }

        .buttons {
            text-align: center;
            margin-top: 45px;
            margin-bottom: 35px;
        }

        .btn {
            width: 120px;
            height: 44px;
            background: royalblue;
            color: white;
            border: none;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            margin: 0 12px;
        }

        .btn:hover,
        .info-btn:hover {
            background: #274fc0;
        }

        .message {
            text-align: center;
            color: red;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 25px;
        }
    </style>
</head>

<body>

    <div class="container">

        <div class="header">
            <img src="photo.jpeg">
        </div>

        <div class="title">
            طلب تقسيط ضريبة المبيعات
        </div>

        <?php if ($message != ""): ?>

            <div class="message">
                <?= htmlspecialchars($message) ?>
            </div>

        <?php endif; ?>

        <form method="POST">

            <div class="form-box">

             <div class="top-row">

<div class="group half">

<label>
رقم المكلف
</label>

<input
type="text"
value="<?= htmlspecialchars($taxpayer["taxpayer_number"]) ?>"
readonly
class="readonly">

</div>

<div class="group half">

<label>
اسم المكلف
</label>

<input
type="text"
value="<?= htmlspecialchars($taxpayer["taxpayer_name"]) ?>"
readonly
class="readonly">

</div>

</div>

                <div class="group">

                    <label>
                        الفترة الضريبية
                    </label>

                    <div class="input-row">

                        <button
                            type="button"
                            class="info-btn"
                            title="اختر فترة يوجد عليها رصيد مستحق">
                            i
                        </button>

                        <select
                            name="period"
                            onchange="window.location.href='installment.php?period=' + encodeURIComponent(this.value)">

                            <option value="">
                                اختر الفترة
                            </option>

                            <?php foreach ($periods as $p): ?>

                                <option
                                    value="<?= htmlspecialchars($p) ?>"
                                    <?= $selected_period == $p ? "selected" : "" ?>>

                                    <?= htmlspecialchars($p) ?>

                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                </div>

                <div class="group">

                    <label>
                        الرصيد المستحق
                    </label>

                    <div class="input-row">

                        <button
                            type="button"
                            class="info-btn"
                            title="يتم جلبه تلقائياً من الإقرار">
                            i
                        </button>

                        <input
                            type="text"
                            value="<?= number_format($current_due, 3) ?>"
                            readonly
                            class="readonly">

                    </div>

                </div>

                <div class="group">

                    <label>
                        الدفعة الأولى
                    </label>

                    <div class="input-row">

                        <button
                            type="button"
                            class="info-btn"
                            title="يجب أن لا تقل عن 25%">
                            i
                        </button>

                        <input
                            type="number"
                            step="0.001"
                            name="first_payment"
                            value="<?= $first_payment ?>"
                            required>

                    </div>

                </div>

                <div class="group">

                    <label>
                        عدد الأقساط
                    </label>

                    <div class="input-row">

                        <button
                            type="button"
                            class="info-btn"
                            title="عدد الأقساط من 1 إلى 24">
                            i
                        </button>

                        <input
                            type="number"
                            name="installment_count"
                            min="1"
                            max="24"
                            value="<?= $installment_count ?>">

                    </div>

                </div>

                <div class="group">

                    <label>
                        قيمة القسط
                    </label>

                    <div class="input-row">

                        <button
                            type="button"
                            class="info-btn"
                            title="تحسب تلقائياً">
                            !
                        </button>

                        <input
                            type="text"
                            value="<?= number_format($installment_value, 3) ?>"
                            readonly
                            class="readonly">

                    </div>

                </div>

                <div class="buttons">

                    <button
                        type="submit"
                        name="send"
                        class="btn">
                        إرسال
                    </button>

                    <button
                        type="reset"
                        class="btn">
                        إلغاء
                    </button>

                    <button
                        type="button"
                        onclick="window.print()"
                        class="btn">
                        طباعة
                    </button>

                    <button
                        type="button"
                        onclick="window.location.href='mains.php'"
                        class="btn">
                        رجوع
                    </button>

                </div>

            </div>

        </form>

    </div>

</body>

</html>
<?php
session_start();
include "config.php";

if (!isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit;
}

$taxpayers = mysqli_query($conn, "SELECT * FROM taxpayers ORDER BY taxpayer_id DESC");
$requests = mysqli_query($conn, "SELECT * FROM service_requests ORDER BY request_id DESC");
$declarations = mysqli_query($conn, "SELECT * FROM tax_declaration ORDER BY declaration_id DESC");
$installments = mysqli_query($conn, "SELECT * FROM installment ORDER BY installment_id DESC");
$cancels = mysqli_query($conn, "SELECT * FROM registration_cancel ORDER BY cancel_id DESC");
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>لوحة تحكم الأدمن</title>

    <style>
        body {
            font-family: Segoe UI;
            margin: 0;
            background: #f4f4f4
        }

        .header {
            background: royalblue;
            color: white;
            padding: 18px;
            text-align: center;
            font-size: 26px;
            font-weight: bold
        }

        .container {
            width: 95%;
            margin: 25px auto
        }

        .section {
            background: white;
            margin-bottom: 35px;
            padding: 20px;
            border: 1px solid #ccc
        }

        h2 {
            color: royalblue
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px
        }

        th,
        td {
            border: 1px solid #aaa;
            padding: 8px;
            text-align: center
        }

        th {
            background: #eee
        }

        .logout {
            display: inline-block;
            margin: 15px;
            background: red;
            color: white;
            padding: 10px 20px;
            text-decoration: none
        }
    </style>
</head>

<body>

    <div class="header">لوحة تحكم الأدمن</div>

    <div class="container">

        <a class="logout" href="admin_logout.php">تسجيل خروج</a>

        <div class="section">
            <h2>المكلفين</h2>
            <table>
                <tr>
                    <th>ID</th>
                    <th>اسم المكلف</th>
                    <th>الرقم الضريبي</th>
                    <th>الهاتف</th>
                    <th>مسجل؟</th>
                    <th>رقم الدفع</th>
                </tr>

                <?php while ($row = mysqli_fetch_assoc($taxpayers)): ?>
                    <tr>
                        <td><?= $row["taxpayer_id"] ?></td>
                        <td><?= htmlspecialchars($row["taxpayer_name"]) ?></td>
                        <td><?= htmlspecialchars($row["taxpayer_number"]) ?></td>
                        <td><?= htmlspecialchars($row["phone_number"]) ?></td>
<td>
<?php
if ($row["is_registered"] == 1) echo "مقبول";
elseif ($row["is_registered"] == 2) echo "قيد المراجعة";
elseif ($row["is_registered"] == 3) echo "مرفوض";
else echo "غير مسجل";
?>
</td>                        <td><?= htmlspecialchars($row["electronic_payment_number"]) ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>
        <?php
        $pending_users = mysqli_query($conn, "
SELECT
    taxpayer_id,
    taxpayer_name,
    taxpayer_number,
    email,
    phone_number
FROM taxpayers
WHERE is_registered = 2
");
        ?>

        <div class="section">
            <h2>طلبات التسجيل الجديدة</h2>

            <table>
                <tr>
                    <th>الاسم</th>
                    <th>رقم المكلف</th>
                    <th>الإيميل</th>
                    <th>الهاتف</th>
                    <th>الإجراء</th>
                </tr>

                <?php while ($row = mysqli_fetch_assoc($pending_users)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row["taxpayer_name"]) ?></td>
                        <td><?= htmlspecialchars($row["taxpayer_number"]) ?></td>
                        <td><?= htmlspecialchars($row["email"]) ?></td>
                        <td><?= htmlspecialchars($row["phone_number"]) ?></td>
                        <td>
                            <a href="manage_user_status.php?id=<?= $row['taxpayer_id'] ?>&action=approve">موافقة</a>
                            |
                            <a href="manage_user_status.php?id=<?= $row['taxpayer_id'] ?>&action=reject" style="color:red">رفض</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>

        <div class="section">
            <h2>طلبات الخدمات</h2>
            <table>
                <tr>
                    <th>رقم الطلب</th>
                    <th>رقم المكلف</th>
                    <th>الخدمة</th>
                    <th>التاريخ</th>
                    <th>الحالة</th>
                    <th>رقم المعاملة</th>
                </tr>

                <?php while ($row = mysqli_fetch_assoc($requests)): ?>
                    <tr>
                        <td><?= $row["request_id"] ?></td>
                        <td><?= $row["taxpayer_id"] ?></td>
                        <td><?= htmlspecialchars($row["service_name"]) ?></td>
                        <td><?= $row["submit_date"] ?></td>
                        <td><?= htmlspecialchars($row["status"]) ?></td>
                        <td><?= htmlspecialchars($row["transaction_number"]) ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>

        <div class="section">
            <h2>الإقرارات الضريبية</h2>
            <table>
                <tr>
                    <th>ID</th>
                    <th>رقم المكلف</th>
                    <th>النوع</th>
                    <th>الفترة</th>
                    <th>المبيعات</th>
                    <th>الضريبة الموجبة</th>
                    <th>الضريبة السالبة</th>
                    <th>فرق التعديل</th>
                </tr>

                <?php while ($row = mysqli_fetch_assoc($declarations)): ?>
                    <tr>
                        <td><?= $row["declaration_id"] ?></td>
                        <td><?= $row["taxpayer_id"] ?></td>
                        <td><?= htmlspecialchars($row["declaration_type"]) ?></td>
                        <td><?= htmlspecialchars($row["period"]) ?></td>
                        <td><?= $row["sales_percent"] ?></td>
                        <td><?= $row["positive_tax_due"] ?></td>
                        <td><?= $row["negative_tax_due"] ?></td>
                        <td><?= $row["diff_amount"] ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>

        <div class="section">
            <h2>طلبات التقسيط</h2>
            <table>
                <tr>
                    <th>ID</th>
                    <th>رقم المكلف</th>
                    <th>الفترة</th>
                    <th>الإجمالي</th>
                    <th>الدفعة الأولى</th>
                    <th>عدد الأقساط</th>
                    <th>قيمة القسط</th>
                </tr>


                <?php while ($row = mysqli_fetch_assoc($installments)): ?>
                    <tr>
                        <td><?= $row["installment_id"] ?></td>
                        <td><?= $row["taxpayer_id"] ?></td>
                        <td><?= htmlspecialchars($row["tax_period"]) ?></td>
                        <td><?= $row["total_due"] ?></td>
                        <td><?= $row["first_payment"] ?></td>
                        <td><?= $row["number_of_installments"] ?></td>
                        <td><?= $row["installment_value"] ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>

        <div class="section">
            <h2>إلغاء التسجيل</h2>
            <table>
                <tr>
                    <th>ID</th>
                    <th>رقم المكلف</th>
                    <th>السبب</th>
                    <th>التاريخ</th>
                </tr>

                <?php while ($row = mysqli_fetch_assoc($cancels)): ?>
                    <tr>
                        <td><?= $row["cancel_id"] ?></td>
                        <td><?= $row["taxpayer_id"] ?></td>
                        <td><?= htmlspecialchars($row["reason"]) ?></td>
                        <td><?= $row["cancel_date"] ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>
        <?php
        $supports = mysqli_query($conn, "
SELECT
    support_messages.*,
    taxpayers.taxpayer_name,
    taxpayers.taxpayer_number
FROM support_messages
JOIN taxpayers
ON taxpayers.taxpayer_id =
support_messages.taxpayer_id
ORDER BY message_id DESC
");
        ?>

        <div class="section">

            <h2>
                رسائل الدعم
            </h2>

            <table>

                <tr>
                    <th>رقم المكلف</th>
                    <th>اسم المكلف</th>
                    <th>الرسالة</th>
                    <th>التاريخ</th>
                    <th>البريد الإلكتروني</th>
                </tr>

                <?php while ($row = mysqli_fetch_assoc($supports)): ?>

                    <tr>

                        <td>
                            <?= htmlspecialchars($row["taxpayer_number"]) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($row["taxpayer_name"]) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($row["message"]) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($row["send_date"]) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($row["email"]) ?>
                        </td>

                    </tr>

                <?php endwhile; ?>

            </table>

        </div>
        <?php
        $edit_requests = mysqli_query($conn, "
SELECT
    edit_requests.*,
    taxpayers.taxpayer_number
FROM edit_requests
JOIN taxpayers ON taxpayers.taxpayer_id = edit_requests.taxpayer_id
ORDER BY request_id DESC
");
        ?>

        <div class="section">
            <h2>طلبات تعديل البيانات الشخصية</h2>

            <table>
                <tr>
                    <th>ID</th>
                    <th>رقم المكلف</th>
                    <th>الاسم الجديد</th>
                    <th>الهاتف</th>
                    <th>الإيميل</th>
                    <th>الاسم التجاري</th>
                    <th>العنوان</th>
                    <th>الحالة</th>
                    <th>إجراء</th>
                </tr>

                <?php while ($row = mysqli_fetch_assoc($edit_requests)): ?>
                    <tr>
                        <td><?= $row["request_id"] ?></td>
                        <td><?= htmlspecialchars($row["taxpayer_number"]) ?></td>
                        <td><?= htmlspecialchars($row["taxpayer_name"]) ?></td>
                        <td><?= htmlspecialchars($row["phone_number"]) ?></td>
                        <td><?= htmlspecialchars($row["email"]) ?></td>
                        <td><?= htmlspecialchars($row["trade_name"]) ?></td>
                        <td><?= htmlspecialchars($row["address"]) ?></td>
                        <td><?= htmlspecialchars($row["status"]) ?></td>
                        <td>
                            <?php if ($row["status"] == "pending"): ?>

                                <a href="approve_edit.php?id=<?= $row["request_id"] ?>">
                                    موافقة
                                </a>

                                |

                                <a
                                    href="reject_edit.php?id=<?= $row["request_id"] ?>"
                                    style="color:red">

                                    رفض

                                </a>

                            <?php else: ?>

                                <?= htmlspecialchars($row["status"]) ?>

                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>

    </div>

</body>

</html>
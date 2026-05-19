<?php
session_start();
include "config.php";

if (!isset($_SESSION["taxpayer_id"])) {
    header("Location: login.php");
    exit;
}

$taxpayer_id = $_SESSION["taxpayer_id"];

/* بيانات المكلف */
$query = "
SELECT
    taxpayer_name,
    taxpayer_number
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

/* الطلبات */
$query2 = "
SELECT
    transaction_number,
    service_name,
    submit_date,
    status
FROM service_requests
WHERE taxpayer_id = ?

UNION ALL

SELECT
    CONCAT('تعديل البيانات-', request_id) AS transaction_number,
    'طلب تعديل البيانات الشخصية' AS service_name,
    request_date AS submit_date,
    status
FROM edit_requests
WHERE taxpayer_id = ?

ORDER BY submit_date DESC
";

$stmt2 = mysqli_prepare($conn, $query2);

mysqli_stmt_bind_param(
    $stmt2,
    "ii",
    $taxpayer_id,
    $taxpayer_id
);

mysqli_stmt_execute($stmt2);

$requests = mysqli_stmt_get_result($stmt2);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>طلباتي</title>

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
            width: 1180px;
            max-width: 96%;
            margin: auto;
            background: white;
            min-height: 100vh;
            border: 1px solid #ccc;
        }

        .header {
            width: 100%;
            height: 90px;
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
            margin-bottom: 35px;
        }

        .top-info {
            display: flex;
            justify-content: center;
            gap: 180px;
            margin-bottom: 35px;
        }

        .info-group {
            width: 280px;
        }

        .info-group label {
            display: block;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
            text-align: right;
        }

        .info-group input {
            width: 100%;
            height: 38px;
            border: 1px solid #999;
            background: #f8f8f8;
            text-align: center;
            font-size: 16px;
        }

        .table-container {
            width: 940px;
            margin: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
        }

        th {
            background: #f1f1f1;
            font-size: 17px;
            padding: 14px;
            border: 1px solid #999;
        }

        td {
            padding: 14px;
            border: 1px solid #999;
            font-size: 15px;
        }

        .no-data {
            text-align: center;
            color: red;
            font-size: 20px;
            font-weight: bold;
            margin-top: 30px;
        }

        .buttons {
            text-align: center;
            margin-top: 45px;
            margin-bottom: 35px;
        }

        .btn {
            width: 140px;
            height: 45px;
            background: royalblue;
            color: white;
            border: none;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            margin: 0 18px;
        }

        .btn:hover {
            background: #274fc0;
        }

        @media (max-width:1000px) {

            .top-info {
                flex-direction: column;
                align-items: center;
                gap: 30px;
            }

            .table-container {
                width: 95%;
                overflow: auto;
            }

            table {
                min-width: 750px;
            }
        }
    </style>
</head>

<body>

    <div class="container">

        <div class="header">
            <img src="photo.jpeg">
        </div>

        <div class="title">
            طلباتي
        </div>

        <div class="top-info">

            <div class="info-group">
                <label>
                    رقم المكلف
                </label>

                <input
                    type="text"
                    value="<?= htmlspecialchars($taxpayer["taxpayer_number"]) ?>"
                    readonly>
            </div>

            <div class="info-group">
                <label>
                    اسم المكلف
                </label>

                <input
                    type="text"
                    value="<?= htmlspecialchars($taxpayer["taxpayer_name"]) ?>"
                    readonly>
            </div>

        </div>

        <div class="table-container">

            <?php if (mysqli_num_rows($requests) > 0): ?>

                <table>

                    <tr>
                        <th>اسم الخدمة</th>
                        <th>رقم المعاملة</th>
                        <th>تاريخ تقديم الطلب</th>
                        <th>الحالة</th>
                    </tr>

                    <?php while ($row = mysqli_fetch_assoc($requests)): ?>

                        <tr>

                            <td>
                                <?= htmlspecialchars($row["service_name"]) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($row["transaction_number"]) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($row["submit_date"]) ?>
                            </td>

                            <td>

                                <?php
                                if ($row["status"] == "pending") {
                                    echo "قيد المراجعة";
                                } elseif ($row["status"] == "approved") {
                                    echo "تمت الموافقة";
                                } elseif ($row["status"] == "rejected") {
                                    echo "مرفوض";
                                } else {
                                    echo htmlspecialchars($row["status"]);
                                }
                                ?>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                </table>

            <?php else: ?>

                <div class="no-data">
                    لا يوجد طلبات لهذا المكلف حتى الآن
                </div>

            <?php endif; ?>

        </div>

        <div class="buttons">

            <button
                class="btn"
                onclick="window.location.href='mains.php'">
                رجوع
            </button>

            <button
                class="btn"
                onclick="window.print()">
                طباعة
            </button>

        </div>

    </div>

</body>

</html>
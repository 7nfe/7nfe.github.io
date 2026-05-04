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



//$sql = "SELECT * FROM service_requests WHERE taxpayer_id = '$session_id' ORDER BY request_date DESC";
//$result = mysqli_query($conn, $sql);aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa

// Loop through rows
//while ($rows = mysqli_fetch_assoc($result)) {}


?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>شاشة طلباتي - نظام الضرائب</title>
    <style>
        :root {
            --primary-blue: #3b71ca;
            --dark-blue: #2c5282;
            --border-color: #dee2e6;
            --bg-light: #f8f9fa;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: white;
            margin: 0;
            padding: 0;
            color: #333;
        }

        /* الهيدر العلوي */
        .header-banner {
            width: 100%;
            height: 100px;
            background-color: #ffffff;
            border-bottom: 6px solid #4873c4;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .header-image {
            width: 200%;
            height: 200%;
            object-fit: contain;
        }

        /* حاوية المحتوى الرئيسي */
        .container {
            max-width: 1000px;
            margin: 20px auto;
            padding: 20px;
            text-align: center;
        }

        h1 {
            font-size: 24px;
            margin-bottom: 30px;
            font-weight: bold;
        }

        /* قسم معلومات المكلف */
        .taxpayer-info {
            display: flex;
            justify-content: center;
            gap: 50px;
            margin-bottom: 40px;
        }

        .input-group {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .input-group label {
            margin-bottom: 8px;
            font-weight: 600;
        }

        .input-group input {
            width: 250px;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 2px;
            outline: none;
        }

        /* الجدول */
        .table-container {
            border: 1px solid #999;
            min-height: 300px;
            margin-bottom: 30px;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background-color: #fff;
            border-bottom: 2px solid var(--border-color);
            padding: 12px;
            font-size: 14px;
            color: #555;
        }

        td {
            padding: 10px;
            border-bottom: 1px solid #eee;
            text-align: center;
        }

        /* الأزرار السفلية */
        .button-group {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }

        .btn {
            padding: 10px 40px;
            border: none;
            border-radius: 4px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn-print {
            background-color: var(--primary-blue);
        }

        .btn-close {
            background-color: var(--primary-blue);
        }

        .btn:hover {
            opacity: 0.9;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        /* الزر الجانبي (AI) */
        .ai-sidebar-btn {
            position: fixed;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            background-color: var(--primary-blue);
            color: white;
            padding: 15px 10px;
            border-radius: 5px 0 0 5px;
            writing-mode: vertical-rl;
            text-orientation: upright;
            font-weight: bold;
            cursor: pointer;
        }
    </style>
</head>

<body>

    <!-- الهيدر كما في الصورة -->
    <header class="header-banner">
        <div class="header-banner">
            <img src="photo.jpeg" alt="شعار دائرة ضريبة الدخل والمبيعات" class="header-image">
        </div>
    </header>

    <!-- الزر الجانبي AI -->
    <div class="ai-sidebar-btn">AI</div>

    <div class="container">
        <h1>طلباتي</h1>

        <!-- فورم معلومات المكلف -->
        <div class="taxpayer-info">
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

        <!-- جدول البيانات -->
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
                    <?php
             
                    $sql = "SELECT * FROM service_requests WHERE taxpayer_id = '$session_id'";
                    $result = mysqli_query($conn, $sql);
                    ?>
                    
                        <?php
                        // Loop through rows
                        while ($rows = mysqli_fetch_assoc($result)) {
                        ?>
                            <tr>
                                <td><?php echo $rows['declaration_id']; ?></td>
                                <td><?php echo $rows['service_name']; ?></td>
                                <td><?php echo $rows['request_date']; ?></td>
                                <td><?php echo $rows['status']; ?></td>
                            </tr>
                        <?php
                        }
                        ?>
                 
                    <?php
                    mysqli_close($conn);
                    ?>
                </tbody>
            </table>
        </div>

        <!-- أزرار التحكم -->
        <div class="button-group">
            <button class="btn btn-close" onclick="window.close()">إغلاق</button>
            <button class="btn btn-print" onclick="window.print()">طباعة</button>
        </div>
    </div>

</body>

</html>
<?php
// 1. بدء الجلسة والاتصال بقاعدة البيانات
session_start();
include("config.php"); 

// 2. التحقق من أن المستخدم سجل دخوله مسبقاً
// إذا لم تكن الجلسة تحتوي على رقم مكلف، يتم توجيهه لصفحة الدخول
if (!isset($_SESSION['taxpayer_id'])) {
    header("Location: login.php");
    exit();
}

// الحصول على رقم المكلف من الجلسة
$session_id = $_SESSION['taxpayer_id'];

// 3. جلب بيانات المكلف من قاعدة البيانات
$taxpayer_number = "";
$taxpayer_name = "";

$stmt = $conn->prepare("SELECT taxpayer_id, taxpayer_name FROM taxpayers WHERE taxpayer_id = ?");
$stmt->bind_param("s", $session_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $taxpayer_number = $row['taxpayer_id'];
    $taxpayer_name   = $row['taxpayer_name'];
} else {
    // في حال حدث خطأ أو لم يتم العثور على البيانات
    $taxpayer_number = $session_id;
    $taxpayer_name   = "مكلف غير معروف";
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الشاشة الرئيسية - ضريبة المبيعات</title>
    
    <link rel="stylesheet" href="stylemain.css">
</head>
<body>

    <div class="main-app-container">
        
        <div class="header-banner">
            <img src="photo.jpeg" alt="شعار دائرة ضريبة الدخل والمبيعات" class="header-image">
        </div>

        <div class="page-title">الشاشة الرئيسية</div>

        <div class="navbar">
            <a href="myrequests.php">طلباتي</a>
            <a href="personal_data.php">البيانات الشخصية</a>
            <a href="internal_services.php">الخدمات الداخلية</a>
            <a href="payment.php">الدفع الإلكتروني</a>
        </div>

        <div class="taxpayer-info-section">
            <div class="input-group">
                <label>رقم المكلف</label>
                <input type="text" value="<?php echo htmlspecialchars($taxpayer_number); ?>" readonly>
            </div>
            <div class="input-group">
                <label>اسم المكلف</label>
                <input type="text" value="<?php echo htmlspecialchars($taxpayer_name); ?>" readonly>
            </div>
        </div>

        <div class="cards-grid">
            <a href="taxreturn.php" class="scroll-card">إقرار ضريبة المبيعات</a>
            <a href="installment.php" class="scroll-card">طلب تقسيط ضريبة المبيعات</a>
            <a href="amend_return.php" class="scroll-card">طلب تعديل إقرار مبيعات</a>
            <a href="cancel.php" class="scroll-card">إلغاء تسجيل مكلف</a>
        </div>

        <button class="btn-ai-float">Ai</button>

    </div>

</body>
</html>
<?php
// البدء في استخدام الجلسة للوصول إلى رقم المكلف المسجل دخوله
session_start();
include("config.php");

// التحقق مما إذا كان المستخدم قد قام بتسجيل الدخول فعلاً
// نفترض أنك قمت بتخزين 'taxpayer_id' في الجلسة عند صفحة login.php
if (!isset($_SESSION['taxpayer_id'])) {
    header("Location: login.php"); // إعادة التوجيه لصفحة الدخول إذا لم يكن مسجلاً
    exit();
}

$taxpayer_id = $_SESSION['taxpayer_id'];

// استعلام لجلب بيانات المكلف من جدول taxpayers
$sql = "SELECT * FROM taxpayers WHERE taxpayer_id = '$taxpayer_id'";
$result = mysqli_query($conn, $sql);
$userData = mysqli_fetch_assoc($result);

if (!$userData) {
    echo "لم يتم العثور على بيانات لهذا المستخدم.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>بياناتي الشخصية - نظام الضريبة</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background-color: #f4f7f6; padding: 20px; }
        .profile-card {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .header { border-bottom: 2px solid #4873c4; margin-bottom: 20px; padding-bottom: 10px; color: #4873c4; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .info-item { margin-bottom: 15px; }
        .label { font-weight: bold; color: #555; display: block; margin-bottom: 5px; }
        .value { background: #f9f9f9; padding: 10px; border-radius: 4px; border: 1px solid #eee; display: block; }
    </style>
</head>
<body>

<div class="profile-card">
    <div class="header">
        <h2>الملف الشخصي للمكلف</h2>
    </div>

    <div class="info-grid">
        <!-- عرض البيانات بناءً على أسماء الأعمدة في قاعدة بياناتك -->
        <div class="info-item">
            <span class="label">رقم المكلف:</span>
            <span class="value"><?php echo htmlspecialchars($userData['taxpayer_id']); ?></span>
        </div>
        <div class="info-item">
            <span class="label">اسم المكلف:</span>
            <span class="value"><?php echo htmlspecialchars($userData['taxpayer_name']); ?></span>
        </div>
        <div class="info-item">
            <span class="label">البريد الإلكتروني:</span>
            <span class="value"><?php echo htmlspecialchars($userData['email']); ?></span>
        </div>
        <div class="info-item">
            <span class="label">رقم الهاتف:</span>
            <span class="value"><?php echo htmlspecialchars($userData['phone_number']); ?></span>
        </div>
        <div class="info-item">
            <span class="label">الاسم التجاري:</span>
            <span class="value"><?php echo htmlspecialchars($userData['trade_name']); ?></span>
        </div>
        <div class="info-item">
            <span class="label">العنوان:</span>
            <span class="value"><?php echo htmlspecialchars($userData['address']); ?></span>
        </div>
        <div class="info-item">
            <span class="label">الرقم الوطني للمنشأة:</span>
            <span class="value"><?php echo htmlspecialchars($userData['national_establishment_number']); ?></span>
        </div>
        <div class="info-item">
            <span class="label">طبيعة النشاط:</span>
            <span class="value"><?php echo htmlspecialchars($userData['nature_of_activity']); ?></span>
        </div>
    </div>

    <div style="margin-top: 30px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #2ecc71; color: white; border: none; border-radius: 4px; cursor: pointer;">طباعة البيانات</button>
        <a href="logout.php" style="margin-right: 10px; color: #e74c3c;">تسجيل الخروج</a>
    </div>
</div>

</body>
</html>
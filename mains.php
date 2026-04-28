<?php
// محاكاة لبيانات قادمة من قاعدة البيانات
$taxpayer_number = "123456789"; 
$taxpayer_name = "شركة العقبة للتجارة العامة"; 
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الشاشة الرئيسية - ضريبة المبيعات</title>
    
    <link rel="stylesheet" href="style.css">
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
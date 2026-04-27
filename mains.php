<?php
// محاكاة لبيانات قادمة من قاعدة البيانات أو الـ Session بعد تسجيل الدخول
$taxpayer_number = "123456789"; 
$taxpayer_name = "شركة العقبة للتجارة العامة"; 
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الشاشة الرئيسية - ضريبة المبيعات</title>
    <style>
        /* التنسيقات العامة */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
        }

        .main-container {
            width: 90%;
            max-width: 1200px;
            background-color: #ffffff;
            min-height: 100vh;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            position: relative;
            padding-bottom: 50px;
        }

        /* ترويسة الشعار (يمكنك استبدال مسار الصورة بصورة الشعار الحقيقي) */
        .header-banner {
    width: 100%;
    height: 100px; /* يمكنك زيادة الارتفاع حسب حجم الصورة */
    background-color: #ffffff;
    border-bottom: 6px solid #4873c4;
    padding: 0; /* أزلنا الحشو الداخلي لتأخذ الصورة كامل المساحة إذا أردت */
    overflow: hidden; /* لمنع خروج الصورة عن الإطار */
    display: flex;
    align-items: center;
    justify-content: center; /* لتوسيط الصورة */
}

.header-image {
    width: 200%; /* تجعل الصورة بعرض الهيدر */
    height: 200%; /* تجعل الصورة بارتفاع الهيدر */
    object-fit: contain; /* أهم خاصية: تحافظ على تناسق أبعاد الصورة دون تمطيطها */
}

        /* عنوان الشاشة */
        .page-title {
            text-align: center;
            color: #000;
            margin: 30px 0;
            font-size: 28px;
            font-weight: bold;
        }

        /* الشريط الملاحي الأزرق */
        .navbar {
            background-color: #4873c4;
            display: flex;
            justify-content: space-around;
            padding: 15px 0;
            margin: 0 40px;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            font-weight: bold;
            font-size: 16px;
        }

        .navbar a:hover {
            text-decoration: underline;
        }

        /* قسم بيانات المكلف */
        .taxpayer-info {
            display: flex;
            justify-content: center;
            gap: 100px;
            margin: 40px 0;
        }

        .input-group {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .input-group label {
            font-weight: bold;
            font-size: 16px;
        }

        .input-group input {
            padding: 8px 10px;
            border: 1px solid #777;
            width: 250px;
            font-size: 16px;
            background-color: #fafafa;
        }

        /* شبكة الأزرار (اللفائف) */
        .cards-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            padding: 0 100px;
            margin-top: 50px;
        }

        .scroll-card {
            background: #ffffff;
            border: 1px solid #ccc;
            border-radius: 10px;
            padding: 40px 20px;
            text-align: center;
            font-size: 22px;
            font-weight: bold;
            color: #000;
            cursor: pointer;
            text-decoration: none;
            position: relative;
            box-shadow: inset 0px 0px 15px rgba(0,0,0,0.05), 0 5px 10px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        /* محاكاة شكل اللفافة (طيات الورق) من الأعلى والأسفل */
        .scroll-card::before {
            content: '';
            position: absolute;
            top: -10px; left: 5%; right: 5%;
            height: 20px;
            background: linear-gradient(to bottom, #e0e0e0, #ffffff);
            border-radius: 50%;
            border: 1px solid #ccc;
            border-bottom: none;
        }

        .scroll-card::after {
            content: '';
            position: absolute;
            bottom: -10px; left: 5%; right: 5%;
            height: 20px;
            background: linear-gradient(to top, #e0e0e0, #ffffff);
            border-radius: 50%;
            border: 1px solid #ccc;
            border-top: none;
        }

        .scroll-card:hover {
            transform: translateY(-5px);
            box-shadow: inset 0px 0px 15px rgba(0,0,0,0.05), 0 10px 15px rgba(0,0,0,0.2);
            border-color: #4873c4;
            color: #4873c4;
        }

        /* زر الذكاء الاصطناعي */
        .ai-button {
            position: absolute;
            left: 40px;
            bottom: 100px;
            background-color: #4873c4;
            color: white;
            border: none;
            width: 70px;
            height: 70px;
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .ai-button:hover {
            background-color: #365a9e;
        }
    </style>
</head>
<body>

    <div class="main-container">
        
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

        <div class="taxpayer-info">
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

        <button class="ai-button">Ai</button>

    </div>

</body>
</html>
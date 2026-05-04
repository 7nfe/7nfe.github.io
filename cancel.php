<?php
session_start();

// محاكاة جلب بيانات المكلف من الجلسة (Session) أو قاعدة البيانات
$taxpayer_number = $_SESSION['taxpayer_number'] ?? "123456789"; 
$taxpayer_name = $_SESSION['taxpayer_name'] ?? "شركة العقبة للتجارة العامة"; 

// معالجة النموذج عند الإرسال
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cancel_reason = $_POST['cancel_reason'] ?? '';
    
    // هنا يمكنك إضافة كود الاتصال بقاعدة البيانات لتحديث حالة المكلف
    // مثال: UPDATE taxpayers SET status = 'cancelled', reason = '$cancel_reason' WHERE id = '$taxpayer_number'
    
    // رسالة نجاح وهمية للتوضيح
    $success_message = "تم إرسال طلب إلغاء التسجيل بنجاح.";
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إلغاء تسجيل مكلف</title>
    <style>
        /* التنسيقات العامة المطابقة للنظام */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
        }

        .main-container {
            width: 95%;
            max-width: 1100px;
            background-color: #ffffff;
            min-height: 100vh;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            position: relative;
            padding-bottom: 50px;
        }

        /* الترويسة */
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

        .page-title {
            text-align: center;
            color: #000;
            margin: 50px 0;
            font-size: 28px;
            font-weight: bold;
        }

        /* تنسيق نموذج الإدخال ليطابق الصورة */
        .form-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 25px;
            margin-top: 40px;
        }

        .input-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 500px; /* التحكم بعرض الحقل والمسافة */
        }

        .input-row label {
            font-weight: bold;
            font-size: 18px;
            width: 150px;
            text-align: right;
        }

        .input-row input {
            width: 300px;
            padding: 10px;
            border: 1px solid #777;
            font-size: 16px;
            text-align: right;
        }

        /* جعل حقول البيانات الثابتة بخلفية رمادية قليلاً */
        .input-row input[readonly] {
            background-color: #fafafa;
            color: #333;
        }

        /* أزرار الإجراءات السفلية */
        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 50px;
            margin-top: 80px;
        }

        .btn-action {
            background-color: #4873c4;
            color: white;
            border: none;
            padding: 12px 60px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
            border-radius: 2px;
        }

        .btn-action:hover {
            background-color: #365a9e;
        }

        /* زر AI العائم على اليسار */
        .btn-ai {
            position: absolute;
            left: 80px;
            top: 40%;
            background-color: #4873c4;
            color: white;
            border: none;
            width: 70px;
            height: 70px;
            font-size: 22px;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .btn-ai:hover {
            background-color: #365a9e;
        }

        /* رسالة النجاح */
        .alert-success {
            text-align: center;
            color: #155724;
            background-color: #d4edda;
            padding: 10px;
            margin: 20px auto;
            width: 50%;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
        }
    </style>
</head>
<body>

    <div class="main-container">
        
        <div class="header-banner">
               <div class="header-banner">
    <img src="photo.jpeg" alt="شعار دائرة ضريبة الدخل والمبيعات" class="header-image">
</div>
        </div>

        <?php if (isset($success_message)): ?>
            <div class="alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <div class="page-title">إلغاء تسجيل مكلف</div>

        <button type="button" class="btn-ai">AI</button>

        <form method="POST" action="">
            <div class="form-section">
                
                <div class="input-row">
                    <label>رقم المكلف</label>
                    <input type="text" name="taxpayer_number" value="<?php echo htmlspecialchars($taxpayer_number); ?>" readonly>
                </div>

                <div class="input-row">
                    <label>اسم المكلف</label>
                    <input type="text" name="taxpayer_name" value="<?php echo htmlspecialchars($taxpayer_name); ?>" readonly>
                </div>

                <div class="input-row">
                    <label>سبب إلغاء التسجيل</label>
                    <input type="text" name="cancel_reason" required placeholder="أدخل السبب هنا...">
                </div>

            </div>

            <div class="action-buttons">
                <button type="button" class="btn-action" onclick="window.location.href='mains.php'">إلغاء</button>
                <button type="submit" class="btn-action">إرسال</button>
            </div>
        </form>

    </div>

</body>
</html>
<?php
session_start();

// التحقق مما إذا تم إرسال النموذج
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // محاكاة التحقق من قاعدة البيانات (يجب استبدالها لاحقاً باستعلام MySQL حقيقي)
    // هنا نفترض أن تسجيل الدخول ناجح دائماً لغايات التجربة
    if (!empty($username) && !empty($password)) {
        
        // حفظ بيانات المكلف في الجلسة (Session) لاستخدامها في باقي الشاشات
        $_SESSION['taxpayer_number'] = $username; // يمكن استبداله بالرقم الفعلي من قاعدة البيانات
        $_SESSION['taxpayer_name'] = "شركة العقبة للتجارة العامة"; // اسم افتراضي

        // التوجيه إلى الشاشة الرئيسية
        header("Location: mains.php");
        exit();
    } else {
        $error_message = "يرجى إدخال اسم المستخدم وكلمة المرور.";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - ضريبة المبيعات</title>
    <style>
        /* التنسيقات العامة */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            height: 100vh;
        }

        /* الترويسة العلوية المطابقة للنظام */
        .header-banner {
            width: 100%;
            height: 80px;
            background-color: #ffffff;
            border-bottom: 4px solid #4873c4;
            display: flex;
            align-items: center;
            padding: 0 20px;
            box-sizing: border-box;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .header-text {
            font-weight: bold;
            color: #4873c4;
            text-align: left;
            width: 100%;
            line-height: 1.5;
        }

        /* حاوية تسجيل الدخول لتوسيط المربع */
        .login-container {
            flex-grow: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
        }

        /* مربع تسجيل الدخول (Card) */
        .login-box {
            background-color: #ffffff;
            width: 400px;
            padding: 40px 30px;
            border: 1px solid #777;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .login-title {
            color: #4873c4;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 40px;
        }

        /* تنسيق مجموعات الإدخال */
        .input-group {
            margin-bottom: 30px;
            text-align: right;
        }

        .input-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 10px;
            color: #000;
        }

        /* تنسيق الحقول لتكون بخط سفلي فقط كما في الصورة */
        .input-group input[type="text"],
        .input-group input[type="password"] {
            width: 100%;
            border: none;
            border-bottom: 1px solid #777;
            padding: 5px 0;
            font-size: 16px;
            outline: none;
            background-color: transparent;
            transition: border-color 0.3s;
        }

        .input-group input:focus {
            border-bottom: 2px solid #4873c4;
        }

        /* تنسيق مربع اختيار "إظهار كلمة المرور" */
        .checkbox-group {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 30px;
            font-size: 14px;
        }

        /* زر تسجيل الدخول */
        .btn-login {
            background-color: #4873c4;
            color: white;
            border: none;
            width: 100%;
            padding: 12px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn-login:hover {
            background-color: #365a9e;
        }

        /* رسالة الخطأ */
        .error-message {
            color: red;
            margin-bottom: 15px;
            font-size: 14px;
        }
    </style>
</head>
<body>

    <div class="header-banner">
        <div class="header-text">
            المملكة الأردنية الهاشمية<br>وزارة المالية<br>دائرة ضريبة الدخل والمبيعات
        </div>
    </div>

    <div class="login-container">
        <div class="login-box">
            <div class="login-title">تسجيل الدخول</div>

            <?php if (isset($error_message)): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                
                <div class="input-group">
                    <label>اسم المستخدم</label>
                    <input type="text" name="username" required>
                </div>

                <div class="input-group">
                    <label>كلمة المرور</label>
                    <input type="password" name="password" id="passwordInput" required>
                </div>

                <div class="checkbox-group">
                    <input type="checkbox" id="showPassword" onclick="togglePassword()">
                    <label for="showPassword" style="margin: 0; cursor: pointer;">إظهار كلمة المرور</label>
                </div>

                <button type="submit" class="btn-login">تسجيل الدخول</button>
                
            </form>
        </div>
    </div>

    <script>
        function togglePassword() {
            var passwordField = document.getElementById("passwordInput");
            if (passwordField.type === "password") {
                passwordField.type = "text";
            } else {
                passwordField.type = "password";
            }
        }
    </script>

</body>
</html>
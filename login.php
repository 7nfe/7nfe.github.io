<?php
// 1. بدء الجلسة والاتصال بقاعدة البيانات
session_start();
include("config.php"); 

$error_message = "";

// 2. معالجة بيانات نموذج تسجيل الدخول
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username'] ?? ''); // يمثل رقم المكلف
    $password = trim($_POST['password'] ?? '');

    if (!empty($username) && !empty($password)) {
        
        // التعديل: البحث في جدول taxpayers باستخدام رقم المكلف
        $stmt = $conn->prepare("SELECT taxpayer_id, password FROM taxpayers WHERE taxpayer_id = ?");
        $stmt->bind_param("i", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            
            // التحقق من كلمة المرور المشفرة (password_verify) 
            // مع دعم النص المجرد فقط إذا كنت قد أدخلت بيانات يدوية قديمة للتجربة
            if (password_verify($password, $row['password']) || $password === $row['password']) {
                
                $_SESSION['taxpayer_id'] = $row['taxpayer_id'];
                header("Location: mains.php");
                exit();
            } else {
                $error_message = "كلمة المرور غير صحيحة.";
            }
        } else {
            $error_message = "رقم المكلف غير موجود في النظام.";
        }
        $stmt->close();
    } else {
        $error_message = "يرجى إدخال رقم المكلف وكلمة المرور.";
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
            text-align: right;
            width: 100%;
            line-height: 1.5;
            font-size: 14px;
        }

        .login-container {
            flex-grow: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
        }

        .login-box {
            background-color: #ffffff;
            width: 90%;
            max-width: 400px;
            padding: 40px 30px;
            border: 1px solid #ccc;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .login-title {
            color: #4873c4;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 40px;
        }

        .input-group {
            margin-bottom: 30px;
            text-align: right;
        }

        .input-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
        }

        .input-group input {
            width: 100%;
            border: none;
            border-bottom: 1px solid #777;
            padding: 8px 0;
            font-size: 16px;
            outline: none;
            background-color: transparent;
            transition: border-color 0.3s;
        }

        .input-group input:focus {
            border-bottom: 2px solid #4873c4;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 10px;
            margin-bottom: 30px;
            font-size: 14px;
        }

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

        .error-message {
            background-color: #ffebee;
            color: #c62828;
            padding: 10px;
            margin-bottom: 20px;
            font-size: 14px;
            border-right: 5px solid #c62828;
            text-align: right;
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

            <?php if (!empty($error_message)): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                
                <div class="input-group">
                    <label>اسم المستخدم (رقم المكلف)</label>
                    <input type="text" name="username" required autocomplete="username">
                </div>

                <div class="input-group">
                    <label>الرمز (كلمة المرور)</label>
                    <input type="password" name="password" id="passwordInput" required autocomplete="current-password">
                </div>

                <div class="checkbox-group">
                    <input type="checkbox" id="showPassword" onclick="togglePassword()">
                    <label for="showPassword" style="cursor: pointer;">إظهار الرمز</label>
                </div>

                <button type="submit" class="btn-login">دخول</button>
                
                <div style="margin-top: 20px; font-size: 14px;">
                    <a href="registration.php" style="color: #4873c4; text-decoration: none;">ليس لديك حساب؟ سجل الآن</a>
                </div>
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
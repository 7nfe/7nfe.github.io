<?php
session_start();
include("config.php"); 

// التأكد من أن المستخدم مسجل دخول
if (!isset($_SESSION['taxpayer_id'])) {
    header("Location: login.php");
    exit();
}

$message = "";
$taxpayer_id = $_SESSION['taxpayer_id'];

// جلب اسم المكلف من القاعدة (اختياري للعرض فقط)
$stmt_user = $conn->prepare("SELECT taxpayer_name FROM taxpayers WHERE taxpayer_id = ?");
$stmt_user->bind_param("s", $taxpayer_id);
$stmt_user->execute();
$user_res = $stmt_user->get_result()->fetch_assoc();
$fixed_tax_name = $user_res['taxpayer_name'] ?? "مكلف غير معروف";

// معالجة إرسال النموذج وحفظه في الداتا بيس
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. استقبال البيانات من النموذج
    $dec_type = $_POST['dec_type'];
    $period = $_POST['period'];
    $balance = floatval($_POST['balance']);
    $sales_7 = floatval($_POST['sales_7']);
    $tax_subject = $sales_7 * 0.07;
    $adj_reg = floatval($_POST['adj_reg']);
    $adj_dep = floatval($_POST['adj_dep']);
    
    // 2. الحسابات النهائية
    $result = $tax_subject - ($balance + $adj_reg) + $adj_dep;
    $tax_due_pos = ($result > 0) ? round($result, 3) : 0;
    $tax_due_neg = ($result < 0) ? round(abs($result), 3) : 0;
    $status = "Submitted"; // حالة الإقرار

    // 3. استعلام الإدخال (INSERT) بناءً على الحقول التي زودتني بها
    $sql = "INSERT INTO  tax_declaration  (
                taxpayer_id, 
                declaration_type, 
                period, 
                balance_previous_period, 
                sales_percent, 
                tax_on_percent, 
                amend_for_registration, 
                amend_for_department, 
                positive_tax_due, 
                negative_tax_due, 
                declaration_status
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssddddddds", 
        $taxpayer_id, 
        $dec_type, 
        $period,
        $balance, 
        $sales_7, 
        $tax_subject, 
        $adj_reg, 
        $adj_dep, 
        $tax_due_pos, 
        $tax_due_neg, 
        $status
    );

    if ($stmt->execute()) {
        $message = "<div class='alert success'>تم إرسال الإقرار وحفظه بنجاح برقم: " . $stmt->insert_id . "</div>";
    } else {
        $message = "<div class='alert' style='background:#f8d7da; color:#721c24; padding:15px;'>خطأ في الحفظ: " . $conn->error . "</div>";
    }
    $stmt->close();
}
?>
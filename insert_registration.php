<?php
include("config.php");

// استلام البيانات من النموذج
$taxpayer_name = $_POST['taxpayer_name'];
$taxpayer_number = $_POST['taxpayer_number'];
$phone = $_POST['phone'];
$legal_entity_type = $_POST['legal_entity_type'];
$directorate = $_POST['directorate'];
$email = $_POST['email'];
$trade_name = $_POST['trade_name'];
$commercial_record = $_POST['commercial_record'];
$creation_date = $_POST['creation_date'];
$address = $_POST['address'];
$facility_national_id = $_POST['facility_national_id'];
$business_nature = $_POST['business_nature'];
$registration_type = "تسجيل جديد"; // نوع الطلب ثابت في هذه الحالة
$password = password_hash($_POST['password'], PASSWORD_DEFAULT); // تأكد من وجود حقل كلمة المرور في النموذج

// جملة الـ SQL المعدلة بأسماء الأعمدة المطابقة لقاعدة البيانات (جدول taxpayers)
$sql = "INSERT INTO taxpayers (
    taxpayer_id, 
    taxpayer_name, 
    phone_number, 
    taxpayer_classification, 
    designated_directorate, 
    email, 
    trade_name, 
    registration_number, 
    date_of_establishment, 
    address, 
       registration_type,
    national_establishment_number, 
    nature_of_activity,
    password
) VALUES (
    '$taxpayer_number', 
    '$taxpayer_name', 
    '$phone',
    '$legal_entity_type',
    '$directorate',
    '$email',
    '$trade_name',
    '$commercial_record',
    '$creation_date',
    '$address',
    '$registration_type',
    '$facility_national_id',
    '$business_nature',
    '$password'
)";

$result = mysqli_query($conn, $sql);

if ($result) {
    echo "Successful<br>";
    echo "<a href='Registration.php'>Back to main page</a>";
} else {
    echo "ERROR: " . mysqli_error($conn);
}

// close connection
mysqli_close($conn);
?>
<?php

require '/Applications/XAMPP/xamppfiles/htdocs/IOT-Project/connect.php';
if (isset($_POST['btn-reg'])) {
    $fullname = $_POST['fullname'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    if (!empty($fullname) && !empty($username) && !empty($password) && !empty($email)) {
        //insert dữ liệu + Mã hoá dữ liệu md5
        $sql = "INSERT INTO `user` (`fullname`, `username`, `password`,`email`)
        VALUES('$fullname', '$username', md5('$password'), '$email' )";

        //Kiểm tra 
        if ($conn->query($sql) === TRUE) {
            echo "Bạn đã đăng ký thành công.!";
        }else {
            echo "Lỗi {$sql}".$conn->error;
        }

    } else {
        echo'Bạn cần nhập đầy đủ thông tin để đăng ký!';
    }
    
}
?>
<br>
<a href ="login.php"> Quay lại trang đăng nhập </a>
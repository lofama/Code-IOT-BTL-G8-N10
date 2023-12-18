<?php
require '/Applications/XAMPP/xamppfiles/htdocs/IOT-Project/connect.php'; // Kết nối đến cơ sở dữ liệu

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $device = $_POST['device'];  // Thiết bị
    $action = $_POST['action'];  // Hành động (ON hoặc OFF)

    // Thực hiện truy vấn SQL để lưu dữ liệu vào cơ sở dữ liệu
    $query = "INSERT INTO activity_history (device, action) VALUES ('$device', '$action')";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        echo "Lỗi khi thêm dữ liệu: " . mysqli_error($conn);
    }
} else {
    echo "Phương thức không hợp lệ";
}

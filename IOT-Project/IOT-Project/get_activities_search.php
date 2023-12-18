<?php
require '/Applications/XAMPP/xamppfiles/htdocs/IOT-Project/connect.php';

// Kết quả mảng JSON
$activities = [];

try {
    $searchTerm = $_GET['searchTerm']; // Lấy từ khóa tìm kiếm từ Ajax

    // Sử dụng prepared statement để tránh SQL injection
    $stmt = $conn->prepare("SELECT date, device, action FROM activity_history WHERE  device LIKE ?");
    $likeParam = "%{$searchTerm}%";
    $stmt->bind_param('s', $likeParam);
    $stmt->execute();

    $result = $stmt->get_result();

    // Chuyển đổi kết quả thành mảng
    while ($row = $result->fetch_assoc()) {
        $activities[] = $row;
    }

    // Đóng prepared statement
    $stmt->close();
} catch (Exception $e) {
    // Xử lý lỗi nếu có
    echo json_encode(['error' => 'Error fetching activities']);
    exit();
}

// Trả về dữ liệu dưới dạng JSON
echo json_encode($activities);

// Đóng kết nối cơ sở dữ liệu
$conn->close();

?>
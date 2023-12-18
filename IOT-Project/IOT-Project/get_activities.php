<?php
require '/Applications/XAMPP/xamppfiles/htdocs/IOT-Project/connect.php';

$selectedTimeRange = $_GET['timeRange']; // Lấy giá trị thời gian từ request


// Kết quả mảng JSON
$activities = [];

if ($selectedTimeRange === 'today'){
    try {
        // Sử dụng prepared statement để tránh SQL injection
        $stmt = $conn->prepare("SELECT date, device, action FROM activity_history WHERE DATE(date) = CURDATE()");
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
}
if ($selectedTimeRange === 'yesterday'){
    try {
        // Sử dụng prepared statement để tránh SQL injection
        $stmt = $conn->prepare("SELECT date, device, action FROM activity_history WHERE DATE(date) = CURDATE()- INTERVAL 1 DAY");
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
}
if ($selectedTimeRange === 'week'){
    try {
        // Sử dụng prepared statement để tránh SQL injection
        $stmt = $conn->prepare("SELECT date, device, action FROM activity_history WHERE YEARWEEK(date) = YEARWEEK(NOW())");
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
}
if ($selectedTimeRange === 'month'){
    try {
        // Sử dụng prepared statement để tránh SQL injection
        $stmt = $conn->prepare("SELECT date, device, action FROM activity_history WHERE MONTH(date) = MONTH(NOW())");
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
}
if ($selectedTimeRange === 'all'){
    try {
        // Sử dụng prepared statement để tránh SQL injection
        $stmt = $conn->prepare("SELECT date, device, action FROM activity_history ");
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
}

?>

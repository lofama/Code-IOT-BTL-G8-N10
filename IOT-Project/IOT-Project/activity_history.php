<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <title>SMART HOME FOR MINH</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="/Users/mac/Desktop/Web server/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body {
            background-color: #f8f9fa;
        }

        .btn-group button {
            margin-right: 10px;
        }

        #activityList {
            list-style-type: none;
            padding: 0;
        }

        .activity-item {
            background-color: #fff;
            margin-bottom: 15px;
            padding: 15px;
            border-radius: 5px ;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .activity-item h5 {
            color: #007bff;
        }

        .activity-item p {
            color: #6c757d;
        }
    </style>
</head>

<body>
    <div class="container-fluid mt-3">
        <div class="row justify-content-center">
            <div class="col-md-6 table-container text-center">
                <!-- Biểu đồ Nhiệt độ và Độ ẩm -->
                <ul class="nav nav-pills " role="tablist">
                    <li class="nav-item m-3">
                        <a class="nav-link " data-bs-toggle="pill" href="http://localhost/IOT-Project/home.php">Home</a>
                    </li>
                    <li class="nav-item m-3">
                        <a class="nav-link" data-bs-toggle="pill" href="http://localhost/IOT-Project/chart.php">Biểu đồ nhiệt độ & độ ẩm</a>
                    </li>
                    <li class="nav-item m-3">
                        <a class="nav-link active" data-bs-toggle="pill" href="http://localhost/IOT-Project/activity_history.php">Lịch sử hoạt động</a>
                    </li>
                </ul>
                <div id="home" class="container tab-pane"><br></div>
                <div id="menu1" class="container tab-pane "><br></div>
                <div id="menu2" class="container tab-pane active"><br></div>
                <div>
                    <h1 class="font-weight-bold display-4">Lịch sử hoạt động</h1>
                    <!-- Nút chọn thời gian -->
                    <div class="d-flex justify-content-end align-items-center">
                        <form class="form-inline">
                            <div class="input-group ">
                                <input class="form-control" type="search" placeholder="Tìm kiếm thiết bị..." aria-label="Search" id="searchInput">
                                <button class="btn btn-outline-success" type="button" onclick="searchDevice()">Tìm kiếm</button>
                            </div>
                            <div class="btn-group ml-2 custom-select-lg" role="group" aria-label="Time Range">
                                <select class="form-select" onchange="showActivity(this.value)">
                                    <option value="today">Hôm nay</option>
                                    <option value="yesterday">Hôm qua</option>
                                    <option value="week">Tuần này</option>
                                    <option value="month">Tháng này</option>
                                    <option value="all">Tất cả</option>
                                </select>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Danh sách hoạt động -->
                        <ul id="activityList"></ul>
                    <table id="activityTable" class ="table table-bordered text-center ">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Device</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="https://unpkg.com/mqtt/dist/mqtt.min.js"></script>
    <script>
        function showActivity(timeRange) {
            // Sử dụng Ajax để gửi yêu cầu đến file PHP
            $.ajax({
                url: 'get_activities.php',
                method: 'GET',
                data: { timeRange: timeRange },
                success: function (data) {
                    // Chuyển đổi dữ liệu JSON thành đối tượng JavaScript
                    const activities = JSON.parse(data);

                    // Hiển thị dữ liệu trên trang web
                    displayActivities(activities);
                },
                error: function () {
                    console.log('Lỗi khi gửi yêu cầu đến server.');
                }
            });
        }
        // Hàm displayActivities được cập nhật
        function displayActivities(activities) {
            const activityTableBody = document.querySelector('#activityTable tbody');
            activityTableBody.innerHTML = ''; // Xóa nội dung cũ

            // Kiểm tra xem activities có phải là mảng hay không
            if (Array.isArray(activities)) {
                // Hiển thị danh sách hoạt động trong bảng
                activities.forEach(activity => {
                    const activityRow = document.createElement('tr');
                    activityRow.innerHTML = `
                        <td>${activity.date}</td>
                        <td>${activity.device}</td>
                        <td>${activity.action}</td>
                    `;
                    activityTableBody.appendChild(activityRow);
                });
            } else {
                console.error('Dữ liệu trả về không phải là một mảng.');
            }
        }
        function searchDevice(){
            const searchTerm = document.getElementById('searchInput').value;
            // Sử dụng Ajax để gửi yêu cầu đến file PHP
            $.ajax({
                url: 'get_activities_search.php',
                method: 'GET',
                data: { searchTerm: searchTerm },
                success: function (data) {
                    // Chuyển đổi dữ liệu JSON thành đối tượng JavaScript
                    const activities = JSON.parse(data);

                    // Hiển thị dữ liệu trên trang web
                    displayActivities(activities);
                },
                error: function () {
                    console.log('Lỗi khi gửi yêu cầu đến server.');
                }
            });
        }


    </script>
</body>
</html>

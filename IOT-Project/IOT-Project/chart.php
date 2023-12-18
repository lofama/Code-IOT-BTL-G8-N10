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
                        <a class="nav-link active" data-bs-toggle="pill" href="http://localhost/IOT-Project/chart.php">Biểu đồ nhiệt độ & độ ẩm</a>
                    </li>
                    <li class="nav-item m-3">
                        <a class="nav-link" data-bs-toggle="pill" href="http://localhost/IOT-Project/activity_history.php">Lịch sử hoạt động</a>
                    </li>
                </ul>
                <div id="home" class="container tab-pane"><br></div>
                <div id="menu1" class="container tab-pane active"><br></div>
                <div id="menu2" class="container tab-pane fade"><br></div>
                <div>
                    <h1 class="font-weight-bold display-4">Biểu đồ nhiệt độ ẩm trong tuần vừa qua</h1>
                    <canvas id="temperatureChart" width="400" height="200"></canvas>
                    <canvas id="humidityChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="https://unpkg.com/mqtt/dist/mqtt.min.js"></script>
    <script>
        
        document.addEventListener('DOMContentLoaded', function () {
            // Khai báo biến biểu đồ
            var temperatureChart, humidityChart;

            // Vẽ biểu đồ nhiệt độ
            var tempChartCtx = document.getElementById('temperatureChart').getContext('2d');
            temperatureChart = new Chart(tempChartCtx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Nhiệt độ (°C)',
                        data: [],
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: false
                        }
                    }
                }
            });

            // Vẽ biểu đồ độ ẩm
            var humidityChartCtx = document.getElementById('humidityChart').getContext('2d');
            humidityChart = new Chart(humidityChartCtx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Độ ẩm (%)',
                        data: [],
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: false
                        }
                    }
                }
            });

            const clientId = 'mqttjs_' + Math.random().toString(16).substr(2, 8)
            const host = 'ws://broker.hivemq.com:8000/mqtt'
            const options = {
                keepalive: 60,
                clientId: clientId,
                protocolId: 'MQTT',
                protocolVersion: 4,
                clean: true,
                reconnectPeriod: 1000,
                connectTimeout: 30 * 1000,
                will: {
                    topic: 'WillMsg',
                    payload: 'Connection Closed abnormally..!',
                    qos: 0,
                    retain: false
                },
            }

            const client = mqtt.connect(host, options);

            client.on('error', (err) => {
                console.log('Connection error: ', err);
                client.end();
            });

            client.on('reconnect', () => {
                console.log('Reconnecting...');
            });

            client.on('connect', () => {
                console.log(`Client connected: ${clientId}`);
                client.subscribe('/IOT_G8/p/temp', { qos: 0 });
                client.subscribe('/IOT_G8/p/hum', { qos: 0 });
            });

            client.on('message', (topic, message, packet) => {
                console.log(`Received Message: ${message.toString()} On topic: ${topic}`);
                
                // Xử lý dữ liệu nhận được tùy thuộc vào topic
                if (topic == '/IOT_G8/p/temp') {
                    // Cập nhật biểu đồ nhiệt độ
                    updateTemperatureChart(message.toString());
                    saveDataToLocalStorage('temperatureData', message.toString());
                }
                if (topic == '/IOT_G8/p/hum') {
                    // Cập nhật biểu đồ độ ẩm
                    updateHumidityChart(message.toString());
                    saveDataToLocalStorage('humidityData', message.toString());
                }
            });

            // Hàm cập nhật biểu đồ nhiệt độ
            function updateTemperatureChart(value) {
                // Cập nhật giá trị nhiệt độ trong biểu đồ
                temperatureChart.data.labels.push('');
                temperatureChart.data.datasets[0].data.push(parseFloat(value));
                temperatureChart.update();
            }

            // Hàm cập nhật biểu đồ độ ẩm
            function updateHumidityChart(value) {
                // Cập nhật giá trị độ ẩm trong biểu đồ
                humidityChart.data.labels.push('');
                humidityChart.data.datasets[0].data.push(parseFloat(value));
                humidityChart.update();
            }
            // Hàm lưu dữ liệu vào localStorage
            function saveDataToLocalStorage(key, value) {
                // Lấy dữ liệu hiện tại từ localStorage
                var data = JSON.parse(localStorage.getItem(key)) || [];

                // Thêm giá trị mới vào mảng
                data.push(value);

                // Lưu mảng mới vào localStorage
                localStorage.setItem(key, JSON.stringify(data));
            }

            // Hàm khởi tạo dữ liệu từ localStorage khi trang được load
            function initializeChartData() {
                // Lấy dữ liệu nhiệt độ từ localStorage
                var temperatureData = JSON.parse(localStorage.getItem('temperatureData')) || [];

                // Lấy dữ liệu độ ẩm từ localStorage
                var humidityData = JSON.parse(localStorage.getItem('humidityData')) || [];

                // Cập nhật biểu đồ nhiệt độ với dữ liệu đã lưu
                temperatureChart.data.labels = Array.from({ length: temperatureData.length }, (_, i) => '');
                temperatureChart.data.datasets[0].data = temperatureData.map(Number);
                temperatureChart.update();

                // Cập nhật biểu đồ độ ẩm với dữ liệu đã lưu
                humidityChart.data.labels = Array.from({ length: humidityData.length }, (_, i) => '');
                humidityChart.data.datasets[0].data = humidityData.map(Number);
                humidityChart.update();
            }

            // Gọi hàm khởi tạo dữ liệu khi trang được load
            initializeChartData();
        });
    </script>
</body>
</html>

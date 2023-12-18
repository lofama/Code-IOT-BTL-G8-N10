<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
  <title>SMART HOME FOR MINH</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
</head>
<body> 
    <div class="container-fluid mt-3">
        <div class="row justify-content-center">
            <div class="col-md-6 table-container text-center">
            <ul class="nav nav-pills" role="tablist">
                <li class="nav-item m-3">
                    <a class="nav-link active " data-bs-toggle="pill" href="http://localhost/IOT-Project/home.php" id="homeTab">Home</a>
                </li> 
                <li class="nav-item m-3">
                    <a class="nav-link" data-bs-toggle="pill" href="http://localhost/IOT-Project/chart.php" id="chartTab">Biểu đồ nhiệt độ và độ ẩm</a>
                </li>
                <li class="nav-item m-3">
                    <a class="nav-link" data-bs-toggle="pill" href="http://localhost/IOT-Project/activity_history.php" id = "historyTab">Lịch sử hoạt động</a>
                </li>
            </ul>
            <div id="home" class="container tab-pane active"><br>
                <table class ="table table-bordered text-center ">
                        <tr >
                            <td  colspan="3">
                                <span class ="font-weight-bold display-5" >THÔNG SỐ MÔI TRƯỜNG</span> 

                                <div id="sensor-data" class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p > 
                                            <i class="fa-solid fa-temperature-low"></i> Nhiệt độ: <span id="temp">-</span>
                                        </p>
                                        <p>
                                           <i class="fa-solid fa-droplet"></i> Độ ẩm: <span id="hum">-</span>
                                        </p>
                                    </div>
                                    <div>
                                      <p>
                                        <i class="fa-solid fa-cloud-sun-rain"></i></i> Thời tiết: <span id="weather">-</span>
                                        </p>
                                      <p style="color:green" id = "style_warning">
                                        <i class="fa-solid fa-triangle-exclamation"></i> Cảnh báo: <span id="warning" >-</span>
                                        </p>
                                    </div>
                                  </div>
                            </td>
                        </tr >
                        <tbody class="table-dark" >
                            <tr >
                                <td colspan="3">
                                    BẢNG ĐIỀU KHIỂN
                                   
                                </td>
                                
                            </tr>   
                            <tr >
                                <td >THIẾT BỊ</td>
                                <td >ĐIỀU KHIỂN</td>
                                <td >TRẠNG THÁI</td>
                            </tr>
                        </tbody>
                        <tr>
                            <td>Đèn phòng khách</td>
                            <td >
                                <div class="col-12 d-flex justify-content-around">
                                    <button id = "livingRoomOn" type="button" class="btn btn-outline-success ">ON</button>
                                    <button id = "livingRoomOff" type="button" class="btn btn-outline-danger">OFF</button>
                                </div>
                            </td>
                            <td>
                                <p> <span id="status_1">-</span></p>
                            </td>
                        </tr> 
                        <tr>
                            <td>Đèn phòng bếp</td>
                            <td >
                                <div class="col-12 d-flex justify-content-around">
                                    <button id = "kitchenOn" type="button" class="btn btn-outline-success " >ON</button>
                                    <button id = "kitchenOff" type="button" class="btn btn-outline-danger" >OFF</button>
                                </div>
                            </td>
                            <td>
                                <p> <span id="status_2">-</span></p>
                            </td>
                        </tr> 
                        <tr>
                            <td>Bật quạt</td>
                            <td >
                                <div class="col-12 d-flex justify-content-around">
                                    <button id = "fanOn" type="button" class="btn btn-outline-success ">ON</button>
                                    <button id = "fanOff" type="button" class="btn btn-outline-danger" >OFF</button>
                                </div>
                            </td>
                            <td>
                                <p> <span id="status_3">-</span></p>
                            </td>
                        </tr> 
                        <tr>
                            <td>Bật còi báo động</td>
                            <td >
                                <div class="col-12 d-flex justify-content-around">
                                    <button id = "hornOn" type="button" class="btn btn-outline-success ">ON</button>
                                    <button id = "hornOff" type="button" class="btn btn-outline-danger">OFF</button>
                                </div>
                            </td>
                            <td>
                                <p> <span id="status_4">-</span></p>
                            </td>
                        </tr> 
                </table>
            </div>
            <div id="menu1" class="container tab-pane fade"><br>
                
            </div>
            <div id="menu2" class="container tab-pane fade"><br>
                
            </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src='https://unpkg.com/mqtt/dist/mqtt.min.js'></script>
    <script>
            let reloadStatus = false;
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
                console.log('Connecting mqtt client')
                const client = mqtt.connect(host, options)
                client.on('error', (err) => {
                    console.log('Connection error: ', err)
                    client.end()
                })
                client.on('reconnect', () => {
                    console.log('Reconnecting...')
                })
                client.on('connect', () => {
                    console.log(`Client connected: ${clientId}`)
                    // Subscribe
                    client.subscribe('/IOT_G8/p/temp', { qos: 0 })
                    client.subscribe('/IOT_G8/p/hum', { qos: 0 })
                    client.subscribe('topic/sensor/weather', { qos: 0 })
                    client.subscribe('topic/sensor/buzzer', { qos: 0 })
                    client.subscribe('topic/sensor/led', { qos: 0 })
                    client.subscribe('topic/sensor/fan', { qos: 0 })

                })
                // Publish
                // Receive
                client.on('message', (topic, message, packet) => {
                    console.log(`Received Message: ${message.toString()} On topic: ${topic}`)
                    if (topic == '/IOT_G8/p/temp') {
                        document.getElementById('temp').innerHTML = message.toString();
                    }
                    if (topic == '/IOT_G8/p/hum') {
                        document.getElementById('hum').innerHTML = message.toString();
                    }
                    if (topic == 'topic/sensor/weather') {
                        document.getElementById('weather').innerHTML = message.toString();
                    }
                    if (topic == '/topic/sensor/buzzer' && message.toString() === "ON_HORN") {
                        document.getElementById('warning').innerHTML ="Đang rò rỉ khí GAS";
                    }
                    if (topic === 'topic/sensor/led' && message.toString() === "ON_LI") {
                        document.getElementById('status_1').innerHTML ="LED ON";
                        
                    }
                    if (topic === 'topic/sensor/led' && message.toString() === "OFF_LI") {
                        document.getElementById('status_1').innerHTML ="LED OFF";
                    }
                    if (topic === 'topic/sensor/led' && message.toString() === "ON_KIT") {
                        document.getElementById('status_2').innerHTML ="LED ON";
                    }
                    if (topic === 'topic/sensor/led' && message.toString() === "OFF_KIT") {
                        document.getElementById('status_2').innerHTML ="LED OFF";
                    }
                    if (topic === 'topic/sensor/fan' && message.toString() === "ON_FAN") {
                        document.getElementById('status_3').innerHTML ="FAN ON";
                    }
                    if (topic === 'topic/sensor/fan' && message.toString() === "OFF_FAN") {
                        document.getElementById('status_3').innerHTML ="FAN OFF";
                    }
                    if (topic === 'topic/sensor/buzzer' && message.toString() === "ON_HORN") {
                        document.getElementById('status_4').innerHTML ="HORN ON";
                        document.getElementById('style_warning').style.color = 'red';
                        document.getElementById('warning').innerHTML ="Đang rò rỉ khí GAS";
                    }
                    if (topic === 'topic/sensor/buzzer' && message.toString() === "OFF_HORN") {
                        document.getElementById('status_4').innerHTML ="HORN OFF";
                        document.getElementById('style_warning').style.color = 'green';
                        document.getElementById('warning').innerHTML ="Bình thường";
                    }

                })

                document.getElementById('livingRoomOn').addEventListener('click', button_livingOn_pressed);
                document.getElementById('livingRoomOff').addEventListener('click', button_livingOff_pressed);
                document.getElementById('kitchenOn').addEventListener('click', button_kichenOn_pressed);
                document.getElementById('kitchenOff').addEventListener('click', button_kichenOff_pressed);
                document.getElementById('fanOn').addEventListener('click', button_fanOn_pressed);
                document.getElementById('fanOff').addEventListener('click', button_fanOff_pressed);
                document.getElementById('hornOn').addEventListener('click', button_hornOn_pressed);
                document.getElementById('hornOff').addEventListener('click', button_hornOff_pressed);
                
                function button_livingOn_pressed() { 
                    //Publish command to turn on led 
                    client.publish('topic/web/led', 'ON_LI', { qos: 0, retain: false });
                    if(reloadStatus){
                        saveDataToDatabase('Đèn phòng khách', 'Bật');
                    }
                    updateDeviceState("livingRoomOn", "ON") 
                }
                function button_livingOff_pressed() {
                    //Publish command to turn off led 
                    client.publish('topic/web/led', 'OFF_LI', { qos: 0, retain: false });
                    
                    if(reloadStatus){
                        saveDataToDatabase('Đèn phòng khách', 'Tắt');
                    }
                    
                    updateDeviceState("livingRoomOff", "OFF")
                }
                
                function button_kichenOn_pressed(){
                    client.publish('topic/web/led', 'ON_KIT', { qos: 0, retain: false });
                
                    if(reloadStatus){
                        saveDataToDatabase('Đèn phòng bếp', 'Bật');
                    }
                    
                    updateDeviceState("kitchenOn", "ON")
                }
                function button_kichenOff_pressed(){
                    client.publish('topic/web/led', 'OFF_KIT', { qos: 0, retain: false });
                    
                    if(reloadStatus){
                        saveDataToDatabase('Đèn phòng bếp', 'Tắt');
                    }
                    
                    updateDeviceState("kitchenOff", "OFF")
                }
                function button_fanOn_pressed(){
                    client.publish('topic/web/fan', 'ON_FAN', { qos: 0, retain: false });
                    
                    if(reloadStatus){
                        saveDataToDatabase('Quạt', 'Bật');
                    }
                    
                    updateDeviceState("fanOn", "ON")

                }
                function button_fanOff_pressed(){
                    client.publish('topic/web/fan', 'OFF_FAN', { qos: 0, retain: false });
                    
                    if(reloadStatus){
                        saveDataToDatabase('Quạt', 'Tắt');
                    }
                    updateDeviceState("fanOff", "OFF")

                }
                function button_hornOn_pressed(){
                    client.publish('topic/web/buzzer', 'ON_HORN', { qos: 0, retain: false });
                    
                    if(reloadStatus){
                        saveDataToDatabase('Cảnh báo', 'Bật');
                    }
                    
                    updateDeviceState("hornOn", "ON")
                }
                function button_hornOff_pressed(){
                    client.publish('topic/web/buzzer', 'OFF_HORN', { qos: 0, retain: false });
                    
                    if(reloadStatus){
                        saveDataToDatabase('Cảnh báo', 'Tắt');
                    }
                    
                    updateDeviceState("hornOff", "OFF")
                }
                // Insert dữ liệu vào db
                function saveDataToDatabase(device, action) {
                    // Sử dụng Fetch API để gửi dữ liệu POST đến file save_data.php
                    
                        fetch('save_state.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: new URLSearchParams({
                            'device': device,
                            'action': action,
                        }),
                        })
                    .then(response => response.text())
                    .then(data => {
                        console.log(data);
                        
                        })
                    .catch(error => {
                        console.error('Lỗi:', error);
                        });
                    }
                
                    
                function updateDeviceState(device, state) {
                    // Lấy trạng thái đã lưu (nếu có)
                    const storedState = JSON.parse(localStorage.getItem('deviceStates')) || {};

                    // Cập nhật trạng thái của thiết bị
                    storedState[device] = state;

                    // Lưu trạng thái mới vào localStorage
                    localStorage.setItem('deviceStates', JSON.stringify(storedState));
                    
                }
                document.addEventListener('DOMContentLoaded', function () {
                    // Lấy trạng thái đã lưu (nếu có)
                    const storedState = JSON.parse(localStorage.getItem('deviceStates')) || {};

                    // Kiểm tra và khôi phục trạng thái cho từng thiết bị
                    if (storedState['livingRoomOn'] === 'ON') {
                        document.getElementById('livingRoomOn').click();
                        
                    }
                    if (storedState['livingRoomOff'] === 'OFF') {
                         document.getElementById('livingRoomOff').click();
                    }
                    if (storedState['kitchenOn'] === 'ON'){
                        document.getElementById('kitchenOn').click();
                    } 
                    if (storedState['kitchenOff'] === 'OFF'){
                        document.getElementById('kitchenOff').click();
                    }
                    if (storedState['fanOn'] === 'ON'){
                        document.getElementById('fanOn').click();
                    } 
                    if (storedState['fanOff'] === 'OFF'){
                        document.getElementById('fanOff').click();
                    }
                    
                    if (storedState['hornOn'] === 'ON'){
                        document.getElementById('hornOn').click();
                    } 
                    if (storedState['hornOff'] === 'OFF'){
                        document.getElementById('hornOff').click();
                    }
                    reloadStatus = true;
                });
                
                // Bắt sự kiện trước khi trang được tải lại
                window.addEventListener('beforeunload', function (event) {
                    // Đặt biến reloadStatus thành true khi nút reload được bấm
                    if (reloadStatus) {
                        console.log('Nút reload trình duyệt đã được bấm.');
                        // Thực hiện các hành động sau khi nút reload được bấm ở đây
                    }
                });
                // Bắt sự kiện keydown để kiểm tra khi nào nút reload trên trình duyệt được bấm
                document.addEventListener('keydown', function (event) {
                    // Kiểm tra xem phím có phải là F5 hoặc Ctrl+R (đối với Windows) hoặc Command+R (đối với MacOS) không
                    if (event.key === 'F5' || (event.key === 'r' && (event.ctrlKey || event.metaKey))) {
                        // Đặt biến reloadStatus thành true khi nút reload trên trình duyệt được bấm
                        reloadStatus = true;
                    }
                
                });

    </script>
    

</body>
</html>
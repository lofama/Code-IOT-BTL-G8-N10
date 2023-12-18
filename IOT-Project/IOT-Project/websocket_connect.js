// Import các module và thư viện cần thiết
const express = require('express');
const http = require('http');
const socketIO = require('socket.io');
const mqtt = require('mqtt');

// Tạo một ứng dụng Express
const app = express();

// Tạo một server HTTP từ ứng dụng Express
const server = http.createServer(app);

// Tạo một WebSocket server từ server HTTP
const io = socketIO(server);

// Kết nối với MQTT Broker (HiveMQ)
const mqttClient = mqtt.connect('mqtt://broker.hivemq.com');

// Định nghĩa route cho trang chủ
app.get('/', (req, res) => {
    res.sendFile(__dirname + '/home.php');
});

// Xử lý sự kiện khi một client kết nối tới WebSocket
io.on('connection', (socket) => {
    console.log('Client connected to WebSocket');

    // Xử lý khi nhận được tin nhắn từ WebSocket
    socket.on('message', (message) => {
        console.log(`Received message from WebSocket: ${message}`);
        // Gửi tin nhắn đến MQTT Broker với chủ đề 'control-panel/status'
        mqttClient.publish('control-panel/status', message);
    });

    // Đăng ký lắng nghe cho chủ đề 'control-panel/status' từ MQTT Broker
    mqttClient.subscribe('control-panel/status');
    mqttClient.on('message', (topic, message) => {
        // Gửi tin nhắn trạng thái từ MQTT đến tất cả các client đang kết nối qua WebSocket
        io.emit('status', message.toString());
    });

    // Xử lý sự kiện khi client ngắt kết nối
    socket.on('disconnect', () => {
        console.log('Client disconnected from WebSocket');
    });
});

// Lắng nghe kết nối từ client trên cổng 3000
server.listen(3000, () => {
    console.log('Server is running on port 3000');
});

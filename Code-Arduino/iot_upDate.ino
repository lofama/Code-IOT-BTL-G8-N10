#include <WiFi.h>
#include <PubSubClient.h>  // Thư viện MQTT cho ESP32
#include <DHT.h>
#include <NTPClient.h>
#include <RTClib.h>
#include <ArduinoJson.h>

#include <WebServer.h>
#include <uri/UriBraces.h>
#include <HTTPClient.h>

// Thông tin mạng Wi-Fi
const char* ssid = "POCO F2 Pro";
const char* password = "12345689";
// Địa chỉ máy chủ MQTT (HiveMQ)
const char* mqttServer = "mqtt-dashboard.com";
const int mqttPort = 1883;
const char* mqttUser = "hivemq.webclient.1695722508989";
const char* mqttPassword = ".?opMS2,tx7OA%3nh1FJ";
String weatherDesc = "";
// Đối tượng để kết nối và lấy thời gian từ máy chủ NTP
WiFiUDP ntpUDP;
NTPClient timeClient(ntpUDP, "pool.ntp.org");


// Định nghĩa chân kết nối
const int DHT_TYPE = DHT11;  //Cảm biến nhiệt độ, độ ẩm
const int DHT_PIN = 12;
const int GAS_PIN = 34;  // Thay đổi chân GAS tại đây
const int FAN_PIN = 22;  // Quạt
const int LED_PIN = 23;
const int LED2_PIN = 33;  // Đèn
// Chân cảm biến ánh sáng (quang điện trở)
const int LIGHT_SENSOR_PIN = 32;
// Chân kết nối còi
const int buzzerPin = 26;

unsigned long previousMillis = 0;
const long buzzerInterval = 5000;  // Thời gian giữa các âm thanh còi (ms)

const int TUDONG = 0;
DHT dht(DHT_PIN, DHT_TYPE);
RTC_DS3231 rtc;
WiFiClient espClient;
PubSubClient client(espClient);

void setup() {
  // put your setup code here, to run once:
  Serial.begin(115200);
  // Kết nối WiFi
  connectWiFi();
  rtc.begin();
  client.setServer(mqttServer, mqttPort);
  client.setCallback(callback);
  while (!client.connected()) {
    String clientId = "ESP32Client-";
    clientId += String(random(0xffff), HEX);
    // Attempt to connect
    if (client.connect(clientId.c_str())) {
      Serial.println("Connected to MQTT server");
      client.subscribe("topic/sensor_data");
      client.subscribe("topic/web/led");
      client.subscribe("topic/web/fan");
      client.subscribe("topic/web/buzzer");
    } else {
      Serial.print("Failed to connect to MQTT server, rc=");
      Serial.println(client.state());
      delay(200);
    }
  }
  pinMode(GAS_PIN, INPUT);
  pinMode(buzzerPin, OUTPUT);
  pinMode(LIGHT_SENSOR_PIN, INPUT);
  pinMode(LED_PIN, OUTPUT);
  pinMode(LED2_PIN, OUTPUT);
  pinMode(FAN_PIN, OUTPUT);
  digitalWrite(LED_PIN, HIGH);  // Kích hoạt đèn bằng cách cấp HIGH
  delay(5000);                  // Giữ đèn kích hoạt trong 10 giây
  digitalWrite(LED_PIN, LOW);   // Tắt đèn bằng cách cấp LOW
    // Khởi tạo đối tượng NTPClient
  timeClient.begin();
  // Đặt múi giờ cho múi giờ Việt Nam
  timeClient.setTimeOffset(7 * 60 * 60);
  offFan();
}

void loop() {
  // Đảm bảo rằng kết nối với MQTT đang được duy trì
  if (!client.connected()) {
    connectMQTT();
  }

  // Lắng nghe các sự kiện MQTT
  client.loop();
  Serial.println(getWeatherData());
  // Đọc dữ liệu từ cảm biến và gửi lên HiveMQ
  float humidity = dht.readHumidity();
  float temperature = dht.readTemperature();
  Serial.print("Humidity: ");
  Serial.println(humidity);
  int lightValue = analogRead(LIGHT_SENSOR_PIN);  // Đọc giá trị từ cảm biến khí gas MQ-2
  int gasValue = analogRead(GAS_PIN);
  String temperatureStr = (String)temperature;
  String humidityStr = (String)humidity;
  client.publish("/IOT_G8/p/temp", temperatureStr.c_str());
  client.publish("/IOT_G8/p/hum", humidityStr.c_str());
  if (lightValue > 2000 && TUDONG == 1) {
    Serial.println("Bật đèn");
    onLed();
  } else {
    if (lightValue < 2000 && TUDONG == 1) {
      Serial.println("Tắt đèn");
      offLed();
    }
  }
  Serial.print("Giá trị khí gas: ");
  Serial.println(gasValue);
  Serial.print("Light: ");
  Serial.println(lightValue);

  if (!isnan(humidity) && !isnan(temperature) && !isnan(lightValue)) {
    publishData(humidity, temperature, lightValue, gasValue);
  }
  if (gasValue > 2000) {
    onHorn();
    delay(5000);
  } else {
    offHorn();
  }
  delay(6000);  // Đợi 5 giây trước khi đọc lại dữ liệu từ cảm biến
}

void connectWiFi() {
  Serial.println("Connecting to WiFi...");
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(100);
    Serial.println("Connecting...");
  }
  Serial.println("Connected to WiFi");
}

void connectMQTT() {
  while (!client.connected()) {
    Serial.println("Connecting to MQTT...");
    String clientId = "ESP32Client-";
    clientId += String(random(0xffff), HEX);
    // Attempt to connect
    if (client.connect(clientId.c_str())) {
      Serial.println("Connected to " + clientId);
      // Đăng ký theo dõi topic "topic/sensor_data" khi kết nối lại MQTT
          if (client.publish("topic/sensor/weather", weatherDesc.c_str())) {
    Serial.println("Publish weather successful!");
  } else {
    Serial.println("Failed to publish. Please check your MQTT connection.");
  }
      client.subscribe("topic/sensor_data");
      client.subscribe("topic/web/led");
      client.subscribe("topic/web/fan");
      client.subscribe("topic/web/buzzer");
      Serial.println("Subscribed to topic 'topic'");
    } else {
      Serial.print("Failed, rc=");
      Serial.print(client.state());
      Serial.println(" Trying again in 5 seconds...");
      delay(500);
    }
  }
}
void callback(char* topic, byte* payload, unsigned int length) {
  Serial.println("Message received on topic: " + String(topic));
  String message = "";
  for (int i = 0; i < length; i++) {
    message += (char)payload[i];
  }

  if (strcmp(topic, "topic/sensor_data") == 0) {
    Serial.println("Payload: " + message);
    // Process the payload as needed
  }
  if (strcmp(topic, "topic/web/led") == 0) {
    if (message == "ON_LI") onLed2();
    if (message == "OFF_LI") offLed2();
    if (message == "ON_KIT") onLed();
    if (message == "OFF_KIT") offLed();
    Serial.println("LED Mess: " + message);
  }
  // Process the payload as needed
  if (strcmp(topic, "topic/web/fan") == 0) {
    if (message == "ON_FAN") onFan();
    if (message == "OFF_FAN") offFan();
    Serial.println("FAN Mess: " + message);
  }
  // Process the payload as needed
  if (strcmp(topic, "topic/web/buzzer") == 0) {
    if (message == "ON_HORN") onHorn();
    if (message == "OFF_HORN") offHorn();
    Serial.println("HORN Mess: " + message);
  }
}
void publishData(float humidity, float temperature, float lightValue, float gasValue) {
  // Tạo chuỗi JSON để gửi lên HiveMQ
  weatherDesc = getWeatherData();
  // Tạo chuỗi JSON để gửi lên
  DynamicJsonDocument doc(1024);  // 256 is the capacity in bytes
  doc["humidity"] = humidity;
  doc["temperature"] = temperature;
  doc["lightValue"] = lightValue;
  doc["gasValue"] = gasValue;
  doc["weatherDesc"] = weatherDesc;
  doc["time"] = getTime();
  String jsonPayload;
  serializeJson(doc, jsonPayload);
  // Gửi dữ liệu lên HiveMQ
  if (client.publish("topic/sensor_data", jsonPayload.c_str())) {
    Serial.println("Publish successful!");
  } else {
    Serial.println("Failed to publish. Please check your MQTT connection.");
  }
    if (client.publish("topic/sensor/weather", weatherDesc.c_str())) {
    Serial.println("Publish weather successful!");
  } else {
    Serial.println("Failed to publish. Please check your MQTT connection.");
  }
}
void onLed() {
  Serial.println("led kit on");
  digitalWrite(LED_PIN, HIGH);
  String mess = "ON_KIT";
  client.publish("topic/sensor/led", mess.c_str());
}
void offLed() {
  Serial.println("led kit off");
  digitalWrite(LED_PIN, LOW);
  String mess = "OFF_KIT";
  client.publish("topic/sensor/led", mess.c_str());
}
void onLed2() {
  Serial.println("led li on");
  digitalWrite(LED2_PIN, HIGH);
  String mess = "ON_LI";
  client.publish("topic/sensor/led", mess.c_str());
}
void offLed2() {
  Serial.println("led 2 off");
  digitalWrite(LED2_PIN, LOW);
  String mess = "OFF_LI";
  client.publish("topic/sensor/led", mess.c_str());
}
void onFan() {
  Serial.println("Fan on");
  digitalWrite(FAN_PIN, HIGH);
  String mess = "ON_FAN";
  client.publish("topic/sensor/fan", mess.c_str());
}
void offFan() {
  Serial.println("Fan off");
  digitalWrite(FAN_PIN, LOW);
  String mess = "OFF_FAN";
  client.publish("topic/sensor/fan", mess.c_str());
}
void onHorn() {
  digitalWrite(buzzerPin, HIGH);
  delay(5000);
  String mess = "ON_HORN";
  client.publish("topic/sensor/buzzer", mess.c_str());
}
void offHorn() {
  digitalWrite(buzzerPin, LOW);
  String mess = "OFF_HORN";
  client.publish("topic/sensor/buzzer", mess.c_str());
}
String getTime() {
  // Lấy thời gian từ máy chủ NTP
  timeClient.update();
  unsigned long epochTime = timeClient.getEpochTime();
  DateTime now = DateTime(epochTime);

  // Định dạng thời gian thành chuỗi "yyyy-MM-dd HH:mm:ss"
  char buffer[20];  // Đủ lớn để chứa chuỗi định dạng
  sprintf(buffer, "%04d-%02d-%02d %02d:%02d:%02d", now.year(), now.month(), now.day(), now.hour(), now.minute(), now.second());
  String nowTime = String(buffer);
  return nowTime;
}
String getWeatherData() {
  HTTPClient http;
  // Replace with your OpenWeather API endpoint and API key
  String url = "http://api.openweathermap.org/data/2.5/weather?q=Hanoi,VN&appid=a92c4ce1c8e7edea775a021700de0043&units=metric";
  http.begin(url);
  int httpCode = http.GET();
  String payload1 = "{}";
  if (httpCode > 0) {
    payload1 = http.getString();
  }

  http.end();
  String weatherData = payload1;
  StaticJsonDocument<1024> weatherDoc;
  DeserializationError error = deserializeJson(weatherDoc, weatherData);
  // Kiểm tra lỗi phân tích JSON
  // if (error) {
  //   Serial.print("Error parsing JSON: ");
  //   Serial.println(error.c_str());
  //   return error.c_str();
  // }

  String weatherDesc1 = weatherDoc["weather"][0]["description"];
  return weatherDesc1;
}
<?php
$host = "localhost";
$username = "root";
$pasword ="";
$dbname = "IOT-Project";
global $conn;
$conn = new mysqli($host, $username, $pasword, $dbname);
if ($conn->connect_error) {
    die("Kết nối không thành công". $conn->connect_error);

}
mysqli_select_db($conn, $dbname);
?>
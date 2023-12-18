 <?php
 include ("connect.php");
 global $conn;
 ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .login-container {
            margin-top: 5%;
            margin-bottom: 5%;
        }

        .login-form {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .login-header {
            text-align: center;
            color: #343a40;
        }

        .login-btn {
            background-color: #007bff;
            color: #ffffff;
            transition: background-color 0.3s;
        }

        .login-btn:hover {
            background-color: #0056b3;
        }

        .register-link {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="container login-container">
    <div class="row justify-content-center">
        <div class="col-md-6 login-form">
            <?php
                if($_POST){
                    $user_name = $_POST['username'];
                    $password = $_POST['password'];
                    $result = mysqli_query($conn,"Select * from user where username ='$user_name' and password = md5('$password') ");
                    $row = mysqli_fetch_assoc($result);
                    if($row) {
                        header("Location:home.php");
                    }
                    else {
                        echo '<p style ="color:red"> Tên đăng nhập hoặc mật khẩu không đúng! </p>' ;
                    }
                }
            ?>
            <h2 class="login-header">Đăng Nhập</h2>
            <form action="login.php" method="post">

                <div class="form-group">
                    <label for="username">Tên Đăng Nhập:</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>

                <div class="form-group">
                    <label for="password">Mật Khẩu:</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>

                <button type="submit" class="btn btn-primary btn-block login-btn" name ="login">Đăng Nhập</button>
            </form>
            <div class="register-link">
                <a href="http://localhost/IOT-Project/register.php">Chưa có tài khoản? Đăng ký ngay!</a>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>



</body>
</html>

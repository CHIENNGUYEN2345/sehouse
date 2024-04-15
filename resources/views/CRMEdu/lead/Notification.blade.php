<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Successful Save Interface</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            text-align: center;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.2);
            padding: 30px;
        }
        h1 {
            font-size: 32px;
            margin-bottom: 20px;
            color: #007bff;
        }
        p {
            font-size: 18px;
            margin-bottom: 30px;
        }
        .success-image {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
        }
        .btn-custom {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .btn-custom:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Chúc Mừng Bạn Đã Lưu Thành Công!</h1>
    <p>Mời Bạn Ấn nút quay lại để trở về trang học viên và xem dữ liệu đã được lưu </p>
    <a href="/admin/lead/add_test " class="btn btn-clean kt-margin-r-10">
    <button class="btn btn-custom">quay lại</button>
    </a>
</div>
</body>
</html>

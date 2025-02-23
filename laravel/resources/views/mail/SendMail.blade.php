<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kích Hoạt Tài Khoản</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header img {
            max-width: 200px;
        }

        .content {
            text-align: center;
            margin-bottom: 20px;
        }

        .content h1 {
            font-size: 24px;
            color: #333333;
            margin-bottom: 10px;
        }

        .content p {
            font-size: 16px;
            color: #666666;
            margin-bottom: 20px;
        }

        .button {
            display: inline-block;
            font-size: 16px;
            font-weight: bold;
            color: #ffffff;
            background-color: #007bff;
            padding: 15px 25px;
            border-radius: 5px;
            text-decoration: none;
            margin: 10px 0;
        }

        .footer {
            text-align: center;
            font-size: 14px;
            color: #999999;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="https://via.placeholder.com/200x50?text=Your+Logo" alt="Your Logo">
        </div>
        <div class="content">
            <h1>Kích Hoạt Tài Khoản Của Bạn</h1>
            <p>Chào [{{ $detail->name }}],</p>
            <p>Cảm ơn bạn đã đăng ký tài khoản với chúng tôi. Để hoàn tất quá trình đăng ký, vui lòng nhấp vào nút dưới
                đây để kích hoạt tài khoản của bạn:</p>
            <a href="{{ route('verify', $detail->id) }}" class="button" style="color: white">Kích Hoạt Tài Khoản</a>
            <p>Nếu bạn không thực hiện yêu cầu này, bạn có thể bỏ qua email này.</p>
        </div>
        <div class="footer">
            <p>&copy; [2004] [Công Ty Cổ Phần 1 Thành Viên]. Tất cả các quyền được bảo lưu.</p>
            <p><a href="[Link Huỷ Đăng Ký]" style="color: #007bff; text-decoration: none;">Huỷ Đăng Ký</a></p>
        </div>
    </div>
</body>

</html>

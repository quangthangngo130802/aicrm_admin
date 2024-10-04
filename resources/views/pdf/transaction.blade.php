<!DOCTYPE html>
<html>

<head>
    <title>Transaction PDF</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            background-color: #f4f4f4;
            color: #333;
        }

        .container {
            width: 80%;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 10px;
        }

        .header h1 {
            font-size: 24px;
            color: #4CAF50;
            margin: 0;
            text-transform: uppercase;
        }

        .content {
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .content p {
            margin: 10px 0;
            font-size: 16px;
        }

        .content p strong {
            color: #4CAF50;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            font-size: 14px;
            color: #777;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Chi tiết hóa đơn chuyển tiền</h1>
        </div>
        <div class="content">
            <p><strong>Ngày thu:</strong> {{ $transaction->created_at->format('H:i:s d/m/y') }}</p>
            <p><strong>Khách hàng:</strong> {{ $transaction->user->name }}</p>
            <p><strong>Nội dung:</strong> {{ $transaction->description }}</p>
            <p><strong>Số tiền:</strong> {{ number_format($transaction->amount) }} VND</p>
        </div>
        <div class="footer">
            <p>Cảm ơn bạn đã sử dụng dịch vụ của chúng tôi!</p>
        </div>
    </div>
</body>

</html>

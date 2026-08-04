<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Không tìm thấy trang | Lisatech</title>
    @include('component.favicon')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        :root {
            --color-primary: #0e3c7d;
            --color-secondary: #fa9301;
            --color-text: #1e293b;
            --color-light: #64748b;
            --font-base: 'Inter', sans-serif;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: var(--font-base);
            color: var(--color-text);
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            width: 100%;
            text-align: center;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            padding: 60px 40px;
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(15, 23, 42, 0.08);
            animation: fadeInUp 0.8s ease-out forwards;
        }
        .error-code {
            font-size: 130px;
            font-weight: 800;
            line-height: 1;
            background: linear-gradient(135deg, var(--color-primary) 0%, #1e40af 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 20px;
            letter-spacing: -2px;
            position: relative;
            display: inline-block;
        }
        .error-code::after {
            content: '404';
            position: absolute;
            left: 0;
            top: 0;
            background: linear-gradient(135deg, var(--color-secondary) 0%, #f97316 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            opacity: 0.15;
            transform: translate(4px, 4px);
            z-index: -1;
        }
        h1 {
            font-size: 24px;
            font-weight: 700;
            color: var(--color-primary);
            margin-bottom: 15px;
        }
        p {
            font-size: 15px;
            color: var(--color-light);
            line-height: 1.6;
            margin-bottom: 35px;
            max-width: 460px;
            margin-left: auto;
            margin-right: auto;
        }
        .actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 28px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .btn-primary {
            background-color: var(--color-primary);
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(14, 60, 125, 0.3);
        }
        .btn-primary:hover {
            background-color: #0b2e60;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(14, 60, 125, 0.4);
        }
        .btn-secondary {
            background-color: #ffffff;
            color: var(--color-primary);
            border: 1px solid rgba(14, 60, 125, 0.2);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.02);
        }
        .btn-secondary:hover {
            background-color: #f8fafc;
            border-color: var(--color-primary);
            transform: translateY(-2px);
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.05);
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        @media (max-width: 480px) {
            .container {
                padding: 40px 20px;
            }
            .error-code {
                font-size: 100px;
            }
            .btn {
                width: 100%;
                justify-content: center;
            }
            .actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-code">404</div>
        <h1>Đã xảy ra lỗi hoặc trang không tồn tại!</h1>
        <p>Đường dẫn quý khách truy cập hiện không khả dụng hoặc đã được di chuyển. Vui lòng quay lại Trang chủ hoặc liên hệ với chúng tôi để được tư vấn.</p>
        <div class="actions">
            <a href="{{ url('/') }}" class="btn btn-primary">
                <i class="fa fa-home"></i> Quay về Trang chủ
            </a>
            <a href="{{ url('lien-he.html') }}" class="btn btn-secondary">
                <i class="fa fa-envelope"></i> Liên hệ hỗ trợ
            </a>
        </div>
    </div>
</body>
</html>

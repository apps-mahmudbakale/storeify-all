<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Session Expired</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            width: 100%;
            height: 100vh;
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        }

        .container {
            text-align: center;
            color: #ffffff;
            padding: 40px;
            max-width: 600px;
        }

        .icon {
            font-size: 80px;
            margin-bottom: 30px;
            opacity: 0.8;
        }

        h1 {
            font-size: 48px;
            margin-bottom: 15px;
            font-weight: 700;
            letter-spacing: -1px;
        }

        p {
            font-size: 18px;
            line-height: 1.6;
            opacity: 0.8;
            margin-bottom: 40px;
        }

        .details {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 40px;
            font-size: 14px;
        }

        .details p {
            margin-bottom: 8px;
            opacity: 0.7;
        }

        .details p:last-child {
            margin-bottom: 0;
        }

        .button {
            display: inline-block;
            background: #ffffff;
            color: #1a1a1a;
            padding: 12px 32px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
        }

        .button:hover {
            background: #f0f0f0;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        }

        @media (max-width: 768px) {
            h1 {
                font-size: 36px;
            }

            p {
                font-size: 16px;
            }

            .icon {
                font-size: 60px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">🔒</div>
        <h1>Server Session Expired</h1>
        <p>The server session has expired and access to the application has been restricted.</p>
        
        <div class="details">
            <p><strong>Status Code:</strong> 503 Service Unavailable</p>
            <p><strong>Reason:</strong> Session License Expired</p>
        </div>

        <p style="font-size: 14px; opacity: 0.6; margin-bottom: 0;">
            Please contact system administrator for more information.
        </p>
    </div>
</body>
</html>

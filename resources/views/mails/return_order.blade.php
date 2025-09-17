<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Return Order Notification</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      background-color: #fff5f5;
      font-family: Arial, sans-serif;
      color: #3b0a0a;
    }

    .email-container {
      max-width: 600px;
      margin: 0 auto;
      background-color: #ffffff;
      border: 1px solid #f5c2c2;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(235, 87, 87, 0.1);
      overflow: hidden;
    }

    .header {
      background-color: #ffffff;
      text-align: center;
      padding: 30px 20px 20px;
    }

    .header img {
      max-width: 120px;
      margin-bottom: 10px;
    }

    .header h1 {
      margin: 0;
      font-size: 22px;
      color: #7f1d1d;
    }

    .content {
      padding: 30px 20px;
    }

    .content h2 {
      font-size: 18px;
      color: #7f1d1d;
      margin-top: 0;
    }

    .content p {
      font-size: 15px;
      color: #444444;
      line-height: 1.6;
      margin-bottom: 20px;
    }

    .order-details {
      background-color: #fdf2f2;
      border-left: 4px solid #dc2626;
      padding: 15px 20px;
      border-radius: 6px;
      margin-bottom: 20px;
    }

    .order-details p {
      margin: 6px 0;
      font-size: 15px;
      color: #3b0a0a;
    }

    .footer {
      background-color: #fef2f2;
      color: #7f1d1d;
      padding: 20px;
      text-align: center;
      font-size: 12px;
      border-top: 1px solid #f3c3c3;
      line-height: 1.6;
    }

    .contact-line {
      display: flex;
      justify-content: center;
      gap: 15px;
      flex-wrap: wrap;
      margin-top: 8px;
    }

    .contact-item span {
      font-weight: bold;
      margin-right: 5px;
    }

    .address {
      margin-top: 10px;
      color: #6b0f0f;
    }

    @media only screen and (max-width: 600px) {
      .email-container {
        width: 100%;
        border-radius: 0;
      }

      .header h1 {
        font-size: 20px;
      }

      .order-details {
        font-size: 14px;
      }

      .contact-line {
        flex-direction: column;
        gap: 5px;
      }
    }
  </style>
</head>
<body>
  <div class="email-container">
    <div class="header">
      <img src="https://colourindigo.com/images/logo.png" alt="Colour Indigo Logo" />
      <h1>{{ $type }} Order Notification</h1>
    </div>
    <div class="content">
      <h2>Hello,</h2>
      <p>You have received a new <strong>{{ strtolower($type) }}</strong> request. Please find the details below:</p>

      <div class="order-details">
        <p><strong>{{ $type }} Order ID:</strong> {{ $return_id }}</p>
        <p><strong>Original Order ID:</strong> {{ $order_id }}</p>
        <p><strong>Date of Request:</strong> {{ formateDate(date("Y-m-d H:i:s")) }}</p>
      </div>

      <p>To process this return, please log in to your dashboard and take the necessary actions.</p>
    </div>
    <div class="footer">
      <p>© 2025 Colour Indigo. All rights reserved.</p>
      <div class="contact-line">
        <div class="contact-item"><span>&#9993;</span> support@colourindigo.com</div>
        <div class="contact-item"><span>&#9742;</span> +91 80615 61999</div>
      </div>
      <p class="address">
        Street 7, Dadari Gate,<br>
        Bhiwani, Haryana 127021, India
      </p>
    </div>
  </div>
</body>
</html>

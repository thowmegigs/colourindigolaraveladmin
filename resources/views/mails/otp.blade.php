<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>OTP Verification</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
      background-color: #fee2e2;
      color: #7f1d1d;
      padding: 30px 20px 20px;
      text-align: center;
    }
    .header img {
      max-width: 120px;
      margin-bottom: 10px;
    }
    .header h2 {
      margin: 0;
      font-size: 22px;
    }
    .content {
      padding: 30px 20px;
      text-align: center;
    }
    .content p {
      font-size: 16px;
      color: #7f1d1d;
      margin: 10px 0;
    }
    .otp-box {
      display: inline-block;
      padding: 15px 30px;
      background-color: #dc2626;
      color: #ffffff;
      font-size: 24px;
      font-weight: bold;
      border-radius: 6px;
      letter-spacing: 4px;
      margin-top: 20px;
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
    .footer p {
      margin: 5px 0;
    }
    .contact-line {
      display: flex;
      justify-content: center;
      gap: 15px;
      flex-wrap: wrap;
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
      .header h2 {
        font-size: 20px;
      }
      .otp-box {
        font-size: 20px;
        padding: 12px 24px;
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
      <h2>Verify Your Email</h2>
    </div>
    <div class="content">
      <p>Use the OTP below to complete your verification:</p>
      <div class="otp-box">{{ $otp }}</div>
      <p style="font-size: 14px; margin-top: 20px;">This OTP is valid for 10 minutes.<br>Do not share it with anyone.</p>
      <p style="font-size: 12px; color: #a94442; margin-top: 30px;">If you did not request this, please ignore this email.</p>
    </div>
    @include('mails.footer')
  </div>
</body>
</html>

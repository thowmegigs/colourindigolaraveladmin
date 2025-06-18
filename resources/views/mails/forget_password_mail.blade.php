<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Reset Your Password</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: Arial, sans-serif;
      background-color: #fff5f5;
      color: #3b0a0a;
    }
    .email-container {
      max-width: 600px;
      margin: auto;
      background-color: #ffffff;
      border: 1px solid #f5c2c2;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 0 10px rgba(235, 87, 87, 0.1);
    }
    .header {
      background-color: #fee2e2;
      color: #7f1d1d;
      padding: 30px 20px 20px;
      text-align: center;
    }
    .header img {
      max-width: 120px;
      height: auto;
      margin-bottom: 10px;
    }
    .header h1 {
      margin: 0;
      font-size: 22px;
    }
    .content {
      padding: 20px 30px;
      color: #3b0a0a;
    }
    .content h2 {
      color: #991b1b;
      margin-top: 0;
    }
    .content p {
      font-size: 16px;
      line-height: 1.5;
    }
    .reset-button {
      display: inline-block;
      margin-top: 20px;
      padding: 12px 25px;
      font-size: 16px;
      color: #ffffff;
      background-color: #dc2626;
      text-decoration: none;
      border-radius: 5px;
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
      .header h1 {
        font-size: 20px;
      }
      .content h2 {
        font-size: 18px;
      }
      .header img {
        max-width: 100px;
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
      <h1>Password Reset Request</h1>
    </div>
    <div class="content">
      <h2>Hello,</h2>
      <p>You are receiving this email because we received a password reset request for your account.</p>
      <p>If you did not request this, no further action is required.</p>
      <a href="{{ $resetUrl ?? $url }}" class="reset-button">Reset Password</a>
    </div>
     @include('mails.footer')
     
  </div>
</body>
</html>

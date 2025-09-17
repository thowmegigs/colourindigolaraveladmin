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
      font-family: 'Segoe UI', Arial, sans-serif;
      background-color: #fdf5f6;
      color: #4a0d0d;
    }

    .email-container {
      max-width: 600px;
      margin: 40px auto;
      background-color: #ffffff;
      border-radius: 12px;
      overflow: hidden;
      border: 1px solid #f1c7c7;
      box-shadow: 0 8px 24px rgba(186, 22, 84, 0.08);
    }

    .header {
      background:'#ffffff';
      color: #7f1d1d;
      padding: 30px 20px 20px;
      text-align: center;
    }

    .header img {
      max-width: 120px;
      height: auto;
      margin-bottom: 12px;
      transition: transform 0.3s ease;
    }

    .header img:hover {
      transform: scale(1.05);
    }

    .header h1 {
      margin: 0;
      font-size: 24px;
      font-weight: 600;
      letter-spacing: 0.5px;
    }

    .content {
      padding: 25px 30px 30px;
      color: #3b0a0a;
    }

    .content h2 {
      color: #a4161d;
      font-size: 20px;
      margin-top: 0;
      margin-bottom: 10px;
    }

    .content p {
      font-size: 15px;
      line-height: 1.6;
      margin: 12px 0;
    }

    .reset-button {
      display: inline-block;
      margin: 20px 0 10px;
      padding: 14px 28px;
      font-size: 16px;
      font-weight: 600;
      color: #ffffff !important;
      background-color: #ba1654;
      text-decoration: none;
      border-radius: 6px;
      box-shadow: 0 4px 10px rgba(186, 22, 84, 0.2);
      transition: background-color 0.3s ease, transform 0.2s ease;
    }

    .reset-button:hover {
      background-color: #a01244;
      transform: translateY(-2px);
    }

    .footer {
      background-color: #fff6f6;
      color: #7f1d1d;
      padding: 18px 25px;
      text-align: center;
      font-size: 12px;
      border-top: 1px solid #f3c3c3;
      line-height: 1.6;
    }

    .footer p {
      margin: 4px 0;
    }

    .footer a {
      color: #ba1654;
      text-decoration: none;
    }

    @media only screen and (max-width: 600px) {
      .email-container {
        margin: 0;
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
      <p>We received a request to reset your password. Click the button below to set a new password for your account:</p>
      <a href="{{ $resetUrl ?? $url }}" class="reset-button">Reset Your Password</a>
      <p>If you did not request this change, please ignore this email — your password will remain unchanged.</p>
    </div>
    @include('mails.footer')
  </div>
</body>
</html>

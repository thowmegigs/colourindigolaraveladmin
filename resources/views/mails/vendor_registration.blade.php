<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <title>Welcome to Colour Indigo Marketplace</title>
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
        padding: 20px;
      }
      .content h2 {
        color: #991b1b;
        margin-top: 0;
      }
      .login-details {
        background-color: #fef2f2;
        border: 1px dashed #dc2626;
        padding: 15px;
        border-radius: 6px;
        margin: 20px 0;
        color: #7f1d1d;
      }
      .login-details p {
        margin: 5px 0;
        font-family: monospace;
        font-size: 14px;
      }
      .button {
        display: inline-block;
        padding: 12px 20px;
        margin-top: 20px;
        background-color: #dc2626;
        color: white;
        text-decoration: none;
        border-radius: 4px;
        font-weight: bold;
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
        <img
          src="https://colourindigo.com/images/logo.png"
          alt="Colour Indigo Logo"
        />
        <h1>Welcome to Colour Indigo Marketplace!</h1>
      </div>
      <div class="content">
        <h2>Hi {{ucwords($vendor->name)}},</h2>
        <p>
          Thank you for registering as a seller on Colour Indigo. We're excited
          to have you onboard and look forward to helping you grow your business.
        </p>

        <div class="login-details">
          <strong>Your Login Details:</strong>
          <p>Email: <span style="color:#7f1d1d;">{{$vendor->email}}</span></p>
          <p>Password: <span style="color:#7f1d1d;">{{$vendor->plain_password}}</span></p>
        
        </div>

        <a href="https://vendor.colourindigo.com/" class="button" style="color:white">
          Login to Seller Dashboard
        </a>

        <p style="margin-top: 30px;">
          If you have any questions, feel free to reach out to our support team.
        </p>
        <p>Best regards,<br />The Colour Indigo Team</p>
      </div>
        @include('mails.footer')
     
    </div>
  </body>
</html>

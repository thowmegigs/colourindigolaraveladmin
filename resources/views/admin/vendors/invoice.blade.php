<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Professional Shipping Label</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      font-size: 12px;
    }
    .label {
      width: 700px;
      margin: 20px auto;
      border: 2px solid black;
      padding: 10px;
    }
    .section {
      border-bottom: 1px solid black;
      padding: 10px 0;
    }
    .half {
      width: 48%;
      display: inline-block;
      vertical-align: top;
    }
    .title {
      font-weight: bold;
      font-size: 16px;
      margin-bottom: 10px;
    }
    .barcode {
      text-align: center;
      margin: 10px 0;
    }
    .barcode img {
      height: 50px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }
    th, td {
      border: 1px solid black;
      padding: 5px;
      text-align: center;
    }
    .footer {
      font-size: 10px;
      color: #444;
      padding-top: 10px;
    }
  </style>
</head>
<body>
  <div class="label">

    <!-- Ship To & Barcode -->
    <div class="section">
      <div class="half">
        <strong>Ship To:</strong><br>
        Vijay Kumar<br>
        Civil Hospital Kalka Near Railway Station<br>
        Panchkula, Haryana, India<br>
        133302<br>
        Phone No.: 9991110716
      </div>
     
    </div>

    <!-- Dimensions, Payment, etc. -->
    <div class="section">
    <div class="half">
      <strong>Dimensions:</strong> 20.00*18.00*4.00(cm)<br>
      <strong>Payment:</strong> PREPAID<br>
      <strong>ORDER TOTAL:</strong> 20.00 INR<br>
      <strong>Weight:</strong> 0.50 kg<br>
      <strong>eWaybill No.:</strong> N/A
    </div>
     <div class="half">
        <div class="title" style="text-align:right;">Delhivery Surface</div>
        <div class="barcode"  style="padding-left:140px ;text-align:center">
         {!! $html!!}
         <p style="padding:0;margin:1px">19041760409866</p>
        </div>
        <div style="text-align:right;">
          <strong>Routing Code:</strong> CHD/RGW
        </div>
      </div>
    </div>

    <!-- Return Address and Order Info -->
    <div class="section">
      <div class="half">
        <strong>Shipped By</strong> (If undelivered, return to):<br>
        <strong>True Colour</strong><br>
        Ino 510, Bitna road Near Khera Mandir Pinjore<br>
        Panchkula<br>
        134102<br>
        GSTIN: 06KBWPK4251A1ZI<br>
        Phone No.: 9991110716
      </div>
      <div class="half" style="padding-left:24px;text-align:center">
        <strong>Order #:</strong> 5-3<br/>
        <div style="padding-left:120px;">
        {!!$invoice !!}
</div><div style="padding-left:40px;margin:2px">
        <strong>Invoice No.:</strong> Retail00007<br>
        <strong>Invoice Date:</strong> 2025-06-11
        
      </div>
      </div>
    </div>

    <!-- Product Table -->
    <div class="section">
      <table>
        <thead>
          <tr>
            <th>Product Name & SKU</th>
            <th>HSN</th>
            <th>Qty</th>
            <th>Unit Price</th>
            <th>Taxable Value</th>
            <th>IGST</th>
            <th>Total</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>The Lifestyle Co Linen Relaxed-Fit Regular Trousers<br><small>SKU: SME-NB-0001-L-V001</small></td>
            <td>-</td>
            <td>1</td>
            <td>20.00</td>
            <td>20.00</td>
            <td>0.00</td>
            <td>20.00</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Footer -->
    <div class="footer">
      All disputes are subject to HARYANA jurisdiction only. Goods once sold will only be taken back or exchanged as per the store's exchange/return policy.<br>
      <strong>THIS IS AN AUTO-GENERATED LABEL AND DOES NOT NEED SIGNATURE.</strong><br>
      <div style="text-align:right; font-size: 10px;">Powered by <img src="https://www.shiprocket.in/wp-content/uploads/2021/07/Shiprocket-Logo-Dark.png" height="12" alt="Shiprocket Logo"></div>
    </div>

  </div>
</body>
</html>

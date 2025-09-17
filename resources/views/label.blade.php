<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Shipping Label</title>
    <style>
        body {
             font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 10px;
            max-width:600px;margin:auto;
        }
        .section {
            border: 1px solid #000;
            padding: 10px;
          
        }
        .no-border {
            border: none !important;
        }
        .info-block {
            font-size: 12px;
            line-height: 1.6;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            margin-top: 10px;
        }
      .with_border  th, .with_border  td {
            border: 1px solid black;
            padding: 4px;
            text-align: left;
        }
        .barcode {
            text-align: center;
        }
        .footer {
            font-size: 10px;
            margin-top: 10px;
        }
        .right-align {
            text-align: right;
            font-size: 11px;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<!-- Ship To Section -->
<div class="section">
    <strong>Ship To</strong><br>
    {{ $order->shipping_address->name ?? 'N/A' }}<br>
    {{ $order->shipping_address->address1 ?? '' }}<br>
    {{ $order->shipping_address->address2 ?? '' }}<br>
    {{ $order->shipping_address->city->name ?? '' }}, {{ $order->shipping_address->state->name ?? '' }}, INDIA<br>
    {{ $order->shipping_address->pincode ?? '' }}
 
</div>

<!-- Dimensions + Barcode Section (No Border) -->
<div class="section">
    <table width="100%" style="border: none;">
        <tr>
            <td class="info-block" style="vertical-align: top; width: 60%;">
               
                <strong>Dimensions:</strong>
               
                    {{$dimension}}<br>
               
                  
                

                <strong>Payment:</strong> {{ $order->paid_status=='Pending'?'COD':'PREPAID' }}<br>
                <strong>ORDER TOTAL:</strong> ₹{{ number_format($vendorOrder->vendor_total, 2) }}<br>

                <strong>Weight:</strong>
                @if($weight)
                    {{ $weight }}<br>
                @else
                    N/A<br>
                @endif

                <strong>eWaybill No.:</strong> N/A
            </td>
            <td class="barcode" style="vertical-align: top;">
                <strong>{{ $vendorOrder->courier_name ?? 'Courier' }}</strong><br>
                {!! DNS1D::getBarcodeHTML($vendorOrder->awb, 'C128', 2, 50) !!}
                <p style="padding:0;margin:0">{{$vendorOrder->awb}}</p>
                Routing Code: {{ $routing_code ?? 'N/A' }}
            </td>
        </tr>
    </table>
</div>

<!-- Shipped By Section -->
<div class="section">
    <table width="100%" style="border: none!important;">
        <tr>
            <td style="vertical-align: top; width: 60%;">
                <strong>Shipped By (If undelivered, return to)</strong><br>
                <div style="font-style:italic">
                {{ $vendorOrder->vendor->name ?? 'Vendor Name' }}<br>
                {{ $vendorOrder->vendor->address1 }}<br>
                {{ $vendorOrder->vendor->address2 }}<br>
                {{ $vendorOrder->vendor->city->name ?? '' }}<br>
                {{ $vendorOrder->vendor->state->name ?? '' }}<br>
                {{ $vendorOrder->vendor->pincode ?? '' }}<br>
                GSTIN: {{ $vendorOrder->vendor->gstin ?? 'N/A' }}<br>
                Phone No.: {{ $vendorOrder->vendor->phone ?? 'N/A' }}</div>
            </td>
            <td style="text-align: center; vertical-align: top;">
                Order #: {{ $vendorOrder->uuid ?? 'N/A' }}<br>
                {!! DNS1D::getBarcodeHTML($vendorOrder->uuid, 'C128', 2, 50) !!}
                Invoice No.: {{ str_replace('ORD', 'INV',$vendorOrder->uuid) }}<br>
                Invoice Date: {{ \Carbon\Carbon::parse($vendorOrder->created_at)->format('Y-m-d') }}
            </td>
        </tr>
    </table>
</div>

<!-- Product Table -->
<div class="section with_border">
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
            @foreach($items as $item)
                <tr>
                    <td>
                        {{ $item->product->name ?? '' }}<br>
                        SKU: {{ $item->product->sku ?? '' }}
                    </td>
                    <td>{{ $item->product->hsn_code ?? 'N/A' }}</td>
                    <td>{{ $item->qty }}</td>
                    <td>{{ number_format($item->sale_price, 2) }}</td>
                    <td>{{ number_format($item->sale_price, 2) }}</td>
                    <td>{{ number_format($item->igst ?? 0, 2) }}</td>
                    <td>{{ number_format($item->sale_price * $item->qty, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Footer -->
<div class="section">
    All disputes are subject to HARYANA jurisdiction only. Goods once sold will only 
    be taken back or exchanged as per the store's exchange/return policy.
</div>
<div class="section">
     <table width="100%" style="border: none!important;">
        <tr>
            <td style="vertical-align: top; width: 60%;">
                <strong>THIS IS AN AUTO-GENERATED LABEL AND DOES NOT NEED SIGNATURE.</strong>
            </td>
            <td style="vertical-align: top; width: 40%;text-align:right">
                <p style="padding:0;margin:1px">Powered By</p>
             <img src="file://{{public_path('shiprocket_logo.png')}}" style="width:80px;"/>
            </td>
    </tr>
</table>
    
</div>



</body>
</html>

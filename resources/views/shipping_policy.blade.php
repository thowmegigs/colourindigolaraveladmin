@extends('layouts.frontend.app')
@section('content')
	<section class="py-5 bg-light border-top">
		<!-- terms -->
		<div class="terms">
			<div class="container">
				<!-- tittle heading -->
				<h3 class="tittle-w3l">Shipping Policy
					<span class="heading-style">
						<i></i>
						<i></i>
						<i></i>
					</span>
				</h3>
				<!-- //tittle heading -->
			   <pre style="background:white!important;font-family:inherit!important;border:0!important;padding:10px; white-space: pre-wrap; /* Wrap the text */
            overflow-wrap: break-word; /* Break long words */
            word-wrap: break-word;">
		
                        
This shipping policy was last updated on 22 oct 2024.

Thank you for visiting and shopping at {{$setting->website_url}}. The following terms and conditions constitute our Shipping Policy.
<b>Domestic Shipping Policy</b>
We are shipping the order only within Jodhpur.
<b>Shipping Timelines</b>
The items will be shipped within 2hrs to 24 hrs  from the time of receiving the Order.
Customers are given chioce of slot and date when they are comfortable to receieve the delivery .We are commited to deliver the product within the time slot choosen by customer.
If we are receiving a large volume of orders, expect  some delay in shipping. We will keep you informed via email or call, during such events. 
{{$setting->tagname}} will have no liability for any delivery delays due to conditions that are beyond our control, such as:
-Natural calamities
-Riots
-Weather conditions
-Pandemic
-War
If your Order is delayed beyond 2 days of the estimated Delivery Time due to above reasons, We will get in touch with you to check if you would still like to continue with
 your Order. 
 
<b>SHIPPING COST/DELIVERY COST</b>

The shipping charges/Delivery Charges are notified at the time of checkout, and customers will know about this before making the payment.
We will be providing free delivery to customers upto cetain order amount.But as our busines grows and we  start getting huge traffic of orders then we may ask for delivery charges from customer to provide timed delivery and prioritize orders.

<b>DAMAGES</b>

{{$setting->tagname}} is not liable for any products damaged or lost during shipping. If You received Your Order damaged, please contact the shipment carrier to file a claim.

Please save all packaging materials and damaged goods before filing a claim.


<b>RETURNS, REPLACEMENTS, AND REFUNDS</b>

If the packaging is found tampered with or damaged, do not accept the goods. Write to us with your Order number at {{$setting->email}}m or contact at {{$setting->phone}} .
 We will ensure that a replacement delivery is made to you at the earliest.
{{$setting->tagname}} are not responsible for any damage or loss of the Goods once it is delivered.
Customers can return the item within 2 days  from the date of delivery.
In the rare instance that you are unhappy with our product, you can drop us an email at {{$setting->email}} with your Order number and a convenient time to call.
 One of our customer service executives will call you within 24 hrs  to understand the issue better and provide you with instructions on returning the item back to us.
 Our Delivery agent will reach the doorstep to receive  the return or exchange order.
 So Return and Exchange both are avialable on our store.

<b>DOMESTIC LOGISTIC PARTNERS</b>
No Courier service from third party will be used in product delivery in starting stage.We will employe own delivery boys to ship orders within city.But as the business expands in other city ,
we can opt for third party delivery services also.



<b>HOW TO TRACK YOUR ORDER?</b>
Customer can see the order status within order history section.All the status of order will be updated within app time to time to keep the customer updated of their order delivery status. 

<b>CONTACT US</b>
 If you have any questions regarding our shipping policy, feel free to send us an inquiry email at {{$setting->email}}


			   </pre>
			</div>
		</div>
		<!-- /terms -->
	</section>
	<!-- //Te
	<!-- //welcome -->
@endsection
@extends('layouts.frontend.app')
@section('content')

	<section class="py-5 bg-light border-top">
		<!-- terms -->
		<div class="terms">
			<div class="container">
				<!-- tittle heading -->
				<h3 class="tittle-w3l">Return Policy
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
		
                        
Return Policy
We pride ourselves on our customer service and your satisfaction is our long term pursuit. Once your package has arrived, we strongly suggest you open it
and check to make sure the item meet's your requirements.

In order to return, please create a refund/return request or request a call back from our supportteam on our mobile application within 2 days from time of receiving the order.
We will contact you and provide you with information about pickup from your address.


Once the product has been received and accepted after verification, we will process the refund and update you once done
Please allow up to 7 days for the refund transfer to be completed.
Instead of refund customer can reqeust for the exchange of the products also by creating exchange request from aplpication with required proof.

CONTACT US
 If you have any questions regarding our return policy, feel free to send us an inquiry email at {{$setting->email}} or call us at {{$setting->phone}}


			   </pre>
			</div>
		</div>
		<!-- /terms -->
	</section>
	<!-- //Te
	<!-- //welcome -->
@endsection
<?php 
namespace App\Services;

use Illuminate\Support\Facades\Http;

class ShiprocketService
{
    protected $token;

    public function __construct()
    {
        // $this->token = cache()->remember('shiprocket_token', 55 * 60, function () {
        //     $response = Http::post('https://apiv2.shiprocket.in/v1/external/auth/login', [
        //         'email' => config('services.shiprocket.email'),
        //         'password' => config('services.shiprocket.password'),
        //     ]);

        //     return $response['token'] ?? null;
        // });
         $response = Http::post('https://apiv2.shiprocket.in/v1/external/auth/login', [
                'email' => config('services.shiprocket.email'),
                'password' => config('services.shiprocket.password'),
            ]);
           $this->token= $response['token'] ?? null;
    }

    protected function headers()
    {
        return [
            'Authorization' => 'Bearer ' . $this->token,
        ];
    }

    public function createOrder(array $orderData)
    {
      
        $response = Http::withHeaders($this->headers())
            ->post('https://apiv2.shiprocket.in/v1/external/orders/create/adhoc', $orderData);

        return $response->json();
    }
    public function createReturnOrder(array $orderData)
    {
      
        $response = Http::withHeaders($this->headers())
            ->post('https://apiv2.shiprocket.in/v1/external/orders/create/return', $orderData);
        $resp=$response->json();
       if($resp['status_code']>=400){
        // dd($resp);
             \DB::table('shiprocket_errors')->insert([
                     'errors'=>json_encode($resp),
                     'line_no'=>42,
                     'function'=>'createReturnOrder in ShiprocketService',
                       'order_data'=>json_encode($orderData)
             ]);
           return ['message'=>'Failed to ship the return order,contact admin for issue ','success'=>false]; 
        }
        else{
           
         return ['message'=>'Return Order Shiped','success'=>true];
        }
    }
    public function createExchOrder(array $orderData)
    {
      
        $response = Http::withHeaders($this->headers())
            ->post('https://apiv2.shiprocket.in/v1/external/orders/create/exchange', $orderData);

        $resp=$response->json();
        if($resp['status_code']>=400){
             \DB::table('shiprocket_errors')->insert([
                     'errors'=>json_encode($resp),
                     'line_no'=>61,
                     'function'=>'createExchOrder in ShiprocketService',
                     'order_data'=>json_encode($orderData)
             ]);
              
           return ['message'=>'Failed to ship the exchange order,contact admin for issue ','success'=>false]; 
        }
        else
         return ['message'=>'Exchange Order Shiped','success'=>true];
    }
    public function addPickupLocationOfVendor($vendor)
    {
        $old_pickup=$vendor->pickup_location_name;
        $new_pickup='';
        if($old_pickup){
           
            $last_number=explode('__',$old_pickup);

            $incrmented=isset($last_number[1])?intVal($last_number[1])+1:1;
           // dd($incrmented);
            $new_pickup=$vendor->name.'_pickup_location__'.$incrmented; 
        }
        else{
           $new_pickup=$vendor->name.'_pickup_location__1'; 
        }
     
          $response = Http::withHeaders($this->headers())
             ->post('https://apiv2.shiprocket.in/v1/external/settings/company/addpickup', [
                    'pickup_location' =>$new_pickup,
                    'name' => $vendor->name,
                    'email' => $vendor->email,
                    'phone' => $vendor->phone,
                    'address' => $vendor->address,
                    'address_2' => $vendor->address2,
                    'city' =>  $vendor->city?->name,
                    'state' =>  $vendor->state?->name,
                    'country' => 'India',
                    'pin_code' =>  $vendor->pincode,
                    'address_type'=>'vendor',
                    'vendor_name'=>$vendor->name
    ]);

            $data=$response->json();
     
            if(isset($data['address'])){
                \DB::table('vendors')->where('id',$vendor->id)->update([
                    'pickup_location_name'=>$new_pickup,'pickup_location_id'=>$data['address']['id']
                ]);
             return  ['success'=>true,'message'=>'ok'];
            }
          
       else{
        // Decode the JSON message
        $decodedMessage = json_decode($response['message'], true);
        $values=array_values($decodedMessage);
        // Access the first error message
        $errorMessage = $values[0] ?? 'Unknown error';
             \DB::table('shiprocket_errors')->insert([
                     'errors'=>json_encode($data),
                     'line_no'=>68,
                     'function'=>'addPickuplocaotio in ShiprocketService',
                     'vendor_id'=>$vendor->id
             ]);
           return  ['success'=>false,'message'=>$errorMessage];
        }
      
      
    }

    public function getOrderTracking($shipmentId)
    {
        $response = Http::withHeaders($this->headers())
            ->get("https://apiv2.shiprocket.in/v1/external/courier/track/shipment/{$shipmentId}");

        return $response->json();
    }
    public function generateDocument($type,$id)
{
   
        $endpoints = [
            'label'    => 'https://apiv2.shiprocket.in/v1/external/courier/generate/label',
            'manifest' => 'https://apiv2.shiprocket.in/v1/external/manifests/generate',
            'invoice'  => 'https://apiv2.shiprocket.in/v1/external/orders/print/invoice',
        ];
        if($type!='invoice'){
        
            $response =Http::withHeaders($this->headers())->post($endpoints[$type], [
                'shipment_id' => [$id],
            ]);
            //dd($response->json());
                return $response->json();
        }
        else{
            
                $response =Http::withHeaders($this->headers())->post($endpoints[$type], [
                    'ids' => [$id],
                ]);
            return $response->json();
        }
   
}
}

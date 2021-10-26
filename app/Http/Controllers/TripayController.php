<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TripayController extends Controller
{
    public function getPaymentChanels(){
        
        $apiKey = config('tripay.api_key');
        
        
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
            CURLOPT_FRESH_CONNECT     => true,
            CURLOPT_URL               => "https://tripay.co.id/api-sandbox/merchant/payment-channel?",
            CURLOPT_RETURNTRANSFER    => true,
            CURLOPT_HEADER            => false,
            CURLOPT_HTTPHEADER        => array(
                "Authorization: Bearer ".$apiKey
            ),
            CURLOPT_FAILONERROR       => false
        ));
        
        $response = curl_exec($curl);
        $err = curl_error($curl);
        
        curl_close($curl);
        
        $response = json_decode($response)->data;
        return $response ? $response : $err;
        
    }
    
    public function requestTransaction($method, $book)
    {
        
        $apiKey = config('tripay.api_key');
        $privateKey = config('tripay.private_key');
        $merchantCode = config('tripay.merchant_code');
        $merchantRef = 'MZ-' . time();
        
        
        $user = auth()->user();
        
        $data = [
            'method'            => $method,
            'merchant_ref'      => $merchantRef,
            'amount'            => $book->price,
            'customer_name'     => $user->name,
            'customer_email'    => $user->email,
            'order_items'       => [
                [
                    'name'      => $book->title,
                    'price'     => $book->price,
                    'quantity'  => 1
                    ]
                ],
                'expired_time'      => (time()+(24*60*60)), // 24 jam
                'signature'         => hash_hmac('sha256', $merchantCode.$merchantRef.$book->price, $privateKey)
            ];
            
            $curl = curl_init();
            
            curl_setopt_array($curl, array(
                CURLOPT_FRESH_CONNECT     => true,
                CURLOPT_URL               => "https://tripay.co.id/api-sandbox/transaction/create",
                CURLOPT_RETURNTRANSFER    => true,
                CURLOPT_HEADER            => false,
                CURLOPT_HTTPHEADER        => array(
                    "Authorization: Bearer ".$apiKey
                ),
                CURLOPT_FAILONERROR       => false,
                CURLOPT_POST              => true,
                CURLOPT_POSTFIELDS        => http_build_query($data)
            ));
            
            $response = curl_exec($curl);
            $err = curl_error($curl);
            
            curl_close($curl);
            
            $response = json_decode($response)->data;
            // dd($response);
            
            return $response ?: $err;
            
        }
        
    public function detailTransaksi($reference)
        {
            
            $apiKey = config('tripay.api_key');
            
            $payload = [
                'reference'	=> $reference
            ];
            
            $curl = curl_init();
            
            curl_setopt_array($curl, array(
                CURLOPT_FRESH_CONNECT     => true,
                CURLOPT_URL               => "https://tripay.co.id/api-sandbox/transaction/detail?".http_build_query($payload),
                CURLOPT_RETURNTRANSFER    => true,
                CURLOPT_HEADER            => false,
                CURLOPT_HTTPHEADER        => array(
                    "Authorization: Bearer ".$apiKey
                ),
                CURLOPT_FAILONERROR       => false,
            ));
            
            $response = curl_exec($curl);
            $err = curl_error($curl);
            
            curl_close($curl);

            $response = json_decode($response)->data;

            // dd($response);   
            
            return $response ?: $err;
            
    }
        
}
    
    
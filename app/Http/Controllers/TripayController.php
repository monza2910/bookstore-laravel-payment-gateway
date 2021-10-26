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
    
}

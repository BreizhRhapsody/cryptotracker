<?php

namespace App\Service;

class CallApiService
{
    public function getApi(): array
    {
        $url = 'https://pro-api.coinmarketcap.com/v1/cryptocurrency/quotes/latest';
        $parameters = [
            'symbol' => 'BTC,ETH,USDT,BNB,USDC,XRP,SOL,ADA,LUNA,AVAX',
            'convert' => 'EUR',
        ];
        $headers = [
            'Accepts: application/json',
            'X-CMC_PRO_API_KEY: 83e3b403-7ae3-4b31-a34b-78eb5c8c0a9c'
        ];
        $qs = http_build_query($parameters); // query string encode the parameters
        $request = "{$url}?{$qs}"; // create the request URL


        $curl = curl_init(); // Get cURL resource
        // Set cURL options
        curl_setopt_array($curl, array(
            CURLOPT_URL => $request,            // set the request URL
            CURLOPT_HTTPHEADER => $headers,     // set the headers 
            CURLOPT_RETURNTRANSFER => 1         // ask for raw response instead of bool
        ));

        $response = curl_exec($curl); // Send the request, save the response
        $datas = json_decode($response, true);
        curl_close($curl); // Close request

        return $datas['data'];
    }
}

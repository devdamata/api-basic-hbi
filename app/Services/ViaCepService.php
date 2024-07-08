<?php

namespace App\Services;

class ViaCepService
{

    public static function requestZipCode($zipCode): array
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => 'viacep.com.br/ws/'.$zipCode.'/json/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'GET'
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        $arr = json_decode($response, true);

        return isset($arr['cep']) ? $arr : [];
    }

}

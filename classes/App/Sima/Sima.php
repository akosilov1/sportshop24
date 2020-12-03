<?php


namespace Sima;


class Sima
{
    private static $baseUrl = 'https://www.sima-land.ru/api/v3';

    /**
     * @param $url string URL
     * @return array
     */
    static function Get($url){
        echo "URL".self::$baseUrl.$url.PHP_EOL;
        $ch = curl_init(self::$baseUrl.$url);
        curl_setopt($ch, CURLINFO_HEADER_OUT,true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        //print_r(curl_getinfo($ch));
        $rez = curl_exec($ch);

        //print_r(curl_getinfo($ch));
        curl_close($ch);
        return json_decode($rez);
    }
}
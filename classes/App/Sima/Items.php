<?php


namespace Sima;


class Items extends \Sima\Sima
{
    /**
     * @param  $ar_params array Массив параметров запроса
     * @return array
     */
    static function Get($ar_params){
        $url = '/item/';
        $url .= "?".http_build_query($ar_params);
        echo $url.PHP_EOL;
        return parent::Get($url);
    }
}
<?php


namespace Sima;


class Category extends \Sima\Sima
{
    static function Get($ar_params = array(),$id = 0){
        $url = '/category/';
        if($id)$url .= $id."/";
        $url .= "?".http_build_query($ar_params);
        echo $url;
        return parent::Get($url);
    }
}
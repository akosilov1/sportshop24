<?php
namespace Api;

class Api
{
    private $req = "http://".API_MAIL.":".API_KEY."@".HOST."/api/2.0/";
    public $error;
    function Get($url){
        $ch = curl_init($this->req.$url);
        $op = array(
            CURLOPT_HEADER  => false,
            CURLOPT_RETURNTRANSFER  => true,
            CURLINFO_HEADER_OUT => true,
            CURLOPT_HTTPHEADER  => array('Content-Type: application/json'),
        );
        curl_setopt_array($ch,$op);
        $c_rez = json_decode(curl_exec($ch));
        $this->error = curl_error($ch);
        curl_close($ch);
        return $c_rez;
    }

    /**
     *
     * @param $url string URL
     * @param $data array Данные
     * @return mixed
     */
    function Put($url,$data){
        $ch = curl_init($this->req.$url);
        $op = array(
            CURLOPT_HEADER    => false,
            CURLOPT_RETURNTRANSFER    => true,
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_HTTPHEADER    => array('Content-Type: application/json'),
            CURLOPT_POSTFIELDS    => json_encode($data),
        );
        curl_setopt_array($ch,$op);
        $c_rez = json_decode(curl_exec($ch));
        $this->error = curl_error($ch);
        curl_close($ch);
        return $c_rez;
    }
    function Post($url,$data){
        $ch = curl_init($this->req.$url);
        $op = array(
            CURLOPT_HEADER    => false,
            CURLOPT_RETURNTRANSFER    => true,
            CURLOPT_CUSTOMREQUEST  => "POST",
            CURLOPT_HTTPHEADER    => array('Content-Type: application/json'),
            CURLOPT_POSTFIELDS    => json_encode($data),
        );
        curl_setopt_array($ch,$op);
        $c_rez = json_decode(curl_exec($ch));
        echo "POST REZ\n";
        $this->error = curl_error($ch);
        curl_close($ch);
        pre_print($c_rez);
        return $c_rez;
    }
}
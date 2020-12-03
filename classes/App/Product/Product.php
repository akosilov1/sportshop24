<?php
namespace Product;
use \Product\Images;
class Product
{
    public $price;
    public $product_id;
    public $amount;
    public $product_code;
    public function Get($filter){
        global $config;
        if(!$filter) return false;
        $db = new \Db;
        $q = 'SELECT product_id, amount, product_code FROM '.$config['table_prefix'].'products WHERE '.$filter;
        $r = $db->query($q,self::class);
        foreach ($r as $k=>$p)
        $r[$k]->price = \Product\Price::get($p->product_id);
        return $r;
    }

    public function GetFilePath(){
        return Files::Path($this->product_id);
    }
    public function GetImages(){
        $images = Images::Get(" object_id=".$this->product_id);
        foreach ($images as $i_id=>$image){
            $image->image_path = Images::GetPath($image->image_id).$image->image_path;
        }
        return $images;
    }
    public function GetOptions(){
        return \Product\Options::Get('product_id='.$this->product_id);
    }

    public static function GetIdByCode($code){
        if(!$code) return false;
        $db = new \Db();
        $q = 'SELECT * FROM import WHERE product_code=:code';
        $ar_rez = $db->query($q,'',array(':code'=>$code));
        return $ar_rez[0]['product_id'];
    }

    function Add($product){
        $db = new \Db();
        $id = $db->insert("INSERT INTO `cscart_products`
            (`product_code`, `status`, `company_id`, `amount`, `min_qty`, `max_qty`, `qty_step`) 
            VALUES (:code, :status, :company, :min, :max, :step)",array(":code" => $product->name));
        if($id){
            $this->AddPrice($id, $product->price);
            return $id;
        }
    }
    function AddPrice($id, $price = 0){
        $db = new \Db();
        $db->insert("INSERT INTO cscart_product_prices (product_id, price) VALUES (:id, :price)",
            array(":id" => $id, ":price" => $price));
    }
}

class newProduct{
    var $product_code, $status = 'A', $company_id = 1, $amount = 1,
        $min_qty = 0, $max_qty = 0, $qty_step = 0, $price = 0;
    function __construct($data){
        $this->$data[0] = 111;
    }
}
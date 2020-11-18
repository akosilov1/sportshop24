<?
namespace Api;
#$req = "http://".API_MAIL.":".API_KEY."@sport.loc/api/2.0/products/1425";
#http://sale%40sportshop24.ru:29680271P59IIux2s7255L297745q94J@sport.loc/api/2.0/products/1425
class Sport extends Api {
	var $api_mail, $api_key, $req, $db, $seo_settings;
	function __construct($seo_settings){
	    $this->seo_settings = $seo_settings;
		$this->api_mail = API_MAIL;
		$this->api_key = API_KEY;
		$this->req = $req = "http://".$this->api_mail.":".$this->api_key."@".HOST."/api/2.0/";
		//$this->db = new mysqli("localhost","u7031_test","3Em5qeC5vl","u7031_test");
	}
	function __destruct()
    {
        //$this->db->close();
    }
    function get_options($product_id){
        $ch = curl_init($this->req."options/?product_id=$product_id");
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $c_rez = json_decode(curl_exec($ch));
        echo curl_error($ch);
        curl_close($ch);
        return $c_rez;
    }
    function get_option_combination($product_id){
        $ch = curl_init($this->req."combinations/?product_id=$product_id");
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $c_rez = json_decode(curl_exec($ch));
        echo curl_error($ch);
        curl_close($ch);
        return $c_rez;
    }
    function update_option_combination($hash,$quantity){
        $options = array("amount" => $quantity);
        $c_rez = $this->Put("combinations/$hash", $options);
        return $c_rez;
    }
    function get_cat($cat_id){
	    $url = "categories";
	    if($cat_id) $url .= '/'.$cat_id;
        $c_rez = $this->Get($url);
        return $c_rez;
    }
    function GetProduct($filter){
        $c_rez = $this->Get("products?$filter");
        return $c_rez;
	}
	function add_cat($parent_id = 0, $name, $description = ""){
        global $log;
        $log .= "**** add_cat **** \n";
        //$log .= $this->req."categories";
        $data = array(
            'category' => $name,
            'company_id'=>1,
            'parent_id'=>$parent_id,
            'description'=>$description,
            'status'=>'A',
            'meta_description' => str_replace("((NAME))",$name,$this->seo_settings["CAT_DESCRIPTION"]),
            'meta_keywords' => str_replace("((NAME))",$name,$this->seo_settings["CAT_KEYWORDS"]),
            'page_title' =>str_replace("((NAME))",$name,$this->seo_settings["CAT_TITLE"]),
        );
        $c_rez = $this->Post("categories",$data);

        return $c_rez->category_id;
	}

    /**
     * Добавляет опцию (option_name) к товару (product_id)
     * @param $params array Массив параметров Опций и Вариантов (variants)
     * @return mixed
     */
    function add_options($params){
	    $prod_options = array(
            "product_id"=>$params["product_id"],
            "option_name"=>$params["option_name"],
            "option_type"=>"S",
            "required"=>"Y",
            "inventory"=>"N",
            "company_id"=>"1",
        );
	    if(is_array($params["variants"])){
            $prod_options["inventory"] = "Y";//Если несколько вариантов используеь количество
	        foreach ($params["variants"] as $item)
            $prod_options["variants"][] = $item;
        }else
            $prod_options["variants"][] = $params["variants"];
	    
        $ch = curl_init($this->req."options");
        $op = array(
            CURLOPT_HEADER    => false,
            CURLOPT_RETURNTRANSFER    => true,
            CURLOPT_POST  => true,
            CURLOPT_HTTPHEADER    => array('Content-Type: application/json'),
            CURLOPT_POSTFIELDS    => json_encode($prod_options),
        );
        curl_setopt_array($ch,$op);
        $c_rez = json_decode(curl_exec($ch));
        echo curl_error($ch);
        curl_close($ch);
        return $c_rez;
    }
    /**
     * Добавляет варианты (option_name) к товару (product_id)
     * @param $params array Массив параметров Опций и Вариантов (variants)
     * @param $id int option_id
     * @return mixed
     */
    function put_options($id,$params){
        $prod_options = array();
        if(is_array($params["variants"])){
            $prod_options["inventory"] = "Y";//Если несколько вариантов используеь количество
            foreach ($params["variants"] as $item)
                $prod_options["variants"][] = $item;
        }else
            $prod_options["variants"][] = $params["variants"];

        $ch = curl_init($this->req."options/$id");
        $op = array(
            CURLOPT_HEADER    => false,
            CURLOPT_RETURNTRANSFER    => true,
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_HTTPHEADER    => array('Content-Type: application/json'),
            CURLOPT_POSTFIELDS    => json_encode($prod_options),
        );
        curl_setopt_array($ch,$op);
        $c_rez = json_decode(curl_exec($ch));
        echo curl_error($ch);
        curl_close($ch);
        return $c_rez;
    }
    /**
     * Создает комбинации опций для товара (product_id) 
     * @param $params array Массив параметров комбинаций опций
     * @return mixed
     */
    function add_option_combinations($params){
        $prod_comb = array(
            "product_id"=>$params["product_id"],
            "amount"=> $params["amount"],
            "position"=> ((isset($params["position"]))?$params["position"]:0),
            "combination"=>array($params["option_id"] => $params["variant_id"]),
        );
        //
        $ch = curl_init($this->req."combinations");
        $op = array(
            CURLOPT_HEADER    => false,
            CURLOPT_RETURNTRANSFER    => true,
            CURLOPT_POST  => true,
            CURLOPT_HTTPHEADER    => array('Content-Type: application/json'),
            CURLOPT_POSTFIELDS    => json_encode($prod_comb),
        );
        curl_setopt_array($ch,$op);
        $c_rez = json_decode(curl_exec($ch));
        echo curl_error($ch);
        curl_close($ch);
        return $c_rez;
    }

    /**
     * @param $id
     * @return mixed
     */
    function get_features($id){
        $c_rez = $this->Get("features".(($id)?"/$id":""));
        return $c_rez;
    }
    /**
     *  C—Check box: Single
        M—Check box: Multiple
        S—Select box: Text
        N—Select box: Number
        E—Select box: Brand/Manufacturer
        T—Others: Text
        O—Others: Number
        D—Others: Date
        G—group of features
     * @param $name string Название характеристики
     * @param $type string Тип
     * @param $variants array/string Варианты
     * @return mixed
     */
    function add_features($name, $type = "T", $variants){
        $ch = curl_init($this->req."features");
        $ar_variants = array();
        if(is_array($variants)){
            foreach ($variants as $v)
            $ar_variants[] = array("variant" => $v);
        }else{
            $ar_variants[] = array("variant" => $variants);
        }
        $op = array(
            CURLOPT_HEADER    => false,
            CURLOPT_RETURNTRANSFER    => true,
            CURLOPT_POST  => true,
            CURLOPT_HTTPHEADER    => array('Content-Type: application/json'),
            CURLOPT_POSTFIELDS    => '{
               "company_id": "1",
               "description": "'.$name.'",
               "display_on_catalog": "Y",
               "display_on_product": "Y",
               "feature_type": "'.$type.'",
               "status": "A",
               "variants": '.json_encode($ar_variants).'
            }',
        );

        curl_setopt_array($ch,$op);
        $c_rez = json_decode(curl_exec($ch));
        echo curl_error($ch);
        curl_close($ch);
        return $c_rez;
    }

    /**
     * @param $id
     * @param $v
     * @return bool|mixed
     */
    function put_features($id, $v){

        //{"company_id":"1", "feature_type":"S", "comparison":"Y", "variants":[{"variant": "Unique"}, {"variant": "Mass-produced"}]}
        if(!$id) return false;
        $ar_props = array(
            "company_id"=>"1",
            "feature_type"=>"S",
            /*"variants"=>array(
                array("variant"=>"Unique"),
                array("variant"=>"Mass-produced")
            )*/
        );
        if(is_array($v)){
            $rez_var = array();
            foreach ($v as $value) {
                $rez_var[] = $value;
            }
            $ar_props["variants"] = $rez_var;
        }
        $c_rez = $this->Put("features/$id", $ar_props);
        return $c_rez;
    }

    /**
     * @param $id int ID Товара
     * @param $params array Массив с параметрами
     * @return mixed
     */
    function put_product($id,$params){
        $c_rez = $this->Put("products/$id", $params);
        return $c_rez;
    }
    /**
     * Добавление товара с характеристиками и картинками
     * @param $prod array Массив свойств товара
     * @return object
     */
	function add_product($prod){
	    global $config;
        //"http://sale%40sportshop24.ru:29680271P59IIux2s7255L297745q94J@sportshop24.ru/api/2.0/products?pcode=1УТ-00008871"
        $ar_rez_prod = array(
        	"product"           => $prod["product"],
        	"price"             => $prod["price"],
        	"category_ids"      => $prod["category_ids"],
        	"product_code"      => $prod["product_code"],
        	"full_description"  => $prod["full_description"],
        	"amount"            => $prod["amount"],
        	'weight'            => str_replace(",",".",$prod["weight"]),
        	"meta_keywords" => str_replace("((NAME))",$prod["product"],$this->seo_settings["PROD_KEYWORDS"]),
        	"meta_description" => str_replace("((NAME))",$prod["product"],$this->seo_settings["PROD_DESCRIPTION"]),
        	"page_title" => str_replace("((NAME))",$prod["product"],$this->seo_settings["PROD_TITLE"])
        );
        if(count($prod["options"]) > 0) $ar_rez_prod["tracking"] = "O";
        // features
        if(is_array($prod["features"])){
            foreach ($prod["features"] as $name => $value){
                if(!$f_id = \Functions::GetFeaturesFromName($name)){
                    echo "ADD NEW FEATURES $name\n";
                    $f_id = $this->add_features($name, "S",array($value));//,array($value)
                    //print_r($f_id);
                    $f_id = $f_id->feature_id;
                    echo "ADD NEW FEATURES $name | $f_id\n";
                }
                if(!$v_id = \Functions::GetFeatureVariantFromName($f_id,$value)){
                    $ar_f = $this->get_features($f_id);
                    $f = (array)$ar_f->variants;
                    array_push($f,array("variant" => $value));
                    echo "PUT NEW FEATURES VARIANT\n";
                    $this->put_features($f_id, $f);
                    $v_id = \Functions::GetFeatureVariantFromName($f_id,$value);
                    echo "VARIANT ID $v_id\n";
                }

                $ar_rez_prod["product_features"][$f_id] = array(
                    "feature_type" => "S",
                    "variant_id" => $v_id,
                );
            }

        }
        /**
         *  Добавляем картинки к товару
         */
        if($prod["pictures"]) {
            // Основные
            $ar_rez_prod["main_pair"] = array(
                "detailed"=>array(
                    'absolute_path' =>  __DIR__."/../..".$prod["pictures"][0],
                    "http_image_path"   => 'https://'.$config['http_host'].$prod["pictures"][0],
                    "image_path"   => 'https://'.$config['http_host'].$prod["pictures"][0],
                ),
                "icon"=>array(
                    'absolute_path' =>  __DIR__."/../..".$prod["pictures"][0],
                    "http_image_path"   => 'https://'.$config['http_host'].$prod["pictures"][0],
                    "image_path"   => 'https://'.$config['http_host'].$prod["pictures"][0],
                )
            );
            // Доп картинки
            foreach ($prod["pictures"] as $img) {
                $ar_rez_prod["image_pairs"][] = array(
                    "detailed" => array(
                        'absolute_path' => __DIR__ . "/../.." . $img,
                        "http_image_path" => 'https://' . $config['http_host'] . $img,
                        "image_path" => 'https://' . $config['http_host'] . $img,
                    ),
                    "icon" => array(
                        'absolute_path' => __DIR__ . "/../.." . $img,
                        "http_image_path" => 'https://' . $config['http_host'] . $img,
                        "image_path" => 'https://' . $config['http_host'] . $img,
                    )
                );
            }
        }
        $c_rez = $this->Post("products", $ar_rez_prod);
        echo 'ERRORS '.$this->error;
        return $c_rez;
    }


}
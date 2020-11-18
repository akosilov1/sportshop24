<?php
define('SOAP_LOGIN', 'sale@sportshop24.ru');
define('SOAP_PASS', '610985');

define("BOOTSTRAP", "value");
define("HOST", $_SERVER["HTTP_HOST"]);
define('DEVELOPMENT', false);

define("API_MAIL","sale%40sportshop24.ru");
define("API_KEY","qcVi1535RM2ap3V9B39wWUl63l3K7Lhy");

require_once __DIR__."/../config.local.php";

$seo_settings = array(
    "CAT_TITLE" => "((NAME)) купить  с доставкой, низкая цена в интернет-магазине СпортШоп24",
    "CAT_DESCRIPTION" => "((NAME)) купить  с доставкой по низкой цене в интернет-магазине СпортШоп24. Индивидуальные скидки. Доставка по России.",
    "CAT_KEYWORDS" => "((NAME)) купить",
    "PROD_TITLE" => "((NAME)) купить  с доставкой, низкая цена в интернет-магазине СпортШоп24",
    "PROD_DESCRIPTION" => "((NAME)) купить  с доставкой по низкой цене в интернет-магазине СпортШоп24. Индивидуальные скидки. Доставка по России. ",
    "PROD_KEYWORDS" => "((NAME)) купить",
);
$price_type = array("Золото","Серебро","Бронза","Старт","РРЦ","Прайм");
define('PRICE', 3);

spl_autoload_register(function ($class_name) {
    //echo $class_name;
    include $_SERVER["DOCUMENT_ROOT"]."/import/classes/App/".str_replace("\\","/",$class_name) . '.php';
});
function pre_print($data){
    echo "<pre>";
    print_r($data);
    echo "</pre>";
}
/*\App\Product\Images::$sub_table = $config['table_prefix'].'images';
\App\Product\Images::$table = $config['table_prefix'].'images';*/
$images['table'] = $config['table_prefix'].'images';
$images['sub_table'] = $config['table_prefix'].'images_links';
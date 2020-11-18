<?
require_once __DIR__.'/config.php';
/*$_GET = array(
    'date-m'=>0,
    'date-d'=>1,
    'action'=>'update',
    'cron'=>'y'
);
include $_SERVER["DOCUMENT_ROOT"]."/import/soap.php";
die();*/
$t = microtime(true);

use \Product\Options;
include __DIR__.'/classes/Api.php';

include __DIR__.'/classes/soap.php';

include __DIR__.'/classes/sport.php';

include __DIR__.'/classes/functions.php';


Functions::init($config);
//echo '<pre>FFF '.Functions::GetFeaturesFromName("d пружины, см");

/*$sport= new Sport($seo_settings);
$p = $sport->GetProduct("pcode=УТ-00011609");
pre_print($p);
die();*/
$s = new Soap();
$db = new Db();

$mag = new \Api\Sport($seo_settings);
echo "<pre>";
/*$ar_new = $s->getProductIdAllByChanged(0,1);
print_r($ar_new);
die();*/
$item = $s->client->getProduct(700750);//777008 'УТ-00016975'
print_r($item);
$ar_prod = Functions::UpdateProduct($item);
print_r($ar_prod);
/*$db_rez = $db->query('SELECT * FROM category WHERE cat_id=805');
print_r($db_rez);
print_r( iconv("CP1251","UTF-8", $db_rez[0]['name']));
die('STOP');*/

die("STOP 999");
echo '<pre>';
//SELECT * FROM cscart_categories WHERE level=1
$db_rez = $db->query('SELECT * FROM cat WHERE depth=1');

foreach ($db_rez as $rez){
    print_r($rez);
    $q = 'SELECT category_id FROM cscart_category_descriptions WHERE category=:name';
    $mag_rez = $db->query($q,'',array(':name'=>$rez['name']));
    print_r($mag_rez);
}

die("STOP");
$ar_cat = $s->client->getCategoryAll();
$i = 1;
$offset = ($_GET['offset'])?$_GET['offset']:0;
foreach ($ar_cat->category as $cat){
    echo '*** CAT ['.$cat->id.'] ***'.PHP_EOL;
    print_r($cat);

    $db->query('INSERT INTO cat SET id=:id, name=:name, lm=:lm, rm=:rm, parent=:parent, depth=:depth','',
        array(
            ':id'=>$cat->id,
            ':name'=>$cat->name,
            ':lm'=>$cat->leftMargin,
            ':rm'=>$cat->rightMargin,
            ':parent'=>$cat->parent,
            ':depth'=>$cat->depth,
        )
    );
    /*$q = 'SELECT * FROM cscart_category_descriptions WHERE category = :category';
    print_r($db->query($q,'',array(':category' => $cat->name)));*/
    //echo 'GET: '.Functions::GetCategoryForName(trim( $cat->name));
    /*$db_rez = $db->query('SELECT * FROM category WHERE cat_id='.$cat->id);
    print_r($db_rez);*/
    //if($i >= ($offset + 500)) break;

    $i++;
}?>
<a id="NEXT" href="?offset=<?=($offset + 500);?>">NEXT</a>
<?php

//print_r($ar_cat);
die('STOP');
$ar_prod = $s->client->getProduct(777008);
// НЕ ПРАВИЛЬНАЯ КАРТИНКА https://sportshop24.ru/images/product/134/7bb2fb1559ec8e12622bc80b2fdf9f52.jpg
print_r($ar_prod);
/*$mag = new Sport($seo_settings);
$date_m = 0;
$date_d = 1;
$date_c = date("Y-m-d\TH:i:sP",mktime(0, 0, 0, $date_m, $date_d, date("Y")));
echo $date_c;
$result = $s->client->getProductIdAllByChanged(array('startDate' =>$date_c));*/


/*$mode = ($_GET['last'] > 0)?'a':'w';
$f = fopen(__DIR__.'/product_codes.csv',$mode);
$result = $s->client->getProductIdAll();
$i = 1;
$is_start = false;
foreach ($result->productId as $p_id){
    if($_GET['last'] && !$is_start){
        if($p_id == $_GET['last']) $is_start = true;
        continue;
    }
    $ar_prod = $s->client->getProduct($p_id);//'УТ-00016975'
    echo $i.' > '.$p_id.'<br>';
    fputcsv($f, array($p_id,$ar_prod->code), ';');
    if($i >= 50) break;
    $i++;
}
if($p_id):?>
    <a id="next" href="?last=<?=$p_id?>">NEXT</a>
    <script>
        document.getElementById('next').click();
    </script>
<?endif;
fclose($f);*/
//print_r($ar_prod);
//echo '<img src="'.$ar_prod->picture[2]->_.'" />';
// weight - Вес
die("STOP");
//pre_print($result);
$db = new \Db;

/*$prod = $s->client->getProduct($result->productId[0]);
pre_print($prod);*/
/*$rez = $db->query("SELECT * FROM import");
pre_print($rez);*/
$i=1;
foreach ($result->productId as $id){
    $import_id = $db->query("SELECT `id` FROM `import` WHERE `product_id`=".$id);
    //pre_print($import_id);
    echo $import_id[0]['id']." | ";
    if(!$import_id){
        $prod = $s->client->getProduct($id);
        if($prod->code){
            $rez = $db->insert("INSERT INTO `import` SET `product_id`=:id, `product_code`=:code", array(":id"=>$id,":code"=>$prod->code));
            if($rez){
                echo "OK ID: ".$id." \t\t>>> CODE ".$prod->code."<br>";
            }else{
                echo "ERROR ".$id." ".$db->error."<br>";
            }
            $i++;
        }
    }
    if($i >= 100){
        ?>
        <a id='next' href='?'>next</a>
        <script>document.getElementById("next").click()</script>
        <?die("NEXT");
    }
}
die("END");
/*$p_id = 16308;
$source_prod = $s->getProduct($p_id);
var_dump($source_prod);*/
die();
//$prods = $s->client->getProduct(6152/*8885 78584*/);
/*pre_print($prods);
die("END");
echo "<pre>";
Functions::init($config);
Functions::AddProduct($prods);
die("END");
$i = 0;
while($err = \Product\Images::CheckImages($i)){
    pre_print($err);
    if($i > 200000) break;
    $i += 1000;
};
die('END');*/
$p = new \Product\Product();
$prod = $p->get("product_code='УТ-00007701'");
pre_print($prod);
pre_print($prod[0]->GetFilePath());
pre_print($prod[0]->GetImages());
//pre_print(\Product\Features::Get($prod[0]->product_id));
//pre_print($prod->GetFilePath()) ;
die();
$pdo = new PDO("mysql:host=".$config["db_host"].";dbname=".$config["db_name"].";charset=UTF8",$config["db_user"],$config["db_password"]);
$r = $pdo->prepare("SELECT *
FROM `bishop_images_links` JOIN ".$config['table_prefix']."images ON detailed_id = bishop_images.image_id WHERE object_type='product'
LIMIT 50");//bishop_images
$r->execute();
pre_print($r->fetchAll(PDO::FETCH_ASSOC));
die("STOP");
spl_autoload_register(function ($class_name) {
    echo $class_name;
    include $_SERVER["DOCUMENT_ROOT"]."/import/classes/".str_replace("\\","/",$class_name) . '.php';
});
$prod = new \Product\Product();
$prod_ = $prod->get("УТ-00007216");
pre_print($prod_);
$prod_->price = new \Product\Prices\Price();
$prod_->price = $prod_->price->get($prod_->product_id);
pre_print($prod_);
die("STOP");
$pdo = new PDO("mysql:host=".$config["db_host"].";dbname=".$config["db_name"].";charset=UTF8",$config["db_user"],$config["db_password"]);
$q = "SELECT * FROM ".$config['table_prefix']."_products LEFT JOIN ".$config['table_prefix']."product_prices as pp ON pp.product_id = bishop_products.product_id WHERE product_code='УТ-00007216'";
$r = $pdo->query($q)->fetchAll(PDO::FETCH_ASSOC);
pre_print($r);//bishop_product_prices
die("STOP");
require $_SERVER["DOCUMENT_ROOT"]."/import/classes/functions.php";
require $_SERVER["DOCUMENT_ROOT"]."/import/classes/sport.php";
require $_SERVER["DOCUMENT_ROOT"]."/import/classes/soap.php";

$seo_settings = array(
    "CAT_TITLE" => "((NAME)) оптом  с доставкой, низкая цена в интернет-магазине СпортШоп24",
    "CAT_DESCRIPTION" => "((NAME)) оптом  с доставкой по низкой цене в интернет-магазине СпортШоп24. Индивидуальные скидки. Доставка по России.",
    "CAT_KEYWORDS" => "((NAME)) оптом",
    "PROD_TITLE" => "((NAME)) оптом  с доставкой, низкая цена в интернет-магазине СпортШоп24",
    "PROD_DESCRIPTION" => "((NAME)) оптом  с доставкой по низкой цене в интернет-магазине СпортШоп24. Индивидуальные скидки. Доставка по России. ",
    "PROD_KEYWORDS" => "((NAME)) оптом",
);
$price_type = ["Золото","Серебро","Бронза","Старт","РРЦ","Прайм"];
define('PRICE', 3);




$mag = new Sport(array());

$_REQUEST["step"] = 1;
$source = new Soap;

Functions::init($config);
/*
pre_print(Functions::GetFeaturesFromName("Цвет"));
pre_print(Functions::GetFeatureVariantFromName(7,"черный/желтый"));
*/

/*$op = $s->get_options(27119);

pre_print($op);

pre_print(Functions::GetOptionsVariantIdFromName(3580,"XS"));
echo "TIME ".(microtime(true)-$t);*/




//$prods = $source->getProductIdAllByChanged(0,1)->productId;//"УТ-00007216" id=78584
$prods = $source->client->getProduct(78584);
pre_print($prods);
$p = $mag->GetProduct("pcode=УТ-00007216");
pre_print($p);

//Functions::UpdateProduct($prods);



$i = 1;
/*foreach ($prods as $id) {
	echo $id."<br>";
	pre_print($source->getProduct($id));
	if($i > 5) break;# code...
	$i++;
}*/


die("STOP");



$ddd = explode("-", date("d-m-y",time()));
echo "<pre>";
print_r($ddd);
    die("STOP");
define("BOOTSTRAP", "value");
include "../config.local.php";
$db = new mysqli($config["db_host"],$config["db_user"],$config["db_password"],$config["db_name"]);
$ar_rez = array();

    $res = $db->query("SELECT
        category_id,
        `level`,
        product_count,
        parent_id 
    FROM
        bishop_categories AS c 
    ORDER BY `level` ASC
    ");
    while ($rez = $res->fetch_assoc()){
        //echo $rez['product_count']." | ".$rez['category_id']."<br>";
        $ar_rez[$rez["level"]][$rez["parent_id"]][]= $rez;
        //getCat($rez['category_id'],1);
    }


echo "<pre>";
print_r($ar_rez);

function getCat($id,$i){
    global $db, $ar_rez;
    $res = $db->query("SELECT product_count, category_id, parent_id FROM bishop_categories WHERE category_id = $id");
    while ($rez = $res->fetch_assoc()){
        if($i>0) echo $i.str_repeat(">",$i);
        echo "C::$id | ".$rez['category_id']." | ".$rez['product_count']."<br>";
        if($rez['product_count'] == 0){
            if($c_id = getParent($rez['category_id'],$rez['parent_id'], ++$i)){
                echo "$i > CID:: ".$c_id["cat_id"]."<br>";
                $ar_rez[] = $c_id;
            }
        }
        //return $rez['product_count'];
    }
    return false;
}
function getParent($id,$parent, $i=0){
    global $db;
    $res = $db->query("SELECT product_count, category_id, parent_id FROM bishop_categories WHERE parent_id = $id");
    if($res->num_rows == 0) return array("cat_id"=>$id,"parent"=>$parent);
    while ($rez = $res->fetch_assoc()){
        if($i>0) echo $i.str_repeat(">",$i);
        echo "P::$id | ".$rez['category_id']." | ".$rez['product_count']."<br>";
        if($rez['product_count'] == 0){
            getCat($rez['category_id'], ++$i);
        }
        //return $rez['product_count'];
    }
    return false;
}
function getProduct($cat_id){

}
exit();
echo "DR:: ".$_SERVER["DOCUMENT_ROOT"]."<br>";
$f = fopen("test.txt","w");
fwrite($f,"111\n");
fwrite($f,"222\n");
fwrite($f,"333\n");
fclose($f);

$t=new Test();
$t2=new Test2();
echo $t->func(4);
echo $t2->func2(4);
class Test{
    function func($num){
        return $num * 2;
    }
}
class Test2 extends Test{
    function func2($num){
        $num++;
        return $this->func($num);
    }
}
exit();
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s")." GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Cache-Control: post-check=0,pre-check=0", false);
header("Cache-Control: max-age=0", false);
header("Pragma: no-cache");
?>
<html>
<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <style>
        #rez{background-color: red;};
    </style>
</head>
<body>
<div id="rez"></div>
<iframe src="/import/index.php" frameborder="0"></iframe><br>
<button id="next"  data-link="/import/index.php?step=1&clear=1">Продолжить</button>
<button id="stop" >Стоп</button>
<?php


/**
 * Created by PhpStorm.
 * User: Александр
 * Date: 31.01.2018
 * Time: 19:07
 */
/*ignore_user_abort(true);
set_time_limit(0);
start(126);
function start($step){
    ob_start();
    $ch = curl_init("http://yml.loc");

    $opt = array(
        CURLOPT_RETURNTRANSFER => true,
        CURLINFO_HEADER_OUT => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => array("step"=>$step,"clear"=>1),
    );
    curl_setopt_array($ch,$opt);
    $rez = stripslashes(curl_exec($ch));
    //echo $rez;
    //$rez = preg_replace("#\s+#um","",$rez);
    //echo $rez."<br>";
    echo "<pre>";
    $ar_rez = json_decode($rez, true);
    print_r($ar_rez);
    if($ar_rez["STATUS"] == "STOP" && (int)$ar_rez["STEP"] > 0){
        ob_end_flush();
        //start((int)$ar_rez["STEP"]);
    }
    echo json_last_error_msg();
    echo curl_error($ch);
    //print_r(curl_getinfo($ch));

    curl_close($ch);
}*/

/*$text = "Турник распорный сделан из прочной стали с порошковым напылением, устойчивым к истиранию.
 
Занимает мало места и универсален в использовании. Усиленный крепежный механизм на три точки крепления значительно повышает надежность турника. Принадлежности для крепежа в комплекте.
Предназначен для спортивных занятий и тренировок, на нем можно подтягиваться узким, широким, обратным и параллельным хватами. 
Характеристики:
Тип: распорный
Материал: сталь
Диаметр, мм: 28
Ширина, см: 76-84
Максимальная нагрузка, кг: 120
Окраска: порошковая ударопрочная
Производство: Россия
Дополнительно: имеются в комплекте принадлежности для крепежа 

 ";*/
/*
$text="Утяжелители ЛЮКС (пара) - это утяжелители специально предназначенные для занятий фитнесом, благодаря мягкому на ощупь материалу он не натирает и не создает дискомфорта при использовании.
Также используются во время тренировок по гимнастике для увеличения силы, выносливости и техничности спортсменок при отработке различных элементов.
Основные характеристики:
Тип: для рук
Вес, кг: 0,3
Материал: трикотаж
Наполнитель: свинцовая дробь
Застежка: липучка с кольцом
Цвет: в ассортименте
Дополнительные характеристики:
Производство: Россия";
echo "<pre>";



print_r(getFeaturesFromDescription($text));
function getFeaturesFromDescription($text){
    $rez = $text_rez = array();
    if($poz = strpos($text,"Характеристики:")){
        $text_rez[] = substr($text,$poz);
        $desc = substr($text,0, $poz);
    }elseif ($poz_osn = strpos($text,"Основные характеристики:")){
        $poz_dop = strpos($text,"Дополнительные характеристики:");
        $desc = substr($text,0, $poz_osn);
        if($poz_osn && $poz_dop) {
            $text_rez[] = substr($text,$poz_osn,($poz_dop - $poz_osn));
            $text_rez[] = substr($text,$poz_dop);
        }elseif($poz_osn) $text_rez[] = substr($text,$poz_osn);
        elseif($poz_dop) $text_rez[] = substr($text,$poz_dop);
    }
    //print_r($text_rez);
//
    foreach ($text_rez as $ttt){
        preg_match_all("#^.*$#m",$ttt,$rrr);
        foreach ($rrr[0] as $k => $str){
            if($k == 0 || trim($str) == "") continue;
            $ar = explode(":",$str);
            $rez["features"][$ar[0]] = $ar[1];
        }
    }
    $rez["description"] = $desc;
    return $rez;
}
echo "</pre>";*/?>
<script type="text/javascript">
    function getCounter() {
        $.get("/import/counter.txt",function (data) {
            var rez = JSON.parse(data);
            console.log(rez);
            var p = rez.PERCENT;
            if (rez.STATUS == "WORCK"){
                $("#rez").html(rez.STEP).css("width", p + "%");
            }
            else if (rez.STATUS == "STOP"){
                console.log("stop");
                $("#rez").html("END STEP=" + rez.STEP).css("width", "100%");
                $("#next").data("link", "/import/index.php?step=" + (rez.STEP * 1 +1) + "&clear=1");
                //setInterval(getCounter, 600);
            }else if(rez.STATUS == "END"){
                console.log("end");
                $("#rez").html("Парсинг выполнен на шаге " + rez.STEP);
                clearInterval(interval);
            }

        });
    }
    window.interval = setInterval(getCounter, 600);
    $("#next").on("click", function(e){
        e.preventDefault();
        var h = $(this).data("link");
        clearInterval(interval);
        window.interval = setInterval(getCounter, 600);
        $.get(h,function(data){
            console.log("qqq");
        });
        //setTimeout(function(){getCounter();}, 1000);
    });
    $("#stop").on("click", function(e){
        e.preventDefault();
        clearInterval(interval);
    });
</script>
</body>
</html>
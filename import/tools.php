<?php
require_once __DIR__.'/config.php';
define('STEP_WIDTH', 5);
$t = microtime(true);
use \Product\Product;

\Product\Images::$sub_table = $config['table_prefix'].'images';
\Product\Images::$table = $config['table_prefix'].'images_links';
require __DIR__."/classes/soap.php";
$soap = new Soap();
require_once(__DIR__."/classes/Api.php");
require_once(__DIR__."/classes/sport.php");
$mag = new \Api\Sport($seo_settings);
$db = new \Db;
?>
    <form action="" method="get">
        <button name="action" value="check_images">Проверка картинок</button>
        <button name="action" value="check_cron">Крон</button>
        <button name="action" value="check_quantity">Наличие</button>
        <button name="action" value="check_cat_desc">Описание для категорий</button>
    </form>
<?
$log = json_decode(file_get_contents(__DIR__.'/update.log'),true) ;
echo 'Крон: '.date('d.m.y',$log['TIME']);pre_print($log);
switch ($_REQUEST['action']){
    case 'check_cat_desc':
        echo "<pre>";
        $template = "В нашем интернет-магазине “СпортШоп24” вы можете купить {{NAME}}, по цене близкой к оптовой! 
        Так как мы работаем с минимальными издержками, поэтому готовы дать вам максимально выгодные условия на рынке. 
        Мы осуществляем доставку товара по России и работаем только с проверенными курьерскими и транспортными компаниями. 
        Для постоянных клиентов мы готовы сделать скидку. 
        Наша команда всегда работает над расширением ассортимента  при этом, работая только с проверенными производителями, 
        давно зарекомендовавших себя, поэтому у вас всегда будет из чего выбрать, но при этом вы можете не сомневаться в качестве покупаемого {{GROUP}}. 
        С удовольствием ждем ваших заказов!";
        $db_rez = $db->query("SELECT category_id, category FROM ".$config['table_prefix']."category_descriptions WHERE description = '' LIMIT 250");
        foreach ($db_rez as $ar_cat){
            print_r($ar_cat);
            $new_desc = str_replace(['{{NAME}}', '{{GROUP}}'],[mb_strtolower($ar_cat['category'])],$template);
            $db->update('UPDATE '.$config['table_prefix']."category_descriptions SET description = :desc WHERE category_id = :id",[':desc' => $new_desc, ':id' => $ar_cat['category_id'] ]);
            echo $new_desc;

        }

        break;
    case 'check_cron':
        $log = array('STATUS' => 'END', 'TIME' => time());
        file_put_contents(__DIR__.'/update.log', json_encode($log));
        break;
    case 'check_images':

        $offset = ((int)$_GET['offset'])?(int)$_GET['offset']:0;
        $q = 'SELECT product_id, amount, product_code FROM '.$config['table_prefix'].'products LIMIT '.$offset.','.STEP_WIDTH;
        $r = $db->query($q);
        foreach ($r as $prod){
            echo $prod['product_code'].' >>><br>';

            $prod_id = Product::GetIdByCode($prod['product_code']);
            echo 'ID = '.$prod_id.' <br>';
            $source_prod = $soap->getProduct($prod_id);//16308 78584
            if($source_prod === false){
                echo 'Товар не найден<br>';
                pre_print($soap->erroors);
            }else{
                $p = new Product();
                $pr = $p->Get('product_id='.$prod['product_id']);
                $pr[0]->images = $pr[0]->GetImages();
                //pre_print($pr);
                if (!$pr[0]->images){
                    //pre_print($source_prod);
                    addImages($prod['product_id'],$source_prod->picture);
                    /*$i = 1;
                    foreach ($images as $img){
                        if($img->source === 'detail' || $img->source === 'more'){
                            $img_type = ($img->source === 'detail')?'M':'A';
                            echo '#'.$i.' ADD IMG >>> '.$img->_.'<br>';
                            if($new_i_id = \Product\Images::AddImage($img->_, 'product',$prod['product_id'],$img_type))
                                echo 'REZ OK'.$new_i_id.'<br>';
                            else
                                echo 'REZ ERR'.$db->error.'<br>';
                            $i++;
                        }
                    }*/
                }else{
                    pre_print($pr[0]->images);
                    foreach ($pr[0]->images as $img){
                        if(file_exists($img->image_path)){
                            echo "ok";
                        }else{
                            echo 'Нет такого файла '.$img->image_path.'<br>';
                            delAllImg($prod['product_id']);
                            addImages($prod['product_id'],$source_prod->picture);
                            break;
                        }
                    }

                }
            }
        }
        if(count($r) > 0):
            ?>
            <a id="next" href="?offset=<?=$offset + STEP_WIDTH?>&action=check_images#next">Next >>></a>
            <script>
                document.getElementById('next').click();
            </script>
        <?
        endif;
        echo "-= Конец =-";
    break;
    case "check_quantity":
        echo "<pre>";
        $ar_ids = $soap->client->getProductIdAll()->productId;
        $i = 1;
        $offset = ($_REQUEST['offset'])?$_REQUEST['offset']:0;
        echo "OFFSET ".$offset."<br>";
        foreach ($ar_ids as $id){
            if($i < $offset){
                $i++;
                continue;
            }
            echo $i."<br>";
            $ar_prod = $soap->client->getProduct($id);
            //if(!$ar_prod->active) continue;
            //$ar_prod->code
            print_r($ar_prod);
            if($ar_prod->active){
                echo "<<< ACTIVE >>>";
            }else{
                echo "<<< NO ACTIVE >>>";
                // Не активные товары у поставщика
                //$q = 'SELECT * FROM '.$config['table_prefix'].'products WHERE product_code=\'УТ-00016164\' LIMIT 1';
                $q = 'SELECT product_id, amount, product_code FROM '.$config['table_prefix'].'products WHERE product_code=\''.$ar_prod->code.'\'';
                $r = $db->query($q);
                if($r === false)
                    echo "ERROR ".$db->GetError();
                if($r) {
                    print_r($r);
                    die("SPORT ");
                }
                echo ">>>";
                /*$s_ar_prod = $mag->GetProduct("pcode=".$ar_prod->code);
                if($s_ar_prod->products){
                    print_r($s_ar_prod);
                    die("SPORT ");
                }*/
            }
            if(is_array($ar_prod->size)){
                foreach ($ar_prod->size as $size){
                    $ar_q[$id][$size->name] = $size->quantity;
                }
            }else{
                $ar_q[$id] = $ar_prod->size->quantity;
            }


            if($i >= ($offset + 100)) break;
            $i++;
        }
        print_r($ar_q) ;
        ?>
        <a href="?action=check_quantity&offset=<?=$i?>" id="next">NEXT >>></a>
    <?php break;
}
//delAllImg(13898 );
function delAllImg($prod_id){
    global $config;
    $db = new \Db();
    if(!$prod_id) return;
    $q = 'SELECT image_id FROM '.$config['table_prefix'].'images_links WHERE object_type="product" AND object_id='.$prod_id;
    $rez = $db->query($q);
    if(!$rez) return;
    foreach ($rez as $img_id){
        $q = 'DELETE FROM '.$config['table_prefix'].'images WHERE image_id='.$img_id;
        $db->query($q);
    }
    $q = 'DELETE FROM '.$config['table_prefix'].'images_links WHERE object_type="product" AND object_id='.$prod_id;
    $db->query($q);
    pre_print($rez);

}
function addImages($prod_id, $images){
    $i = 1;$db = new \DB;
    array_map('unlink', glob(__DIR__."/images/copy/*.png"));
    array_map('unlink', glob(__DIR__."/images/copy/*.jpg"));
    array_map('unlink', glob(__DIR__."/images/copy/*.JPG"));
    foreach ($images as $img){
        if($img->source === 'detail' || $img->source === 'more'){
            $img_type = ($img->source === 'detail')?'M':'A';
            echo '#'.$i.' ADD IMG >>> '.$img->_.'<br>';
            if($new_i_id = \Product\Images::AddImage($img->_, 'product',$prod_id,$img_type))
                echo 'REZ OK'.$new_i_id.'<br>';
            else
                echo 'REZ ERR'.$db->error.'<br>';
            $i++;
        }
    }
}

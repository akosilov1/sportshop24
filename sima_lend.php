<form action="">
    <input type="hidden" name="page" value="1" />
    <button name="action" value="load_items">Загрузить товары</button>
    <button name="action" value="load_cats">Загрузить категории</button>
</form>

<pre>
<?
error_reporting(E_ALL);
spl_autoload_register(function ($class_name) {
    echo(str_replace("\\","/",$class_name )).".php<br>";
    include __DIR__."/classes/App/".str_replace("\\","/",$class_name) . '.php';
});
$db = new \PDO("mysql:host=127.0.0.1;dbname=sima","root","root");
//$db_rez = $db->query("SELECT * FROM categories");
$page = ($_GET['page'])?$_GET['page']:1;
    switch ($_GET['action']){
        case 'load_cats':
            if($page == 1){
                $db->query("TRUNCATE TABLE categories");
            }
            $db_i = $db->prepare("INSERT INTO categories (`cat_id`,`name`,`path`,`level`) VALUES (:id,:name,:parent,:level)");
            print_r($db->errorInfo());
//print_r($db_rez->fetchAll(PDO::FETCH_ASSOC));

            $ar_cat = \Sima\Category::Get(array('path'=>16,'page'=>$page));
            if($ar_cat->items):?>
                <a href="?page=<?=$page+1?>&action=<?=$_GET['action']?>" id="next">NEXT <?=$page+1?> >>></a>
                <script>
                    document.getElementById("next").click();
                </script>
                <?
//print_r($ar_cat);
                foreach ($ar_cat->items as $cat){
                    print_r($cat);
                    $db_i->execute(array(':id'=>$cat->id,':name'=>$cat->name,':parent'=>$cat->path,':level'=>$cat->level));
                    print_r($db_i->errorInfo());
                    echo $db->lastInsertId();
                }
            endif;
        break;
        case 'load_items':
            print_r($rez = \Sima\Items::Get(['page' => $page, 'category_id' => 16, 'expand' => 'categories']));

            foreach ($rez->items as $item){
                print_r($ar_cat = \Sima\Functions::getCat(array_unique($item->categories)));
                if($ar_cat){
                    $item_category = \Sima\Functions::getCat(explode(".",$ar_cat[0]['path']));
                }
                print_r($item_category);
            }

            /*foreach ($items[0]->categories as $cat_id){
                $db->query("SELECT * FROM `categories` WHERE `cat_id` = ".$cat_id);
            }*/
        break;
    }

class newItem{
        var $product, $category_ids, $price, $company_id = 1, $status = 'A', $amount = 1,
            $full_description = '', $image_pairs = array(), $min_qty = 0, $product_code, $main_pair;
        function __construct($arr){

        }
        function AddCategories($ar_cat){
            $rez_cat = implode(",",$ar_cat);
            $this->category_ids = $rez_cat;
        }
        function AddImages($img){
            $this->main_pair = new newImage($img);
        }
}
class newImage{
        var $image_path;
    function __construct($img){
        $this->image_path = $img;
    }
}
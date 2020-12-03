<?php


namespace Sima;


class Functions
{
    static function getCat($ar_cat){
        global $db;
        $q = "SELECT * FROM `categories` WHERE `cat_id` IN (".implode(",",$ar_cat).")";
        $db_rez = $db->query($q);
        if($db->errorCode() == 00000)
            return $db_rez->fetchAll(\PDO::FETCH_ASSOC);
        else return false;
    }
}
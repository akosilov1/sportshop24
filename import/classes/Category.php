<?php


namespace Api;


class Category
{
    public $db_table, $db_table_desc;
    function __construct()
    {
        global $config;
        $this->db_table = $config['table_prefix']."categories";
        $this->db_table_desc = $config['table_prefix']."category_descriptions";
    }

    function Get(){

    }
}
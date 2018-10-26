<?php
/**
 * Created by PhpStorm.
 * User: Sergey
 * Date: 10.10.2018
 * Time: 12:17
 */

namespace Core\models;


use PDO;
use Support\Db\Db;

class Analyze
{
    public function getResult($trafficTable)
    {
        $param = ['phone' => ''];
        $helix = new Helix();
        $tablesWithPhones = $helix->selectTablesByParams($param);
        $db = Db::getConnection('helix');
        $output = [];
        foreach ($tablesWithPhones as $table ){
            $query = "select * from helix.{$table} where helix.{$table}.phone in (select traffics.{$trafficTable}.ta from traffics.{$trafficTable})";
            $prepare = $db->prepare($query);
            $prepare->execute();
            $output[$table] = $prepare->fetchAll(PDO::FETCH_ASSOC);
        }
        return $output;
    }
}